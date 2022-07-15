////////////////////////// USER FUNCTIONS ///////////////////////////

navigator.isIE = navigator.appName == "Microsoft Internet Explorer";
navigator.isOpera = navigator.appName == "Opera";
if(navigator.isIE)
{
	var i = navigator.appVersion.indexOf("MSIE") + 4;
	var s = navigator.appVersion.substring(i);
	navigator.version = parseInt(s);
}
else
{
	navigator.version = parseInt(navigator.appVersion);
}

window.getWidth = function()
{
	if(navigator.isIE)
	{
		if(document.body == null) return 0;
		return document.body.offsetWidth;
	}
	else
	{
		return window.innerWidth;
	}
}

window.getHeight = function()
{
	if(navigator.isIE)
	{
		if(document.body == null) return 0;
		return document.body.offsetHeight;
	}
	else
	{
		return window.innerHeight;
	}
}

window.winW = window.getWidth();
window.winH = window.getHeight();

window.objectsToResize = new Array();
function updateSize(obj) { 
  obj.onResize(obj.resizer.clientWidth, obj.resizer.clientHeight); 
}

window.onresize = function() 
{
//	if(window.winW != window.getWidth() || window.winH != window.getHeight())
	{
//		alert(window.getWidth() + " " + window.getHeight());
		for(var i = 0; i < window.objectsToResize.length; i++)
			updateSize(window.objectsToResize[i]);
		window.winW = window.getWidth();
		window.winH = window.getHeight();
	}
};

function getContainer(container_id)
{
		var container = document.getElementById(container_id);
	//	if(container != undefined)
	//	   return container;

		//if you're in a constructor
		if(document.getElementById('site_container') != undefined)
		{
			var divs = container.getElementsByTagName('div');
			for(var i = 0; i < divs.length; i++)
				if(divs[i].id == 'code')
				{
					container = divs[i].parentNode.children[1];
					break;
				}
		}
		
		if(container == undefined)
		{
//			alert('Error: container is not found!');
                        window.status = 'Error: container is not found!';
			return;
		}
		
		return container;
}

////////////////////////////// COLOR ////////////////////////////////

