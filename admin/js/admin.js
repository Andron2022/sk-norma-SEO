    (function ($) {
      // custom css expression for a case-insensitive contains()
      jQuery.expr[':'].Contains = function(a,i,m){
          return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
      };

function listFilter(header, list) { // header is any element, list is an unordered list
    // create and add the filter form to the header
    var form = $("<form>").attr({"class":"filterform","action":"#"}),
        input = $("<input>").attr({"class":"filterinput","type":"text"});
    $(form).append(input).appendTo(header);

    $(input)
      .change( function () {
        var filter = $(this).val();
        if(filter) {
          // this finds all links in a list that contain the input,
          // and hide the ones not containing the input while showing the ones that do
          $(list).find("a:not(:Contains(" + filter + "))").parent().slideUp();
          $(list).find("a:Contains(" + filter + ")").parent().slideDown();
        } else {
          $(list).find("li").slideDown();
        }
        return false;
      })
    .keyup( function () {
        // fire the above change event after every letter
        $(this).change();
    });
}

    //ondomready
    $(function () {
      listFilter($("#header"), $("#list"));
    });
    $(function () {
      listFilter($("#header_pubs"), $("#list_pub"));
    });
  }(jQuery));

  isDOM=document.getElementById; //DOM1 browser (MSIE 5+, Netscape 6, Opera 5+)
  isMSIE=document.all && document.all.item; //Microsoft Internet Explorer 4+
  isNetscape4=document.layers; //Netscape 4.*
  isOpera=window.opera; //Opera
  isOpera5=isOpera && isDOM; //Opera 5+
  isMSIE5=isDOM && isMSIE; //MSIE 5+
  isMozilla=isNetscape6=isDOM && !isMSIE && !isOpera; //Mozilla или Netscape 6
    
  var html = document.documentElement;
  var body = document.body;
  
  var hint = null;
  
  function setmousepos(e)
  {
    var x = 0, y = 0;
  
    if (!e) e = window.event;
  
    if (e.pageX || e.pageY)
    {
      x = e.pageX;
      y = e.pageY;
    }
    else if (e.clientX || e.clientY)
    {
      x = e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft) - document.documentElement.clientLeft;
      y = e.clientY + (document.documentElement.scrollTop || document.body.scrollTop) - document.documentElement.clientTop;
    }
  
    mousex=x;
    mousey=y;
  }
  
  
  function showhint(e,id)
  {
          // добавить target для IE
          if (!e.target) {
          e.target = e.srcElement;
          }
  
  
  
         setmousepos(e);
  
  	var elem = e.target;
  	//if(elem.className.indexOf('hint_link_')!=-1 /*|| (elem.parentNode && elem.parentNode.className.indexOf('hint_link_')!=-1)*/)
  	{
                  //var id = elem.className.replace(/hint_link_/g,'');
                  if(isDOM) hint = document.getElementById('hint_'+id);
                  else hint = document.all['hint_'+id];
                  if(hint.innerHTML != '' && hint)
                  {
                  hint.style.display = 'block';
                  hint.style.visibility = 'visible';
  		hint.style.position = 'absolute';
  		hint.style.top = mousey + 6;
  		hint.style.left = mousex + 6;
  		hint.style.zIndex = 99;
  		}
  	}
  
  
          return true;
  }

  if(isNetscape4)
  { window.captureEvents(Event.MOUSEOVER|Event.MOUSEOUT|Event.MOUSEMOVE);}
  //if(isMozilla) document.onmouseover = showhint;
  //else /*if(isMSIE || isOpera)*/ document.onmouseover=function(){ showhint(event);}
  
  
  //if(isMozilla) document.onmouseout = hidehint;
  //else /*if(isMSIE || isOpera)*/ document.onmouseout=function(){ hidehint(event);}
  
  //if(isMozilla) document.onmousemove = movehint;
  //else /*if(isMSIE || isOpera)*/ document.onmousemove=function(){ movehint(event);}
  
  //window.onclick = hidehint;
  
  
  function hidehint(e)
  {
  
  	if (!e.target) {
          e.target = e.srcElement;
          }
          var elem = e.target;
  	//if(elem.className.indexOf('hint_link_')!=-1)
  	{
                  var id = elem.className.replace(/hint_link_/g,'');
                  if(hint!=null)
                  {
                  hint.style.display = 'none';
                  //hint.style.visibility = 'hidden';
                  }
  	}
  
  	return true;
  }
  
  function movehint(e)
  {
          setmousepos(e);
          if (!e.target) {
          e.target = e.srcElement;
          }
          var elem = e.target;
  	//if(elem.className.indexOf('hint_link_')!=-1)
  	{
               var id = elem.className.replace(/hint_link_/g,'');
                  //if(isDOM) hint = document.getElementById('hint_'+id);
                  //else hint = document.all['hint_'+id];
               if(hint != null)
               {
               hint.style.top = mousey + 6;
               hint.style.left = mousex + 6;
               }
          }
          /*else
  	{
  		if(hint) hint.style.display = 'none';
  	}*/
  	return true;
  }
  

  function ImgWin(src,title,width,height)
  {  
    var width1 = Number(width) + 40;
    var height1 = Number(height) + 40;
    var leftvar = (screen.width-width1)/2;
    var topvar = (screen.height-height1)/2;
    if(topvar < 0) topvar = 0;
    if(leftvar < 0) leftvar = 0;

    var param = "height="+height1+",width="+width1+",left="+leftvar+",top="+topvar+",status=no,toolbar=no,menubar=no";
    var NewWin=window.open("", "", param);
    NewWin.document.write('<html><head>');
    NewWin.document.write('<title>'+title+'</title></head>');
    NewWin.document.write('<body bgcolor="#E0E0E0" rightmargin="20" leftmargin="20">');
    NewWin.document.write('<center>');
    NewWin.document.write('<a href="#" style="text-decoration: none;" onClick="self.close();"><img src="'+src+'" border="0" alt="'+title+'" title="'+title+'" width="'+width+'" /></a><br />');
    NewWin.document.write('<a href="#" style="text-decoration: none;" onClick="self.close();">'+close_window+'</a>');
    NewWin.document.write('</center>');
    NewWin.document.write('</body>');
    NewWin.document.write('</html>');
  }

  function CheckAll854(Element,Name)
  {
  $('input[name='+Name+']').each(function() { $(this).attr('checked',!$(this).attr('checked')); });
  }
  
  function CheckAll(Element,Name)
{
    Name = Name.split('[').join('\\[').split(']').join('\\]');
	$('input[name='+Name+']').each(function() { $(this).attr('checked',!$(this).attr('checked')); });
}
  
  function CheckGroup(Element,Name, Values){
  if(document.getElementById) {
  	thisCheckBoxes = Element.parentNode.parentNode.parentNode.getElementsByTagName('input');
  	for (i = 1; i < thisCheckBoxes.length; i++){
  		if (thisCheckBoxes[i].name == Name && in_array(thisCheckBoxes[i].value,Values)){
  			thisCheckBoxes[i].checked = Element.checked;
  			Colorize(document.getElementById(thisCheckBoxes[i].id.replace('cb','tr')), thisCheckBoxes[i]);
  		}
  	}
  	}
  }
  
  function Colorize(Element, CBElement){
  if(document.getElementById) {
  	if(Element && CBElement){
  		Element.className = ( CBElement.checked ? 'selected' : 'default' );
  	}
  }
  }
  
  function CheckRadioTR(Element){
  if(document.getElementById) {
  	CheckTR(Element);
  	thisTRs = Element.parentNode.getElementsByTagName('tr');
  	for (i = 0; i < thisTRs.length; i++){
  		if (thisTRs[i].id != Element.id && thisTRs[i].className != 'header') thisTRs[i].className = 'default';
  	}
  }
  }
  
  function CheckTR(Element){
  if(document.getElementById) {
  	thisCheckbox = document.getElementById(Element.id.replace('tr','cb'));
  	thisCheckbox.checked = !thisCheckbox.checked;
  	Colorize(Element, thisCheckbox);
  }
  }
  
  function CheckCB(Element){
  if(document.getElementById) {
  	if(document.getElementById(Element.id.replace('cb','tr'))){Element.checked = !Element.checked;}
  }
  }
  
  function in_array(val, arr){
  	for(var i = 0; i < arr.length; i++){
  	if(val == arr[i]) return true;
  	}
  	return false;
  }
  
