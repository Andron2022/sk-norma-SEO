/***************************************************************************
* Floatbox v3.51
* April 25, 2009
*
* Copyright (c) 2008-2009 Byron McGregor
* Website: http://randomous.com/floatbox
* License: Attribution-Noncommercial-No Derivative Works 3.0 Unported
*          http://creativecommons.org/licenses/by-nc-nd/3.0/
* Use on any commercial site requires purchase and registration.
* See http://randomous.com/floatbox/license for details.
* This comment block must be retained in all deployments and distributions.
***************************************************************************/

function Floatbox() {
this.defaultOptions = {

/***** BEGIN OPTIONS CONFIGURATION *****/
// See docs/options.html for detailed descriptions.
// All options can be overridden with rev/data-fb-options tag or page options (see docs/instructions.html).

/*** <General Options> ***/
theme:            'white'    ,// 'auto'|'black'|'white'|'blue'|'yellow'|'red'|'custom'
padding:           10       ,// pixels
panelPadding:      10        ,// pixels
overlayOpacity:    55       ,// 0-100
shadowType:       'drop'    ,// 'drop'|'halo'|'none'
shadowSize:        12       ,// 8|12|16|24
roundCorners:     'all'     ,// 'all'|'top'|'none'
cornerRadius:      8       ,// 8|12|20
roundBorder:       0        ,// 0|1
outerBorder:       0        ,// pixels
innerBorder:       0        ,// pixels
autoFitImages:     true     ,// true|false
resizeImages:      true     ,// true|false
autoFitOther:      false    ,// true|false
resizeOther:       false    ,// true|false
resizeTool:       'cursor'  ,// 'cursor'|'topleft'|'both'
infoPos:          'tl'      ,// 'tl'|'tc'|'tr'|'bl'|'bc'|'br'
controlPos:       'tl'      ,// 'tl'|'tr'|'bl'|'br'
centerNav:         false    ,// true|false
boxLeft:          'auto'    ,// 'auto'|pixels|'[-]xx%'
boxTop:           'auto'    ,// 'auto'|pixels|'[-]xx%'
enableDragMove:    false    ,// true|false
stickyDragMove:    true     ,// true|false
enableDragResize:  false    ,// true|false
stickyDragResize:  true     ,// true|false
draggerLocation:  'frame'   ,// 'frame'|'content'
minContentWidth:   200      ,// pixels
minContentHeight:  200      ,// pixels
centerOnResize:    true     ,// true|false
showCaption:       true     ,// true|false
showItemNumber:    true     ,// true|false
showClose:         true     ,// true|false
hideFlash:         true     ,// true|false
hideJava:          true     ,// true|false
disableScroll:     false    ,// true|false
randomOrder:       false    ,// true|false
preloadAll:        true     ,// true|false
autoGallery:       false    ,// true|false
autoTitle:        ''        ,// common caption string to use with autoGallery
printCSS:         ''        ,// path to css file or inline css string to apply to print pages (see showPrint)
language:         'ru'    ,// 'auto'|'en'|... (see the languages folder)
graphicsType:     'auto'    ,// 'auto'|'international'|'english'
/*** </General Options> ***/

/*** <Animation Options> ***/
doAnimations:         true   ,// true|false
resizeDuration:       3.5    ,// 0-10
imageFadeDuration:    3      ,// 0-10
overlayFadeDuration:  4      ,// 0-10
startAtClick:         true   ,// true|false
zoomImageStart:       true   ,// true|false
liveImageResize:      true   ,// true|false
splitResize:         'no'    ,// 'no'|'auto'|'wh'|'hw'
/*** </Animation Options> ***/

/*** <Navigation Options> ***/
navType:            'overlay'    ,// 'overlay'|'button'|'both'|'none'
navOverlayWidth:     35       ,// 0-50
navOverlayPos:       30       ,// 0-100
showNavOverlay:     'always'   ,// 'always'|'once'|'never'
showHints:          'once'    ,// 'always'|'once'|'never'
enableWrap:          true     ,// true|false
enableKeyboardNav:   true     ,// true|false
outsideClickCloses:  true     ,// true|false
imageClickCloses:    false    ,// true|false
numIndexLinks:       0        ,// number, -1 = no limit
indexLinksPanel:    'control' ,// 'info'|'control'
showIndexThumbs:     true     ,// true|false
/*** </Navigation Options> ***/

/*** <Slideshow Options> ***/
doSlideshow:    false  ,// true|false
slideInterval:  4.5    ,// seconds
endTask:       'exit'  ,// 'stop'|'exit'|'loop'
showPlayPause:  true   ,// true|false
startPaused:    false  ,// true|false
pauseOnResize:  true   ,// true|false
pauseOnPrev:    true   ,// true|false
pauseOnNext:    false   // true|false
/*** </Slideshow Options> ***/
};

/*** <New Child Window Options> ***/
// Will inherit from the primary floatbox options unless overridden here.
// Add any you like.
this.childOptions = {
padding:             16,
overlayOpacity:      45,
resizeDuration:       3,
imageFadeDuration:    3,
overlayFadeDuration:  0
};
/*** </New Child Window Options> ***/

/*** <Custom Paths> ***/
// Normally leave these blank.
// Floatbox will auto-find folders based on the location of floatbox.js and background-images.
// If you have a custom odd-ball configuration, fill in the details here.
// (Trailing slashes please)
this.customPaths = {
	jsModules: ''   ,// default: <floatbox.js>/modules/
	cssModules: ''  ,// default: <floatbox.js>/modules/
	languages: ''   ,// default: <floatbox.js>/languages/
	graphics: ''     // default: background-image:url(<parsed folder>);
};
/*** </Custom Paths> ***/

/***** END OPTIONS CONFIGURATION *****/
this.init();
}
Floatbox.prototype = {
	magicClass: 'floatbox',
	panelGap: 20,
	infoLinkGap: 16,
	draggerSize: 12,
	controlOpacity: 60,
	showHintsTime: 1600,
	zoomPopBorder: 1,
	controlSpacing: 8,
	minInfoWidth: 80,
	minIndexWidth: 120,
	ctrlJump: 5,
	slowLoadDelay: 750,
	autoFitSpace: 5,
	maxInitialSize: 120,
	minInitialSize: 70,
	defaultWidth: '85%',
	defaultHeight: '82%',
init: function() {
	var t = this;
	t.doc = document;
	t.docEl = t.doc.documentElement;
	t.head = t.doc.getElementsByTagName('head')[0];
	t.bod = t.doc.getElementsByTagName('body')[0];
	t.getGlobalOptions();
	t.currentSet = [];
	t.nodes = [];
	t.hiddenEls = [];
	t.timeouts = {};
	t.pos = {};
	var agent = navigator.userAgent,
		version = navigator.appVersion;
	t.mac = version.indexOf('Macintosh') !== -1;
	if (window.opera) {
		t.opera = true;
		t.operaOld = parseFloat(version) < 9.5;
	} else if (document.all) {
		t.ie = true;
		t.ieOld = parseInt(version.substr(version.indexOf('MSIE') + 5), 10) < 7;
		t.ieXP = parseInt(version.substr(version.indexOf('Windows NT') + 11), 10) < 6;
	} else if (agent.indexOf('Firefox') !== -1) {
		t.ff = true;
		t.ffOld = parseInt(agent.substr(agent.indexOf('Firefox') + 8), 10) < 3;
		t.ffNew = !t.ffOld;
		t.ffMac = t.mac;
	} else if (version.indexOf('WebKit') !== -1) {
		t.webkit = true;
		t.webkitMac = t.mac;
	} else if (agent.indexOf('SeaMonkey') !== -1) {
		t.seaMonkey = true;
	}
	t.browserLanguage = (navigator.language || navigator.userLanguage ||
		navigator.systemLanguage || navigator.browserLanguage || 'en').substring(0, 2);
	t.isChild = !!self.fb;
	if (!t.isChild) {
  		t.parent = t.lastChild = t;
		t.anchors = [];
		t.children = [];
		t.popups = [];
		t.preloads = {};
		var test1 = function(value) { return value; },
			test2 = function(value) { return value && t.doAnimations; },
			test3 = function(value) { return test2(value) && t.resizeDuration; };
		t.modules = {
			enableKeyboardNav: { files: ['keydownHandler.js'], test: test1 },
			enableDragMove: { files: ['mousedownHandler.js'], test: test1 },
			enableDragResize: { files: ['mousedownHandler.js'], test: test1 },
			centerOnResize: { files: ['resizeHandler.js'], test: test1 },
			showPrint: { files: ['printContents.js'], test: test1 },
			imageFadeDuration: { files: ['setOpacity.js'], test: test2 },
			overlayFadeDuration: { files: ['setOpacity.js'], test: test2 },
			resizeDuration: { files: ['setSize.js'], test: test2 },
			startAtClick: { files: ['getLeftTop.js'], test: test3 },
			zoomImageStart: { files: ['getLeftTop.js', 'zoomInOut.js'], test: test3 },
			loaded: {}
		};
		t.jsModulesPath = t.customPaths.jsModules;
		t.cssModulesPath = t.customPaths.cssModules;
		t.languagesPath = t.customPaths.languages;
		if (!(t.jsModulesPath && t.cssModulesPath && t.languagesPath)) {
			var fbPath = t.getPath('script', 'src', /(.*)f(?:loat|rame)box.js(?:\?|$)/i) || '/floatbox/';
			if (!t.jsModulesPath) t.jsModulesPath = fbPath + 'modules/';
			if (!t.cssModulesPath) t.cssModulesPath = fbPath + 'modules/';
			if (!t.languagesPath) t.languagesPath = fbPath + 'languages/';
		}
		t.graphicsPath = t.customPaths.graphics;
		if (!t.graphicsPath) {
			var match,
				node = t.doc.createElement('div');
			node.id = 'fbPathChecker';
			t.bod.appendChild(node);
			if ((match = /(?:url\()?["']?(.*)blank.gif["']?\)?$/i.exec(t.getStyle(node, 'background-image')))) {
				t.graphicsPath = match[1];
			}
			t.bod.removeChild(node);
			delete node;
			if (!t.graphicsPath) t.graphicsPath = (t.getPath('link', 'href', /(.*)floatbox.css(?:\?|$)/i) || '/floatbox/') + 'graphics/';
		}
		t.rtl = t.getStyle(t.bod, 'direction') === 'rtl' || t.getStyle(t.docEl, 'direction') === 'rtl';
  	} else {
		t.parent = fb.lastChild;
		fb.lastChild = t;
		fb.children.push(t);
		t.anchors = fb.anchors;
		t.popups = fb.popups;
		t.preloads = fb.preloads;
		t.modules = fb.modules;
		t.jsModulesPath = fb.jsModulesPath;
		t.cssModulesPath = fb.cssModulesPath;
		t.languagesPath = fb.languagesPath;
		t.graphicsPath = fb.graphicsPath;
		t.strings = fb.strings;
		t.rtl = fb.rtl;
		if (t.parent.isSlideshow) t.parent.setPause(true);
	}
	var path = t.graphicsPath;
	t.resizeUpCursor = path + 'magnify_plus.cur';
	t.resizeDownCursor = path + 'magnify_minus.cur';
	t.notFoundImg = path + '404.jpg';
	t.blank = path + 'blank.gif';
	t.zIndex = {
		base: 90000 + (t.isChild ? 12 * fb.children.length : 0),
		fbOverlay: 1,
		fbBox: 2,
		fbCanvas: 3,
		fbContent: 4,
		fbMainLoader: 5,
		fbLeftNav: 6,
		fbRightNav: 6,
		fbOverlayPrev: 7,
		fbOverlayNext: 7,
		fbResizer: 8,
		fbInfoPanel: 9,
		fbControlPanel: 9,
		fbDragger: 10,
		fbZoomDiv: 11
	};
	var match = /\bautoStart=(.+?)(?:&|$)/i.exec(location.search);
	t.autoHref = match ? match[1] : false;
},
tagAnchors: function(baseEl) {
	var t = this;
	function tag(tagName) {
		var elements = baseEl.getElementsByTagName(tagName);
		for (var i = 0, len = elements.length; i < len; i++) {
			t.tagOneAnchor(elements[i]);
		}
	}
	tag('a');
	tag('area');
	t.getModule('core.js');
	t.getModules(t.defaultOptions, true);
	if (t.popups.length) {
		t.getModule('getLeftTop.js');
		t.getModule('setOpacity.js');
		t.getModule('tagPopup.js');
		if (t.tagPopup) {
			while (t.popups.length) t.tagPopup(t.popups.pop());
		}
	}
	if (t.ieOld) t.getModule('ieOld.js');
},
tagOneAnchor: function(anchor, assumeClass) {
	var t = this,
		isAnchor = !!anchor.getAttribute,
		match;
	if (isAnchor) {
		var a = {
			href: anchor.getAttribute('href') || '',
			rev: anchor.getAttribute('data-fb-options') || anchor.getAttribute('rev') || '',
			rel: anchor.getAttribute('rel') || '',
			title: anchor.getAttribute('title') || '',
			className: anchor.className || '',
			ownerDoc: anchor.ownerDocument,
			anchor: anchor,
			thumb: (anchor.getElementsByTagName('img') || [])[0]
			};
	} else {
		var a = anchor;
		a.anchor = a.thumb = a.ownerDoc = false;
	}
	if ((match = new RegExp('(?:^|\\s)' + t.magicClass + '(\\S*)', 'i').exec(a.className))) {
		a.tagged = true;
		if (match[1]) a.group = match[1];
	}
	if (t.autoGallery && !a.tagged && t.fileType(a.href) === 'img' && a.rel.toLowerCase() !== 'nofloatbox' &&
		a.className.toLowerCase().indexOf('nofloatbox') === -1)
	{
		a.tagged = true;
		a.group = '.autoGallery';
		if (t.autoTitle && !a.title) a.title = t.autoTitle;
	}
	if (!a.tagged) {
		if ((match = /^(?:floatbox|gallery|iframe|slideshow|lytebox|lyteshow|lyteframe|lightbox)(.*)/i.exec(a.rel))) {
			a.tagged = true;
			a.group = match[1];
			if (/^(slide|lyte)show/i.test(a.rel)) {
				a.rev += ' doSlideshow:true';
			} else if (/^(i|lyte)frame/i.test(a.rel)) {
				a.rev += ' type:iframe';
			}
		}
	}
	if (a.thumb && ((match = /(?:^|\s)fbPop(up|down)(?:\s|$)/i.exec(anchor.className)))) {
		a.popup = true;
		a.popupType = match[1];
		t.popups.push(a);
	}
	if (assumeClass) a.tagged = true;
	if (a.tagged) {
		a.options = t.parseOptionString(a.rev);
		a.href = a.options.href || a.href;
		a.group = a.options.group || a.group || '';
		if (!a.href && a.options.showThis !== false) return false;
		a.level = fb.children.length + (fb.lastChild.fbBox && !a.options.sameBox ? 1 : 0);
		var i = t.anchors.length;
		while (i--) {
			var a_i = t.anchors[i];
			if (a_i.href === a.href && a_i.rev === a.rev && a_i.rel === a.rel && a_i.title === a.title && a_i.level === a.level &&
				(a_i.anchor === a.anchor || (a.ownerDoc && a.ownerDoc !== t.doc)))
			{
				a_i.anchor = a.anchor;
				a_i.thumb = a.thumb;
				break;
			}
		}
		if (i === -1) {
			a.type = a.options.type || t.fileType(a.href);
			if (a.type === 'html') {
				a.type = 'iframe';
				var match = /#(\w+)/.exec(a.href);
				if (match) {
					var doc = document;
					if (a.anchor) {
						doc = a.ownerDoc || doc;
					}
					if (doc === document && t.itemToShow && t.itemToShow.anchor) {
						doc = t.itemToShow.ownerDoc || doc;
					}
					var el = doc.getElementById(match[1]);
					if (el) {
						a.type = 'inline';
						a.sourceEl = el;
					}
				}
			}
			t.anchors.push(a);
			t.getModules(a.options, false);
			if (a.type.indexOf('media') === 0) t.getModule('mediaHTML.js');
			if (t.autoHref) {
				if (a.options.showThis !== false && t.autoHref === a.href.substr(a.href.length - t.autoHref.length)) {
					t.autoStart = a;
				}
			} else if (a.options.autoStart === true) {
				t.autoStart = a;
			} else if (a.options.autoStart === 'once') {
				var match = /fbAutoShown=(.+?)(?:;|$)/.exec(document.cookie),
					val = match ? match[1] : '',
					href = escape(a.href);
				if (val.indexOf(href) === -1) {
					t.autoStart = a;
					document.cookie = 'fbAutoShown=' + val + href + '; path=/';
				}
			}
			if (t.ieOld && a.anchor) a.anchor.hideFocus = 'true';
		}
		if (isAnchor) {
			anchor.onclick = function(e) {
				if (!e) {
					var doc = this.ownerDocument;
					e = doc && doc.parentWindow && doc.parentWindow.event;
				}
				if (!(e && (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey)) ||
					a.options.showThis === false || !/img|iframe/.test(a.type))
				{
					if (t.start) t.start(anchor);
					return t.stopEvent(e);
				}
			};
		}
	}
	return a;
},
fileType: function(href) {
	var t = this,
		s = (href || '').toLowerCase(),
		i = s.indexOf('?');
	if (i !== -1) s = s.substr(0, i);
	s = s.substr(s.lastIndexOf('.') + 1);
	if (/^(jpe?g|png|gif|bmp)$/.test(s)) return 'img';
	if (s === 'swf' || /^(http:)?\/\/(www.)?(youtube|dailymotion).com\/(v|swf)\//i.test(href)) return 'media:flash';
	if (/^(mov|mpe?g|movie)$/.test(s)) return 'media:quicktime';
	if (s === 'xap') return 'media:silverlight';
	return 'html';
},
getGlobalOptions: function() {
	var t = this;
	if (!t.isChild) {
		t.setOptions(t.defaultOptions);
		if (typeof setFloatboxOptions === 'function') setFloatboxOptions();
		t.pageOptions = typeof fbPageOptions === 'object' ? fbPageOptions : {};
	} else {
		for (var name in t.defaultOptions) {
			if (t.defaultOptions.hasOwnProperty(name)) t[name] = t.parent[name];
		}
		t.setOptions(t.childOptions);
		t.pageOptions = {};
		for (var name in t.parent.pageOptions) {
			if (t.parent.pageOptions.hasOwnProperty(name)) t.pageOptions[name] = t.parent.pageOptions[name];
		}
		if (typeof fbChildOptions === 'object') {
			for (var name in fbChildOptions) {
				if (fbChildOptions.hasOwnProperty(name)) t.pageOptions[name] = fbChildOptions[name];
			}
		}
	}
	t.setOptions(t.pageOptions);
	if (t.pageOptions.enableCookies) {
		var match = /fbOptions=(.+?)(;|$)/.exec(document.cookie);
		if (match) t.setOptions(t.parseOptionString(match[1]));
	}
	t.setOptions(t.parseOptionString(location.search.substring(1)));
},
parseOptionString: function(str) {
	var t = this;
	if (!str) return {};
	var quotes = [], match,
		rex = /`([^`]*?)`/g;
		rex.lastIndex = 0;
	while ((match = rex.exec(str))) quotes.push(match[1]);
	if (quotes.length) str = str.replace(rex, '``');
	str = str.replace(/\s*[:=]\s*/g, ':');
	str = str.replace(/\s*[;&]\s*/g, ' ');
	str = str.replace(/^\s+|\s+$/g, '');
	str = str.replace(/(:\d+)px\b/gi, function(match, p1) { return p1; });
	var pairs = {},
		aVars = str.split(' '),
		i = aVars.length;
	while (i--) {
		var aThisVar = aVars[i].split(':'),
			name = aThisVar[0],
			value = aThisVar[1];
		if (typeof value === 'string') {
			if (!isNaN(value)) value = +value;
			else if (value === 'true') value = true;
			else if (value === 'false') value = false;
		}
		if (value === '``') value = quotes.pop() || '';
		pairs[name] = value;
	}
	return pairs;
},
setOptions: function(pairs) {
	var t = this;
	for (var name in pairs) {
		if (t.defaultOptions.hasOwnProperty(name)) t[name] = pairs[name];
	}
},
getModule: function(file) {
	var t = this;
	if (t.modules.loaded[file]) return;
	if (file.slice(-3) === '.js') {
		var tag = 'script',
			attr = { type: 'text/javascript', src: t.jsModulesPath + file };
	} else {
		var tag = 'link',
			attr = { rel: 'stylesheet', type: 'text/css', href: t.cssModulesPath + file };
	}
	var el = t.doc.createElement(tag);
	for (var name in attr) {
		if (attr.hasOwnProperty(name)) el.setAttribute(name, attr[name]);
	}
	t.head.appendChild(el);
	t.modules.loaded[file] = true;
},
getModules: function(options, fbValue) {
	var t = this;
	for (var name in options) {
		if (t.modules.hasOwnProperty(name)) {
			var mod = t.modules[name],
				val = fbValue ? t[name] : options[name],
				loaded = 0,
				i = mod.files.length;
			while (i--) {
				if (mod.test(val)) {
					t.getModule(mod.files[i]);
					loaded++;
				}
			}
			if (loaded === mod.files.length) delete t.modules[name];
		}
	}
},
getStyle: function(el, prop) {
	if (!(el && prop)) return '';
	if (el.currentStyle) {
		return el.currentStyle[prop.replace(/-(\w)/g, function(match, p1) { return p1.toUpperCase(); })] || '';
	} else {
		var win = el.ownerDocument.defaultView || el.ownerDocument.parentWindow;
		return (win.getComputedStyle && win.getComputedStyle(el, '').getPropertyValue(prop)) || '';
	}
},
getPath: function(tag, attr, rex) {
	var match,
		els = document.getElementsByTagName(tag),
		i = els.length;
	while(i--) {
		if ((match = rex.exec(els[i][attr]))) return match[1];
	}
	return '';
},
addEvent: function(node, action, func) {
	if (node.addEventListener) {
		node.addEventListener(action, func, false);
	} else if (node.attachEvent) {
		node.attachEvent('on' + action, func);
	} else {
		node['prior' + action] = node['on' + action];
		node['on' + action] = func;
	}
},
removeEvent: function(node, action, func) {
	if (node.removeEventListener) {
		node.removeEventListener(action, func, false);
	} else if (node.detachEvent) {
		node.detachEvent('on' + action, func);
	} else {
		node['on' + action] = node['prior' + action];
		delete node['prior' + action];
	}
},
stopEvent: function(e) {
	if (e) {
		if (e.stopPropagation) e.stopPropagation();
		if (e.preventDefault) e.preventDefault();
		e.cancelBubble = true;
		e.returnValue = false;
	}
	return false;
},
preloadImages: function(href, chain) {
	var t = this;
	setTimeout(function() { t.preloadImages(href, chain); }, 100);
}
};
var fb;
function initfb() {
	if (arguments.callee.done) return;
	var fbWindow = 'self';
	if (self !== parent) {
		try {
			if (self.location.host === parent.location.host && self.location.protocol === parent.location.protocol) fbWindow = 'parent';
		} catch(e) {}
		if (fbWindow === 'parent' && !parent.fb) return setTimeout(initfb, 50);
	}
	arguments.callee.done = true;
	if (document.compatMode === 'BackCompat') {
		alert('Floatbox does not support quirks mode.\nPage needs to have a valid doctype declaration.');
		return;
	}
	fb = (fbWindow === 'self' ? new Floatbox() : parent.fb);
	fb.tagAnchors(self.document.getElementsByTagName('body')[0]);
}
if (document.addEventListener) document.addEventListener('DOMContentLoaded', initfb, false);
(function() {
	/*@cc_on
	if (document.body) {
		try {
			document.createElement('div').doScroll('left');
			return initfb();
		} catch(e) {}
	}
	/*@if (false) @*/
	if (/loaded|complete/.test(document.readyState)) return initfb();
	/*@end @*/
	if (!initfb.done) setTimeout(arguments.callee, 30);
})();
fb_prevOnload = window.onload;
window.onload = function() {
	if (arguments.callee.done) return;
	arguments.callee.done = true;
	if (typeof fb_prevOnload === 'function') fb_prevOnload();
	initfb();
	(function() {
		if (!(self.fb && self.fb.start)) return setTimeout(arguments.callee, 50);
		if (fb.autoStart && fb.autoStart.ownerDoc) {
			if (fb.autoStart.ownerDoc === self.document) setTimeout ( function() { fb.start(fb.autoStart); }, 100);
		} else {
			setTimeout ( function() { if (typeof fb.preloads.count === 'undefined') fb.preloadImages('', true); }, 200);
		}
	})();
};