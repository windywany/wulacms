(function(nUI, $) {
	var idx = 1000;
	var dialogTpl = '<div class="panel nui-dialog  modal-content">';
	dialogTpl += '<div class="panel-heading nui-title"><h6 class="pull-left"><i></i> <span></span></h6>';
	dialogTpl += '<button class="close pull-right" aria-hidden="true">&times;</button>';
	dialogTpl += '<div class="clearfix"></div>';
	dialogTpl += '</div></div>';
	nUI.dialogsInstances = {};
	nUI.dialogCallbacks = {};
	var nuiDialog = function(id, title, opts) {
		if (!id) {
			id = idx++;
		}
		var me = this;
		this.insertElem = '';
		this.hasInsertElem = false;
		this.id = id;
		this.idStr = 'nuiDialog-' + id;
		this.isOpen = false;
		this.title = title;
		this.opts = $.extend({
			width : 'auto',
			height : 'auto',
			model : false,
			icon : 'fa fa-windows',
			theme : '',
			dispose : true,
			closable : true,
			content : false
		}, opts || {});
		this.dialogElm = $('#' + this.idStr);
		if (this.dialogElm.length == 0) {
			this.dialogElm = $(dialogTpl).css({
				width : this.opts.width,
				height : this.opts.height
			});
			if (me.opts.closable) {
				this.dialogElm.on('click', 'div.nui-title button.close', function() {
					me.close(1);
				});
			} else {
				this.dialogElm.find('.close').remove();
			}
			this.dialogElm.attr('id', this.idStr);
			this.dialogElm.appendTo($('body'));
			nUI.dialogsInstances[id] = this;
		}
		this.titleElm = this.dialogElm.find('div.nui-title h6 span');
		this.iconElm = this.dialogElm.find('div.nui-title h6 i');
		//if(this.opts.theme){
		//	this.dialogElm.addClass('panel-' + this.opts.theme);
		//}
		this.titleElm.text(title);
		this.iconElm.addClass(this.opts.icon);
	};
	nuiDialog.prototype.setInsertElem = function(elem) {
		this.hasInsertElem = false;
		if (elem) {
			this.insertElem = $(elem);
			this.hasInsertElem = this.insertElem.length > 0;
		}
		if (this.hasInsertElem) {
			this.insertElemId = elem;
		}
	};
	nuiDialog.prototype.getInsertElem = function() {
		return this.insertElem;
	};
	nuiDialog.prototype.open = function(url, option) {
		var me = this;
		option = option || {};
		option.callback = function(data) {
			me.dialogElm.append($(data));
			me.dialogElm.applyNUI();
			me.setGridCallback();
			me.show();
			if ($.isFunction(me.opts.onLoaded)) {
				me.opts.onLoaded.call(me, option);
			}
		};
		me.opts.opacity = .45;
		nUI.ajax(url, option);
	};
	nuiDialog.prototype.openLocal = function(content, buttons) {
		var me = this;
		if (!content && $.isFunction(me.opts.content)) {
			content = me.opts.content.call(me, me);
		}
		this.body = $('<div class="panel-body txtc">' + content + '</div>').applyNUI();
		this.dialogElm.find('.panel-body').remove();
		this.dialogElm.append(this.body);
		if ($.isArray(buttons) && buttons.length > 0) {
			this.footer = $('<div class="panel-footer txtc"></div>');
			this.dialogElm.append(this.footer);
			for ( var i in buttons) {
				var button = buttons[i], cls = button.cls || 'btn-default';
				var btn = '<button class="btn btn-sm ' + cls + '">';
				if (button.icon) {
					btn += '<i class="' + button.icon + '"> ';
				}
				btn += (button.text || '') + '</button>';
				btn = $(btn);
				btn.data('btnData', button).on('click', function() {
					me.hide();
					var btnData = $(this).data('btnData'), rtn = true;
					if ($.isFunction(btnData.click)) {
						rtn = btnData.click.call(me, me.dialogElm);
					}
					if (rtn !== false) {
						me.close();
					} else {
						me.show();
					}
				});
				btn.appendTo(this.footer);
			}
		}
		if ($.isFunction(me.opts.onCreated)) {
			me.opts.onCreated.call(me, me);
		}
		this.setGridCallback();
		this.show();
	};
	nuiDialog.prototype.setGridCallback = function() {
		var me = this, body = me.dialogElm;
		if (body.find('[data-widget=nuiGrid]').length > 0 && body.find('a.nui-insert-btn').length > 0) {
			body.find('a.nui-insert-btn').on('click', function() {
				var ids = [], oids = null, ss = $(this).hasClass('single-select'), cb = false, close = $(this).hasClass('close-after');
				if (me.hasInsertElem) {
					var oids = me.insertElem.val();
					if (oids) {
						oids = oids.split(',');
					} else {
						oids = [];
					}
					cb = me.insertElem.data('onInsert');
				} else {
					oids = [];
				}
				if (ss) {
					oids = [];
				}
				var selectedElem = body.find('td input.grp:checked');
				if (cb && $.isFunction(cb)) {
					cb.call(me, me.insertElemId, selectedElem);
				} else if (me.hasInsertElem) {
					selectedElem.each(function(i, n) {
						var v = $(n).val();
						ids[i] = v;
						if (jQuery.inArray(v, oids) == -1) {
							oids.push(v);
						}
					});
					if (ss) {
						me.insertElem.val(oids.length > 0 ? oids[0] : '');
					} else {
						me.insertElem.val(oids.join(','));
					}
				}
				if (close) {
					me.dialogElm.find('div.nui-title button.close').click();
					if (me.hasInsertElem) {
						me.insertElem.change();
					}
				}
			});
		}
	};

	nuiDialog.prototype.show = function() {
		var zIndex = 9009;
		if (this.opts.model) {
			zIndex = nUI.Overlay.show(null, this.opts.opacity);
		}
		if (this.opts.width == 'auto') {
			this.opts.width = this.dialogElm.width();
		}
		if (this.opts.height == 'auto') {
			this.opts.height = this.dialogElm.height();
		}
		this.dialogElm.css('z-index', zIndex).centerMe().show();		
	};
	nuiDialog.prototype.center = function(){
		this.dialogElm.centerMe();
	};
	nuiDialog.prototype.hide = function() {
		if (this.opts.model) {
			nUI.Overlay.hide();
		}		
		this.dialogElm.hide();
	};
	nuiDialog.prototype.setTitle = function(title) {
		this.titleElm.text(title);
	};
	nuiDialog.prototype.setContent = function(content) {
		this.dialogElm.children().not('.nui-title').remove();
		var ce = $(content);
		ce.appendTo(this.dialogElm).applyNUI();
		return ce;
	};
	nuiDialog.prototype.setIcon = function(icon) {
		this.iconElm.removeClass('').addClass(icon);
	};
	nuiDialog.prototype.close = function(arg) {
		this.hide();
		if ($.isFunction(this.opts.onClose)) {
			arg = arg ? 1 : 0;
			this.opts.onClose.call(this, arg);
		}
		this.dialogElm.remove();
		this.dialogElm = null;
		nUI.dialogsInstances[this.id] = null;
		delete nUI.dialogsInstances[this.id];
	};

	nUI.alert = nuiDialog.alert = function(content, cb, theme, buttons, title) {
		if (theme == 'info' && !title) {
			title = '请您确认';
		}
		title = title || '提示';
		var dialog = new nuiDialog(false, title, {
			model : true,
			theme : theme || 'primary',
			width : 'auto',
			height : 'auto',
		});
		var btns = buttons || [ {
			'text' : '确定',
			'icon' : 'glyphicon glyphicon-ok',
			'cls' : 'btn-success',
			click : cb
		} ];
		dialog.openLocal(content, btns);
		return dialog;
	};

	nUI.successTip = nuiDialog.successTip = function(content, cb) {
		return nUI.alert(content, cb, 'success', false, '操作成功!');
	};
	nUI.errorTip = nuiDialog.errorTip = function(content, cb) {
		return nUI.alert(content, cb, 'danger', false, '出错啦!');
	};
	nUI.confirm = nuiDialog.confirm = function(content, onOk, onCancel) {
		return nUI.alert(content, null, 'info', [ {
			'text' : '是',
			'icon' : 'glyphicon glyphicon-ok',
			'cls' : 'btn-success',
			click : onOk
		}, {
			'text' : '否',
			'icon' : 'glyphicon glyphicon-remove',
			click : onCancel
		} ]);
	};
	nUI.Dialog = nuiDialog;
	nUI.closeDialog = function(id, arg) {
		if (nUI.dialogsInstances[id]) {
			nUI.dialogsInstances[id].close(arg == undefined ? 1 : arg);
		}
	};
	nUI.hideDialog = function(id) {
		if (nUI.dialogsInstances[id]) {
			nUI.dialogsInstances[id].hide();
		}
	};
	$(window).resize(function(){
		for(var idx in nUI.dialogsInstances){
			var dlg = nUI.dialogsInstances[idx];
			if(dlg){
				 dlg.center();
			}
		}
	});
})(window.nUI, jQuery);