function Color()
{
	if(arguments.length == 1)
	{
		var val = arguments[0];
		
		if(typeof(val) == "string")
			val = parseInt(val.replace("#", "0x"), 16);
			
		this.r = (val & 0xff0000) >> 16;
		this.g = (val & 0x00ff00) >> 8;
		this.b = (val & 0x0000ff);
	}
	else
	{
		this.r = arguments[0];
		this.g = arguments[1];
		this.b = arguments[2];
	}
	
	this.toHex = function()
	{
		var hex = (this.r << 16) | (this.g << 8) | this.b;
		var s = hex.toString(16);
		for(var i = 0; 6 - s.length; i++) s = "0" + s;
		return "#" + s;	
	}
	
	this.toString = function() { return this.toHex(); }
	this.add = function(color) { return new Color(this.r + color.r, this.g + color.g, this.b + color.b); }
	this.subtract = function(color) { return new Color(this.r - color.r, this.g - color.g, this.b - color.b); }
	this.multiply = function(factor) { return new Color(this.r * factor, this.g * factor, this.b * factor); }
	
	this.clamp = function()
	{
		return new Color(
			this.r < 0 ? 0 : this.r > 255 ? 255 : this.r,
			this.g < 0 ? 0 : this.g > 255 ? 255 : this.g,
			this.b < 0 ? 0 : this.b > 255 ? 255 : this.b);
	}
	
	this.invert = function() { return new Color(255, 255, 255).subtract(this); }
	this.lighten = function(amount) { return this.add(new Color(amount, amount, amount)).clamp(); }
	this.darken = function(amount) { return this.subtract(new Color(amount, amount, amount)).clamp(); }
	
	this.getTextColor = function()
	{
		var gray = this.toGray();
		var hsv = this.toHSV();
		
		if(gray > 0.6 * 255) 
		{
			hsv.v = 0.4;
			if(hsv.s != 0.0) hsv.s = 1.0;
		}
		else
		{ 
			hsv.v = 1.0;
			if(hsv.s != 0.0) hsv.s = 0.2;
		}
		
		var res = new Color(0, 0, 0);
		res.setHSV(hsv.h, hsv.s, hsv.v);
		return res;
	}
	
	this.getTextShade = function()
	{
		if(this.getValue() > 0.6) return this.multiply(0.4);
		else return this.multiply(4.0).clamp();
	}
	
	this.getTextColorBW = function()
	{
		var gray = this.toGray();
		
		if(gray > 0.6 * 255)  return new Color(0, 0, 0); //black
		return new Color(255, 255, 255); //white
	}	
	
	this.getValue = function() { return Math.max(this.r, this.g, this.b) / 255; }
	
	this.getSaturation = function()
	{
		var max = Math.max(this.r, this.g, this.b);
		if(max == 0) return 0;
		var min = Math.min(this.r, this.g, this.b);
		return  (max - min) / max;
	}
	
	this.getHue = function()
	{
		var max = Math.max(this.r, this.g, this.b);
		if(max == 0) return 0;
		var min = Math.min(this.r, this.g, this.b);				
		var s = (max - min) / max;
		
		if(max == this.r) return (this.g - this.b) / (6 * s);
		if(max == this.g) return 0.5 + (this.b - this.r) / (6 * s);
		if(max == this.b) return 2.0 / 3.0 + (this.r - this.g) / (6 * s);
	}
	
	this.toGray = function()
	{
		return this.r * 0.299 + this.g * 0.587 + this.b * 0.114;
	}
	
	this.toHSV = function()
	{
		var r = this.r / 255.0;
		var g = this.g / 255.0;
		var b = this.b / 255.0;
		//clamping
		r = r > 1 ? 1 : r < 0 ? 0 : r;
		g = g > 1 ? 1 : g < 0 ? 0 : g;
		b = b > 1 ? 1 : b < 0 ? 0 : b;
		
		var max = Math.max(r, g, b);
		var min = Math.min(r, g, b);
		var d = max - min;
		var v = max;
		var s = max > 0 ? d / max : 0;
		
		if(s == 0) h = 0;
		else
		{
			h = 60 * (r == max ? (g - b) / d : g == max ? 2 + (b - r) / d : 4 + (r - g) / d);
			if(h < 0) h += 360;
		}
		
		return {h:h, s:s, v:v};
	}
	
	this.setHSV = function(h, s, v)
	{
		while(h < 0) h += 360;
		h %= 360;
		
		//clamping
		s = s > 1 ? 1 : s < 0 ? 0 : s;
		v = v > 1 ? 1 : v < 0 ? 0 : v;
		
		var r, g, b, f, p, q, t;
		
		if(s == 0) r = g = b = v;
		else
		{
			h /= 60;
			f = h - (i = Math.floor(h));
			p = v * (1 - s);
			q = v * (1 - s * f);
			t = v * (1 - s * (1 - f));
			switch(i)
			{
				case 0: r = v; g = t; b = p; break;
				case 1: r = q; g = v; b = p; break;
				case 2: r = p; g = v; b = t; break;
				case 3: r = p; g = q; b = v; break;
				case 4: r = t; g = p; b = v; break;
				case 5: r = v; g = p; b = q; break;		
		   }
		}
		this.r = Math.round(r * 255);
		this.g = Math.round(g * 255);
		this.b = Math.round(b * 255);
	}
}

function updateColors()
{
	//with(window.parent) 
	{
		if(document.getElementById('site_place') == undefined) return;
		var containers = document.getElementsByTagName('div');
		for(var i = 0; i < containers.length; i++)
		{
			if(containers[i].svgstyle != undefined)
			{
				if(containers[i].svgstyle.updateColors != undefined)
					containers[i].svgstyle.updateColors();
			}
		}
	}
}

