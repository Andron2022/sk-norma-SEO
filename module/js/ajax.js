
function getelem(elemid)
{
	return document.getElementById(elemid);
}

function setIndicator(src,target)
{

     setHtml(target,'<img src="'+src+'" />');

}


function getHttpRequest()
{
        if(window.XMLHttpRequest){
        http_request = new XMLHttpRequest();
        }
	else if (window.ActiveXObject) { // IE
	try {
		http_request = new ActiveXObject("Msxml2.XMLHTTP");
	}
	catch (e) {
	try {
		http_request = new ActiveXObject("Microsoft.XMLHTTP");
	}
	catch (e) {}
	}
	}
	if (!http_request) {
		alert('Невозможно создать экземпляр класса XMLHTTP');
		return false;
	}
        /*if (http_request.overrideMimeType) {
		http_request.overrideMimeType('text/XML');
	}*/
	return http_request;
}

function elems(xmldoc,tagname)
{
	return xmldoc.getElementsByTagName(tagname);
}

function elemvalue(elem)
{
	return elem.firstChild.nodeValue
}

function setHtml(id,html)
{
	var element = document.getElementById(id);
	var html_old = element.innerHTML;
	element.innerHTML = html;
	return html_old;
}

function myajax()
{
        var request = getHttpRequest();
        this.type = 'text/xml';

	this.request = request;

	this.indicator = null;
	this.onsuccess = null;
	this.onError = onError;

	/*this.callback = function()
	{
		if(request.readyState != 4) return;
		if(request.status == 200)
		{
			if(this.onsuccess) this.onsuccess();
			else alert(request.responseText);
		}
		else
		{
			if(this.onError) this.onError();
			else alert(request.statusText);
		}
	}*/

        this.setType = setType;

	this.setIndicator = function(src)
	{
		var img = new Image();
		img.src = src;
		this.indicator = src;
	}

	this.load = load;

	this.serializeform = serializeform;

	this.setHtml = function(elemid,HtmlText)
	{
		getelem(elemid).innetHTML = HtmlText;
	}

	this.submitform = submitform;

	return this;
}

function load(url,target)
{
                var request = getHttpRequest();
                if(this.indicator && target != '') document.getElementById(target).innerHTML = '<img src="'+this.indicator+'" />';
                if(url.indexOf('?') == -1) url += '?';else url += '&';
                url += 'ajax=1';
		request.open('get',url,true);
		request.send(null);

		var onsuccess = null;

		var args = load.arguments;
		if(args.length > 3) onsuccess = args[3];

        //this.request.onreadystatechange = this.callback;
        onError = this.onError;
		request.onreadystatechange = function()
		{
			if(request.readyState != 4) return;
			if(request.status == 200)
			{
				if(onsuccess)
				{
					setHtml(target,'');
					onsuccess(request.responseText);
				}
				else
				{
					elem = document.getElementById(target);
					elem.innerHTML = request.responseText;
				}
			}
			else
			{
				onError();
			}
		}
}

function submitform(formname,target)
	{
                var request = getHttpRequest();
                var form = document.forms[formname];
		var action = null;
		if(!form.action) action = window.location;
		else action = form.action;
		var params = this.serializeform(formname);
		params += '&ajax=1';
		var url = action;
		var data = null;
		var method = null;
		if(!form.method) method = 'get';
		else method = form.method;
		if(method == 'get') url += '?' + params;
		else data = params;

		var args = submitform.arguments;
		if(args.length > 2) onsuccess = args[2];

		var onsuccess = null;
		var args = submitform.arguments;
		if(args.length > 2) onsuccess = args[2];

		if(this.indicator) document.getElementById(target).innerHTML = '<img src="'+this.indicator+'" />';

                if(request.overrideMimeType) request.overrideMimeType(this.type);
                request.open(method,url,true);
        if(method == 'post') request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

		onError = this.onError;
		request.onreadystatechange = function()
		{
			if(request.readyState != 4) return;
			if(request.status == 200)
			{
				if(onsuccess)
				{
					setHtml(target,'');
					onsuccess(request.responseText);
				}
				else
				{
					elem = document.getElementById(target);
					elem.innerHTML = request.responseText;
				}
			}
			else
			{
				//onError();
			}
		}

		request.send(data);
}

function onError()
{
		alert('Error: ' + this.request.status + ' ' + this.request.statusText);
}

function setType(type)
{
    	        this.type = type;
}

function serializeform(formname)
{
  var form = document.forms[formname];
	var elements = form.elements;
	var paramstr = '';

	var params = new Array();

	for(i = 0; i < elements.length; i++)
	{
		//alert(elements[i].value);
        if(elements[i].name)
		{
             if(elements[i].type != 'checkbox' && elements[i].type != 'radio')
             {
              params.push([elements[i].name, elements[i].value]);
             }
             else
             {
             	if(elements[i].checked)
             	//params[elements[i].name] = elements[i].value;
             	params.push([elements[i].name, elements[i].value]);
             }
             /*if(paramstr != '') paramstr += '&';
			paramstr += elements[i].name + '=' + encodeURIComponent(elements[i].value);*/
		}
	}

	params.push(['ajax' ,1]);

	for(var i = 0; i < params.length; i++)
	{
		if(paramstr != '') paramstr += '&' + params[i][0] + '='+ encodeURIComponent(params[i][1]);
		else paramstr += params[i][0] + '='+ encodeURIComponent(params[i][1]);
	}

	return paramstr;
}

function ajax_load(url,target)
{
        var callback = null;
        if(ajax_load.arguments.length > 2) indicator = tpl + ajax_load.arguments[2];
        else indicator = '/tpl/default/images/ajax-loader.gif';
        if(ajax_load.arguments.length > 3) callback = ajax_load.arguments[3];

        if(url.indexOf('?') == -1) url += '?';else url += '&';
        url += 'ajax=1';
        var req = new Request(
	{url: url,
	evalScripts: true,
	noCache: true,
	onSuccess:function(txt)
	{
		$(target).set('html',txt);
		if(callback) callback(txt);
	}
	});
    if(indicator) $(target).set('html','<img src="'+indicator+'" />');
	req.send();
}

function ajax_submitform(formname,target)
{
     var act = $(formname).get('action');
     if(ajax_submitform.arguments.length > 2) indicator = ajax_submitform.arguments[2];
     $(formname).set('send',{onSuccess: function(response)
	{
		$(target).set('html',response);
		//$('indicator').set('html','');
	}
	 });
      if(indicator) $(indicator).set('html','<img src="/tpl/default/images/ajax-loader.gif" />');
      $(formname).send(act+'?ajax=1');
}

var ajax = new myajax();