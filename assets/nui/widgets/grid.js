(function(nUI, $) {
    var gridTpl = '<div class="nui-grid">';
    gridTpl += '<div class="nui-gridheader"></div>';
    gridTpl += '</div>';
    var createGridDom = function(grid, me) {
        var gridElm = $(gridTpl);
        grid.wrap(gridElm);
        me.bodyElm = $('<div class="nui-gridbody" style="overflow-y:scroll"><table class="table table-striped table-hover"></table></div>');
        me.tableElm = me.bodyElm.find('table');
        me.gridElm = grid.parents('.nui-grid');
        me.gridElm.append(me.bodyElm);
        me.gridHeight = parseInt(grid.attr('data-height'), 10);        
        if (me.gridHeight > 0) {
            var dialog = grid.parents('.nui-dialog');            
            var w = me.gridElm.width() - nUI._config.scrollBarWidth;
            if (dialog.length > 0) {                
                w = dialog.width() - nUI._config.scrollBarWidth;                
            }                
            grid.width(w);
            me.tableElm.width(w);
            me.bodyElm.height(me.gridHeight);
        }
        me.cols = [];
        grid.find('tr:first th').each(function(i, n) {
            me.cols[i] = $(n).attr('width');
        });            
        return me.gridElm;
    };
    var nuiGrid = function(grid) {
        grid.data('gridObj', this);
        grid.data('formTarget', this);
        grid.data('pagerTarget', this);
        grid.data('reloadObj',this);
        var me = this;
        this.grid = grid.addClass('table');
        
        this.table = createGridDom(grid, me);
        this.data = {};
        this.initSorter();
        var gridId = grid.attr('id');
        var sform = $('form[data-widget=nuiSearchForm]').filter('[data-for=#'+gridId+']');
        if(sform.length==0){
            this.initData();
        }        
        this.grid.find('th input[type="checkbox"].grp').click(function() {
            var $this = $(this), checked = $this.prop('checked');
            var selected = me.tableElm.find('td input[type="checkbox"].grp');
            selected.prop('checked', checked);            
            me.grid.find('th input[type="checkbox"].grp').not($this).prop('checked', checked);
        });
    };
    nuiGrid.prototype.initData = function() {
        var me = this, limit = 10, pager = $('div[data-widget=nuiPager][data-for=#' + me.grid.attr('id') + ']');
        if (pager.length == 1 && pager.attr('data-limit')) {
            limit = parseInt(pager.attr('data-limit'), 10);
        }
        this.data.cp = 1;
        this.data.limit = limit;
        if (this.grid.parents('.nui-dialog').length > 0) {
            setTimeout(function() {
                me.reload(null, true);
            }, 200);
        } else {
            this.reload(null, true);
        }
    };
    nuiGrid.prototype.initSorter = function() {
        var defaultSort = this.grid.attr('data-sort'), me = this;
        this.grid.find('th[data-sort]').each(function(i, n) {
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
        var ths = this.grid.find('div.sorthd');
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

    nuiGrid.prototype.reload = function(cb, search) {
        var me = this, data = new Array();
        if (me.searchForm) {
            data = me.searchForm.serializeArray();
        }
        if (search) {
            data.push({
                name : '_ct',
                value : 1
            });
            this.data.cp = 1;
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
        nUI.ajax(me.grid.attr('data-source'), {
            dataType : 'html',
            element : me.grid,
            data : data,
            type : 'get',
            blockUI:true,
            callback : function(html) {
                me.tableElm.find('tbody').remove();
                me.table.find('th input[type="checkbox"].grp').prop('checked', false);
                html = $(html);
                html.find('tr:first td').each(function(i, n) {
                    if (me.cols[i]) {
                        $(n).attr('width', me.cols[i]);
                    }
                });
                me.tableElm.append(html);
                html.applyNUI();
                if ($.isFunction(cb)) {
                    cb();
                }
                if (me.pagerCtl && $.isFunction(me.pagerCtl.pageIt)) {
                    var total = -1;
                    if (search) {
                        total = parseInt(html.attr('data-total') || -1, 10);
                    }
                    me.pagerCtl.pageIt(total);
                }
            }
        });
    };

    nuiGrid.prototype.doPage = function(cp, limit, reload,ct) {
        this.data.cp = cp;
        if (limit) {
            this.data.limit = limit;
        }
        if (reload) {
            this.reload(null,ct);
        }
    };

    nuiGrid.prototype.doSort = function(field, order, cb) {
        this.data.sf = field;
        this.data.dir = order;
        this.reload(cb);
    };

    nuiGrid.prototype.form = function(form) {
        if (form) {
            this.searchForm = form;
            this.initData();
        } else {
            return this.searchForm;
        }
    };
    nuiGrid.prototype.pager = function(pager) {
        if (pager) {
            this.pagerCtl = pager;
        } else {
            return this.pagerCtl;
        }
    };

    $.fn.nuiGrid = function() {
        return $(this).each(function(i, elm) {
            var grid = $(elm);
            if (!grid.data('gridObj')) {
                new nuiGrid(grid);
            }
        });
    };
})(window.nUI, jQuery);