function ClearSearchInput(el,opt)
{
    if(opt)
    {
        $(el).val('');
    }
    else
    {
        if($(el).val() == '')
        $(el).val('поиск');
    }
}


function MoveUp(img_id)
{
    var parent_table_id = $(img_id).parents('table').attr('id');
    var prev_table_id = $('#'+parent_table_id).prev('table').attr('id');

    var curr_pos = $('#'+parent_table_id+'_position').val();
    var prev_pos = $('#'+prev_table_id+'_position').val();

    $('#'+parent_table_id+'_position_text').html(prev_pos);
    $('#'+prev_table_id+'_position_text').html(curr_pos);
    $('#'+parent_table_id+'_position').val(prev_pos);
    $('#'+prev_table_id+'_position').val(curr_pos);

    $('#'+prev_table_id).before($('#'+parent_table_id));
}

function MoveDown(img_id)
{
    var parent_table_id = $(img_id).parents('table').attr('id');
    var next_table_id = $('#'+parent_table_id).next('table').attr('id');

    var curr_pos = $('#'+parent_table_id+'_position').val();
    var next_pos = $('#'+next_table_id+'_position').val();

    $('#'+parent_table_id+'_position_text').html(next_pos);
    $('#'+next_table_id+'_position_text').html(curr_pos);
    $('#'+parent_table_id+'_position').val(next_pos);
    $('#'+next_table_id+'_position').val(curr_pos);


    $('#'+next_table_id).after($('#'+parent_table_id));
}


	$(function() {
	
			$("#choose_category_form").dialog({
			autoOpen: false,
			height: 400,
			width: 500,
			modal: false,
			draggable: false,
			resizable: false,
			buttons: {
				'Выбрать страницу': function() {
					var chosen_parent_id = $('input[name=id_parent]:checked').val();
					var chosen_parent_text = $('#categ'+chosen_parent_id).html();
					$('#verh_rubrika').html('&raquo; '+chosen_parent_text);
                                        $('#id_parental').val(chosen_parent_id);
					$(this).dialog('close');
				},
				Отмена: function() {
					$(this).dialog('close');
				}
			}
		});


			$("#choose_razdel_form").dialog({
			autoOpen: false,
			height: 400,
			width: 500,
			modal: false,
			draggable: false,
			resizable: false,
			buttons: {
				'Выбрать': function() {
					
					$('#rubriki').html('');
                                        $('#id_razdelov').val('');
                                        var id_razdelov_array = [];
					$("input[name='categs[]']:checked:enabled").each(function()
					{
						$('#rubriki').append('&raquo;'+$('#categ'+$(this).val()).html()+'<br>');
                                                id_razdelov_array.push($(this).val());
					});
                                        $('#id_razdelov').val(id_razdelov_array);
					
					$(this).dialog('close');
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			}
		});		
	
			$("#choose_rubrik_form").dialog({
			autoOpen: false,
			height: 400,
			width: 500,
			modal: false,
			draggable: false,
			resizable: false,
			buttons: {
				'Выбрать': function() {
					
					$('#rubriki').html('');
                                        $('#id_razdelov').val('');
                                        var id_razdelov_array = [];
					$("input[name='pub_categs[]']:checked:enabled").each(function()
					{
						$('#rubriki').append('&raquo;'+$('#categ'+$(this).val()).html()+'<br>');
                                                id_razdelov_array.push($(this).val());
					});
                                        $('#id_razdelov').val(id_razdelov_array);
					
					$(this).dialog('close');
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			}
		});
		$("#choose_product_form").dialog({
			autoOpen: false,
			height: 400,
			width: 600,
			modal: false,
			draggable: false,
			resizable: true,
			buttons: {
				'Привязать предложения': function() {
					
					$('#produkti').html('');
                                        $('#id_produktov').val('');
                                        var id_produktov_array = [];
					$("input[name='products[]']:checked:enabled").each(function()
					{
						$('#produkti').append('&raquo; '+$('#produkt'+$(this).val()).html()+'<br>');
                                                id_produktov_array.push($(this).val());
					});
					$('#id_produktov').val(id_produktov_array);
                                        
					$(this).dialog('close');
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			}
		});

		$("#choose_pub_form").dialog({
			autoOpen: false,
			height: 400,
			width: 600,
			modal: false,
			draggable: false,
			resizable: true,
			buttons: {
				'Привязать публикации': function() {
					
					$('#pubs').html('');
                                        $('#id_pubs').val('');
                                        var id_pubs_array = [];
					$("input[name='pubs[]']:checked:enabled").each(function()
					{
						$('#pubs').append('&raquo; '+$('#pub'+$(this).val()).html()+'<br>');
                                                id_pubs_array.push($(this).val());
					});
					$('#id_pubs').val(id_pubs_array);
                                        
					$(this).dialog('close');
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			}
		});
		
	});
