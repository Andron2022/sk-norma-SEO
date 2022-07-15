Floatbox.prototype.zoomIn = function(phase) {
	var t = this,
		item = t.itemToShow,
		zoomDiv = t.fbZoomDiv.style;
	if (!phase) {
		t.clearTimeout('slowLoad');
		t.fbZoomLoader.style.display = 'none';
		zoomDiv.display = t.fbZoomImg.style.display = '';
		if (item.popup) {
			item.popupLocked = false;
			item.anchor.onmouseout();
		}
		var borders = t.innerBorder + t.realBorder - t.zoomPopBorder,
			oncomplete = function () {
				t.fbZoomImg.src = item.href;
				t.setSize(
					{ id: 'fbZoomDiv', width: t.pos.fbMainDiv.width, height: t.pos.fbMainDiv.height,
						left: t.pos.fbBox.left + t.pos.fbMainDiv.left + borders,
						top: t.pos.fbBox.top + t.pos.fbMainDiv.top + borders - (t.cornerRadius - t.roundBorder) },
					function() { t.zoomIn(1); } );
			};
		return t.setOpacity(t.fbOverlay, t.overlayOpacity, t.overlayFadeDuration, oncomplete);
	}
	if (phase === 1) {
		var boxPos = { id: 'fbBox',
			left: t.pos.fbBox.left, top: t.pos.fbBox.top,
			width: t.pos.fbBox.width, height: t.pos.fbBox.height
		};
		var size = 2*(t.realBorder - t.zoomPopBorder + t.cornerRadius);
		t.pos.fbBox.left = t.pos.fbZoomDiv.left + t.cornerRadius;
		t.pos.fbBox.top = t.pos.fbZoomDiv.top + t.cornerRadius;
		t.pos.fbBox.width = t.pos.fbZoomDiv.width - size;
		t.pos.fbBox.height = t.pos.fbZoomDiv.height - size;
		t.fbBox.style.visibility = '';
		var oncomplete = function() {
			t.restore(function() { t.zoomIn(2); });
		};
		return t.setSize( boxPos, oncomplete);
	}
	var show = function() {
		zoomDiv.display = 'none';
		t.fbZoomImg.src = t.blank;
		zoomDiv.left = zoomDiv.width = zoomDiv.height = t.fbZoomImg.width = t.fbZoomImg.height = '0';
		zoomDiv.top = '-9999px';
		t.showContent();
	};
	t.timeouts.showContent = setTimeout(show, 10);
};
Floatbox.prototype.zoomOut = function(phase) {
	var t = this;
	if (!phase) {
		t.fbZoomImg.src = t.currentItem.href;
		t.setPosition(t.fbBox, 'absolute');
		var pad = t.realBorder + t.innerBorder - t.zoomPopBorder;
		return t.setSize(
			{ id: 'fbZoomDiv', width: t.pos.fbMainDiv.width, height: t.pos.fbMainDiv.height,
				left: t.pos.fbBox.left + t.realPadding + pad, top: t.pos.fbBox.top + t.upperSpace - (t.cornerRadius - t.roundBorder) + pad },
			function() { t.zoomOut(1); }
		);
	}
	if (phase === 1) {
		t.fbZoomDiv.style.display = t.fbZoomImg.style.display = '';
		t.fbCanvas.style.visibility = 'hidden';
		return t.collapse(function() { t.zoomOut(2); });
	}
	if (phase === 2) {
		if (t.shadowSize) t.fbShadows.style.display = 'none';
		var pad = 2*(t.realBorder - t.zoomPopBorder + t.cornerRadius);
		return t.setSize(
			{ id: 'fbBox', left: t.pos.fbZoomDiv.left + t.cornerRadius, top: t.pos.fbZoomDiv.top + t.cornerRadius,
			width: t.pos.fbZoomDiv.width - pad, height: t.pos.fbZoomDiv.height - pad },
			function() { t.zoomOut(3); }
		);
	}
	t.fbBox.style.visibility = 'hidden';
	var end = function() {
		t.fbZoomImg.src = t.pos.thumb.src;
		t.end();
	};
	t.setSize(
		{ id: 'fbZoomDiv', left: t.pos.thumb.left, top: t.pos.thumb.top,
			width: t.pos.thumb.width, height: t.pos.thumb.height
		}, end);
};