(function(nUI, $) {
    // data-source: url for loadding table rows
    // data-sort: field,dir default
    // data-tree: true or false
    // data-hh: hide head
    // data-expend: auto expend
    // data-tfoot: auto-generate tfoot
    // data-auto: auto-load data
    // th[data-sort] : sort field	
	var nuiTableDataCache = {};
    var nuiTable = function(table) {
        var me = this;
        this.table = table;
        this.data = {};
        this.keep = table.attr('data-keep') || false;        
        this.isTree = table.attr('data-tree') == 'true' ? true : false;
        this.hideHead = table.attr('data-hh') == 'true' ? true : false;
        this.autoExpend = table.attr('data-expend') == 'true' ? true : false;
        this.autoLoad = table.attr('data-auto') == 'true' ? true : false;
        this.blockUI  = table.attr('data-blockui') === 'false'? false : true;
        this.currentTreeNode = null;
        if (this.isTree) {
            this.folderOpenIcon = table.attr('data-folderIcon1') || 'glyphicon glyphicon-minus';
            this.folderCloseIcon = table.attr('data-folderIcon2') || 'glyphicon glyphicon-plus';
            this.leafIcon = table.attr('data-leafIcon') || '';
        }
        table.addClass('table table-hover');
        if (!this.isTree) {
            table.addClass('table-striped');
        }
        table.data('tableObj', this);
        table.data('formTarget', this);
        table.data('pagerTarget', this);
        table.data('reloadObj',this);
        this.initSorter();
        var gridId = table.attr('id');
        var sform = $('form[data-widget=nuiSearchForm]').filter('[data-for=#'+gridId+']');
        if(sform.length==0){
            this.initData();
        }
        this.table.find('th input[type="checkbox"].grp').click(function() {
            var $this = $(this), checked = $this.prop('checked');
            var selected = me.table.find('td input[type="checkbox"].grp');
            selected.prop('checked', checked);
            if (checked) {
                me.table.find('tbody tr').addClass('nui-selected');
            } else {
                me.table.find('tbody tr').removeClass('nui-selected');
            }
            me.table.find('th input[type="checkbox"].grp').not($this).prop('checked', checked);
        });
        if (this.isTree) {
            if (this.table.find('tbody tr').length > 0) {
                this.initTree();                
            }
            this.table.on('click', 'tbody tr > td:first-child span.tt-folder', function() {
                var h = $(this), node = me.currentTreeNode = h.parent().parent();
                if (node.attr('data-parent') == 'true') {
                    if (h.hasClass(me.folderOpenIcon)) {
                        h.removeClass(me.folderOpenIcon).addClass(me.folderCloseIcon);
                        collapseNode(node, me);
                    } else {
                        h.removeClass(me.folderCloseIcon).addClass(me.folderOpenIcon);
                        if (node.data('loaded')) {
                            expendNode(node, me);
                        } else {
                            me.reload();
                        }
                    }
                }
            });
        }
        if (this.hideHead) {
            this.table.find('thead').hide();
            this.table.find('tfoot').hide();
        }
    };
    nuiTable.prototype.initData = function() {
        if (this.autoLoad) {        	
            var me=this,limit = 10, pager = $('div[data-widget=nuiPager][data-for=#' + me.table.attr('id') + ']');
            if(this.keep){
               this.data = 	nuiTableDataCache[this.keep];
            }
            if(!this.data){
            	if (pager.length == 1 && pager.attr('data-limit')) {
                    limit = parseInt(pager.attr('data-limit'), 10);
                }
            	this.data = {};
            	this.data.cp = 1;
            	this.data.limit = limit;
            }else{
            	if (pager.length == 1) {
            		pager.attr('data-limit',this.data.limit);
                    pager.find('select').val(this.data.limit);
                    if(this.pagerCtl){
                    	this.pagerCtl.limit = this.data.limit;
                    	this.pagerCtl.current  = this.data.cp;
                    }
                }
            	if(this.data.sf){
            		me.table.find("div.sorthd i").removeClass('asc desc');
            		var abc = me.table.find("div[data-field='" + this.data.sf + "']");
            		if(this.data.dir == 'd'){
            			abc.attr('data-dir','a');
            			abc.find('i').addClass('desc');
            		}else{
            			abc.attr('data-dir','d');
            			abc.find('i').addClass('asc');
            		}
            	}
            }
            this.inited = false;
            if (me.table.parents('.nui-dialog').length > 0) {
                setTimeout(function() {
                    me.reload();
                    me.inited = true;
                }, 200);
            } else {
                this.reload();
                this.inited = true;
            }
        }
    };
    nuiTable.prototype.initSorter = function() {
        var defaultSort = this.table.attr('data-sort'), me = this;
        this.table.find('th[data-sort]').each(function(i, n) {
            var th = $(n), sort = th.attr('data-sort');
            if (sort) {
                var sorts = sort.split(',');
                var field = sorts.shift(), dir = 'd', cls = 'sort ';
                if (sorts.length > 0) {
                    dir = sorts.shift();
                    if (dir != 'd' && dir != 'a') {
                        dir = 'd';
                    }
                }
                if (defaultSort == sort) {
                    me.data.sf = field;
                    me.data.dir = dir;
                    if (dir == 'd') {
                        cls += 'desc';
                        dir = 'a';
                    } else {
                        cls += 'asc';
                        dir = 'd';
                    }
                }
                var html = '<div class="sorthd" data-field="' + field + '"';
                html += ' data-dir="' + dir + '">' + th.html();
                html += '<i class="' + cls + '"></i></div>';
                th.empty().append($(html)).removeAttr('data-sort');
            }
        });
        if ('true' == this.table.attr('data-tfoot')) {
            var tfoot = $('<tfoot></tfoot>');
            this.table.find('thead tr').clone().appendTo(tfoot);
            this.table.append(tfoot);
        }
        var ths = this.table.find('div.sorthd');
        ths.click(function() {
            ths.find('i').removeClass('asc desc');
            var th = $(this), f = th.attr('data-field'), d = th.attr('data-dir'), th2 = $("div[data-field='" + f + "']");
            me.doSort(f, d, function() {
                if (d == 'd') {
                    th2.attr('data-dir', 'a');
                    th2.find('i').removeClass('asc').addClass('desc');
                } else {
                    th2.attr('data-dir', 'd');
                    th2.find('i').removeClass('desc').addClass('asc');
                }
            });
        });
    };

    nuiTable.prototype.initTree = function(html) {
        var me = this;        
        if (!html) {
            html = this.table.find('tbody tr');
        }
        var level = 0,ref = '0';
        if (me.currentTreeNode != null) {
            level = me.currentTreeNode.data('ttLevel');
            ref = me.currentTreeNode.attr('rel');
        }        
        var cls = 'tt-folder', icon = me.folderCloseIcon;
        $.each(html, function(i, n) {
            var $this = $(n), id = $this.attr('rel');            
            if($this.data('handleAdded')){
                return;
            }
            var children = html.is('[parent="' + id + '"]');
            if ($this.attr('data-parent') == 'true' || children) {
                cls = 'tt-folder';
                if(!$this.attr('data-parent')){
                    $this.attr('data-parent','true');
                }
                icon = me.autoExpend && (children || !me.autoLoad) ? me.folderOpenIcon : me.folderCloseIcon;
            } else {
                cls = 'tt-leaf';
                icon = me.leafIcon;
            }
            if (!$this.attr('parent')) {
                $this.attr('parent', ref);
            }
            var parent = $this.attr('parent');
            if(html.is('[rel="' + parent + '"]')){
                $this.css('display', 'none');
            }else{
                $this.css('display', 'table-row');
            }
            $this.find('td:first').prepend($('<span class="' + cls + ' ' + icon + '"></span>'));
            $this.find('td:first').prepend($('<span class="tt-line"></span>'));            
            $this.data('handleAdded',true);
            $this.data('ttLevel', level);
            $this.data('loaded',children);
        }); 
        if(me.autoExpend){
            me.table.find("tbody tr[parent='0']").each(function(i,n){
                expendNode($(n),me);
            });
        }
    };

    nuiTable.prototype.doPage = function(cp, limit, reload,ct) {
        this.data.cp = cp;
        this.currentTreeNode = null;
        if (limit) {
            this.data.limit = limit;
        }
        if (reload) {
            this.reload(null,ct);
        }
    };

    nuiTable.prototype.doSort = function(field, order, cb) {
        this.currentTreeNode = null;
        this.data.sf = field;
        this.data.dir = order;
        this.reload(cb);
    };

    nuiTable.prototype.reload = function(cb, search) {
        if (!this.table.attr('data-source')) {
            return;
        }        
        var me = this, data = new Array(),pageit = false;        
        if (me.searchForm) {
            data = me.searchForm.serializeArray();
        }        
        if(search || !this.inited){
        	data.push({
                name : '_ct',
                value : 1
            });
        	pageit = true;
        }
        if (search) {
            this.data.cp = 1;
            this.currentTreeNode = null;
            if (this.pagerCtl) {
                this.pagerCtl.current = 1;
            }
        }
        
        data.push({
            name : '_cp',
            value : this.data.cp
        });
        
        data.push({
            name : '_lt',
            value : this.data.limit
        });
        
        if (this.data.sf) {
            data.push({
                name : '_sf',
                value : this.data.sf
            });
            if (this.data.dir) {
                data.push({
                    name : '_od',
                    value : this.data.dir
                });
            } else {
                data.push({
                    name : '_od',
                    value : 'd'
                });
            }
        }
        
        if (me.currentTreeNode != null) {
            data.push({
                name : '_tid',
                value : me.currentTreeNode.attr('rel')
            });
        }
        if(this.keep){
        	nuiTableDataCache[this.keep] = this.data;
        }
        nUI.ajax(me.table.attr('data-source'), {
            dataType : 'html',
            element : me.table,
            data : data,
            type : 'get',
            blockUI : me.blockUI,
            callback : function(html) {                
                me.table.find('th input[type="checkbox"].grp').prop('checked', false);
                html = $(html);
                var disableTree = html.attr('data-disable-tree') === 'true';
                if (me.isTree && !disableTree) {
                    if (me.currentTreeNode) {
                        html = html.find('tr');
                        me.initTree(html);
                        me.currentTreeNode.after(html);
                        me.currentTreeNode.data('loaded', true);
                        expendNode(me.currentTreeNode,me);
                    } else {
                        me.table.find('tbody').remove();
                        me.table.find('thead').after(html);
                        me.initTree();
                    }
                } else {
                    me.table.find('tbody').remove();
                    me.table.find('thead').after(html);
                }
                html.applyNUI();
                if ($.isFunction(cb)) {
                    cb();
                }
                if (me.pagerCtl && $.isFunction(me.pagerCtl.pageIt)) {
                    var total = -1;
                    if (pageit) {
                        total = parseInt(html.attr('data-total') || -1, 10);
                    }
                    me.pagerCtl.pageIt(total);
                }
            }
        });
    };
    nuiTable.prototype.form = function(form) {
        if (form) {
            this.searchForm = form;             
            this.initData();
        } else {
            return this.searchForm;
        }
    };
    nuiTable.prototype.pager = function(pager) {
        if (pager) {
            this.pagerCtl = pager;
        } else {
            return this.pagerCtl;
        }
    };
    $.fn.nuiTable = function() {
        return $(this).each(function(i, elm) {
            var table = $(elm);
            if (!table.data('tableObj')) {
                new nuiTable(table);
            }
        });
    };

    var expendNode = function(node, table) {
        var treeid = node.attr('rel'),tree = table.table,subLevel = node.data('ttLevel')+1;
        if(!node.data('childrenMoved')){
            node.after(tree.find('[parent="' + treeid + '"]'));
            node.data('childrenMoved',true);
        }
        var ml = (subLevel * 16)+'px';
        tree.find('[parent="' + treeid + '"]').each(function(i, n) {            
            var $this = $(n), h = $this.find('td:first span.tt-folder');            
            $this.data('ttLevel',subLevel);            
            $this.find('td:first span.tt-line').css({'margin-left':ml});
            $this.css({'display':'table-row'});
            if (h.hasClass(table.folderOpenIcon)) {
                expendNode($this, table);
            }
        });
    };
    var collapseNode = function(node, table) {
        var treeid = node.attr('rel');
        var tree = table.table;
        tree.find('[parent="' + treeid + '"]').each(function(i, n) {
            var $this = $(n);
            $this.css('display', 'none');
            collapseNode($this, table);
        });
    };    
})(window.nUI, jQuery);
