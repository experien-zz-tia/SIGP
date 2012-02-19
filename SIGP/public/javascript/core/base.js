/***************************************************************************
* Kumbia Enterprise Framework (New BSD License);
****************************************************************************
* (c) 2008-2009 Andres Felipe Gutierrez <gutierrezandresfelipe at gmail.com>
* (c) 2008-2009 Louder Technology COL.
****************************************************************************/

var dummy = function(){}

Object.extend(Array.prototype, {
	append: function(item){
		this[this.length] = item;
		return this;
	}
})

Object.extend(String.prototype, {

	explode: function(separator){
		return this.split(separator)
	},

	sprintf: function(format){
		var number;
		var fill = " ";
		if(format.startsWith("0")){
			number = format.substr(1)
			fill = "0";
		} else {
			number = format;
		}
		var limit = (this.toString().length-1)+(number.length-1);
		var return_string = "";
		for(var i=0;i<=limit;i++){
			return_string+=fill;
		}
		return return_string+this;
	}

})

Object.extend(Number.prototype, {

	upto: function(up, iterator){
		$R(this, up).each(iterator);
    	return this;
	},

	downto: function(down, iterator){
		$A($R(down, this)).reverse().each(iterator);
    	return this;
	},

	step: function(limit, step, iterator){
		range = []
		if(step>0){
			for(i=this;i<=limit;i+=step){
				range.append(i)
			}
		} else {
			for(i=this;i>=limit;i+=step){
				range.append(i)
			}
		}
		range.each(iterator);
    	return this;
	},

	next: function(){
		return this+1;
	}

});

//Obtiene una referencia a un ob
function $O(obj){
	if($("flid_"+obj)){
		return $("flid_"+obj);
	};
	return $(obj);
}

var Utils = {

	getKumbiaURL: function(url){
		if(typeof url == "undefined"){
			url = "";
		};
		if($Kumbia.app!=""){
			return $Kumbia.path+$Kumbia.app+"/"+url;
		} else {
			return $Kumbia.path+url;
		}
	},

	//Redirecciona la Ventana padre a un accion determinada
	redirectParentToAction: function(url){
		new Utils.redirectToAction(url, window.parent);
	},

	//Redirecciona la Ventana padre a un accion determinada
	redirectOpenerToAction: function(url){
		new Utils.redirectToAction(url, window.opener);
	},

	//Redirecciona una ventana a un url definido
	redirectToAction: function(url, win){
		win = win ? win : window;
		win.location = Utils.getKumbiaURL() + url;
	}
}

// Obtiene una referencia a un objeto del formulario generado
// o un document.getElementById
function $C(obj){
	return $("flid_"+obj);
}

// Obtiene el valor de un objeto de un formulario generado
function $V(obj){
	return $F("flid_"+obj);
}


/****************************************************
* Auth Functions
****************************************************/
//Funcion que envia un formulario via AJAX
function ajaxRemoteForm(form, up, callback){
	if(callback==undefined){
		callback = {};
	};
	new Ajax.Updater(up, form.action, {
		 method: "post",
		 asynchronous: true,
         evalScripts: true,
         onSuccess: function(transport){
			$(up).update(transport.responseText)
		},
		onLoaded: callback.before!=undefined ? callback.before: function(){},
		onComplete: callback.success!=undefined ? callback.success: function(){},
  		parameters: Form.serialize(form)
    });
  	return false;
}

var AJAX = new Object();

AJAX.xmlRequest = function(params){
	this.options = $H();
	if(!params.url && params.action){
		this.url = Utils.getKumbiaURL() + params.action;
	}
	if(params.parameters){
		this.url+= "/&"+params.parameters;
	} else {
		this.options.method = 'GET';
	}
	if(params.debug){
		alert(this.url);
	}
	if(this.action) {
		this.action = params.action;
	}
	if(params.asynchronous==undefined){
		this.options.asynchronous = true;
	} else {
		this.options.asynchronous = params.asynchronous;
	}
	if(params.callbacks){
		if(params.callbacks.oncomplete){
			this.options.onComplete = params.callbacks.oncomplete;
		}
		if(params.callbacks.before){
			this.options.onLoading = params.callbacks.before;
		}
		if(params.callbacks.success){
			this.options.onSuccess = params.callbacks.success;
		}
	}
	try {
		return new Ajax.Request(this.url, this.options)
	}
	catch(e){
		alert("KumbiaError: "+e.message+"["+e.name+"]");
	}
}


AJAX.viewRequest = function(params){
	this.options = {};
	if(!params.action){
		alert("KumbiaError: Ajax Action is not set!");
		return;
	};

	this.url = Utils.getKumbiaURL() + params.action;
	if(params.parameters){
		this.url+="&"+params.parameters;
	} else {
		this.options.method = 'GET';
	}
	this.action = params.action;
	if(params.debug){
		alert(this.action)
	};
	if(params.asynchronous==undefined) {
		this.asynchronous = true
	} else {
		this.asynchronous = params.asynchronous
	};

	if(params.callbacks){
		if(params.callbacks.oncomplete){
			this.options.onComplete = params.callbacks.oncomplete
		};
		if(params.callbacks.before){
			this.options.onLoading = params.callbacks.before
		};
		if(params.callbacks.success){
			this.options.onSuccess = params.callbacks.success
		}
	}

	container = params.container;
	this.options.evalScripts = true

	if(!$(container)){
		window.alert("KumbiaError: Container Ajax Object '"+container+"' Not Found")
		return null
	};

	try {
		return new Ajax.Updater(container, this.url, this.options)
	}
	catch(e){
		alert("KumbiaError: "+e.message+" ["+e.name+"]");
	}

}

AJAX.execute = function(params){
	this.options = {};
	if(!params.action){
		alert("KumbiaError: AJAX Action is not set!");
		return;
	};
	this.url = Utils.getKumbiaURL(params.action);
	if(params.parameters){
		this.url+="&"+params.parameters;
	} else {
		this.options.method = 'GET';
	}
	this.action = params.action;
	if(typeof params.debug != "undefined"){
		if(params.debug==true){
			alert(this.url)
		}
	};
	if(typeof params.asynchronous == "undefined") {
		this.asynchronous = false
	} else {
		this.asynchronous = params.asynchronous
	}

	if(params.callbacks){
		if(params.callbacks.oncomplete){
			this.options.onComplete = params.callbacks.onend;
		};
		if(params.callbacks.before){
			this.options.onLoading = params.callbacks.before;
		};
		if(params.callbacks.success){
			this.options.onSuccess = params.callbacks.success;
		}
	};
	try {
		return new Ajax.Request(this.url, this.options)
	}
	catch(e){
		alert("KumbiaError: "+e.message+" ["+e.name+"]");
	}
}

AJAX.query = function(queryAction){
	var me;
	new Ajax.Request(Utils.getKumbiaURL(queryAction), {
		method: 'GET',
		asynchronous: false,
		onSuccess: function(transport){
			var xml = transport.responseXML;
			var data = xml.getElementsByTagName("data");
			if(Prototype.Browser.IE){
				xmlValue = data[0].text;
			} else {
				xmlValue = data[0].textContent;
			}
			me = xmlValue;
		}
	});
	return me;
}

function gup(name){
	var regexS = "[\\?&]"+name+"=([^&#]*)";
	var regex = new RegExp(regexS);
	var tmpURL = window.location.href;
	var results = regex.exec(tmpURL);
	if(results==null){
		return "";
	} else {
		return results[1];
	}
}