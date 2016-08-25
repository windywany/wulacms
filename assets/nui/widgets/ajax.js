(function(nUI, $) {
    var nuiAjax = function(type, elm) {
        this.type = type;
        this.element = elm;        
        var me = this;
        var ajaxCb = function(event) {
            event.preventDefault();
            if(elm.data('ajax_sending')){
            	return;
            }
            elm.data('ajax_sending',true);
            if (elm.attr('role') == 'menuitem') {
                elm.parents('li.dropdown').removeClass('open');
            }
            if(elm.data('validateObj')){
                if(!elm.data('validateObj').validate()){
                	elm.data('ajax_sending',false);
                    return;
                }
            }
            var url = elm.attr('href') || elm.attr('data-url') || elm.attr('action');
            if(!url) {
            	elm.data('ajax_sending',false);
            	return;
            }
            var args = url.parseArgs();
            if (args.length > 0) {
                var arg = null, value = '';
                for ( var i in args) {
                    arg = args[i];
                    if (arg.startsWith('#')) {
                        value = $(arg).val();
                    } else if (arg.startsWith('.')) {
                        var values = [];
                        $(arg).each(function(i, n) {
                            values.push($(n).val());
                        });
                        value = values.join(',');
                        if (values.length > 0) {
                            param[arg] = value;
                        }
                    } else {
                        if ($('[name=' + arg + '].nui-selected').length == 1) {
                            value = $('[name=' + arg + '].nui-selected').attr('rel');
                        } else if($('input[type=radio][name=' + arg + ']:checked').length == 1){
                            value = $('input[type=radio][name=' + arg + ']:checked').val();
                        }else if($('select[name=' + arg + ']').length == 1){
                            value = $('select[name=' + arg + ']').val();
                        }else{
                            var warn = elm.attr('data-warn');
                            if (!warn) {
                                warn = 'Please select one {0} record!'.format(arg);
                            }
                            nUI.alert(warn,null,'warning');
                            elm.data('ajax_sending',false);
                            return;
                        }
                    }
                    url = url.replace('$' + arg + '$', value);
                }
            }

            var selected = elm.attr('data-grp');
            var selectedVars = [];
            if (selected) {
                var ids = [], name = elm.attr('data-arg');
                $(selected).each(function(i, n) {
                    ids.push($(n).val());
                });
                if (ids.length == 0) {
                    var warn = elm.attr('data-warn');
                    if (!warn) {
                        warn = 'please select some records!';
                    }
                    nUI.alert(warn,null,'warning');
                    elm.data('ajax_sending',false);
                    return;
                }
                if (!name) {
                    name = 'selected';
                }
                ids = ids.join(',');
                selectedVars[0] = {
                    name : name,
                    value : ids
                };
            }
            var func    = elm.attr('data-beforeAjax');
            if(func &&$.isFunction(nUI.ajaxCallbacks[func])){
            	var crst = nUI.ajaxCallbacks[func].call(elm);
            	if(crst === false){
            		elm.data('ajax_sending',false);
            		return;
            	}
            }
            var confirmTxt = elm.attr('data-confirm');
            if (confirmTxt) {
                nUI.confirm(confirmTxt, function() {
                    me.ajaxCall(elm, selectedVars,url);
                },function(){
                	elm.data('ajax_sending',false);
                });
            } else {
                me.ajaxCall(elm, selectedVars,url);
            }
        };
        if (elm.is('form')) {
            elm.on('submit', ajaxCb);
        } else if(elm.is('select')){
            elm.on('change',ajaxCb);
        } else {
            elm.on('click', ajaxCb);
        }
    };

    nuiAjax.prototype.ajaxCall = function(elm, data,url) {
        if ($.isFunction(nuiAjax.ajaxMethods[this.type])) {            
            if (url) {
                var timeout = elm.attr('data-timeout');
                var opacity = elm.attr('data-opacity')?parseInt(elm.attr('data-opacity'),10)/100:0.35;
                var opts = {
                    data : data,
                    element : elm,                    
                    blockUI: elm.attr('data-blockUI') === 'false' ? false : true,
                    opacity: opacity
                };
                if(timeout){
                    opts.timeout = parseInt(timeout,10);
                }                
                elm.data('ajax_sending',true);
                nuiAjax.ajaxMethods[this.type].call(elm, url, opts);
            } else {
            	elm.data('ajax_sending',false);                
            }
        } else {
        	elm.data('ajax_sending',false);            
        }
    };

    var successCb = function(data, options) {
        if ($.isFunction(options.callback)) {
            options.callback(data);
        } else if (typeof data == 'string') {
            options.element.html(data).applyNUI();
        } else if (data.status == 200) {
            ajaxDone(nUI.successTip, data, options.element);
        } else if (data.status == 300) {
            ajaxDone(nUI.errorTip, data, options.element);
        } else if (data.status == 301) {
            window.location.href = data.loginURL;            
        }
    };
    var ajaxDoned = function(data, element){
        try{
            switch (data.cb) {
                case 'close':   
                	if($.isFunction(nUI.closeDialog) && data.args){
                		nUI.closeDialog(data.args);
                	}
                    break;
                case 'refresh':
                    window.location.reload();
                    break;
                case 'click':
                	var a = $(data.args);
                	if(a.length >0 ){
                		if(/^#.+/.test(a.attr('href'))){
                			window.location.hash = a.attr('href');
                		}else{
                			a.click();
                		}
                	}
                    break;
                case 'redirect':
                	if(data.args.startsWith('#')){
                		window.location.hash = data.args;
                	}else{
                		window.location.href = data.args;
                	}
                    break;
                case 'reload':
                    var obj = $(data.args).data('reloadObj');
                    if(obj){
                        obj.reload(null,true);
                    }
                    break;
                case 'validate':
                	var fname = data.args;
                	var errs  = {};
                	if(typeof(data.args) != 'string' ){
                		fname = data.args.form;
                		errs  = data.args.errors;
                	}
                    var obj = $('form[name="'+fname+'"]').data('validateObj');
                    if(obj){
                        obj.validate(errs);
                    }
                    break;                
                case 'update':
                    if(data.args.element){
                        $(data.args.element).empty().html(data.args.content).applyNUI();
                    }
                    break;
                case 'dialog':
                    if (nUI.Dialog) {
                        var id = 'ajax-dialog';
                        var title = data.args.title || '提示';
                        var width = data.args.width || 400, height = data.args.height || 'auto';
                        var model = data.args.model == true?true:false;
                        var icon = data.args.icon || '', theme = data.args.theme || '', opts = {};
                        if (width) {
                            opts.width = width;
                        }
                        if (height) {
                            opts.height = height;
                        }
                        opts.model = model;
                        if (icon) {
                            opts.icon = icon;
                        }
                        if (theme) {
                            opts.theme = theme;
                        }
                        var dialog = new nUI.Dialog(id, title, opts);
                        if(data.args.insertTo){
                            dialog.setInsertElem(data.args.insertTo);
                        }                        
                        dialog.openLocal(data.args.content);
                    }else{
                        $('#content').empty().html(data.content).applyNUI();
                    }                    
                case 'callback':
                    var func = data.args,args={};
                    if(typeof(func) == 'object'){
                        func = data.args.func;
                        args = data.args;
                    }
                    if($.isFunction(nUI.ajaxCallbacks[func])){
                        nUI.ajaxCallbacks[func].call(element,args);
                    }else if(data.cb =='callback'){
                        nUI.errorTip('未知道回调函数:'+func);
                    }
                    break;
                default:
                    break;
            }
        }catch(e){
            nUI.errorTip('出错啦:'+e);                
        }    
    };
    var ajaxDone = function(alertMsg, data, element) {
        if (data.message) {
            alertMsg(data.message,function(){
                if(data.cb){
                    ajaxDoned(data,element);
                }                
            });
        }else if(data.cb){
            ajaxDoned(data,element);
        }
    };

    nUI.ajax = nuiAjax.ajax = function(url, options) {        
        var opts = $.extend({element:$('body')}, options);
        if (opts.element.is('form')) {
            opts.data = opts.element.serializeArray();
        }
        options.element = opts.element;
        opts.type = opts.element.attr('method') || options.type || 'GET';
        opts.success = function(data) {
        	opts.element.data('ajax_sending',false);
            successCb(data, options);
        };
        $.ajax(url, opts);
    };

    nuiAjax.ajaxMethods = {
        'ajax' : function(url, option) {
            option.dataType = 'json';            
            nuiAjax.ajax(url, option);
        },
        'tag' : function(url, option) {
            var me = this;
            tag = me.attr('data-tag');
            var scrollTop = me.attr('data-scroll') || window;
            var elem = $(tag);
            if (elem.length) {
                option.dataType = 'html';                
                option.callback = function(data) {
                    if (elem.length) {
                        elem.html(data).applyNUI();
                        $(scrollTop).scrollTop(0);
                    }
                };
                if(window.ignore_key_elms){
                    option.beforeSend = function(r,opt){
                        $('body').find('> *').filter(':not(' + window.ignore_key_elms + ')').empty().remove();
                    };
                }
                nuiAjax.ajax(url, option);
            } else {
                nUI.log('[ajax:target=tag] tag was not found!');
            }
        },
        'dialog' : function(url, option) {
            if (nUI.Dialog) {
                var me = this;
                var id = me.attr('dialog-id');
                var title = me.attr('dialog-title');
                var width = me.attr('dialog-width'), height = me.attr('dialog-height');
                var model = me.attr('dialog-model') == 'true' ? true : false;
                var forelem = me.attr('data-for') || '';
                var icon = me.attr('dialog-icon'), theme = me.attr('dialog-theme'), opts = {};
                if (width) {
                    opts.width = width;
                }
                if (height) {
                    opts.height = height;
                }
                opts.model = model;
                if (icon) {
                    opts.icon = icon;
                }
                if (theme) {
                    opts.theme = theme;
                }
                var dialog = new nUI.Dialog(id, title, opts);
                dialog.setInsertElem(forelem);
                option.dataType = 'html';                
                dialog.open(url, option);
            }
        }
    };

    $.fn.nuiAjax = function(type) {
        return $(this).each(function(i, elm) {
            var $elm = $(elm);
            if (!$elm.data('ajaxObj')) {
                $elm.data('ajaxObj', new nuiAjax(type, $elm));
            }
        });
    };
    nUI.ajaxCallbacks = {};
    nUI.closeAjaxDialog = function(){
        nUI.closeDialog('ajax-dialog');
    };
})(window.nUI, jQuery);
