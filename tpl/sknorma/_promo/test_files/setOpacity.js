Floatbox.prototype.setOpacity_module = function(el, opacity, duration, callback) {
	var t = this;
	duration = duration || 0;
	var startOp = +(el.style.opacity || 0),
		endOp = opacity/100;
	t.clearTimeout['fade_' + el.id];
	var fadeIn = (startOp <= endOp && endOp > 0);
	if (duration > 10) duration = 10;
	if (duration < 0) duration = 0;
	if (duration === 0) {
		startOp = endOp;
		var incr = 1;
	} else {
		var root = Math.pow(100, 0.1),
			power = duration + ((10 - duration)/9) * (Math.log(2)/Math.log(root) - 1),
			incr = 1/Math.pow(root, power);
	}
	if (fadeIn) {
		if (el.style.removeAttribute) el.style.filter = 'alpha(opacity=' + startOp*100 + ')';
		el.style.opacity = startOp + '';
		el.style.display = el.style.visibility = '';
	} else {
		incr = -incr;
	}
	t.stepFade(el, startOp + incr, endOp, incr, fadeIn, callback);
};
Floatbox.prototype.stepFade = function(el, thisOp, finishOp, incr, fadeIn, callback) {
	var t = this;
	if (!el) return;
	if ((fadeIn && thisOp >= finishOp) || (!fadeIn && thisOp <= finishOp)) thisOp = finishOp;
	if (el.style.removeAttribute) el.style.filter = 'alpha(opacity=' + thisOp*100 + ')';
	el.style.opacity = thisOp + '';
	if (thisOp === finishOp) {
		delete t.timeouts['fade_' + el.id];
		if (el.style.removeAttribute && finishOp >= 1) el.style.removeAttribute('filter');
		if (callback) callback();
	} else {
		t.setTimeout('fade_' + el.id, function() { t.stepFade(el, thisOp + incr, finishOp, incr, fadeIn, callback); }, 20);
	}
};