


function lastComment(id,type)
{
	var request = getHttpRequest();
	var elem = document.getElementById("comment_list");
	request.open('post',"/ajax/last_comment/",true);
	request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	request.onreadystatechange = function()
	{
		if(request.readyState == 4 && request.status == 200)
		{

			if(request.getResponseHeader('Content-type').indexOf('text/xml') != -1)
			{

				if(request.responseXML.documentElement == null){

				  try {

				    request.responseXML.loadXML(request.responseText)

				  } catch (e) {

				    alert("Can't load");

				}
			}

			}

			//elem.innerHTML += request.responseText;
			var msg_elems = request.responseXML.getElementsByTagName('message');
			var msg = msg_elems[0].firstChild.nodeValue;
			var id_elems = request.responseXML.getElementsByTagName('id');

			elem.innerHTML = msg + elem.innerHTML;//request.responseText;

			var id = id_elems[0];
			if(id) id = id.firstChild.nodeValue;
			var comment_elem = document.getElementById('comment-'+id);
			comment_elem. scrollIntoView();

		}
	}

	request.send('record_type='+type+'&record_id='+id+'&ajax=1');
}

function postComment(formname)
{
	var request = getHttpRequest();
	var msgdiv = document.getElementById('commentresult');
	var form = document.forms[formname];
	request.open('post',form.action,true);
	var postdata = serializeform(formname);
	postdata += '&ajax=1';
	setIndicator('/tpl/default/images/ajax-loader.gif','commentresult');
	request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

	request.onreadystatechange = function()
	{
		if(request.readyState == 4 && request.status == 200)
		{
			if(request.getResponseHeader('Content-type').indexOf('text/xml') != -1)
			{

				if(request.responseXML.documentElement == null){

				  try {

				    request.responseXML.loadXML(request.responseText)

				  } catch (e) {

				    alert("Can't load");

				}

			}
				var result_elems = request.responseXML.getElementsByTagName('result');
				var msg_elems = request.responseXML.getElementsByTagName('message');
				var elem = result_elems.item(0);
				var message = msg_elems[0].firstChild.nodeValue;
				var result = elem.firstChild.nodeValue;

				var msgdiv = document.getElementById('commentresult');
				//if(result == 'error') form.reset();
				if(result == 'error')
				{
					msgdiv.innerHTML = '<span style="color: red; font-weight: bold;">'+message+'</span>\n';

				}
				else
				{
					form.reset();
					msgdiv.innerHTML = '<span style="color: green; font-weight: bold;">'+message+'</span>\n';
					lastComment(form.record_id.value,form.record_type.value);
				}

				var captcha_name = form.captcha_name.value;
				var captcha = document.getElementById(captcha_name + "_captcha");
				captcha.src = '/module/captcha/index.php?name='+captcha_name+'&rnd='+Math.random();
			}
		}
	}

	request.send(postdata);
}