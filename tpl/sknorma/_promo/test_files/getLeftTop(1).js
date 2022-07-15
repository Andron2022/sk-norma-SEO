Floatbox.prototype.getLeftTop_module = function(el, local) {
	var t = this,
		left = el.offsetLeft || 0,
		top = el.offsetTop || 0,
		doc = el.ownerDocument,
		bod = doc.getElementsByTagName('body')[0],
		docEl = doc.documentElement || doc.document,
		win = doc.defaultView || doc.parentWindow,
		scroll = t.getScroll(win);
	if (el.getBoundingClientRect && !local) {
		var rect = el.getBoundingClientRect();
		left = rect.left + scroll.left;
		top = rect.top + scroll.top;
		if (t.ie) {
			left -= docEl.clientLeft || bod.clientLeft;
			top -= docEl.clientTop || bod.clientTop;
		}
	} else {
		var position = t.getStyle(el, 'position'),
			rex = /absolute|fixed/i,
			elFlow = !rex.test(position),
			inFlow = elFlow,
			node = el;
		if (position === 'fixed') {
			left += scroll.left;
			top += scroll.top;
		}
		while (position !== 'fixed' && (node = node.offsetParent)) {
			var borderLeft = 0,
				borderTop = 0,
				nodeFlow = true,
				position = t.getStyle(node, 'position'),
				nodeFlow = !rex.test(position);
			if (t.opera) {
				if (local && node !== bod) {
					left += node.scrollLeft - node.clientLeft;
					top += node.scrollTop - node.clientTop;
				}
			} else if (t.ie) {
				if (node.currentStyle.hasLayout && node !== doc.documentElement) {
					borderLeft = node.clientLeft;
					borderTop = node.clientTop;
				}
			} else {
				borderLeft = parseInt(t.getStyle(node, 'border-left-width'), 10);
				borderTop = parseInt(t.getStyle(node, 'border-top-width'), 10);
				if (t.ff && node === el.offsetParent && !nodeFlow && (t.ffOld || !elFlow)) {
					left += borderLeft;
					top += borderTop;
				}
			}
			if (!nodeFlow) {
				if (local) return { left: left, top: top };
				inFlow = false;
			}
			if (node.offsetLeft > 0) left += node.offsetLeft;
			left += borderLeft;
			top += node.offsetTop + borderTop;
			if (position === 'fixed') {
				left += scroll.left;
				top += scroll.top;
			}
			if (!(t.opera && elFlow) && node !== bod && node !== doc.documentElement) {
				left -= node.scrollLeft;
				top -= node.scrollTop;
			}
		}
		if (t.ff && inFlow) {
			left += parseInt(t.getStyle(bod, 'border-left-width'), 10);
			top += parseInt(t.getStyle(bod, 'border-top-width'), 10);
		}
	}
	if (!local && win !== self) {
		var iframes = win.parent.document.getElementsByTagName('iframe'),
			i = iframes.length;
		while (i--) {
			var node = iframes[i],
				idoc = t.getIframeDocument(node);
			if (idoc === doc) {
				var pos = t.getLeftTop(node);
				left += pos.left - scroll.left;
				top += pos.top - scroll.top;
				if (t.ie || t.opera) {
					var padLeft = 0, padTop = 0;
					if (!t.ie || elFlow) {
						padLeft = parseInt(t.getStyle(node, 'padding-left'), 10);
						padTop = parseInt(t.getStyle(node, 'padding-top'), 10);
					}
					left += node.clientLeft + padLeft;
					top += node.clientTop + padTop;
				} else {
					left += parseInt(t.getStyle(node, 'border-left-width'), 10) +
					parseInt(t.getStyle(node, 'padding-left'), 10);
					top += parseInt(t.getStyle(node, 'border-top-width'), 10) +
					parseInt(t.getStyle(node, 'padding-top'), 10);
				}
				break;
			}
		}
	}
	return { left: left, top: top };
};