function sendColors()
{
//	var wnd = (window.parent != undefined) ? window.parent : window;
        var wnd = window;
	with(wnd)
	{
		styleColors = new Array();
	
		colors = new Array();
		colors.main = arguments[0];
		colors.active = arguments[1];
		colors.additional = arguments[2];
		
		//main color
		styleColors[0] = '#' + arguments[0];
		
		//active color
		if(arguments[1] != '') styleColors[1] = '#' + arguments[1];
		else styleColors[1] = new Color(arguments[0]).lighten(-20).toHex();
		
		//additional
		if(arguments[2] != '') styleColors[2] = '#' + arguments[2];
		else styleColors[2] = styleColors[0];
		
		clrBase = new Color(styleColors[0]);
		clrAct = new Color(styleColors[1]);
		clrExt = new Color(styleColors[2]);

		//////////////////////////////////////////////////////////////////////////////////////
		
		color = window.styleColors[0];
		colorText = new Color(color).getTextColor().toHex();
		colorTextBW = new Color(color).getTextColorBW().toHex();
		colorLight = new Color(color).lighten(50).toHex();
		colorLighter = new Color(color).lighten(100).toHex();
		colorLighter200 = new Color(color).lighten(200).toHex();
		colorDark = new Color(color).darken(50).toHex();
		colorDarker = new Color(color).darken(100).toHex();

		colorActive = window.styleColors[1];
		colorActiveText = new Color(colorActive).getTextColor().toHex();
		colorActiveTextBW = new Color(colorActive).getTextColorBW().toHex();
		colorActiveLight = new Color(colorActive).lighten(50).toHex();
		colorActiveLighter = new Color(colorActive).lighten(100).toHex();
		colorActiveDark = new Color(colorActive).darken(50).toHex();
		colorActiveDarker = new Color(colorActive).darken(100).toHex();		
		
		colorTextActive = colorActiveText;
	}
}


function addEvent(obj, evType, fn, useCapture)
{
	var ret = false;
	if(obj != null)
	{
		if(obj.addEventListener)
		{
			obj.addEventListener(evType, fn, useCapture);
			ret = true;
		}
		else if(obj.attachEvent)
		{
			obj.attachEvent("on" + evType, fn);
			ret = true;
		}
	}
	return ret;
}

//adds a table inside a container's div and moves all the child nodes of the container into the table
//in order to watch the container's resizing
//also adds onresize event to the window

function createResizer(container_id, obj)
{
	var container = undefined;
	
	if(typeof(container_id) == "string")
	{
		container = document.getElementById(container_id);
	}
	else
	{
		container = container_id;
	}

	//create a table with only one cell
	var tbl = document.createElement("table");
	tbl.setAttribute("cellpadding", "0");
	tbl.setAttribute("cellspacing", "0");
	tbl.style.width = "100%";
	var row = tbl.insertRow(0);
	var cell = row.insertCell(0);

	//move all the child nodes of the container's div to the table
	//and add the table to the container's div
	while(container.hasChildNodes())
	{
		if(container.firstChild.style != undefined)
			container.firstChild.style.position = 'relative';
		cell.appendChild(container.firstChild);
	}
	container.appendChild(tbl);
	
	window.objectsToResize.push(obj);
	
	var canvas = document.createElement('div');
	canvas.style.position = "absolute"; //to exclude from piling up
	cell.insertBefore(canvas, cell.firstChild);
	var g = Raphael(canvas, 101, 101);
	obj.resizer = cell;

	return g;
}
//////////////////////  EXTENDED DRAWING FUNCTIONS ////////////////////////////////////////////////

function rectx(x, y, w, h, r1, r2, r3, r4)
{
	var s = 'M' + x + ',' + (y + r1);
	
	if(r1 != 0) s += ' a' + r1 + ',' + r1 + ' 0 0,1 ' + r1 + ',-' + r1;
	s += ' h' + (w - r1 - r2);
	
	if(r2 != 0) s += ' a' + r2 + ',' + r2 + ' 0 0,1 ' + r2 + ',' + r2;
	s += ' v' + (h - r2 - r3);
	
	if(r3 != 0) s += ' a' + r3 + ',' + r3 + ' 0 0,1 -' + r3 + ',' + r3;
	s += ' h-' + (w - r3 - r4);
	
	if(r4 != 0) s += ' a' + r4 + ',' + r4 + ' 0 0,1 -' + r4 + ',-' + r4;
	s += ' z';
	
	return s;
}



