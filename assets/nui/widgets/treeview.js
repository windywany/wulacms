(function(nUI, $) {
	var idx = 1;
	var tpl = '<div class="select2-container">\
				<a tabindex="-1" class="select2-choice" href="javascript:void(0)">\
				<span class="select2-chosen"></span>\
				<span role="presentation" class="select2-arrow">\
				 	<b role="presentation"></b>\
				</span>\
				</a>\
			   </div>';
	var treeContainer = '<div class="select2-drop select2-display-none">\
							<ul role="listbox" class="ztree"></ul>\
						 </div>';
	var treeMask  = '<div id="select2-drop-mask1" class="select2-drop-mask"></div>';
	var nuiTreeView = function(treeviewElm){
		var me = this;
		this.multi = treeviewElm.data('multi');
		this.treeView = $(tpl);
		this.treeView.css('width',treeviewElm.css('width'));
		this.treeviewElm = treeviewElm;
		this.oldText = treeviewElm.data('text');
		this.treeText = this.treeView.find('span.select2-chosen');
		this.placeholder = treeviewElm.attr('placeholder');
		if(this.oldText){
			this.treeText.html(this.oldText);
		}else{
			this.setPlaceholder(this.placeholder);
		}
		this.dataURL = treeviewElm.data('source');
		
		treeviewElm.before(this.treeView);
		this.treeSettings = {
				check: {
					enable: true,
					chkboxType : { "Y" : "", "N" : "" }
				},
				async: {
					enable: true,
					url:me.dataURL,
					autoParam:["id=pid"],
					dataFilter: me._filter()
				},
				data: {
					simpleData: {
						enable: true
					}
				},
				view: {
					dblClickExpand: false
				},
				callback: {
					beforeCheck: me._beforeCheck(),
					onCheck: me._onCheck()
				},
				treeOwner:me
			};
		
		this.treeView.find('a').click(function(){
			me.show();
		});
	};
	nuiTreeView.prototype.setCid = function(cid){
		if(/&cid=[^&]*$/gm.test(this.treeSettings.async.url)){
			this.treeSettings.async.url = this.treeSettings.async.url.replace(/&cid=[^&]*$/gm,'&cid='+cid);
		}else{
			this.treeSettings.async.url += '&cid='+cid;
		}
	};
	nuiTreeView.prototype.showMask = function(){
		this.mask = $('#select2-drop-mask1');
		var me = this;
		if(this.mask.length == 0){
			this.mask = $(treeMask);
			$('body').append(this.mask);
		}
		this.mask.click(function(){
			me.close();
		}).show();
	};
	nuiTreeView.prototype.show = function(){
		if(this.treeView.hasClass('select2-dropdown-open')){
			this.close();
		}else{
			var value = this.treeviewElm.val();
			
			this.showMask();
			this.treeView.addClass('select2-dropdown-open select2-container-active');
			this.treeC = $(treeContainer).appendTo($('body'));
			var of = this.treeView.offset();
			var w = this.treeView.width();
			var h = this.treeView.height();
			this.treeC.css({'min-height':'88px','max-height':'300px','overflow':'auto','left':of.left,'top':of.top+h,'width':w}).addClass('select2-drop-active');
			var treeWrap = this.treeC.find('ul.ztree');
			treeWrap.attr('id','ztree-'+(idx++));
			this.zTree = $.fn.zTree.init(treeWrap, this.treeSettings);
			this.treeC.show();
		}
	};
	nuiTreeView.prototype.close = function(){
		if(this.mask){
			this.mask.hide().unbind('click');
		}
		this.treeView.removeClass('select2-dropdown-open select2-container-active');
		this.treeC.hide();
		this.zTree.destroy();
		this.treeC.remove();
	};
	nuiTreeView.prototype.setPlaceholder = function(text){
		this.treeText.addClass('text-muted').html(text);
	};
	nuiTreeView.prototype._beforeCheck = function() {
		var me = this;
		return function (treeId, treeNode){
			if(!me.multi && !treeNode.checked){
				me.zTree.checkAllNodes(false);
			}
			return true;
		}
	};
	nuiTreeView.prototype._onCheck = function(){
		var me = this;
		return function(e, treeId, treeNode){
			var nodes = me.zTree.getCheckedNodes(true);
			var ids = [],names = [];
			$.each(nodes,function(i,n){
				ids.push(n.id);
				names.push(n.name);
			});
			me.treeviewElm.val(ids.join(',')+'');
			if(names.length>0){
				me.treeText.removeClass('text-muted').html(names.join(',')+'');
			}else{
				me.setPlaceholder(me.placeholder);
			}
			var cb = me.treeviewElm.data('onCheck');
			if(cb && $.isFunction(cb)){
				cb.call(me,nodes);
			}
		}
	};
	nuiTreeView.prototype._filter = function(){
		var me = this;
		return function(treeId, parentNode, childNodes){
			var value = me.treeviewElm.val(),values = [];
			if(value){
				values = value.split(',');
			}
			for (var i=0, l=childNodes.length; i<l; i++) {
				if($.inArray(childNodes[i].id + '',values) >= 0 ){
					childNodes[i].checked = true;
				}
			}
			return childNodes;
		}
	};
	
	$.fn.nuiTreeview = function() {
		return $(this).each(function(i, elm) {
			var treeview = $(elm);
			if (!treeview.data('treeObj')) {
				treeview.data('treeObj', new nuiTreeView(treeview));
			}
		});
	};
})(window.nUI, jQuery);