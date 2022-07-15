Floatbox.prototype.setSize_module = function(order) {
	var t = this,
		oncomplete = false,
		arr = [[], []],
		defer = {},
		node,
		i = arguments.length;
	if (order === 'wh') {
		defer.top = 1;
		defer.height = 1;
	} else if (order === 'hw') {
		defer.left = 1;
		defer.width = 1;
	}
	while (i--) {
		if (typeof arguments[i] === 'object' && arguments[i].id) {
			var obj = arguments[i],
				node = t[obj.id],
				tag = node.tagName;
			if (!t.pos[obj.id]) t.pos[obj.id] = {};
			for (var prop in obj) {
				if (obj.hasOwnProperty(prop) && prop !== 'id') {
					var target = node,
						val = obj[prop],
						px = 0;
					if ((tag !== 'img' && tag !== 'iframe') || (prop !== 'width' && prop !== 'height')) {
						target = node.style;
						px = 'px';
					}
					var idx = defer[prop] || 0,
						start = t.pos[obj.id][prop];
					if (typeof start !== 'number' || node.style.display || node.style.visibility) {
						start = val;
					}
					arr[idx].push({ target: target, prop: prop, start: start, finish: val, px: px });
					if (/width|height/i.test(prop)) {
						if (obj.id === 'fbMainDiv') {
							arr[idx].push({ target: t.fbContent, prop: prop, start: start, finish: val, px: 0 });
						} else if (obj.id === 'fbZoomDiv') {
							arr[idx].push({ target: t.fbZoomImg, prop: prop, start: start, finish: val, px: 0 });
						}
					}
					t.pos[obj.id][prop] = val;
				}
			}
		} else if (typeof arguments[i] === 'function') {
			oncomplete = arguments[i];
		}
	}
	t.resizeGroup(arr[0], function() { t.resizeGroup(arr[1], oncomplete); });
};
Floatbox.prototype.resizeGroup = function(arr, callback) {
	var t = this,
		i = arr.length;
	if (!i) return callback ? callback() : null;
	t.clearTimeout('resize');
	var diff = 0;
	while (i--) {
		diff = Math.max(diff, Math.abs(arr[i].finish - arr[i].start));
	}
	var duration = t.resizeDuration * (t.liveResize ? 0.65 : 1);
	var rate = diff && duration ? Math.pow(Math.max(1, 2.2 - duration/10), (Math.log(diff))) / diff : 1;
	i = arr.length;
	while (i--) arr[i].diff = arr[i].finish - arr[i].start;
	t.stepResize(0, rate, arr, callback);
};
Floatbox.prototype.stepResize = function(increment, rate, arr, callback) {
	var t = this;
	if (increment > 1) increment = 1;
	var i = arr.length;
	while (i--) {
		var target = arr[i].target,
			prop = arr[i].prop,
			val = Math.round(arr[i].start + arr[i].diff * increment);
		if (!target) {
			increment = 1;
			break;
		}
		target[prop] = val + arr[i].px;
	}
	if (increment >= 1) {
		delete t.timeouts.resize;
		if (callback) callback();
	} else {
		t.timeouts.resize = setTimeout(function() { t.stepResize(increment + rate, rate, arr, callback); }, 20);
	}
};