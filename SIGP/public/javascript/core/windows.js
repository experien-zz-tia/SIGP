/*
* Kumbia Enterprise Framework
* Window Object Manipulation Base Functions
*
* @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
**/

var WindowUtilities = {

  getWindowScroll: function(parent) {
    var T, L, W, H;
    parent = parent || document.body;
    if (parent != document.body) {
      T = parent.scrollTop;
      L = parent.scrollLeft;
      W = parent.scrollWidth;
      H = parent.scrollHeight;
    }
    else {
      var w = window;
      with (w.document) {
        if (w.document.documentElement && documentElement.scrollTop) {
          T = documentElement.scrollTop;
          L = documentElement.scrollLeft;
        } else if (w.document.body) {
          T = body.scrollTop;
          L = body.scrollLeft;
        }
        if (w.innerWidth) {
          W = w.innerWidth;
          H = w.innerHeight;
        } else if (w.document.documentElement && documentElement.clientWidth) {
          W = documentElement.clientWidth;
          H = documentElement.clientHeight;
        } else {
          W = body.offsetWidth;
          H = body.offsetHeight
        }
      }
    }
    return { top: T, left: L, width: W, height: H };
  },

  getPageSize: function(parent){
    parent = parent || document.body;
    var windowWidth, windowHeight;
    var pageHeight, pageWidth;
    if (parent != document.body) {
      windowWidth = parent.getWidth();
      windowHeight = parent.getHeight();
      pageWidth = parent.scrollWidth;
      pageHeight = parent.scrollHeight;
    }
    else {
      var xScroll, yScroll;

      if (window.innerHeight && window.scrollMaxY) {
        xScroll = document.body.scrollWidth;
        yScroll = window.innerHeight + window.scrollMaxY;
      } else if (document.body.scrollHeight > document.body.offsetHeight){
        xScroll = document.body.scrollWidth;
        yScroll = document.body.scrollHeight;
      } else {
        xScroll = document.body.offsetWidth;
        yScroll = document.body.offsetHeight;
      }
      if (self.innerHeight) {
        windowWidth = self.innerWidth;
        windowHeight = self.innerHeight;
      } else if (document.documentElement && document.documentElement.clientHeight) {
        windowWidth = document.documentElement.clientWidth;
        windowHeight = document.documentElement.clientHeight;
      } else if (document.body) {
        windowWidth = document.body.clientWidth;
        windowHeight = document.body.clientHeight;
      }
      if(yScroll < windowHeight){
        pageHeight = windowHeight;
      } else {
        pageHeight = yScroll;
      }
      if(xScroll < windowWidth){
        pageWidth = windowWidth;
      } else {
        pageWidth = xScroll;
      }
    }
    return {pageWidth: pageWidth ,pageHeight: pageHeight , windowWidth: windowWidth, windowHeight: windowHeight};
  }
};

$W = function(objectName) {
	return document.frames('openWindow').document.getElementById(objectName)
}

var WINDOW = {
	open: function(properties){
		if($('myWindow')){
			return;
		};
		var windowScroll = WindowUtilities.getWindowScroll(document.body);
	    var pageSize = WindowUtilities.getPageSize(document.body);
		var obj = document.createElement("DIV");
		if(!properties.title){
			properties.title = ""
		};
		if(!properties.url){
			properties.url = properties.action
		};
	    left = parseInt((pageSize.windowWidth - (parseInt(properties.width)+36))/2);
	    left += windowScroll.left;
	    obj.style.left = left+"px"
		if(typeof properties.width != "undefined"){
			obj.style.width = properties.width;
		};
		if(typeof properties.height != "undefined"){
			obj.style.height = (parseInt(properties.height)+10)+"px";
		};
		obj.hide();
		var html = "<table cellspacing='0' cellpadding='0' width='100%'><tr>"+
		"<td align='center' id='myWindowTitle'>"+properties.title+"</td></tr>"+
		"<tr><td id='myWindowData'></td></tr></table>";
		obj.innerHTML = html;
		obj.id = "myWindow"
		document.body.appendChild(obj);
		new Draggable(obj.id);
		if(typeof properties.onclose != "undefined"){
			WINDOW.onclose = properties.onclose;
		};
		if(typeof properties.onbeforeclose != "undefined"){
			WINDOW.onbeforeclose = properties.onbeforeclose;
		};
		obj.close = function(action){
			var myWindow = $("myWindow");
			var shadowWin = $('shadow_win');
			if(typeof properties.onbeforeclose != "undefined"){
				if(properties.onbeforeclose.call(this, action)==false){
					return;
				}
			};
			if(typeof myWindow.onclose != "undefined"){
				properties.onclose = myWindow.onclose;
			};
			document.body.removeChild(myWindow);
			if(shadowWin){
				document.body.removeChild(shadowWin);
			};
			if(typeof properties.onclose != "undefined"){
				properties.onclose.call(this, action)
			}
		};
		new Ajax.Request(Utils.getKumbiaURL(properties.url), {
			method: 'GET',
			onSuccess: function(properties, t){
				$('myWindowData').update(t.responseText);
				if(typeof properties.afterRender != "undefined"){
					properties.afterRender();
				};
				var div = document.createElement("DIV")
				div.id = "shadow_win";
				$(div).setOpacity(0.2);
				document.body.appendChild(div);
				$('myWindow').show();
			}.bind(this, properties)
		});
	}
}
