(function(nUI, $) {
	// data-source : load data from this url,default false
	// placeholder : placeholder
	// data-allowClear: allow clear the combox
	// data-mnl: minimumInputLength
	// data-parent: values accosiate to parent
	// multiple : multiple|''
	var nuiCombox = function(combox, options) {
		var me = this;
		this.combox = combox;
		combox.data('comboxObj', this);
		var opts = {
			allowClear : true
		};
		this.isTagMode = combox.attr('data-tagMode') == 'true' ? true : false;
		this.parent = combox.attr('data-parent') || false;
		this.url = combox.attr('data-source') || false;
		this.multiple = combox.attr('multiple') == 'multiple' ? true : false;
		opts.placeholder = combox.attr('placeholder') || '';
		opts.allowClear = combox.attr('data-allowClear') == 'false' ? false
				: opts.allowClear;
		opts.minimumInputLength = combox.attr('data-mnl') ? parseInt(combox
				.attr('data-mnl'), 10) : 0;
		if (this.isTagMode && this.multiple) {
			opts.separator = ',';
			opts.tokenSeparators = [ ',', ' ' ];
			opts.tokenizer = function(input, selection, selectCallback, opts) {
				if (input.length > 1) {
					var len = input.length, token = input.substring(len - 1,
							len);
					if (token == ',' || token == ' ') {
						var sl = selection.length;
						input = input.replace(/[, ]+$/g, '');
						for ( var i = 0; i < sl; i++) {
							if (input == selection[i].id) {
								me.comboxObj.select2('close');
								return;
							}
						}
						selectCallback({
							id : input,
							text : input
						});
					}
				}
			};
		}
		if (!combox.is('select')) {
			opts.multiple = this.multiple;
			opts.initSelection = function(element, callback) {
				var vars = $(element).val(), data = null, svar, values = [];
				if (me.multiple) {
					data = [ {
						id : '',
						text : ''
					} ];
				} else {
					data = {
						id : '',
						text : ''
					};
				}
				if (vars) {
					if (me.multiple) {
						vars = vars.split(',');
						data = [];
						for ( var i in vars) {
							svar = vars[i].split(':');
							if (svar.length > 1) {
								data.push({
									id : svar[0],
									text : svar[1]
								});
							} else {
								data.push({
									id : svar[0],
									text : svar[0]
								});
							}
							values.push(svar[0]);
						}
					} else {
						vars = vars.split(':');
						if (vars.length > 1) {
							data = {
								id : vars[0],
								text : vars[1]
							};
						} else {
							data = {
								id : vars[0],
								text : vars[0]
							};
						}
						values.push(vars[0]);
					}
					$(element).attr('value', values.join(','));
				}
				callback(data);
			};
		}

		if (this.url) {
			opts.ajax = {
				quietMillis : 100,
				cache : true,
				data : function(term, page) {
					var data = {
						q : term,
						_cp : page,
					};
					if (me.parent) {
						data.p = $('#' + me.parent).val();
					}
					return data;
				},
				dataType : 'json',
				url : this.url,
				results : function(data, page) {
					return data;
				}
			};
		}
		this.options = $.extend(opts, options || {});
		this.comboxObj = combox.select2(this.options);
		if (this.parent) {
			var pCombox = $('#' + this.parent);
			if (pCombox.length > 0) {
				pCombox.change(function() {
					me.setValue();
				});
			}
		}
	};
	nuiCombox.prototype.getComboxObj = function() {
		return this.comboxObj;
	};
	nuiCombox.prototype.getCombox = function() {
		return this.combox;
	};
	nuiCombox.prototype.setValue = function(value) {
		if (!value) {
			value = this.multiple ? null : '';
		}
		this.combox.select2('val', value, true);
	};
	$.fn.nuiCombox = function(options) {
		return $(this).each(function(i, elm) {
			var combox = $(elm);
			if (!combox.data('comboxObj')) {
				new nuiCombox(combox, options);
			}
		});
	};

})(window.nUI, jQuery);