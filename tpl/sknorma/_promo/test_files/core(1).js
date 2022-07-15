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
Floatbox.prototype.start = function(anchor) {
	var t = fb.lastChild;
	if (!anchor) return;
	if (t.ieOld && !(window.DD_belatedPNG && DD_belatedPNG.ready)) return;
	var a = false;
	fb.preloadImages('', false);
	if (!anchor.setAttribute) {
		a = anchor;
		if (!a.tagged) return t.start(t.tagOneAnchor(a, true));
	} else {
		anchor.blur();
		var i = t.anchors.length;
		while (i--) {
			if (t.anchors[i].anchor === anchor) {
				a = t.anchors[i];
				break;
			}
		}
		if (!a) return t.start(t.tagOneAnchor(anchor, true));
	}
	if (!a.href && a.options.showThis !== false) return;
	var inBox = !!t.fbBox;
	if (inBox && t.currentItem) {
		if (!a.options.sameBox) return new Floatbox().start(a);
		if (t.currentIndex !== t.indexToShow) t.exec(t.currentItem, 'onItemEnd');
		t.moveInnerHTML(t.fbContent, t.currentItem.sourceEl);
		t.setOptions(a.options);
	}
	a.level = fb.children.length + (fb.lastChild.fbBox && !a.options.sameBox ? 1 : 0);
	t.getCurrentSet(a);
	if (!t.itemCount) return;
	if (t.itemCount === 1 && t.fbNavControls) t.fbNavControls.style.display = 'none';
	self.focus();
	t.clickOptions = a.options;
	if (!inBox) {
		t.clickedAnchor = a.anchor;
		t.getAllOptions();
		if (t.itemCount > 1 && t.randomOrder) t.randomous();
		t.exec(t.itemToShow, 'beforeBoxStart');
		t.getModule(t.lclTheme + '.css');
		t.buildDOM();
		t.addEventHandlers();
		t.initState();
	}
	t.itemsShown = 0;
	if (t.cornerRadius) t.fbCorners.style.display = '';
	if (t.resizeDuration) t.fbBoxLoader.style.display = '';
	var go = function() {
		if (!inBox) t.exec(t.itemToShow, 'afterBoxStart');
		t.measureContent();
	};
	if (t.fbBox.style.visibility || inBox) {
		go();
	} else {
		var size = Math.max(t.maxInitialSize - 2*(t.cornerRadius || t.realBorder), t.minInitialSize),
			halfSize = Math.round(size/2),
			smallBox = { id: 'fbBox', left: t.pos.fbBox.left - halfSize, top: t.pos.fbBox.top - halfSize, width: size, height: size };
		if (t.splitResize) {
			var oncomplete = function() {
				t.setSize(smallBox, go);
			};
		} else {
			t.setTimeout('slowLoad', function() {
				t.fbBoxLoader.style.display = '';
				t.setSize(smallBox);
			}, t.slowLoadDelay);
			var oncomplete = go;
		}
		t.setOpacity(t.fbOverlay, t.overlayOpacity, t.overlayFadeDuration, oncomplete);
	}
};
Floatbox.prototype.getCurrentSet = function(a) {
	var t = this;
	t.currentSet.length = t.itemCount = t.indexToShow = 0;
	t.justImages = true;
	t.gotImg = t.gotMedia = false;
	for (var i = 0; i < t.anchors.length; i++) {
		var a_i = t.anchors[i];
		if (a_i.level === a.level && (a === a_i || (a.group && a.group === a_i.group)) && a_i.options.showThis !== false) {
			a_i.seen = false;
			t.currentSet.push(a_i);
			if (a_i.type === 'img') {
				t.gotImg = true;
			} else {
				t.justImages = false;
			}
			if (a_i.type.indexOf('media') === 0) t.gotMedia = true;
		}
		if (!a_i.anchor) {
			t.anchors.splice(i, 1);
			i--;
		}
	}
  	t.itemCount = t.currentSet.length;
	if (t.itemCount > 1) {
		var matchHref = a.options.showThis === false && a.href,
			i = t.itemCount;
		while (i--) {
			var a_i = t.currentSet[i];
			if (a_i === a || (matchHref && a_i.href === matchHref.substr(matchHref.length - a_i.href.length))) {
				t.indexToShow = i;
				break;
			}
		}
	}
  	t.itemToShow = t.currentSet[t.indexToShow];
};
Floatbox.prototype.randomous = function() {
	var t = this;
	if (!t.randomOrder || t.itemCount < 2) return;
	var i = t.itemCount;
	while (--i) {
		var j = Math.floor(Math.random() * (i + 1)),
			swap = t.currentSet[i];
		t.currentSet[i] = t.currentSet[j];
		t.currentSet[j] = swap;
		if (i === t.indexToShow) {
			t.indexToShow = j;
		} else if (j === t.indexToShow) {
			t.indexToShow = i;
		}
	}
	if (t.isSlideshow) t.indexToShow = 0;
	t.itemToShow = t.currentSet[t.indexToShow];
};
Floatbox.prototype.getAllOptions = function() {
	var t = this;
	t.getGlobalOptions();
	t.setOptions(t.clickOptions);
	t.setOptions(t.parseOptionString(location.search.substring(1)));
	if (t.theme === 'grey') t.theme = 'white';
	if (t.endTask === 'cont') t.endTask = 'loop';
	if (t.navType === 'upper') t.navType = 'overlay';
	if (t.navType === 'lower') t.navType = 'button';
	if (t.dropShadow) t.shadowType = 'drop';
	if (t.enableDrag) t.enableDragMove = true;
	if (t.upperNavWidth) t.navOverlayWidth = t.upperNavWidth;
	if (typeof t.upperNavPos === 'number') t.navOverlayPos = t.upperNavPos;
	if (typeof t.showUpperNav === 'boolean') t.showNavOverlay = t.showUpperNav;
	if (typeof t.autoSizeImages === 'boolean') t.autoFitImages = t.autoSizeImages;
	if (typeof t.autoSizeOther === 'boolean') t.autoFitOther = t.autoSizeOther;
	t.fbParent = t.parent;
	if (!/^(auto|black|white|blue|yellow|red|custom)$/.test(t.theme)) t.theme='auto';
	if (!/^(overlay|button|both|none)$/i.test(t.navType)) t.navType = 'button';
	if (!/^(auto|wh|hw)$/.test(t.splitResize)) t.splitResize = false;
	if (t.itemCount > 1) {
		t.isSlideshow = t.doSlideshow;
		var overlayRequest = /overlay|both/i.test(t.navType),
			buttonRequest = /button|both/i.test(t.navType);
		t.navOverlay = t.justImages && overlayRequest;
		t.navButton = buttonRequest || (!t.justImages && overlayRequest);
		t.lclShowItemNumber = t.showItemNumber;
		t.lclNumIndexLinks = t.numIndexLinks;
	} else {
		t.isSlideshow = t.navOverlay = t.navButton = t.lclShowItemNumber = t.lclNumIndexLinks = false;
	}
	if (t.outsideClickCloses && !t.overlayOpacity) t.overlayOpacity = 0.01;
	t.isPaused = t.startPaused;
	if ((t.lclTheme = t.theme) === 'auto') {
		t.lclTheme = t.itemToShow.type === 'img' ? 'black' : (t.itemToShow.type.indexOf('media') === 0 ? 'blue' : 'white');
	}
	if (!t.doAnimations) {
		t.resizeDuration = t.imageFadeDuration = t.overlayFadeDuration = 0;
	}
	if (!t.resizeDuration) {
		t.zoomImageStart = t.startAtClick = t.liveImageChange = t.liveImageResize = false;
	}
	if (!/[tb][lr]/.test(t.controlPos)) t.controlPos = '';
	if (!/[tb][lcr]/.test(t.infoPos)) t.infoPos = '';
	t.controlTop = t.controlPos.charAt(0) === 't';
	t.controlLeft = t.controlPos.charAt(1) === 'l';
	t.infoTop = t.infoPos.charAt(0) === 't';
	t.infoCenter = t.infoPos.charAt(1) === 'c';
	t.infoLeft = t.infoPos.charAt(1) === 'l' || (t.infoCenter && t.controlTop === t.infoTop && !t.controlLeft);
	if (t.infoTop === t.controlTop) {
		if (t.infoLeft === t.controlLeft) {
			t.infoLeft = true;
			t.controlLeft = false;
		}
		if (t.infoCenter && t.centerNav && t.navButton) {
			t.infoCenter = false;
			t.infoLeft = !t.controlLeft;
		}
	}
	if (t.indexLinksPanel === 'info') {
		t.indexCenter = t.infoCenter;
		t.indexLeft = t.infoLeft;
		t.indexTop = t.infoTop;
	} else {
		t.indexLeft = t.controlLeft;
		t.indexTop = t.controlTop;
	}
	if (!/^(drop|halo|none)$/.test(t.shadowType)) t.shadowType = 'drop';
	if (t.shadowType === 'none') {
		t.shadowSize = 0;
	} else {
		if (!/^(8|12|16|24)$/.test(t.shadowSize + '')) t.shadowSize = 8;
		t.shadowSize = +t.shadowSize;
	}
	if (t.ieOld || t.cornerRadius === 0) t.roundCorners = 'none';
	if (!/^(all|top|none)$/.test(t.roundCorners)) t.roundCorners = t.enableDragResize ? 'top' : 'all';
	t.roundBorder = t.roundBorder ? 1 : 0;
	if (!/^(8|12|20)$/.test(t.cornerRadius + '')) t.cornerRadius = 12;
	t.cornerRadius = +t.cornerRadius;
	if (t.roundCorners === 'none') t.cornerRadius = t.roundBorder = 0;
	t.bottomCorners = t.roundCorners === 'all';
	if (t.cornerRadius) {
		t.apparentBorder = t.roundBorder;
		t.realBorder = 0;
		t.realPadding = Math.max(t.padding - (t.cornerRadius - t.roundBorder), 0);
		t.apparentPadding = t.realPadding + t.cornerRadius - t.roundBorder;
	} else {
		t.realBorder = t.apparentBorder = t.outerBorder;
		t.realPadding = t.apparentPadding = t.padding;
	}
	if (t.gotMedia) t.enableDragResize = false;
	if (t.enableDragResize) {
		if (t.draggerLocation !== 'content') t.draggerLocation = 'frame';
	} else {
		t.draggerLocation = false;
	}
	if (t.imageClickCloses || t.opera || (t.mac && !t.webkit)) {
		t.resizeTool = 'topleft';
	} else {
		t.resizeTool = t.resizeTool.toLowerCase();
		if (!/topleft|cursor|both/.test(t.resizeTool)) t.resizeTool = 'cursor';
	}
	if (!(t.apparentPadding || t.realBorder)) t.zoomPopBorder = 0;
};
Floatbox.prototype.buildDOM = function() {
	var t = this;
	t.fbOverlay = t.newNode('div', 'fbOverlay', t.bod);
	t.fbZoomDiv = t.newNode('div', 'fbZoomDiv', t.bod);
	t.fbZoomImg = t.newNode('img', 'fbZoomImg', t.fbZoomDiv);
	t.fbZoomLoader = t.newNode('div', 'fbZoomLoader', t.fbZoomDiv);
	t.fbBox = t.newNode('div', 'fbBox');
	if (t.shadowSize) {
		t.fbShadows = t.newNode('div', 'fbShadows', t.fbBox);
		t.fbShadowTop = t.newNode('div', 'fbShadowTop', t.fbShadows);
		t.fbShadowRight = t.newNode('div', 'fbShadowRight', t.fbShadows);
		t.fbShadowBottom = t.newNode('div', 'fbShadowBottom', t.fbShadows);
		t.fbShadowLeft = t.newNode('div', 'fbShadowLeft', t.fbShadows);
	}
	if (t.cornerRadius) {
		t.fbCorners = t.newNode('div', 'fbCorners', t.fbBox);
		t.fbCornerTop = t.newNode('div', 'fbCornerTop', t.fbCorners);
		t.fbCornerRight = t.newNode('div', 'fbCornerRight', t.fbCorners);
		t.fbCornerBottom = t.newNode('div', 'fbCornerBottom', t.fbCorners);
		t.fbCornerLeft = t.newNode('div', 'fbCornerLeft', t.fbCorners);
	}
	t.fbBoxLoader = t.newNode('div', 'fbBoxLoader', t.fbBox);
	t.fbCanvas = t.newNode('div', 'fbCanvas', t.fbBox);
	t.fbMainDiv = t.newNode('div', 'fbMainDiv', t.fbCanvas);
	t.fbMainLoader = t.newNode('div', 'fbMainLoader', t.fbMainDiv);
	if (t.navOverlay) {
		t.fbLeftNav = t.newNode('a', 'fbLeftNav', t.fbMainDiv);
		t.fbRightNav = t.newNode('a', 'fbRightNav', t.fbMainDiv);
		t.fbOverlayPrev = t.newNode('a', 'fbOverlayPrev', t.fbMainDiv, t.strings.hintPrev);
		t.fbOverlayNext = t.newNode('a', 'fbOverlayNext', t.fbMainDiv, t.strings.hintNext);
	}
	t.fbResizer = t.newNode('a', 'fbResizer', t.fbMainDiv, t.strings.hintResize);
	t.fbInfoPanel = t.newNode('div', 'fbInfoPanel', t.fbCanvas);
	t.fbCaptionDiv = t.newNode('div', 'fbCaptionDiv', t.fbInfoPanel);
	t.fbCaption = t.newNode('span', 'fbCaption', t.fbCaptionDiv);
	t.fbInfoDiv = t.newNode('div', 'fbInfoDiv', t.fbInfoPanel);
	if (t.infoLeft || t.infoCenter) {
		t.fbInfoLink = t.newNode('span', 'fbInfoLink', t.fbInfoDiv);
		t.fbPrintLink = t.newNode('span', 'fbPrintLink', t.fbInfoDiv);
		t.fbItemNumber = t.newNode('span', 'fbItemNumber', t.fbInfoDiv);
	} else {
		t.fbItemNumber = t.newNode('span', 'fbItemNumber', t.fbInfoDiv);
		t.fbPrintLink = t.newNode('span', 'fbPrintLink', t.fbInfoDiv);
		t.fbInfoLink = t.newNode('span', 'fbInfoLink', t.fbInfoDiv);
	}
	t.fbControlPanel = t.newNode('div', 'fbControlPanel', t.fbCanvas);
	t.fbControls = t.newNode('div', 'fbControls', t.fbControlPanel);
	t.fbNavControls = t.newNode('div', 'fbNavControls', t.fbControls);
	t.fbPrev = t.newNode('a', 'fbPrev', t.fbNavControls, t.strings.hintPrev);
	t.fbNext = t.newNode('a', 'fbNext', t.fbNavControls, t.strings.hintNext);
	t.fbSubControls = t.newNode('div', 'fbSubControls', t.fbControls);
	t.fbPlayPause = t.newNode('div', 'fbPlayPause', t.fbSubControls);
	t.fbPlay = t.newNode('a', 'fbPlay', t.fbPlayPause, t.strings.hintPlay);
	t.fbPause = t.newNode('a', 'fbPause', t.fbPlayPause, t.strings.hintPause);
	t.fbClose = t.newNode('a', 'fbClose', t.fbSubControls, t.strings.hintClose);
	t.fbIndexLinks = t.newNode('span', 'fbIndexLinks', t.indexLinksPanel === 'info' ? t.fbInfoPanel : t.fbControlPanel);
	t.fbDragger = t.newNode('div', 'fbDragger', t.fbCanvas);
	t.bod.appendChild(t.fbBox);
};
Floatbox.prototype.newNode = function(nodeType, id, parentNode, title) {
	var t = this;
	if (t[id] && t[id].parentNode) {
		t[id].parentNode.removeChild(t[id]);
	}
	var node = document.createElement(nodeType);
	node.id = id;
	node.className = id + '_' + t.lclTheme;
	if (nodeType === 'a') {
		if (!t.operaOld) node.href = '';
		if (t.ieOld) node.hideFocus = 'true';
		node.style.outline = 'none';
	} else if (nodeType === 'iframe') {
		node.scrolling = t.itemScroll;
		node.frameBorder = '0';
		node.align = 'middle';
		node.src = t.graphicsPath + 'loader_iframe_' + t.lclTheme + '.html';
	}
	if (t.isChild && t.parent[id]) title = t.parent[id].title;
	if (title && t.showHints !== 'never') node.title = title;
	if (t.zIndex[id]) node.style.zIndex = t.zIndex.base + t.zIndex[id];
	node.style.display = 'none';
	if (parentNode) parentNode.appendChild(node);
	t.nodes.push(node);
	return node;
};
Floatbox.prototype.addEventHandlers = function() {
	var t = this,
		prev = t.fbPrev.style,
		next = t.fbNext.style;
	if (!t.rtl && (t.graphicsType.toLowerCase() === 'english' || (t.graphicsType === 'auto' && t.browserLanguage === 'en'))) {
		t.offPos = 'top left';
		t.onPos = 'bottom left';
	} else {
		t.offPos = 'top right';
		t.onPos = 'bottom right';
		t.controlSpacing = 0;
	}
	if (t.showHints === 'once') {
		var hideHint = function(id) {
			if (t[id].title) {
				t.setTimeout(id, function() {
					t[id].title = '';
					if (t.parent[id]) t.parent[id].title = '';
					var id2 = '';
					if (t.navOverlay) {
						if (/fbOverlay(Prev|Next)/.test(id)) {
							id2 = id.replace('Overlay', '');
						} else if (/fb(Prev|Next)/.test(id)) {
							id2 = id.replace('fb', 'fbOverlay');
						}
						if (t[id2]) t[id2].title = '';
						if (t.parent[id2]) t.parent[id2].title = '';
					}
				}, t.showHintsTime);
			}
		};
	} else {
		var hideHint = function() {};
	}
	t.fbPlay.onclick = function(e) {
		t.setPause(false);
		return t.stopEvent(e || window.event);
	};
	t.fbPause.onclick = function(e) {
		t.setPause(true);
		return t.stopEvent(e || window.event);
	};
	t.fbClose.onclick = function(e) {
		t.end();
		return t.stopEvent(e || window.event);
	};
	if (t.outsideClickCloses) {
		t.fbOverlay.onclick = t.fbClose.onclick;
		if (t.shadowSize) t.fbShadows.onclick = t.fbClose.onclick;
	}
	t[fb.rtl ? 'fbNext' : 'fbPrev'].onclick = function(eOrStep) {
		if (typeof eOrStep === 'number') {
			var step = eOrStep;
			var e = null;
		} else {
			var step = 1;
			var e = eOrStep;
		}
		if (t.currentIndex === t.indexToShow) {
			var newIndex = (t.currentIndex - step) % t.itemCount;
			if (newIndex < 0) newIndex += t.itemCount;
			if (t.enableWrap || newIndex < t.currentIndex) {
				t.newContent(newIndex);
				if (t.isSlideshow && t.pauseOnPrev && !t.isPaused) {
					t.setPause(true);
				}
			}
		}
		return t.stopEvent(e || window.event);
	};
	t[fb.rtl ? 'fbPrev' : 'fbNext'].onclick = function(eOrStep) {
		if (typeof eOrStep === 'number') {
			var step = eOrStep;
			var e = null;
		} else {
			var step = 1;
			var e = eOrStep;
		}
		if (t.currentIndex === t.indexToShow) {
			var newIndex = (t.currentIndex + step) % t.itemCount;
			if (t.enableWrap || newIndex > t.currentIndex) {
				t.newContent(newIndex);
				if (t.isSlideshow && t.pauseOnNext && !t.isPaused) {
					t.setPause(true);
				}
			}
		}
		return t.stopEvent(e || window.event);
	};
	if (t.navOverlay) {
		var leftNav = t.fbLeftNav.style,
			rightNav = t.fbRightNav.style,
			overlayPrev = t.fbOverlayPrev.style,
			overlayNext = t.fbOverlayNext.style;
		t.fbLeftNav.onclick = t.fbOverlayPrev.onclick = t.fbPrev.onclick;
		t.fbRightNav.onclick = t.fbOverlayNext.onclick = t.fbNext.onclick;
		t.fbLeftNav.onmouseover = t.fbLeftNav.onmousemove =
		t.fbOverlayPrev.onmousemove = function() {
			if (!t.timeouts.fade_fbCanvas) overlayPrev.visibility = '';
			if (t.navButton) prev.backgroundPosition = t.onPos;
			return true;
		};
		t.fbRightNav.onmouseover = t.fbRightNav.onmousemove =
		t.fbOverlayNext.onmousemove = function() {
			if (!t.timeouts.fade_fbCanvas) overlayNext.visibility = '';
			if (t.navButton) next.backgroundPosition = t.onPos;
			return true;
		};
		t.fbOverlayPrev.onmouseover = t.fbOverlayNext.onmouseover = function() {
			this.onmousemove();
			hideHint(this.id);
			return true;
		};
		t.fbLeftNav.onmouseout = function() {
			overlayPrev.visibility = 'hidden';
			if (t.navButton) prev.backgroundPosition = t.offPos;
		};
		t.fbRightNav.onmouseout = function() {
			overlayNext.visibility = 'hidden';
			if (t.navButton) next.backgroundPosition = t.offPos;
		};
		t.fbOverlayPrev.onmouseout = t.fbOverlayNext.onmouseout = function() {
			this.style.visibility = 'hidden';
			t.clearTimeout(this.id);
		};
		t.fbLeftNav.onmousedown = t.fbRightNav.onmousedown = function(e) {
			e = e || window.event;
			if (e.button === 2) {
				leftNav.visibility = rightNav.visibility = 'hidden';
				t.setTimeout('hideNavOverlay', function() {
					leftNav.visibility = rightNav.visibility = '';
				}, 600);
			}
		};
	}
	t.fbPlay.onmouseover = t.fbPause.onmouseover = t.fbClose.onmouseover =
	t.fbPrev.onmouseover = t.fbNext.onmouseover = function() {
		this.style.backgroundPosition = t.onPos;
		hideHint(this.id);
		return true;
	};
	t.fbResizer.onmouseover = function() {
		hideHint(this.id);
		return true;
	};
	t.fbPlay.onmouseout = t.fbPause.onmouseout = t.fbClose.onmouseout =
	t.fbPrev.onmouseout = t.fbNext.onmouseout = function() {
		this.style.backgroundPosition = t.offPos;
		t.clearTimeout(this.id);
	};
	t.fbResizer.onmouseout = function() {
		t.clearTimeout(this.id);
	};
	if (fb.enableKeyboardNav  && !fb.keydownHandlerSet && fb.keydownHandler) {
		fb.addEvent(fb.doc, 'keydown', fb.keydownHandler);
		fb.keydownHandlerSet = true;
	}
	if (t.opera && t.isSlideShow && !fb.keypressHandlerSet) {
		fb.priorOnkeypress = fb.doc.onkeypress || null;
		fb.keypressHandler = function(e) { return fb.stopEvent(e); };
		fb.addEvent(fb.doc, 'keypress', fb.keypressHandler);
		fb.keypressHandlerSet = true;
	}
	if (fb.centerOnResize && !fb.resizeHandlerSet && fb.resizeHandler) {
		fb.addEvent(window, 'resize', fb.resizeHandler);
		fb.resizeHandlerSet = true;
	}
	if ((t.enableDragMove || t.enableDragResize) && !t.fbBox.onmousedown && t.getMousedownHandler) t.fbBox.onmousedown = t.getMousedownHandler();
};
Floatbox.prototype.initState = function() {
	var t = this,
		box = t.fbBox.style,
		mainDiv = t.fbMainDiv.style,
		canvas = t.fbCanvas.style,
		zoomDiv = t.fbZoomDiv.style,
		zoomImg = t.fbZoomImg.style,
		item = t.itemToShow;
	t.dragMoveDx = t.dragMoveDy = t.dragResizeDx = t.dragResizeDy = 0;
	t.currentItem = t.currentIndex = t.previousItem = t.endCalled = false;
	var anchorPos = t.getAnchorPos(t.clickedAnchor, item.anchor === t.clickedAnchor && item.type === 'img');
	if (anchorPos.width) {
		zoomDiv.borderWidth = t.zoomPopBorder + 'px';
		zoomDiv.left = (anchorPos.left -= t.zoomPopBorder) + 'px';
		zoomDiv.top = (anchorPos.top -= t.zoomPopBorder) + 'px';
		zoomDiv.width = (t.fbZoomImg.width = anchorPos.width) + 'px';
		zoomDiv.height = (t.fbZoomImg.height = anchorPos.height) + 'px';
		t.pos.fbZoomDiv = anchorPos;
		t.fbZoomImg.src = anchorPos.src;
		box.visibility = 'hidden';
		t.pos.fbBox = {};
		if (item.popup) item.popupLocked = true;
		t.setTimeout('slowLoad', function() {
			if (t.fbOverlay.style.display) t.setOpacity(t.fbOverlay, t.overlayOpacity, t.overlayFadeDuration);
			t.fbZoomDiv.style.display = t.fbZoomImg.style.display = t.fbZoomLoader.style.display = '';
		}, t.slowLoadDelay);
	} else {
		anchorPos.borderWidth = t.realBorder;
		anchorPos.left -= t.realBorder;
		anchorPos.top -= t.realBorder;
		for (var name in anchorPos) {
			if (anchorPos.hasOwnProperty(name)) box[name] = anchorPos[name] + 'px';
		}
		t.pos.fbBox = anchorPos;
	}
	canvas.visibility = 'hidden';
	box.position = 'absolute';
	box.top = '-9999px';
	mainDiv.width = mainDiv.height = '0';
	mainDiv.borderWidth = t.innerBorder + 'px';
	t.pos.fbMainDiv = { width:0, height:0, top: t.cornerRadius - t.roundBorder };
	box.display = canvas.display = '';
	if (t.cornerRadius) {
		var cornerTop = t.fbCornerTop.style,
			cornerRight = t.fbCornerRight.style,
			cornerBottom = t.fbCornerBottom.style,
			cornerLeft = t.fbCornerLeft.style;
		cornerTop.left = cornerTop.top = cornerRight.top = cornerLeft.left = -t.cornerRadius + 'px';
		cornerTop.paddingRight = cornerTop.paddingBottom = cornerRight.paddingRight = cornerBottom.paddingBottom =
			cornerRight.paddingBottom = cornerBottom.paddingRight = cornerLeft.paddingRight =
			cornerLeft.paddingBottom = t.cornerRadius + 'px';
		var pre = 'url(' + t.graphicsPath + 'corner',
			post = '_' + t.lclTheme + '_r' + t.cornerRadius + '_b' + t.roundBorder + '.png)',
			post0 = '_' + t.lclTheme + '_r0_b' + t.roundBorder + '.png)';
		cornerTop.backgroundImage = pre + 'Top' + post;
		cornerRight.backgroundImage = pre + 'Right' + post;
		cornerBottom.backgroundImage = pre + 'Bottom' + (box.visibility && (!t.bottomCorners || t.draggerLocation === 'frame') ? post0 : post);
		cornerLeft.backgroundImage = pre + 'Left' + (box.visibility && !t.bottomCorners ? post0 : post);
		cornerTop.display = cornerRight.display = cornerBottom.display = cornerLeft.display = '';
		t.fbCanvas.style.paddingTop = t.fbCanvas.style.paddingBottom = (t.cornerRadius - t.roundBorder) + 'px';
		t.fbCanvas.style.top = -(t.cornerRadius - t.roundBorder) + 'px';
	}
	if (t.shadowSize) {
		var shadowTop = t.fbShadowTop.style,
			shadowRight = t.fbShadowRight.style,
			shadowBottom = t.fbShadowBottom.style,
			shadowLeft = t.fbShadowLeft.style,
			pad = t.realBorder || t.cornerRadius,
			pad2 = pad + t.realBorder;
		shadowTop.left = shadowTop.top = shadowRight.top = shadowLeft.left = -(pad + t.shadowSize) + 'px';
		if (!t.cornerRadius) shadowBottom.left = shadowLeft.top = -pad + 'px';
		shadowTop.paddingRight = shadowRight.paddingBottom = shadowBottom.paddingRight =
			shadowLeft.paddingBottom = (pad2 + t.shadowSize) + 'px';
		shadowTop.paddingBottom = shadowLeft.paddingRight = pad + 'px';
		shadowRight.paddingRight = shadowBottom.paddingBottom = (pad + t.shadowSize) + 'px';
		if (t.shadowType === 'drop') {
			shadowLeft.left = -pad + 'px';
			shadowBottom.left = (t.shadowSize - t.realBorder) + 'px';
			shadowBottom.paddingRight = pad2 + 'px';
		} else {
			shadowTop.display = '';
		}
		if (t.cornerRadius && !t.bottomCorners) {
			if (t.shadowType === 'drop') {
				shadowBottom.left = (t.shadowSize - pad) + 'px';
				shadowBottom.paddingRight = (2*pad) + 'px';
			} else {
				shadowBottom.left = (-pad) + 'px';
				shadowBottom.paddingRight = (2*pad + t.shadowSize) + 'px';
			}
			shadowRight.paddingBottom = (2*pad + t.shadowSize) + 'px';
		}
		if (t.bottomCorners && t.draggerLocation === 'frame') {
			shadowRight.paddingBottom = (2*pad + t.shadowSize) + 'px';
		}
		var pre = 'url(' + t.graphicsPath + 'shadow',
			post = '_s' + t.shadowSize + '_r' + t.cornerRadius + '.png)',
			post0 = '_s' + t.shadowSize + '_r0.png)';
		shadowTop.backgroundImage = pre + 'Top' + post;
		shadowBottom.backgroundImage = pre + 'Bottom' + (!t.bottomCorners || t.draggerLocation === 'frame' ? post0 : post);
		var drop = t.shadowType === 'drop' ? '_drop' : '';
		shadowRight.backgroundImage = pre + 'Right' + drop + post;
		shadowLeft.backgroundImage = pre + 'Left' + drop + (t.bottomCorners ? post : post0);
		shadowRight.display = shadowBottom.display = shadowLeft.display = '';
	}
	t.initPanels();
	if (t.navOverlay) {
		if (t.showNavOverlay === 'never' || (t.showNavOverlay === 'once' && fb.navOverlayShown)) {
			fb.showNavOverlay = false;
		} else {
			t.fbOverlayPrev.style.backgroundPosition = t.fbOverlayNext.style.backgroundPosition = t.onPos;
			t.setOpacity(t.fbOverlayPrev, t.controlOpacity);
			t.setOpacity(t.fbOverlayNext, t.controlOpacity);
			t.fbOverlayPrev.style.display = t.fbOverlayNext.style.display = 'none';
		}
	}
	t.setOpacity(t.fbResizer, t.controlOpacity);
	t.setOpacity(t.fbDragger, t.controlOpacity);
	t.fbResizer.style.display = t.fbDragger.style.display = 'none';
	t.startingScroll = t.getScroll();
	if (t.hideFlash) t.hideElements('flash');
	if (t.hideJava) t.hideElements('applet');
	if (t.ieOld) {
		t.hideElements('select');
		t.fbOverlay.style.position = 'absolute';
		if (t.stretchOverlay) {
			t.stretchOverlay();
			if (!t.ieOldInitialized) {
				window.attachEvent('onresize', t.stretchOverlay);
				window.attachEvent('onscroll', t.stretchOverlay);
				t.ieOldInitialized = true;
			}
			if (t.pngFix) t.pngFix();
		}
	}
};
Floatbox.prototype.hideElements = function(type, thisWindow) {
	var t = this;
	if (!thisWindow) {
		t.hideElements(type, self);
	} else {
		var tagName, tagNames = type === 'flash' ? ['object', 'embed'] : [type];
		try {
			while ((tagName = tagNames.pop())) {
				var els = thisWindow.document.getElementsByTagName(tagName),
					i = els.length;
				while (i--) {
					var el = els[i];
					if (el.style.visibility !== 'hidden' && (tagName !== 'object' ||
					/x-shockwave-flash|444553540000/i.test(el.innerHTML) ||
					/\b(movie|src|data)\b[^>]+\b(swf|youtube\.com\/v)\b/i.test(el.innerHTML))) {
						t.hiddenEls.push(el);
						el.style.visibility = 'hidden';
					}
				}
			}
		} catch(e) {}
		var iframes = thisWindow.document.getElementsByTagName('iframe'),
			i = iframes.length;
		while (i--) {
			var win = t.getIframeWindow(iframes[i]),
				src = iframes[i].src;
			if (win && src && !t.isXSite(src)) t.hideElements(type, win);
		}
	}
};
Floatbox.prototype.getAnchorPos = function(anchor, useThumb) {
	var t = this;
	if (anchor && anchor.tagName.toLowerCase() === 'a') {
		var thumb = useThumb && (anchor.getElementsByTagName('img') || [])[0];
		if (thumb && t.zoomImageStart && fb.zoomIn) {
			var pos = t.getLeftTop(thumb),
				border = (thumb.offsetWidth - thumb.width)/2;
			pos.left += border;
			pos.top += border;
			pos.width = thumb.width;
			pos.height = thumb.height;
			pos.src = thumb.src;
			return pos;
		}
		if (t.startAtClick && anchor.offsetWidth) {
			var el = thumb || anchor,
				pos = t.getLeftTop(el);
			pos.left += Math.round(el.offsetWidth / 2);
			pos.top += Math.round(el.offsetHeight / 2);
			pos.width = pos.height = 0;
			return pos;
		}
	}
	var display = t.getDisplaySize(),
		scroll = t.getScroll();
	return { left: Math.round(display.width/2) + scroll.left, top: Math.round(display.height/3) + scroll.top, width: 0, height: 0 };
};
Floatbox.prototype.initPanels = function() {
	var t = this;
	var infoPanel = t.fbInfoPanel.style,
		infoLink = t.fbInfoLink.style,
		printLink = t.fbPrintLink.style,
		itemNumber = t.fbItemNumber.style;
	if (t.infoCenter) {
		var infoPos = ' posCenter';
		infoPanel.textAlign = 'center';
		infoLink.paddingLeft = printLink.paddingLeft = itemNumber.paddingLeft =
		infoLink.paddingRight = printLink.paddingRight = itemNumber.paddingRight = Math.round(t.infoLinkGap/2) + 'px';
	} else if (t.infoLeft) {
		var infoPos = ' posLeft';
		infoPanel.textAlign = 'left';
		infoLink.paddingRight = printLink.paddingRight = t.infoLinkGap + 'px';
	} else {
		var infoPos = ' posRight';
		infoPanel.textAlign = 'right';
		infoLink.paddingLeft = printLink.paddingLeft = t.infoLinkGap + 'px';
	}
	t.fbInfoPanel.className += infoPos;
	t.fbInfoDiv.className += infoPos;
	infoPanel.width = '400px';
	var controlPanel = t.fbControlPanel.style,
		controls = t.fbControls.style,
		subControls = t.fbSubControls.style;
	if (t.controlLeft) {
		var controlPos = ' posLeft';
		controlPanel.textAlign = 'left';
	} else {
		var controlPos = ' posRight';
		controlPanel.textAlign = 'right';
		controls.right = '0';
	}
	t.fbControlPanel.className += controlPos;
	t.fbSubControls.className += controlPos;
	if (!t.ieOld) t.fbControls.className += controlPos;
	if (t.navButton) {
		var prev = t.fbPrev.style,
			next = t.fbNext.style,
			navControls = t.fbNavControls.style;
		prev.backgroundPosition = next.backgroundPosition = t.offPos;
		navControls['padding' + (t.controlLeft ? 'Left' : 'Right')] = t.controlSpacing + 'px';
		t.fbNavControls.className += controlPos;
		controlPanel.display = navControls.display = prev.display = next.display = '';
	}
	var width = 0;
	if (t.showClose) {
		var close = t.fbClose.style;
		close.backgroundPosition = t.offPos;
		t.fbClose.className += controlPos;
		controlPanel.display = controls.display = subControls.display = close.display = '';
		width = t.fbClose.offsetWidth;
	}
	if (t.showPlayPause && t.isSlideshow) {
		var play = t.fbPlay.style,
			pause = t.fbPause.style,
			playPause = t.fbPlayPause.style;
		play.backgroundPosition = pause.backgroundPosition = t.offPos;
		playPause['padding' + (t.controlLeft ? 'Left' : 'Right')] = t.controlSpacing + 'px';
		t.fbPlayPause.className += controlPos;
		controlPanel.display = controls.display = subControls.display = playPause.display = play.display = pause.display = '';
		play.top = t.isPaused ? '' : '-9999px';
		pause.top = t.isPaused ? '-9999px' : '';
		width += t.fbPlayPause.offsetWidth;
	}
	subControls.width = width + 'px';
	controlPanel.width = controls.width = (width + t.fbNavControls.offsetWidth) + 'px';
	if (t.lclNumIndexLinks) {
		var indexLinks = t.fbIndexLinks.style;
		if (t.indexLinksPanel === 'info') {
			t.fbIndexLinks.className += infoPos;
			infoPanel.display = '';
			if (t.showIndexThumbs) infoPanel.overflow = 'visible';
		} else {
			t.fbIndexLinks.className += controlPos;
			controlPanel.display = '';
			if (t.showIndexThumbs) controlPanel.overflow = 'visible';
			indexLinks['padding' + (t.indexLeft ? 'Left' : 'Right')] = '2px';
		}
		indexLinks.width = '250px';
		indexLinks.display = '';
	}
	if (t.enableDragResize && t.draggerLocation === 'frame' && Math.max(t.apparentPadding, t.panelPadding) < t.draggerSize) {
		if (!(t.controlTop || t.controlLeft)) {
			controlPanel.paddingRight = t.draggerSize + 'px';
		} else if (!(t.infoTop || t.infoLeft)) {
			infoPanel.paddingRight = t.draggerSize + 'px';
		}
	}
};
Floatbox.prototype.updatePanels = function() {
	var t = this;
	var infoPanel = t.fbInfoPanel.style,
		captionDiv = t.fbCaptionDiv.style,
		caption = t.fbCaption.style,
		infoDiv = t.fbInfoDiv.style,
		infoLink = t.fbInfoLink.style,
		printLink = t.fbPrintLink.style,
		itemNumber = t.fbItemNumber.style,
		index = t.indexToShow,
		item = t.itemToShow,
		str;
	infoPanel.display = captionDiv.display = caption.display = infoDiv.display =
	infoLink.display = printLink.display = itemNumber.display = 'none';
	if (t.showCaption && (str = item.options.caption || t.pageOptions.caption || item.title)) {
		if (str === 'href') {
			str = t.encodeHTML(item.href);
		} else {
			str = t.decodeHTML(str).replace(/&/g, '&amp;');
		}
		t.setInnerHTML(t.fbCaption, str);
		infoPanel.display = captionDiv.display = caption.display = '';
	} else {
		t.setInnerHTML(t.fbCaption, '');
	}
	if ((str = item.options.info)) {
		str = t.encodeHTML(t.decodeHTML(str));
		var options = item.options.infoOptions || t.pageOptions.infoOptions || '';
		if (options) options = t.encodeHTML(t.decodeHTML(options));
		str = '<a href="' + str + '" class="' + t.magicClass + '" rev="' + options + '">' +
		(item.options.infoText || t.pageOptions.infoText || t.strings.infoText) + '</a>';
		t.setInnerHTML(t.fbInfoLink, str);
		infoPanel.display = infoDiv.display = infoLink.display = '';
	} else {
		t.setInnerHTML(t.fbInfoLink, '');
	}
	if (item.options.showPrint || (item.options.showPrint !== false && t.pageOptions.showPrint)) {
		if (!(item.type === 'iframe' && t.isXSite(item.href))) {
			str = '<a href="' + t.encodeHTML(item.href) + '" rel="nofloatbox"' +
			' onclick="fb.printContents(\'' + t.printCSS + '\'); return fb.stopEvent(window.event);">' +
			(item.options.printText || t.pageOptions.printText || t.strings.printText) + '</a>';
			t.setInnerHTML(t.fbPrintLink, str);
			infoPanel.display = infoDiv.display = printLink.display = '';
		}
	} else {
		t.setInnerHTML(t.fbPrintLink, '');
	}
	if (t.lclShowItemNumber) {
		str = t.justImages ? t.strings.imgCount : (t.gotImg ? t.strings.mixedCount : t.strings.nonImgCount);
		str = str.replace('%1', index + 1);
		str = str.replace('%2', t.itemCount);
		t.setInnerHTML(t.fbItemNumber, str);
		infoPanel.display = infoDiv.display = itemNumber.display = '';
	} else {
		t.setInnerHTML(t.fbItemNumber, '');
	}
	var w = t.fbInfoLink.offsetWidth + t.fbPrintLink.offsetWidth + t.fbItemNumber.offsetWidth;
	if (t.ie) {
		if (t.fbInfoLink.offsetWidth) w += t.infoLinkGap;
		if (t.fbPrintLink.offsetWidth) w += t.infoLinkGap;
		if (t.fbItemNumber.offsetWidth) w += t.infoLinkGap;
	}
	infoDiv.width = w + 'px';
	if (t.lclNumIndexLinks) {
		str = '';
		var max = t.itemCount - 1,
			loRange, hiRange;
		if (t.lclNumIndexLinks === -1) {
			loRange = 0;
			hiRange = max;
		} else {
			var range = Math.floor(t.lclNumIndexLinks/2) - 1;
			loRange = index - range;
			hiRange = index + range;
			if (loRange <= 0) hiRange += Math.min(1 - loRange, range);
			if (index === 0) hiRange++;
			if (hiRange - max >= 0) loRange -= Math.min(1 + hiRange - max, range);
			if (index === max) loRange--;
		}
		var pos = t.indexTop ? 'down' : 'up',
			i = 0;
		while (i < t.itemCount) {
			if (i !== 0 && i < loRange) {
				str += '... ';
				i = loRange;
			} else if (i !== max && i > hiRange) {
				str += '... ';
				i = max;
			} else {
				if (i !== index) {
					var item = t.currentSet[i];
					str += '<a class="fbPop' + pos + '" rel="nofloatbox" href="' + item.href +
					'" onclick="fb.newContent(' + i + '); return fb.stopEvent(window.event);">' + ++i;
					try {
						if (t.showIndexThumbs && item.thumb) {
							str += '<img src="' + item.thumb.src + '" />';
						}
					} catch(e) {}
					str += '</a> ';
				} else {
					str += ++i + ' ';
				}
			}
		}
		t.setInnerHTML(t.fbIndexLinks, str);
		if (t.indexLinksPanel === 'info') {
			infoPanel.display = '';
		} else {
			t.tagAnchors(t.fbIndexLinks);
		}
	}
	if (!infoPanel.display) t.tagAnchors(t.fbInfoPanel);
};
Floatbox.prototype.measureContent = function() {
	var t = this,
		item = t.itemToShow,
		oncomplete = function() {
			t.collapse(function() {
				t.fetchContent();
				t.updatePanels();
				t.calcSize();
			});
		};
	item.nativeWidth = item.options.width;
	item.nativeHeight = item.options.height;
	if (item.type !== 'img') {
		item.nativeWidth = item.nativeWidth || (t.currentItem && t.currentItem.nativeWidth) || t.defaultWidth;
		item.nativeHeight = item.nativeHeight || (t.currentItem && t.currentItem.nativeHeight) || t.defaultHeight;
		t.quickChange = false;
	} else {
		if (!t.timeouts['slowLoad']) {
			t.setTimeout('slowLoad', function() {
				t.fbMainLoader.style.display = '';
			}, t.slowLoadDelay);
		}
		t.imgLoader = new Image();
		t.imgLoader.onload = function() {
			if (!t.endCalled) {
				item.nativeWidth = item.nativeWidth || t.imgLoader.width;
				item.nativeHeight = item.nativeHeight || t.imgLoader.height;
				t.quickChange = (t.currentItem && t.currentItem.type === 'img' &&
					(!t.resizeDuration || (!t.imageFadeDuration &&
					t.currentItem.nativeWidth === item.nativeWidth &&
					t.currentItem.nativeHeight === item.nativeHeight)));
				oncomplete();
			}
		};
		t.imgLoader.onerror = function() {
			if (this.src.indexOf(t.notFoundImg) === -1) this.src = t.notFoundImg;
		};
		t.imgLoader.src = item.href;
	}
	if (item.type !== 'img') oncomplete();
};
Floatbox.prototype.fetchContent = function(phase) {
	var t = this,
		item = t.itemToShow;
	if (!phase) {
		if (t.fbContent && !(item.type === 'img' && t.fbContent.tagName.toLowerCase() === 'img')) {
			t.fbMainDiv.removeChild(t.fbContent);
			delete t.fbContent;
			return t.setTimeout('fetch', function() { t.fetchContent(1); }, 10);
		}
	}
	if (t.ieOld) t.fbMainDiv.style.backgroundColor = item.type === 'img' ? '#000' : '';
	t.itemScroll = item.options.scrolling || item.options.scroll || t.pageOptions.scrolling || t.pageOptions.scroll || 'auto';
	t.fbMainDiv.style.overflow = 'hidden';
	if (/img|iframe/.test(item.type)) {
		if (!t.fbContent) t.fbContent = t.newNode(item.type, 'fbContent', t.fbMainDiv);
		if (item.type === 'img') {
			t.fbContent.src = t.imgLoader.src;
			if (t.imageClickCloses) t.fbContent.onclick = function() { t.end(); };
		}
	} else {
		t.fbContent = t.newNode('div', 'fbContent', t.fbMainDiv);
		t.fbMainDiv.style.overflow = t.itemScroll === 'yes' ? 'scroll' : (t.itemScroll === 'no' ? 'hidden' : 'auto');
		if (item.type === 'inline') {
			t.moveInnerHTML(item.sourceEl, t.fbContent);
			t.tagAnchors(t.fbContent);
		} else if (item.type === 'ajax') {
			fb.xhr.getResponse(item.href, function(xhr) {
				if ((xhr.status === 200 || xhr.status === 203 || xhr.status === 304) && xhr.responseText) {
					t.setInnerHTML(t.fbContent, xhr.responseText);
					t.tagAnchors(t.fbContent);
					setTimeout(function() {
						var match, rex = /<script[^>]*>([\s\S]+?)<\/script>/gi;
						rex.lastIndex = 0;
						while ((match = rex.exec(xhr.responseText))) {
							try { eval(match[1]); } catch(e) {}
						}
					}, 40);
				} else {
					t.setInnerHTML(t.fbContent, '<p style="color:#000; background:#fff; margin:1em; padding:1em;">' +
					'Unable to fetch content from ' + item.href + '</p>');
				}
			});
		}
	}
	t.fbContent.style.border = '0';
	t.fbContent.style.display = '';
};
Floatbox.prototype.calcSize = function(fit, pass) {
	var t = this,
		mainW, mainH, boxW, boxH, boxX, boxY,
		item = t.itemToShow,
		posBox = t.pos.fbBox;
	if (!t.fbBox) return;
	if (typeof fit === 'undefined') {
		fit = item.type === 'img' ? t.autoFitImages : t.autoFitOther;
	}
	var box = t.fbBox.style,
		infoPanel = t.fbInfoPanel.style,
		controlPanel = t.fbControlPanel.style,
		indexLinks = t.fbIndexLinks.style,
		captionDiv = t.fbCaptionDiv.style,
		itemNumber = t.fbItemNumber.style;
	if (!pass) {
		fb.displaySize = t.getDisplaySize();
		if (t.showCaption && t.fbCaption.innerHTML) captionDiv.display = '';
		if (t.lclShowItemNumber) itemNumber.display = '';
	}
	var infoH = t.fbInfoPanel.offsetHeight,
		controlH = t.fbControlPanel.offsetHeight;
	t.upperSpace = Math.max(t.infoTop ? infoH : 0, t.controlTop ? controlH : 0);
	t.lowerSpace = Math.max(t.infoTop ? 0 : infoH, t.controlTop ? 0 : controlH);
	if (t.upperSpace) t.upperSpace += 2*t.panelPadding;
	if (t.lowerSpace) t.lowerSpace += 2*t.panelPadding;
	t.upperSpace = Math.max(t.upperSpace, t.apparentPadding);
	t.lowerSpace = Math.max(t.lowerSpace, t.apparentPadding);
	var rightGap = Math.max(t.shadowSize, t.autoFitSpace),
		leftGap = t.shadowType === 'halo' ? rightGap : t.autoFitSpace,
		bottomGap = rightGap,
		topGap = leftGap,
		pad = 2*(t.apparentBorder + t.innerBorder) + leftGap + rightGap,
		maxW = fb.displaySize.width - 2*t.apparentPadding - pad,
		maxH = fb.displaySize.height - t.upperSpace - t.lowerSpace - pad,
		sizeRatio = eval('(' + (item.options.sizeRatio || '0') + ')'),
		isPercentW = false,
		isPercentH = false;
	if (sizeRatio) {
		mainW = maxW;
		mainH = mainW / sizeRatio;
		if (mainH > maxH) {
			mainH = maxH;
			mainW = mainH * sizeRatio;
		}
	} else {
		mainW = item.nativeWidth + '';
		isPercentW = mainW.substr(mainW.length - 1) === '%';
		if (mainW === 'max') {
			mainW = maxW;
		} else if (isPercentW) {
			mainW = Math.floor(maxW * parseInt(mainW, 10) / 100);
		} else {
			mainW = +mainW;
		}
		mainH = item.nativeHeight + '';
		isPercentH = mainH.substr(mainH.length - 1) === '%';
		if (mainH === 'max') {
			mainH = maxH;
		} else if (isPercentH) {
			mainH = Math.floor(maxH * parseInt(mainH, 10) / 100);
		} else {
			mainH = +mainH;
		}
	}
	if (t.enableDragResize && !(t.dragResizing || t.stickyDragResize)) {
		t.dragResizeDx = t.dragResizeDy = 0;
	}
	var dx, dy;
	if (t.dragResizing) {
		mainW += t.dragResizeDx + t.autoFitDx;
		if (mainW < t.minContentWidth) {
			dx = (t.dragResizeDx + (t.minContentWidth - mainW)) / t.dragResizeDx;
			mainW = t.minContentWidth;
			t.dragResizeDx *= dx;
			if (t.proportional) t.dragResizeDy *= dx;
		}
		mainH += t.dragResizeDy + t.autoFitDy;
		if (mainH < t.minContentHeight) {
			dy = (t.dragResizeDy + (t.minContentHeight - mainH)) / t.dragResizeDy;
			mainH = t.minContentHeight;
			t.dragResizeDy *= dy;
			if (t.proportional) {
				mainW -= t.dragResizeDx * (1 - dy);
				t.dragResizeDx *= dy;
			}
		}
	} else {
		var ratio = mainW/mainH,
			total = t.dragResizeDx + t.dragResizeDy;
		if (total) {
			dy = total / (ratio + 1);
			dx = total - dy;
			mainW += dx;
			mainH += dy;
		}
		dx = Math.max(t.minContentWidth - mainW, 0);
		dy = Math.max(t.minContentHeight - mainH, 0);
		if (dx || dy) {
			if (!(isPercentW || isPercentH)) {
				if (dy * ratio >= dx) {
					dx = dy * ratio;
				} else {
					dy = dx / ratio;
				}
			}
			mainW += dx;
			mainH += dy;
		}
		t.autoFitDx = t.autoFitDy = 0;
		if (fit && !sizeRatio) {
			dx = Math.min(maxW - mainW, 0);
			dy = Math.min(maxH - mainH, 0);
			if (dx || dy) {
				if (!(isPercentW || isPercentH)) {
					if (dy * ratio <= dx) {
						dx = dy * ratio;
					} else {
						dy = dx / ratio;
					}
				}
				mainW += (t.autoFitDx = dx);
				mainH += (t.autoFitDy = dy);
			}
		}
	}
	mainW = Math.round(mainW);
	mainH = Math.round(mainH);
	boxW = mainW + 2*(t.innerBorder + t.realPadding);
	boxH = mainH + 2*t.innerBorder + t.upperSpace + t.lowerSpace;
	if (t.cornerRadius) boxH -= 2*(t.cornerRadius - t.roundBorder);
	if (t.centerNav && t.navButton) {
		t.fbControls.style.width = Math.round(mainW/2 + t.innerBorder + t[t.controlLeft ? 'fbNext' : 'fbPrev'].offsetWidth) + 'px';
	}
	var infoW = boxW - 2*Math.max(t.apparentPadding, t.panelPadding);
	if (t.infoTop === t.controlTop && t.fbControls.offsetWidth) {
		infoW -= t.fbControls.offsetWidth + t.panelGap;
	}
	if (infoW < 0) infoW = 0;
	infoPanel.width = infoW + 'px';
	if (!t.lclNumIndexLinks) {
		var indexW = 0;
	} else if (t.indexLinksPanel === 'info' || t.infoTop !== t.controlTop) {
		var indexW = infoW;
	} else if (t.indexLinksPanel !== 'info' && t.infoTop === t.controlTop && t.infoCenter) {
		var indexW = Math.max(t.minIndexWidth, t.fbControls.offsetWidth);
	} else {
		var infoUsed = Math.max(t.fbCaption.offsetWidth, t.fbInfoLink.offsetWidth + t.fbPrintLink.offsetWidth + t.fbItemNumber.offsetWidth);
		var indexW = Math.max(t.minIndexWidth, t.fbControls.offsetWidth, (boxW - infoUsed - 2*Math.max(t.apparentPadding, t.panelPadding)));
		if (infoUsed) indexW -= t.panelGap;
	}
	if (indexW) indexLinks.width = (indexW - (t.indexLinksPanel !== 'info' ? 2 : 0)) + 'px';
	controlPanel.width = Math.max(indexW, t.fbControls.offsetWidth) + 'px';
	var changed = t.fbInfoPanel.offsetHeight !== infoH || t.fbControlPanel.offsetHeight !== controlH;
	if (t.showCaption) {
		if (t.minInfoWidth > infoW && !captionDiv.display) {
			captionDiv.display = 'none';
			changed = true;
		}
	}
	if (t.lclShowItemNumber) {
		if (t.fbInfoLink.offsetWidth + t.fbPrintLink.offsetWidth + t.fbItemNumber.offsetWidth > infoW && !itemNumber.display) {
			itemNumber.display = 'none';
			changed = true;
		}
	}
	if (changed && pass !== 3) return t.calcSize(fit, (pass || 0) + 1);
	t.widthOversize = mainW - maxW;
	t.heightOversize = mainH - maxH;
	if (t.dragResizing) {
		boxX = posBox.left;
		boxY = posBox.top;
	} else {
		if (t.ieOld) {
			if (t.currentItem) box[t.rtl ? 'right' : 'left'] = box.top = '-9999px';
			t.stretchOverlay();
		} else {
			var boxPosition = box.position;
			t.setPosition(t.fbBox, 'fixed');
		}
		var scroll = t.getScroll();
		if (t.ieOld) {
			if (t.currentItem) {
				box.left = posBox.left + 'px';
				box.top = posBox.top + 'px';
			}
		} else {
			t.setPosition(t.fbBox, boxPosition);
		}
		if (typeof t.boxLeft === 'number') {
			boxX = t.boxLeft;
		} else {
			var freeSpace = fb.displaySize.width - boxW - 2*(t.cornerRadius + t.realBorder) - t.shadowSize;
			if (t.shadowType === 'halo') freeSpace -= t.shadowSize;
			boxX = freeSpace/2;
			if (typeof t.boxLeft === 'string' && t.boxLeft.substr(t.boxLeft.length - 1) === '%') {
				boxX += parseInt(t.boxLeft, 10)/100 * boxX;
			}
		}
		if (t.stickyDragMove) boxX += t.dragResizeDx/2 + t.dragMoveDx;
		var rightSide = boxX + 2*(t.apparentBorder + t.apparentPadding + t.innerBorder) + mainW + rightGap,
			offset = rightSide - fb.displaySize.width;
		if (offset > 0) boxX -= offset;
		if (boxX < leftGap) {
			boxX = typeof t.boxLeft === 'number' ? t.boxLeft : leftGap;
		}
		boxX = Math.round(boxX) + t.cornerRadius + scroll.left;
		var factor = 3;
		if (typeof t.boxTop === 'number') {
			boxY = t.boxTop;
		} else {
			var freeSpace = fb.displaySize.height - boxH - 2*(t.cornerRadius + t.realBorder) - t.shadowSize;
			if (t.shadowType === 'halo') freeSpace -= t.shadowSize;
			var	ratio = freeSpace / fb.displaySize.height;
			if (ratio <= 0.15) {
				factor = 2;
			} else if (ratio < 0.3) {
				factor = 1 + ratio/0.15;
			}
			boxY = freeSpace/factor;
			if (typeof t.boxTop === 'string' && t.boxTop.substr(t.boxTop.length - 1) === '%') {
				boxY += parseInt(t.boxTop, 10)/100 * boxY;
			}
		}
		if (t.stickyDragMove) boxY += t.dragResizeDy/factor + t.dragMoveDy;
		var bottomSide = boxY + 2*(t.apparentBorder + t.innerBorder) + t.upperSpace + t.lowerSpace + mainH + bottomGap,
			offset = bottomSide - fb.displaySize.height;
		if (offset > 0) boxY -= offset;
		if (boxY < topGap) {
			boxY = typeof t.boxTop === 'number' ? t.boxTop : topGap;
		}
		boxY = Math.round(boxY) + t.cornerRadius + scroll.top;
		if (t.isChild && !(t.dragMoveDx || t.dragMoveDy)) {
			var rex = /max|%/i,
				pos = t.parent.pos.fbBox,
				childX = rex.test(item.nativeWidth) ? 99999 : (pos.left + boxX)/2,
				childY = rex.test(item.nativeHeight) ? 99999 : (pos.top + boxY)/2;
			if (scroll.left < childX && scroll.top < childY) {
				boxX = Math.min(boxX, childX);
				boxY = Math.min(boxY, childY);
			}
		}
	}
  	t.leftRatio = t.topRatio = false;
	if (t.dragResizing) {
		var oncomplete = function() { t.restore(); };
	} else {
		var oncomplete = function() {
			if (t.fbBox) t.fbBox.style.visibility ? t.zoomIn() : t.showContent();
		};
	}
	var timer = t.quickChange || t.dragResizing ? 0 : 10,
		split = !(t.liveResize || t.quickChange || t.dragResizing) && t.splitResize;
	if (split === 'auto') split = boxW - posBox.width <= boxH - posBox.height ? 'wh' : 'hw';
	t.setTimeout('setSize', function() {
		t.setSize(split, !timer,
			{ id: 'fbBox', left: boxX, top: boxY, width: boxW, height: boxH, borderWidth: t.realBorder },
			{ id: 'fbMainDiv', width: mainW, height: mainH, left: t.realPadding, top: t.upperSpace },
			function() { t.setTimeout('showContent', oncomplete, timer); }
		);
	}, timer);
};
Floatbox.prototype.setPosition = function(el, position) {
	if (el.style.position === position) return;
	var t = this,
		pos = t.pos[el.id],
		scroll = t.getScroll();
	if (position === 'fixed') {
		scroll.left = -scroll.left;
		scroll.top = -scroll.top;
	}
	if (pos) {
		pos.left += scroll.left;
		pos.top += scroll.top;
	}
	el.style.left = (el.offsetLeft + scroll.left) + 'px';
	el.style.top = (el.offsetTop + scroll.top) + 'px';
	el.style.position = position;
};
Floatbox.prototype.collapse = function(callback, phase) {
	var t = this;
	if (!phase) {
		t.clearTimeout('slowLoad');
		t.fbMainLoader.style.display = 'none';
		t.setPosition(t.fbBox, 'absolute');
		t.liveResize = (t.liveImageResize && t.resizeDuration && t.itemToShow === t.currentItem && t.itemToShow.type === 'img' && !t.endCalled);
		t.fbResizer.onclick = null;
		t.fbResizer.style.display = 'none';
		if (t.fbContent && /cursor|both/.test(t.resizeTool)) {
			t.fbContent.onclick = null;
			t.fbContent.style.cursor = '';
		}
		if (t.navOverlay) {
			t.fbLeftNav.style.display = t.fbRightNav.style.display =
			t.fbOverlayPrev.style.display = t.fbOverlayNext.style.display = 'none';
		}
		if (this.ie) t.fbDragger.style.display = 'none';
		var duration = (t.currentItem && t.currentItem.type === 'img' && !t.fbCanvas.style.visibility ? t.imageFadeDuration : 0);
		if (!(t.liveResize || (t.quickChange && !duration))) {
			return t.setOpacity(t.fbCanvas, 0, duration, function() { t.collapse(callback, 1); });
		}
	}
		t.fbDragger.style.display = 'none';
	var infoPanel = t.fbInfoPanel.style,
		controlPanel = t.fbControlPanel.style,
		offScreen = t.rtl ? 'right' : 'left';
	infoPanel.left = infoPanel.right = infoPanel.bottom =
		controlPanel.left = controlPanel.right = controlPanel.bottom = '';
	infoPanel[offScreen] = controlPanel[offScreen] = infoPanel.top = controlPanel.top = '-9999px';
	if (callback) callback();
};
Floatbox.prototype.restore = function(callback, phase) {
	var t = this,
		type = t.currentItem.type;
	if (!phase) {
		if (t.cornerRadius) {
			if (!t.bottomCorners || t.draggerLocation === 'frame') {
				t.fbCornerBottom.style.backgroundImage = t.fbCornerBottom.style.backgroundImage.replace(/_r\d+_/, '_r0_');
			}
			if (!t.bottomCorners) {
				t.fbCornerLeft.style.backgroundImage = t.fbCornerLeft.style.backgroundImage.replace(/_r\d+_/, '_r0_');
			}
		}
		if (t.shadowSize) t.fbShadows.style.display = '';
		var infoPanel = t.fbInfoPanel.style,
			controlPanel = t.fbControlPanel.style,
			setPos = function(panel, onTop) {
				if (onTop) {
					panel.style.bottom = '';
					panel.style.top = Math.round((t.upperSpace - panel.offsetHeight)/2) + 'px';
				} else {
					panel.style.top = '';
					panel.style.bottom = Math.round((t.lowerSpace - panel.offsetHeight)/2) + 'px';
				}
			};
		infoPanel.left = infoPanel.right = controlPanel.left = controlPanel.right = '';
		controlPanel[t.controlLeft ? 'left' : 'right'] =
			infoPanel[t.infoLeft ? 'left' : 'right'] = Math.max(t.realPadding, t.panelPadding) + 'px';
		setPos(t.fbInfoPanel, t.infoTop);
		setPos(t.fbControlPanel, t.controlTop);
	if (t.enableDragResize) {
		var dragger = t.fbDragger.style;
		if (t.draggerLocation === 'frame') {
			dragger.right = (t.roundBorder - t.cornerRadius) + 'px';
			dragger.bottom = '0';
		} else {
			dragger.right = (t.apparentPadding + t.roundBorder + t.innerBorder - t.cornerRadius) + 'px';
			dragger.bottom = (t.lowerSpace + t.innerBorder) + 'px';
		}
		if (!this.ie) t.fbDragger.style.display = '';
	}
		t.clearTimeout('mainLoader');
		t.fbBoxLoader.style.display = 'none';
		t.fbMainDiv.style.display = '';
		if (t.fbContent) t.fbContent.style.display = '';
		var duration = (type === 'img' && !t.fbCanvas.style.visibility) ? t.imageFadeDuration : 0;
		if (!(t.liveResize || (t.quickChange && !duration))) {
			return t.setOpacity(t.fbCanvas, 100, duration, function() { t.restore(callback, 1); });
		}
	}
	if (this.ie && t.enableDragResize) t.fbDragger.style.display = '';
	if (type === 'img' ? t.resizeImages : t.resizeOther) {
		var scale = 0;
		if (Math.min(t.autoFitDx + t.dragResizeDx, t.autoFitDy + t.dragResizeDy) < -30) {
			scale = 1;
		} else if (Math.max(t.widthOversize, t.heightOversize, t.dragResizeDx, t.dragResizeDy) > 30){
			scale = -1;
		}
		if (scale) {
			t.fbResizer.onclick = function(e) {
				if (t.isSlideshow && t.pauseOnResize && !t.isPaused) {
					t.setPause(true);
				}
				t.dragResizeDx = t.dragResizeDy = t.autoFitDx = t.autoFitDy = 0;
				t.collapse(function() { t.calcSize(scale === -1); });
				return t.stopEvent(e || window.event);
			};
			if (type === 'img' && /cursor|both/.test(t.resizeTool)) {
				t.fbContent.style.cursor = 'url(' + (scale === -1 ? t.resizeDownCursor : t.resizeUpCursor) +'), default';
				t.fbContent.onclick = t.fbResizer.onclick;
			}
			if (type !== 'img' || /topleft|both/.test(t.resizeTool)) {
				t.fbResizer.style.backgroundPosition = (scale === -1 ? 'bottom' : 'top');
				t.fbResizer.style.display = '';
			}
		}
	}
	if (t.navOverlay) {
		var leftNav = t.fbLeftNav.style,
			rightNav = t.fbRightNav.style,
			overlayPrev = t.fbOverlayPrev.style,
			overlayNext = t.fbOverlayNext.style;
		leftNav.width = rightNav.width =
			Math.max(t.navOverlayWidth/100 * t.pos.fbMainDiv.width, t.fbOverlayPrev.offsetWidth) + 'px';
		leftNav.display = rightNav.display = '';
		if (fb.showNavOverlay) {
			overlayPrev.visibility = overlayNext.visibility = 'hidden';
			overlayPrev.display = overlayNext.display = '';
			overlayPrev.top = overlayNext.top =
				((t.pos.fbMainDiv.height - t.fbOverlayPrev.offsetHeight) * t.navOverlayPos/100) + 'px';
		}
	}
	if (callback) callback();
};
Floatbox.prototype.setSize = function(order, quick) {
	var t = this;
	if (quick !== true && t.resizeDuration && t.setSize_module) {
		return t.setSize_module(arguments[0], arguments[1], arguments[2], arguments[3], arguments[4], arguments[5], arguments[6]);
	}
	var oncomplete = false,
		node,
		i = arguments.length;
	while (i--) {
		if (typeof arguments[i] === 'object' && arguments[i].id) {
			var obj = arguments[i],
				node = t[obj.id],
				tag = node.tagName;
			if (!t.pos[obj.id]) t.pos[obj.id] = {};
			for (var prop in obj) {
				if (obj.hasOwnProperty(prop) && prop !== 'id') {
					var val = obj[prop],
						px = 0,
						target = node;
					if ((tag !== 'img' && tag !== 'iframe') || (prop !== 'width' && prop !== 'height')) {
						target = node.style;
						px = 'px';
					}
					target[prop] = val + px;
					if (/width|height/i.test(prop)) {
						if (obj.id === 'fbMainDiv') {
							t.fbContent[prop] = val;
						} else if (obj.id === 'fbZoomDiv') {
							t.fbZoomImg[prop] = val;
						}
					}
					t.pos[obj.id][prop] = val;
				}
			}
		} else if (typeof arguments[i] === 'function') {
			oncomplete = arguments[i];
		}
	}
	if (oncomplete) setTimeout(oncomplete, 10);
};
Floatbox.prototype.setOpacity = function(el, opacity, duration, callback) {
	var t = this;
	if (duration && t.setOpacity_module) return t.setOpacity_module(el, opacity, duration, callback);
	if (opacity) el.style.display = el.style.visibility = '';
	if (el.style.removeAttribute) {
		if (opacity === 100) {
			el.style.removeAttribute('filter');
		} else {
			el.style.filter = 'alpha(opacity=' + opacity + ')';
		}
	}
	el.style.opacity = opacity/100 + '';
	if (callback) setTimeout(callback, 10);
};
Floatbox.prototype.showContent = function(phase) {
	var t = this;
	if (!phase) {
		var index = t.indexToShow,
			item = t.itemToShow,
			type = item.type,
			posBox = t.pos.fbBox,
			displaySize = t.getDisplaySize();
		if (!t.reCalced) {
			if ((displaySize.width !== fb.displaySize.width && t.widthOversize > -40) ||
				(displaySize.height !== fb.displaySize.height && t.heightOversize > -40)) {
				t.reCalced = true;
				return t.calcSize(t.autoFitDx || t.autoFitDy);
			}
		}
		t.reCalced = false;
		self.focus();
		if (t.ieOld) t.stretchOverlay();
		if ((t.ffOld && /silverlight|quicktime/.test(type)) || (t.seaMonkey && (type.indexOf('media') === 0)) ||
		((t.disableScroll || (t.ffOld && type === 'iframe')) && !t.ieOld &&
		posBox.width <= displaySize.width && posBox.height <= displaySize.height)) {
			t.setPosition(t.fbBox, 'fixed');
		}
		if (type === 'iframe') {
			var src = t.fbContent.src;
			if (src.substr(src.length - item.href.length) !== item.href) t.fbContent.src = item.href;
		} else if (type.indexOf('media') === 0) {
			t.setInnerHTML(t.fbContent, t.mediaHTML(item.href, type, t.pos.fbMainDiv.width, t.pos.fbMainDiv.height));
		}
		t.prevIndex = index ? index - 1 : t.itemCount - 1;
		t.nextIndex = index < t.itemCount - 1 ? index + 1 : 0;
		t.prevHref = t.enableWrap || index !== 0 ? t.currentSet[t.prevIndex].href : '';
		t.nextHref = t.enableWrap || index !== t.itemCount - 1 ?  t.currentSet[t.nextIndex].href : '';
		if (t.navButton) {
			if (t.prevHref) {
				if (!t.operaOld) t.fbPrev.href = t.prevHref;
				t.fbPrev.title = t.fbPrev.title || t.fbPrev.titleOff;
				t.fbPrev.titleOff = '';
			} else {
				t.fbPrev.removeAttribute('href');
				t.fbPrev.titleOff = t.fbPrev.title;
				t.fbPrev.title = '';
			}
			if (t.nextHref) {
				if (!t.operaOld) t.fbNext.href = t.nextHref;
				t.fbNext.title = t.fbNext.title || t.fbNext.titleOff;
				t.fbNext.titleOff = '';
			} else {
				t.fbNext.removeAttribute('href');
				t.fbNext.titleOff = t.fbNext.title;
				t.fbNext.title = '';
			}
			t.fbPrev.className = t.fbPrev.className.replace('_off', '') + (t.prevHref ? '' : '_off');
			t.fbNext.className = t.fbNext.className.replace('_off', '') + (t.nextHref ? '' : '_off');
		}
		if (t.navOverlay) {
			if (!t.operaOld) {
				t.fbLeftNav.href = t.fbOverlayPrev.href = t.prevHref;
				t.fbRightNav.href = t.fbOverlayNext.href = t.nextHref;
			}
			t.fbLeftNav.style.visibility = t.prevHref ? '' : 'hidden';
			t.fbRightNav.style.visibility = t.nextHref ? '' : 'hidden';
			fb.navOverlayShown = true;
		}
		if (t.currentItem !== t.itemToShow) {
			t.previousItem = t.currentItem;
			t.currentItem = t.itemToShow;
			t.currentIndex = t.indexToShow;
		}
		t.fbCanvas.style.visibility = '';
		return t.restore(function() {
			t.setTimeout('showContent', function() { t.showContent(1); }, 10);
		} );
	}
	var item = t.currentItem;
	if (!item.seen) {
		item.seen = true;
		t.itemsShown++;
	}
	if (t.isSlideshow && !t.isPaused) {
		t.setTimeout('slideshow', function() {
			if (t.endTask === 'loop' || t.itemsShown < t.itemCount) {
				t.newContent(t.nextIndex);
			} else if (t.endTask === 'exit') {
				t.end();
			} else {
				t.setPause(true);
				var i = t.itemCount;
				while (i--) t.currentSet[i].seen = false;
				t.itemsShown = 0;
			}
		}, t.slideInterval*1000);
	}
	setTimeout(function() { t.exec(item, 'onItemStart'); }, 10);
	t.setTimeout('preload', function() {
			fb.preloadImages(t.nextHref || t.prevHref || '', true);
	}, 100);
};
Floatbox.prototype.newContent = function(index) {
	var t = fb.lastChild;
	if (!t.currentItem || index === t.currentIndex) return;
	t.clearTimeout('slideshow');
	t.clearTimeout('resize');
	fb.preloadImages('', false);
	t.exec(t.currentItem, 'onItemEnd');
	if (t.currentItem.type === 'inline') {
		t.moveInnerHTML(t.fbContent, t.currentItem.sourceEl);
	}
	if (fb.showNavOverlay == 'once' && fb.navOverlayShown) fb.showNavOverlay = false;
	if (window.scrollTo) window.scrollTo(t.startingScroll.left, t.startingScroll.top);
	t.indexToShow = index;
	t.itemToShow = t.currentSet[index];
	t.measureContent();
};
Floatbox.prototype.end = function(all) {
	var t = fb.lastChild,
		item = t.currentItem;
	if (!t.fbBox) return;
	t.endCalled = true;
	t.endAll = t.endAll || all;
	for (var key in t.timeouts) {
		if (t.timeouts.hasOwnProperty(key)) t.clearTimeout(key);
	}
	t.fbMainLoader.style.display = 'none';
	parent.focus();
	t.fbOverlay.onclick = null;
	if (t.ieOld) {
		window.detachEvent('onresize', t.stretchOverlay);
		window.detachEvent('onscroll', t.stretchOverlay);
	}
	if (!t.isChild) {
		if (fb.keydownHandlerSet) {
			fb.removeEvent(fb.doc, 'keydown', fb.keydownHandler);
			fb.keydownHandlerSet = false;
		}
		if (fb.keypressHandlerSet) {
			fb.removeEvent(fb.doc, 'keypress', fb.keypressHandler);
			fb.keypressHandlerSet = false;
		}
		if (fb.resizeHandlerSet) {
			fb.removeEvent(window, 'resize', fb.resizeHandler);
			fb.resizeHandlerSet = false;
		}
	} else {
		if (t.endAll) t.imageFadeDuration = t.overlayFadeDuration = t.resizeDuration = 0;
	}
	if (t.fbBox.style.visibility) {
		if (!t.currentItem) t.fbZoomDiv.style.display = 'none';
	} else {
		t.exec(item, 'onItemEnd');
		if (item.type === 'inline' && t.fbContent.innerHTML) t.moveInnerHTML(t.fbContent, item.sourceEl);
		t.exec(item, 'beforeBoxEnd');
		if (t.resizeDuration) {
			if (t.zoomImageStart && item.type === 'img' && t.zoomOut) {
				if (item.popup) item.anchor.onmouseover(true);
				var anchorPos = t.getAnchorPos(item.anchor, true);
				item.popupLocked = false;
				if (item.popup) item.anchor.onmouseout();
				if (anchorPos.width) {
					t.fbZoomDiv.style.borderWidth = t.zoomPopBorder + 'px';
					anchorPos.left -= t.zoomPopBorder;
					anchorPos.top -= t.zoomPopBorder;
					t.pos.thumb = anchorPos;
					return t.zoomOut();
				}
			}
			var anchorPos = t.getAnchorPos(t.clickedAnchor, !item.popup),
				split = t.splitResize,
				zeroBox = { id: 'fbBox', left: (anchorPos.left + anchorPos.width/2), top: (anchorPos.top + anchorPos.height/2), width: 0, height: 0 };
			if (split === 'wh') {
				split = 'hw';
			} else if (split === 'hw') {
				split = 'wh';
			} else if (split === 'auto') {
				split = t.pos.fbBox.width <= t.pos.fbBox.height ? 'hw' : 'wh';
			}
			var oncomplete3 = function() {
				setTimeout(function() {
					t.fbBox.style.top = '-9999px';
					t.fbBox.style.visibility = 'hidden';
					t.end();
				}, 10);
			};
			if (split) {
				var size = Math.max(t.maxInitialSize - 2*(t.cornerRadius || t.realBorder), t.minInitialSize),
					smallBox = { id: 'fbBox', left: Math.round(anchorPos.left - size/2), top: Math.round(anchorPos.top - size/2), width: size, height: size },
					oncomplete2 = function() {
						t.setSize(split, smallBox, function() {
							t.setSize(zeroBox, oncomplete3);
						});
					};
			} else {
				var oncomplete2 = function() {
					t.setSize(zeroBox, oncomplete3);
				};
			}
			var oncomplete = function() {
				t.fbCanvas.style.display = 'none';
				if (t.resizeDuration) t.fbBoxLoader.style.display = '';
				if (t.shadowSize) t.fbShadows.style.display = 'none';
				if (t.cornerRadius) {
					t.fbCornerBottom.style.backgroundImage = t.fbCornerBottom.style.backgroundImage.replace('_r0', '_r' + t.cornerRadius);
					t.fbCornerLeft.style.backgroundImage = t.fbCornerLeft.style.backgroundImage.replace('_r0', '_r' + t.cornerRadius);
				}
				if (item && item.type && item.type.indexOf('media') === 0) {
					setTimeout( function() {
						if (t.fbContent) {
							var objects = t.fbContent.getElementsByTagName('object'),
								i = objects.length;
							while (i--) {
								var obj = objects[i];
								if (obj && obj.parentNode) obj.parentNode.removeChild(obj);
							}
						}
					}, 20);
				}
				oncomplete2();
			};
			return t.collapse(oncomplete);
		}
	}
	t.fbBox.style.display = 'none';
	var level = fb.children.length + 1,
		i = t.anchors.length;
	while (i--) {
		if (t.anchors[i].level >= level) t.anchors.splice(i, 1);
	}
	if (t.isChild) fb.children.length--;
	fb.lastChild = fb.children[fb.children.length-1] || fb;
	var oncomplete2 = function() {
		setTimeout(function() {
			while (t.nodes.length) {
				var node = t.nodes.pop();
				if (node && node.parentNode) {
					node.parentNode.removeChild(node);
					delete t[node.id];
				}
			}
			t.exec(item, 'afterBoxEnd');
			if (t.endAll && t.isChild) {
				return fb.end(true);
			} else {
				var page = t.loadPageOnClose || t.clickOptions.loadPageOnClose || t.pageOptions.loadPageOnClose;
				if (page) {
					if (page === 'self' || page === 'this') {
						location.reload(true);
					} else if (page === 'back') {
						history.back();
					} else {
						location.href = page;
					}
				}
			}
		}, 10);
	};
	var oncomplete = function() {
		while(t.hiddenEls.length) {
			var el = t.hiddenEls.pop();
			el.style.visibility = 'visible';
			if (t.ffOld && t.ffMac) {
				el.focus();
				el.blur();
			}
		}
		if (t.fbOverlay) {
			var overlay = t.fbOverlay.style;
			overlay.display = 'none';
			overlay.width = overlay.height = '0';
			var duration = item.popup ? 5 : 0;
			t.fbZoomDiv.style.opacity = '1';
			t.setOpacity( t.fbZoomDiv, 0, duration, oncomplete2);
		}
		t.currentItem = t.previousItem = t.clickedAnchor = t.itemToShow = t.indexToShow = false;
	};
	t.setOpacity(t.fbOverlay, 0, t.overlayFadeDuration, oncomplete);
};
Floatbox.prototype.exec = function(item, funcName) {
	var t = this,
		options = (item && item.options) || t.clickOptions,
		func = t[funcName] || (options && options[funcName]) || t.pageOptions[funcName];
	try {
		if (typeof func === 'function') {
			func();
		} else if (typeof func === 'string') {
			eval(func);
		}
	} catch(e) {}
};
Floatbox.prototype.setPause = function(pause) {
	var t = this;
	t.isPaused = pause;
	if (pause) {
		t.clearTimeout('slideshow');
	} else {
		t.newContent(t.nextIndex);
	}
	if (t.showPlayPause) {
		t.fbPlay.style.top = pause ? '' : '-9999px';
		t.fbPause.style.top = pause ? '-9999px' : '';
	}
};
Floatbox.prototype.getDisplaySize = function() {
	var t = this;
	return { width: t.getDisplayWidth(), height: t.getDisplayHeight() };
};
Floatbox.prototype.getDisplayWidth = function() {
	var t = this;
	return t.docEl.clientWidth || t.bod.clientWidth;
};
Floatbox.prototype.getDisplayHeight = function() {
	var t = this;
	return (!t.docEl.clientHeight || t.operaOld || document.compatMode === 'BackCompat') ?
		t.bod.clientHeight : t.docEl.clientHeight;
};
Floatbox.prototype.getScroll = function(win) {
	var t = this;
	if (!(win && win.document)) win = self;
	var doc = win.document,
		html = doc.documentElement,
		bod = doc.getElementsByTagName('body')[0],
		left = win.pageXOffset || bod.scrollLeft || doc.documentElement.scrollLeft || 0;
	if (t.ie && t.rtl) left -= html.scrollWidth - html.clientWidth;
	return {
		left: left,
		top: win.pageYOffset || bod.scrollTop || doc.documentElement.scrollTop || 0
	};
};
Floatbox.prototype.setTimeout = function(key, func, delay) {
	var t = this;
	if (delay) {
		t.timeouts[key] = setTimeout(func, delay);
	} else {
		return func();
	}
};
Floatbox.prototype.clearTimeout = function(key) {
	var t = this;
	if (t.timeouts[key]) {
		clearTimeout(t.timeouts[key]);
		delete t.timeouts[key];
	}
};
Floatbox.prototype.encodeHTML = function(str) {
	return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
};
Floatbox.prototype.decodeHTML = function(str) {
	var s = str.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&apos;/g, "'").replace(/&amp;/g, '&');
	return s.replace(/&#(\d+);/g, function(match, p1) { return String.fromCharCode(+p1); });
};
Floatbox.prototype.isXSite = function(src) {
	var base = (location.protocol + '//' + location.host).toLowerCase();
	return /https?:\/\/\w/i.test(src) && src.toLowerCase().indexOf(base) !== 0;
};
Floatbox.prototype.getXMLHttpRequest = function() {
	var xhr;
	if (window.XMLHttpRequest) {
		if (!(xhr = new XMLHttpRequest())) return false;
	} else {
		try { xhr = new ActiveXObject("Msxml2.XMLHTTP"); } catch(e) {
			try { xhr = new ActiveXObject("Microsoft.XMLHTTP"); } catch(ee) { return false; }
		}
	}
	return {
		getResponse: function(url, callback) {
			try {
				xhr.open('GET', url, true);
				xhr.setRequestHeader('If-Modified-Since', 'Thu, 1 Jan 1970 00:00:00 GMT');
				xhr.setRequestHeader('Cache-Control', 'no-cache');
				xhr.onreadystatechange = function() {
					if (xhr.readyState === 4) {
						xhr.onreadystatechange = function() {};
						callback(xhr);
					}
				};
				xhr.send(null);
			} catch(e) {}
		}
	};
};
Floatbox.prototype.setInnerHTML = function(el, strHTML) {
	try {
		el.innerHTML = strHTML;
		return true;
	} catch(e) {}
	try {
		var doc = el.ownerDocument,
			range = doc.createRange();
		range.selectNodeContents(el);
		range.deleteContents();
		if (strHTML) {
			var xmlDiv = new DOMParser().parseFromString('<div xmlns="http://www.w3.org/1999/xhtml">' + strHTML + '</div>', 'application/xhtml+xml;charset=utf-8'),
				childNodes = xmlDiv.documentElement.childNodes;
			for (var i = 0, len = childNodes.length; i < len; i++) {
				el.appendChild(doc.importNode(childNodes[i], true));
			}
		}
		return true;
	} catch (e) {}
	return false;
};
Floatbox.prototype.getOuterHTML = function(el) {
	if (el.outerHTML) return el.outerHTML;
	var div = (el.ownerDocument || el.document).createElement('div');
	div.appendChild(el.cloneNode(true));
	return div.innerHTML;
};
Floatbox.prototype.moveInnerHTML = function(from, to) {
	var t = this;
	if (from && to && t.setInnerHTML(to, from.innerHTML)) t.setInnerHTML(from, '');
};
Floatbox.prototype.preloadImages = function(href, chain) {
	var t = fb;
	if (typeof t.preloads.count === 'undefined') {
		t.preloads.count = 0;
		return t.preloadImages('/cms/js/floatbox/graphics/blank.gif', chain);
	}
	if (typeof chain !== 'undefined') t.preloads.chain = chain;
	if (!href && t.preloads.chain && (t.preloadAll || !t.preloads.count)) {
		for (var i = 0, len = t.anchors.length; i < len; i++) {
			var a = t.anchors[i];
			if (a.type === 'img' && !t.preloads[a.href]) {
				href = a.href;
				break;
			}
		}
	}
	if (href) {
		if (t.preloads[href]) {
			fb.preloadImages();
		} else {
			var img = t.preloads[href] = new Image();
			img.onerror = function() {
				setTimeout(function() { fb.preloadImages(); }, 200);
				t.preloads[href] = true;
			};
			img.onload = function() {
				t.preloads.count++;
				this.onerror();
			};
			img.src = href;
		}
	}
};
Floatbox.prototype.getLeftTop = function(el, local) {
	var t = this;
	if (t.getLeftTop_module) return t.getLeftTop_module(el, local);
	if (el.getBoundingClientRect) {
		var rect = el.getBoundingClientRect(),
			doc = el.ownerDocument,
			bod = doc.getElementsByTagName('body')[0],
			docEl = doc.documentElement || doc.document,
			win = doc.defaultView || doc.parentWindow,
			scroll = t.getScroll(win);
		left = rect.left + scroll.left;
		top = rect.top + scroll.top;
		if (t.ie) {
			left -= docEl.clientLeft || bod.clientLeft;
			top -= docEl.clientTop || bod.clientTop;
		}
	} else {
		var left = el.offsetLeft || 0,
			top = el.offsetTop || 0,
			node = el;
		while ((node = node.offsetParent)) {
			left += node.offsetLeft;
			top += node.offsetTop;
		}
	}
	return {left: left, top: top};
};
Floatbox.prototype.mediaHTML = function(href, type, width, height) {
	if (this.mediaHTML_module) return this.mediaHTML_module(href, type, width, height);
	return 'Failed to load media HTML\nPlease try again';
};
Floatbox.prototype.printContents = function(css) {
	if (this.printContents_module) return this.printContents_module(css);
	alert('Failed to load printContents function\nPlease try again');
};
Floatbox.prototype.loadAnchor = function(href, rev, title) {
	var t = fb.lastChild;
	if (href) {
		if (href.getAttribute) {
			t.start(href);
		} else {
			t.start({ href: href, rev: rev, title: title });
		}
	}
};
Floatbox.prototype.goBack = function() {
	var t = fb.lastChild,
		a = t.previousItem;
	if (a) t.start({ href: a.href, rev: a.rev + ' sameBox:true', title: a.title });
};
Floatbox.prototype.resize = function(width, height) {
	var t = fb.lastChild,
		item = t.currentItem || false,
		changed = false;
	width = parseInt(width, 10);
	height = parseInt(height, 10);
	if (width && item && item.nativeWidth !== width) {
		item.nativeWidth = width;
		changed = true;
	}
	if (height && item && item.nativeHeight !== height) {
		item.nativeHeight = height;
		changed = true;
	}
	if (changed) t.collapse(function() { t.calcSize(false); });
};
Floatbox.prototype.getIframeDocument = function(iframe) {
	var t = this;
	if (!iframe && t.currentItem.type === 'iframe') iframe = t.currentItem;
	if (typeof iframe === 'object') {
		try {
			return iframe.contentDocument || (iframe.contentWindow && iframe.contentWindow.document);
		} catch(e) {}
	}
	return false;
};
Floatbox.prototype.getIframeWindow = function(iframe) {
	var t = this;
	if (!iframe && t.currentItem.type === 'iframe') iframe = t.currentItem;
	if (typeof iframe === 'object') {
		try {
			return iframe.contentWindow || (iframe.contentDocument && iframe.contentDocument.defaultView);
		} catch(e) {}
	}
	return false;
};
(function() {
	var t = fb;
	t.xhr = t.getXMLHttpRequest();
	t.strings = {
		hintClose: 'Exit (key: Esc)',
		hintPrev: 'Previous (key: <--)',
		hintNext: 'Next (key: -->)',
		hintPlay: 'Play (key: spacebar)',
		hintPause: 'Pause (key: spacebar)',
		hintResize: 'Resize (key: Tab)',
		imgCount: 'Image %1 of %2',
		nonImgCount: 'Page %1 of %2',
		mixedCount: '(%1 of %2)',
		infoText: 'Info...',
		printText: 'Print...'
	};
	var lang = t.language === 'auto' ? t.browserLanguage : t.language;
	if (t.xhr) {
		t.xhr.getResponse(t.languagesPath + lang + '.json', function(xhr) {
			if ((xhr.status === 200 || xhr.status === 203 || xhr.status === 304) && xhr.responseText) {
				var ltArrow = String.fromCharCode(8592),
					rtArrow = String.fromCharCode(8594),
					text = xhr.responseText;
				if (t.ieXP) {
					text = text.replace(ltArrow, '<--').replace(rtArrow, '-->');
				}
				try {
					var obj = eval('(' + text + ')');
					if (obj && obj.hintClose) t.strings = obj;
				} catch(e) {}
			}
			if (t.rtl) {
				if (!/^(ar|he)$/.test(t.language)) {
					t.strings.infoText = t.strings.infoText.replace('...', '');
					t.strings.printText = t.strings.printText.replace('...', '');
				}
				t.strings.hintPrev = t.strings.hintPrev.replace(ltArrow, rtArrow).replace('-->', '<--');
				t.strings.hintNext = t.strings.hintNext.replace(rtArrow, ltArrow).replace('<--', '-->');
				var swap = t.strings.hintPrev;
				t.strings.hintPrev = t.strings.hintNext;
				t.strings.hintNext = swap;
			}
		});
	}
})();