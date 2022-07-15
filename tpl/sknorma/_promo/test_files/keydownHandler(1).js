Floatbox.prototype.keydownHandler = function(e) {
	e = e || window.event;
	var t = fb.lastChild,
		keyCode = e.keyCode || e.which,
		ctrl = e.ctrlKey || e.metaKey,
		alt = e.altKey,
		shift = e.shiftKey,
		modKey = ctrl || alt || shift;
	switch (keyCode) {
	case 37: case 39:
		if (alt || shift) return;
		if (t.itemCount > 1) {
			t[keyCode === 37 ? 'fbPrev' : 'fbNext'].onclick(ctrl ? t.ctrlJump : 1);
			if (t.showHints === 'once') {
				t.fbPrev.title = t.fbNext.title = '';
				if (t.overlayNav) t.fbOverlayPrev.title = t.fbOverlayNext.title = '';
			}
		}
		return t.stopEvent(e);
	case 32:
		if (modKey) return;
		if (t.isSlideshow) {
			t.setPause(!t.isPaused);
			if (t.showHints === 'once') t.fbPlay.title = t.fbPause.title = '';
		}
		return t.stopEvent(e);
	case 9:
		if (modKey) return;
		if (t.fbResizer.onclick) {
			t.fbResizer.onclick();
			if (t.showHints === 'once') t.fbResizer.title = '';
		}
		return t.stopEvent(e);
	case 27:
		if (modKey) return;
		if (t.showHints === 'once') t.fbClose.title = '';
		t.end();
		return t.stopEvent(e);
	case 13:
		if (!modKey) return t.stopEvent(e);
	}
};
if (fb.enableKeyboardNav  && fb.fbBox && !fb.keydownHandlerSet) {
	fb.addEvent(fb.doc, 'keydown', fb.keydownHandler);
	fb.keydownHandlerSet = true;
}