////////////////////////////////////////////  BLOCK   /////////////////////////////////////////////////////////////////


function Block(container_id)
{	
	//define stub functions
	this.onCreate = function() { };
	this.onRecolor = function() { };
	this.onSize = function() { };

        this.update = function() {}	

	//get the container
	var container = getContainer(container_id);
	if(container == undefined) return;
	if(container.innerHTML == '') return;
	if (container.parentNode.id.indexOf('container_') > -1)
  	  container.parentNode.svgstyle = this;
	container.svgstyle = this;

	this.update = function()
	{
		//change HTML structure
		//create a table with only one cell
		var tbl = document.createElement('table');
		tbl.setAttribute("cellpadding", "0");
		tbl.setAttribute("cellspacing", "0");
		tbl.setAttribute("width", "100%");
		var row = tbl.insertRow(0);
		var cell = row.insertCell(0);
		cell.style.padding = "10px";

		//move all the child nodes of the container's div to the table
		//and add the table to the container's div
		while(container.hasChildNodes())
		{
			if(container.firstChild.style != undefined)
				container.firstChild.style.position = 'relative';
			cell.appendChild(container.firstChild);
		}
		
		//create Raphael's canvas
		var canvas = document.createElement('div');
		canvas.style.position = "absolute"; //to exclude from piling up
		tbl.style.position = "relative";
		tbl.style.zIndex = "1";
		
		container.appendChild(canvas);
		container.appendChild(tbl);
		
		cell.x = navigator.isIE ? 0 : 0.5;
		cell.y = navigator.isIE ? 0 : 0.5;
		cell.w = cell.clientWidth;
		cell.h = cell.clientHeight;
		var g = Raphael(canvas, 101, 101);
		this.onCreate(g, cell);
		tbl.raphael = g;
		
		this.onResize = function(width, height)
		{
			var tbl = this.resizer;
			
			var cell = tbl.rows[0].cells[0];
			cell.x = navigator.isIE ? 0 : 0.5;
			cell.y = navigator.isIE ? 0 : 0.5;
			cell.h = height - 1;
			cell.w = width - 1;
			this.onSize(cell);
			
			tbl.raphael.setSize(cell.w + 1, cell.h + 1);
			//raphael container
			tbl.parentNode.children[0].style.width = (cell.w + 1) + 'px';
			tbl.parentNode.children[0].style.height = (cell.h + 1) + 'px';
		}
		
		this.updateColors = function()
		{
			var cell = this.resizer.rows[0].cells[0];
			this.onRecolor(cell);
		}
		
		window.objectsToResize.push(this);
		this.resizer = tbl;
		this.updateColors();
		updateSize(this);
	}
}

//////////////////////////////////////////     MENU    ///////////////////////////////////////////////////////////////////////////

