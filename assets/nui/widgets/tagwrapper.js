(function(nUI, $) {
	var nuiTagWrapper = function(wrapper) {
		this.elem = wrapper.addClass('item-removable');
		this.wrapper = wrapper.find('.tags-wrapper');
		this.vElem = wrapper.find('input[type=hidden]');
		this.val = new nUI.Map();
		var values = this.vElem.val();
		if (values) {
			values = values.split(',');
			if (values.length > 0) {
				for (v in values) {
					this.val.put(values[v], values[v]);
				}
			}
		}
		var me = this;
		wrapper.on('click', 'a.close', function() {
			var id = $(this).attr('data-value');
			me.remove(id);
		});
		this.vElem.data('onInsert', function(id, elems) {
			var tags = [];
			$.each(elems, function(i, e) {
				var $e = $(e);
				tags.push({
					id : $e.val(),
					text : $e.attr('data-text')
				});
			});
			me.add(tags);
		});
	};
	nuiTagWrapper.prototype.remove = function(id) {
		this.val.remove(id);
		this.elem.find('a[data-value=' + id + ']').parent().remove();
		var values = this.val.values().join(',');
		this.vElem.val(values);
	};

	nuiTagWrapper.prototype.add = function(tags) {
		for (i in tags) {
			var tag = tags[i];
			if (this.val.containsKey(tag.id)) {
				continue;
			}
			this.val.put(tag.id, tag.id);
			this.wrapper.append($('<span class="badge removable">' + tag.text
					+ '<a class="close" data-value="' + tag.id
					+ '">&times;</a></span>'));
		}
		var values = this.val.values().join(',');
		this.vElem.val(values);
	};

	$.fn.nuiTagWrapper = function() {
		return $(this).each(function(i, elm) {
			var wrapper = $(elm);
			if (!wrapper.data('wrapperObj')) {
				wrapper.data('wrapperObj', new nuiTagWrapper(wrapper));
			}
		});
	};

})(window.nUI, jQuery);
