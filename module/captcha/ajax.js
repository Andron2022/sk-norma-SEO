
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
        if (http_request.overrideMimeType) {
		//http_request.overrideMimeType(/*'text/XML'*/);
		http_request.overrideMimeType(ajax_type);
	}
	return http_request;
}

function setText(id,text)
{
	var element = document.getElementById(id);
	var text_old = element.innerHTML;
	element.innerText = text;
	return html_old;
}

function setHtml(id,html)
{
	var element = document.getElementById(id);
	var html_old = element.innerHTML;
	element.innerHTML = html;
	return html_old;
}

function serializeform(formname)
{
 var form = document.forms[formname];
		var elements = form.elements;
		var paramstr = '';

		for(i = 0; i < elements.length; i++)
		{
			//alert(elements[i].type);
	        if(elements[i].name)
			{
				//if(paramstr != '') paramstr += '&';
				var part = '';
				if(elements[i].type == 'radio' || elements[i].type == 'checkbox')
				{if(elements[i].checked) part = elements[i].name + '=' + encodeURIComponent(elements[i].value);}
				else
				part = elements[i].name + '=' + encodeURIComponent(elements[i].value);

                                if(part != '' && paramstr != '') paramstr += '&';
                                paramstr += part;
			}
		}
		return paramstr;
}

function submitform(formname,target)
{
        var request = getHttpRequest();

        var form = document.forms[formname];
	var action = null;
	if(!form.action) action = window.location;
	else action = form.action;
	var params = serializeform(formname);
	params += '&ajax=1';
	var url = action;
	var data = null;
	var method = null;
	if(!form.method) method = 'get';
	else method = form.method;
	if(method == 'get') url += '?' + params;
	else data = params;
	
	setIndicator('ajax-loader.gif',target);

	request.open(method,url,true);
	if(method == 'get') request.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
    if(method == 'post') request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	request.send(data);
	//request.onreadystatechange = callback;
	request.onreadystatechange = function()
	{
		if(request.readyState == 4 && request.status == 200) setHtml(target,request.responseText);
	}
}

function load(url,target)
{
        var request = getHttpRequest();
        setIndicator('/module/rating/ajax-loader.gif',target);
        request.open('GET',url,true);
	request.send(null);

	request.onreadystatechange = function()
	{
		if(request.readyState == 4 && request.status == 200) setHtml(target,request.responseText);
	}
}

function setIndicator(src,target)
{
     setHtml(target,'<img src="'+src+'" />');
}

var img = new Image();
img.src = 'ajax-loader.gif';

var ajax_type = 'text/html; charset=windows-1251';