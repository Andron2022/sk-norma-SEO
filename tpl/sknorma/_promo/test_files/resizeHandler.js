Floatbox.prototype.resizeHandler = function(e) {
	var t = fb;
	if (!(t && t.fbBox && t.currentItem)) return;
	var displaySize = t.getDisplaySize(),
		scroll = t.getScroll();
	function move(box) {
		if (!box.currentItem || box.itemToShow !== box.currentItem) return;
		var	blankLeft = box.fbBox.offsetLeft - box.cornerRadius - scroll.left,
			blankRight = t.displaySize.width - box.fbBox.offsetWidth - 2*box.cornerRadius - blankLeft,
			blankTop = box.fbBox.offsetTop - box.cornerRadius - scroll.top,
			blankBottom = t.displaySize.height - box.fbBox.offsetHeight - 2*box.cornerRadius - blankTop;
		if (box.leftRatio === false) {
			var	boxWidth = box.fbBox.offsetWidth + 2*box.cornerRadius,
				boxHeight = box.fbBox.offsetHeight + 2*box.cornerRadius;
			box.leftRatio = box.topRatio = 0.5;
			if (blankLeft >= 0 && blankRight >= 0 &&
			boxWidth <= displaySize.width && boxWidth <= t.displaySize.width &&
			!(blankLeft === box.autoFitSpace && blankRight === (box.shadowSize || box.autoFitSpace))) {
				box.leftRatio = blankLeft/(blankLeft + blankRight);
			}
			if (blankTop >= 0 && blankBottom >= 0 &&
			boxHeight <= displaySize.height && boxHeight <= t.displaySize.height &&
			!(blankTop === box.autoFitSpace && blankBottom === (box.shadowSize || box.autoFitSpace))) {
				box.topRatio = blankTop/(blankTop + blankBottom);
			}
		}
		var dx = displaySize.width - t.displaySize.width,
			dy = displaySize.height - t.displaySize.height;
		if (blankLeft >= 0 && blankRight < 0 && dx > 0) dx = 0;
		if (blankTop >= 0 && blankBottom < 0 && dy > 0) dy = 0;
		box.fbBox.style.left = (box.pos.fbBox.left = Math.floor(box.fbBox.offsetLeft + dx*box.leftRatio)) + 'px';
		box.fbBox.style.top = (box.pos.fbBox.top = Math.floor(box.fbBox.offsetTop + dy*box.topRatio)) + 'px';
	}
	move(fb);
	for (var i = 0; i < t.children.length; i++) move(t.children[i]);
	t.displaySize.width = displaySize.width;
	t.displaySize.height = displaySize.height;
};
if (fb.centerOnResize  && fb.fbBox && !fb.resizeHandlerSet) {
	fb.addEvent(window, 'resize', fb.resizeHandler);
	fb.resizeHandlerSet = true;
}