function Menu(container_id)
{
	//define stub functions
	this.onCreate = function() { };
	this.onSize = function() { };
	this.onMouseOver = function() { };
	this.onMouseOut = function() { };
	this.onColor = function() { };
	
	//get the container
	var container = getContainer(container_id);
	if(container == undefined) return;

	if (container.parentNode.id.indexOf('container_') > -1)
  	  container.parentNode.svgstyle = this;
        container.svgstyle = this;
	
	//get all the tables (menus)
	this.tables = container.getElementsByTagName('table');
		
	this.update = function()
	{
		//shift menu items by level and find the active menu item
		
		//loop through all the items in the menu
		for(var i = 0; i < this.tables.length; i++)
			for(var j = 0; j < this.tables[i].rows.length; j++) 
			for(var k = 0; k < this.tables[i].rows[j].cells.length; k++) 
			{
				//get the class of the menu item
				var cell = this.tables[i].rows[j].cells[k];
				var cls = cell.className;
				
				 //if the menu item is marked active
				var pos =  cls.indexOf('act');
				if(pos > 0) 
				{
					cell.active = true;
					cls = cls.substr(6);	//find the level for an active menu item
				} else cls = cls.substr(3); //find the level for all the other menu items
				
				//adjust padding
				cell.style.paddingLeft = (parseInt(cls) * 15).toString() + 'px';
			}
		

		//adding raphael
		for(var i = 0; i < this.tables.length; i++)
		{
			var raphaelContainer = this.tables[i].parentNode.children[0];
			//adjust the container's size to the table's that
			with(raphaelContainer.style) { width = this.tables[i].clientWidth + 'px'; height = this.tables[i].clientHeight + 'px'; }
			this.tables[i].raphaelContainer = raphaelContainer;
			this.tables[i].raphael = Raphael(raphaelContainer, this.tables[i].clientWidth + 1, this.tables[i].clientHeight + 1);
			
			if(i == 0 && this.tables[i].className == "hmenu") //for horizontal menu
			{
				var row = this.tables[i].rows[0];
				var h = this.tables[i].clientHeight - 1;
				for(var j = 0; j < row.cells.length; j++)
				{
					var cell = row.cells[j];
					cell.x = cell.offsetLeft + 1;
					cell.y = 0;
					cell.w = j != row.cells.length - 1 ? 
						row.cells[j + 1].offsetLeft - row.cells[j].offsetLeft : 
						this.tables[i].clientWidth - row.cells[j].offsetLeft - 1;
					cell.h = h;
					cell.ref = cell.getElementsByTagName('a')[0];
					cell.menu = this;
					cell.type = "horizontal";
					
					if(!navigator.isIE)
					{
						//remove antialiasing
						cell.x += 0.5;
						cell.y += 0.5;
					}
					var last_el;
                                        last_el = false;
					if (j == row.cells.length-1) {
                                           last_el = true;
                                        }
					this.onCreate(this.tables[i].raphael, cell, last_el);
					this.onColor(cell);
					
					cell.onmouseover = function()
					{
						this.menu.onMouseOver(this);
						if(this.getElementsByTagName('table').length > 0) 
						{
							var submenu = this.getElementsByTagName('table')[0];							
//							this.children[1].style.visibility = 'visible';
							submenu.parentNode.parentNode.style.visibility = "visible";
							if(navigator.isOpera && navigator.version >= 9)
							{
								var r = submenu.parentNode.children[0].children[0];
								r.style.visibility = "visible";
								//document.title = "top visible";
							}
						}
					}
					
					cell.onmouseout = function()
					{
						this.menu.onMouseOut(this);
						if(this.getElementsByTagName('table').length > 0) 
						{
							var submenu = this.getElementsByTagName('table')[0];							
							this.children[1].style.visibility = 'hidden';
							//submenu.parentNode.parentNode.style.visibility = "hidden";
							if(navigator.isOpera && navigator.version >= 9)
							{
								var r = submenu.parentNode.children[0].children[0];
								//document.title = "top hidden";
								r.style.visibility = "inherit";
							}							
						}
					}					
				}
			}
			else //for vertical menu and submenus
			{
				if(this.tables[i].className == 'submenu')
				{
					this.tables[i].parentNode.parentNode.style.visibility = 'visible';
				}
				for(var j = 0; j < this.tables[i].rows.length; j++)
				{
					var cell = this.tables[i].rows[j].cells[0];
					cell.x = 0.5;
					cell.y = cell.offsetTop + (navigator.isIE ? 0 : 0.5);
					cell.w = this.tables[i].clientWidth;
					cell.h = cell.clientHeight;
					cell.ref = cell.children[0];
					cell.menu = this;
					if(navigator.isIE)
					{
						cell.x = 3;
						cell.w -= 4;
					}
					this.onCreate(this.tables[i].raphael, cell);
					this.onColor(cell);
					
					cell.onmouseover = function()
					{
						this.menu.onMouseOver(this);
						
						if(this.getElementsByTagName('table').length > 0)
						{
							var submenu = this.getElementsByTagName('table')[0];
							submenu.parentNode.parentNode.style.visibility = "visible";
							submenu.parentNode.style.left = (this.clientWidth - (navigator.isIE ? 3 : 0) - parseInt(this.style.paddingLeft)) + 'px';
							submenu.parentNode.style.top = (-this.clientHeight) + 'px';
							if(navigator.isOpera && navigator.version >= 9)
							{
								var r = submenu.parentNode.children[0].children[0];
								r.style.visibility = "visible";
							}
						}
					}
					cell.onmouseout = function()
					{
						this.menu.onMouseOut(this);
						//this.parentNode.rc.attr({fill:"90-" + window.styleColors[0] + "-#fff"});
						//this.children[0].style.color = (new Color(window.styleColors[0])).getTextColor().toHex();
						if(this.getElementsByTagName('table').length > 0)
						{
							var submenu = this.getElementsByTagName('table')[0];
							submenu.parentNode.parentNode.style.visibility = "hidden";
							
							if(navigator.isOpera && navigator.version >= 9)
							{
								var r = submenu.parentNode.children[0].children[0];
								r.style.visibility = "hidden";
							}
						}
					}
				}
				if(this.tables[i].className == 'submenu') this.tables[i].parentNode.parentNode.style.visibility = 'hidden';
			}
		}
		
		this.resizeSubmenu = function(tbl)
		{
			var width = tbl.clientWidth;
			var height = tbl.clientHeight;
			alert('w ' + width + ' h ' + height);
			tbl.raphael.setSize(width, height);
			//raphael container
			tbl.raphaelContainer.style.width = width + 'px';
			tbl.raphaelContainer.style.height = height + 'px';

			//if(tbl.className == 'submenu')
			for(var i = 0; i < tbl.rows.length; i++)
			{
				var cell = tbl.rows[i].cells[0];
				cell.x = navigator.isIE ? 0 : 0.5;
				cell.y = cell.offsetTop + (navigator.isIE ? 0 : 0.5);
				cell.w = cell.clientWidth - (navigator.isIE ? 4 : 0);
				cell.h = cell.clientHeight;
				this.onSize(cell);
			}
		}
		
		
		this.onResize = function(width, height)
		{
			var tbl = this.resizer;
			if(tbl.className == 'hmenu')
				tbl.raphael.setSize(width, height);
			else
				tbl.raphael.setSize(width + 1, height + 1);
			//raphael container
			tbl.parentNode.children[0].style.width = width + 'px';
			tbl.parentNode.children[0].style.height = height + 'px';

			if(tbl.className == 'hmenu')
			{
				var cells = tbl.rows[0].cells;
				for(var i = 0; i < cells.length; i++) 
				{
					var x = cells[i].offsetLeft;
					cells[i].x = cells[i].offsetLeft + (navigator.isIE ? 0 : 0.5);
					cells[i].h = height - 1;
					cells[i].w = i != cells.length - 1 ? cells[i + 1].offsetLeft - cells[i].offsetLeft : width - row.cells[i].offsetLeft - 1;
					this.onSize(cells[i]);
				}
			}
			else //if(tbl.className == 'vmenu')
			{
				for(var i = 0; i < tbl.rows.length; i++)
				{
					var cell = tbl.rows[i].cells[0];
					cell.x = navigator.isIE ? 0 : 0.5;
					cell.y = cell.offsetTop + (navigator.isIE ? 0 : 0.5);
					cell.w = cell.clientWidth - (navigator.isIE ? 4 : 0);
					cell.h = cell.clientHeight;
					this.onSize(cell);
				}
			}
		}
		
		this.updateColors = function()
		{
			var tbl = this.tables[0];
			for(var i = 0; i < tbl.rows.length; i++)
				for(var j = 0; j < tbl.rows[i].cells.length; j++)
					this.onColor(tbl.rows[i].cells[j]);
		}
		
		window.objectsToResize.push(this);
		this.resizer = this.tables[0];
		updateSize(this);
	}
}