(function(global, $) {    
    var exports = {},nUI = global.nUI = exports.nUI = {
        version : "0.1.0",
        _config : {}
    };
    nUI.lang = $.extend({
        waitMsg : 'Please Wait ...'
    }, {}, {} || global.nUIi18n);
    exports.log = nUI.log = function(log) {
        if (nUI._config.debug && typeof console != undefined) {
            console.log(log);
        }
    };
    nUI.validateRules = {};
    nUI.emptyFn = function() {
    };
    exports._ = nUI._ = function(string) {
        var str = "" || nUI.lang[string], args = [];
        if (arguments.length > 1) {
            for ( var i = 1; i < arguments.length; i++) {
                args[i - 1] = arguments[i];
            }
        }
        return str.format.apply(str, args);
    };
    nUI.Map = function() {
        this.elements = new Array();

        this.size = function() {
            return this.elements.length;
        };

        this.isEmpty = function() {
            return (this.elements.length < 1);
        };

        this.clear = function() {
            this.elements = new Array();
        };

        this.put = function(_key, _value) {
            this.remove(_key);
            this.elements.push({
                key : _key,
                value : _value
            });
        };

        this.remove = function(_key) {
            try {
                for ( var i = 0; i < this.elements.length; i++) {
                    if (this.elements[i].key == _key) {
                        this.elements.splice(i, 1);
                        return true;
                    }
                }
            } catch (e) {
                return false;
            }
            return false;
        };

        this.get = function(_key) {
            try {
                for ( var i = 0; i < this.elements.length; i++) {
                    if (this.elements[i].key == _key) {
                        return this.elements[i].value;
                    }
                }
            } catch (e) {
                return null;
            }
        };

        this.element = function(_index) {
            if (_index < 0 || _index >= this.elements.length) {
                return null;
            }
            return this.elements[_index];
        };

        this.containsKey = function(_key) {
            try {
                for ( var i = 0; i < this.elements.length; i++) {
                    if (this.elements[i].key == _key) {
                        return true;
                    }
                }
            } catch (e) {
                return false;
            }
            return false;
        };

        this.values = function() {
            var arr = new Array();
            for ( var i = 0; i < this.elements.length; i++) {
                arr.push(this.elements[i].value);
            }
            return arr;
        };
        this.keys = function() {
            var arr = new Array();
            for ( var i = 0; i < this.elements.length; i++) {
                arr.push(this.elements[i].key);
            }
            return arr;
        };
    };
    nUI.Overlay = {
        zIndexes : [],
        currentZdx : 0,
        overlay : null,
        identifier : null,
        msgBody : null,
        init : function() {
            var me = this;
            this.overlay = $('<div id="nuiOverlay" class="keepit"></div>');
            this.identifier = $('<div class="panel panel-default keepit" id="nuiWaitIdentifier"><div class="panel-body"></div></div>');            
            this.overlay.appendTo($('body'));
            this.identifier.appendTo($('body'));
            this.msgBody = this.identifier.find('.panel-body');
            $(window).on('nui-layout', function() {
                me.identifier.centerMe();
            });
        },
        show : function(cb,opacity) {
            this.currentZdx = 9008;
            if (this.zIndexes.length != 0) {
                this.currentZdx = this.zIndexes[this.zIndexes.length - 1] + 3;
            }
            this.zIndexes.push(this.currentZdx);
            var op = 0;
            if(opacity !==false){
                op = opacity;
            }
            this.overlay.css('z-index', this.currentZdx).fadeTo(0, op, cb || nUI.emptyFn);
            return this.currentZdx + 1;
        },
        hide : function() {
            if (this.zIndexes.length > 0) {
                zIndex = this.zIndexes.pop();
                this.overlay.css('z-index', zIndex);
                if (this.zIndexes.length > 0) {
                    this.currentZdx = this.zIndexes[this.zIndexes.length - 1];
                    this.overlay.css('z-index', this.currentZdx);
                    return;
                } else {
                    this.currentZdx = 0;
                }
            }
            this.overlay.hide();
        },
        wait : function(msg,opacity) {
            if (this.identifier.is(':hidden')) {
                var me = this;
                this.show(function() {
                    msg = msg || nUI._('waitMsg');
                    me.msgBody.html(msg);
                    me.identifier.css('z-index', 9999).centerMe().show();
                },opacity);
            } else if (msg) {
                this.msgBody.html(msg);
                this.identifier.centerMe();
            }
        },
        done : function() {
            if (this.identifier.is(':visible')) {
                this.identifier.hide();
                this.hide();
            }
        }
    };
    nUI.keyCode = {
        ENTER : 13,
        ESC : 27,
        END : 35,
        HOME : 36,
        SHIFT : 16,
        TAB : 9,
        LEFT : 37,
        RIGHT : 39,
        UP : 38,
        DOWN : 40,
        DELETE : 46,
        BACKSPACE : 8
    };
    // init ui
    exports.init = nUI.init = function(args) {
        nUI._config = $.extend({
            debug : false
        }, {}, args || {});
        nUI._config.scrollBarWidth = $.scrollbarWidth();
        nUI.log([ 'init nUI', nUI._config ]);
        if (typeof (CollectGarbage) == 'function') {
            setInterval("CollectGarbage();", 10000);
        }
        nUI.Overlay.init();
        $(document).ajaxComplete(function(event, jqxhr,options) {
        	if(options.element){
        		options.element.data('ajax_sending',false);
        	}
            if(!jqxhr.responseChecked){
                if(options.async && options.blockUI){
                    nUI.Overlay.done();
                }
                checkResponse(jqxhr);
            }            
        }).ajaxError(function(event, jqxhr, options,t) {
        	if(options.element){
        		options.element.data('ajax_sending',false);
        	}
            if(options.async && options.blockUI){
                nUI.Overlay.done();
            }
            if (jqxhr.status != 0 && !checkResponse(jqxhr)) {                
                var ajax = 'url:' + options.url;
                ajax += '<br/>type:' + options.type;
                ajax += '<br/>dataType:' + options.dataType;
                ajax += '<br/>data:' + exports.JsonStringfy(options.data);
                var m = jqxhr.responseText;
                if(!m && t){
                	m = t.message;
                }else if(!m){
                	m = '未知错误！';
                }
                var message = '<div class="txtl"><h4>Request:</h4>' + ajax + '</div><div class="txtl"><h4>Response:</h4>' + m + '</div>';
                nUI.errorTip(message,null,{min_width:800,max_height:600,max_width:1000});
            }
        }).ajaxSend(function(event, jqXHR, ajaxOptions) {
            if(ajaxOptions.async && ajaxOptions.blockUI){
                nUI.Overlay.wait(null,ajaxOptions.opacity);
            }
            jqXHR.setRequestHeader('X-AJAX-TYPE', ajaxOptions.dataType);
        }).ajaxStop(function(){        	
            nUI.Overlay.done();
        });
        $.ajaxSetup({
            cache : false,
            timeout : 900000,
            blockUI : false
        });
        $(window).resize(function() {
            $(this).trigger('nui-layout');
        }); 
        if ($.fn.datepicker) {
            $.datepicker.setDefaults({
                dayNames : [ "星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日" ],
                dayNamesShort : [ "周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日" ],
                dayNamesMin : [ "日", "一", "二", "三", "四", "五", "六", "日" ],
                monthNames : [ "一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月" ],
                monthNamesShort : [ "一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月" ],
                firstDay:1
            });                
        }           
        if ($.fn.select2) {
            $.extend($.fn.select2.defaults, {
                formatNoMatches : function() {
                    return "没有找到匹配项";
                },
                formatInputTooShort : function(input, min) {
                    var n = min - input.length;
                    return "请再输入" + n + "个字符";
                },
                formatInputTooLong : function(input, max) {
                    var n = input.length - max;
                    return "请删掉" + n + "个字符";
                },
                formatSelectionTooBig : function(limit) {
                    return "你只能选择最多" + limit + "项";
                },
                formatLoadMore : function(pageNumber) {
                    return "加载结果中...";
                },
                formatSearching : function() {
                    return "搜索中...";
                }
            });
        }
        if ($.fn.nuiValidate) {
            window.nuiCheckFormValid = function(form) {
                if (!form) {
                    form = $(form);
                    if (form.length > 0 && form.data('validateObj')) {
                        return form.data('validateObj').validate();
                    }
                }
                return true;
            };
        }
        $(document).on('apply-nui-widgets', function(e, elm) {
            if ($.fn.nuiAjax) {
                elm.find('[target=ajax]').nuiAjax('ajax');
                elm.find('[target=dialog]').nuiAjax('dialog');
                //elm.find('[target=tab]').nuiAjax('tab');
                elm.find('[target=tag]').nuiAjax('tag');
                //elm.find('[target=popup]').nuiAjax('popup');
            }
            if ($.fn.nuiTable) {
                elm.find('table[data-widget=nuiTable]').nuiTable();
            }
            if ($.fn.nuiGrid) {
                elm.find('table[data-widget=nuiGrid]').nuiGrid();
            }
            if ($.fn.nuiPager) {
                elm.find('div[data-widget=nuiPager]').nuiPager();
            }
            if ($.fn.nuiSearchForm) {
                elm.find('form[data-widget=nuiSearchForm]').nuiSearchForm();
            }               
            if ($.fn.nuiValidate) {
                elm.find('[data-widget=nuiValidate]').nuiValidate();
            }
            if($.fn.nuiTagWrapper){
            	elm.find('[data-widget=nuiTagWrapper]').nuiTagWrapper();
            }
            if ($.fn.select2 && $.fn.nuiCombox) {
                elm.find('[data-widget=nuiCombox]').nuiCombox();
            }
            if(typeof(plupload) !=undefined && $.fn.nuiAjaxUpload){
                elm.find('[data-widget=nuiAjaxUploader]').nuiAjaxUpload();
            }
            if ($.fn.datepicker) {
                var ds = {
                    dateFormat : 'yy-mm-dd',
                    changeMonth: true, 
                    changeYear: true,                       
                    prevText : '<i class="fa fa-chevron-left"></i>',
                    nextText : '<i class="fa fa-chevron-right"></i>',
                    gotoCurrent : true
                };
                elm.find('[data-widget=nuiDatepicker]').each(function(i,n){
                    var $this = $(n),hasRange=false;
                    if($this.attr('data-range-from')){
                        ds.onClose = function (selectedDate){
                            var dd = $this.attr('data-range-from');
                            $( "#"+dd ).datepicker( "option", "maxDate", selectedDate );   
                        }
                        hasRange = true;
                    }
                    if($this.attr('data-range-to')){
                        ds.onClose = function (selectedDate){
                            var dd = $this.attr('data-range-to').split(',');
                            $(dd).each(function(j,e){
                            	$( "#"+e ).datepicker( "option", "minDate", selectedDate );   
                            });                            
                        }
                        hasRange = true;
                    }
                    if(!hasRange){
                    	ds.onClose = null;
                    }

                    $this.datepicker(ds);
                });
            }
            if($.fn.timepicker){
                elm.find('[data-widget=nuiTimepicker]').timepicker({ showMeridian:false ,minuteStep:5 });
            }
            if(typeof(KindEditor) == 'function' && $.fn.nuiKindEditor){
                elm.find('[data-widget=nuiKindEditor]').nuiKindEditor();
            }
            if($.fn.zTree && $.fn.nuiTreeview){
                elm.find('[data-widget=nuiTreeview]').nuiTreeview();
            }
            if(typeof(pageSetUp) == 'function' ){
                pageSetUp(elm);
            }
            if(typeof(tableHeightSize) == 'function' ){
            	tableHeightSize();
            }
        });
        $('body').on('blur', '.form-control.ipt-error', function() {
            var ipt = $(this), msge = ipt.data('msge'), fde = ipt.data('fde'), p = ipt.data('parent');
            ipt.removeClass('ipt-error');
            if (p) {
                p.removeClass('ipt-error has-feedback');
            }
            if (msge) {
                msge.hide();
            }
            if (fde) {
                fde.hide();
            }
        });
        $(document).trigger('apply-nui-widgets', [ $('body') ]);
    };
    exports.block = nUI.block = function(msg) {
        nUI.Overlay.wait(msg);
    };
    exports.unblock = nUI.unblock = function() {
        nUI.Overlay.done();
    };
    exports.callback = nUI.callback = function(cb){
    	if($.isFunction(nUI.ajaxCallbacks[cb])){
    		return nUI.ajaxCallbacks[cb];
    	}
    };
    exports.setFormError = nUI.setFormError = function(errors, showMsg, feedback) {
        if (errors) {
            var msg = '', ipt = null, p = null, msge = null, fde = null;
            for ( var i in errors) {
                msg = errors[i];
                ipt = $('#' + i + '.form-control');
                if (ipt.length > 0) {
                    ipt.addClass('ipt-error');
                    p = ipt.parents('div.form-group');
                    if (p.length > 0) {
                        p.addClass('ipt-error');
                        ipt.data('parent', p);
                        if (showMsg) {
                            msge = ipt.data('msge') || p.find('span.help-black');
                            if (msge.length == 0) {
                                msge = $('<span class="help-black">' + msg + '</span>');
                                ipt.after(msge);
                            } else {
                                msge.html(msg).show();
                            }
                            ipt.data('msge', msge);
                        }
                        if (feedback) {
                            p.addClass('has-feedback');
                            fde = ipt.data('fde') || p.find('span.form-control-feedback');
                            if (fde.length == 0) {
                                fde = $('<span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>');
                                ipt.after(fde);
                            } else {
                                fde.show();
                            }
                            fde.attr('title', msg);
                            ipt.data('fde', fde);
                        }
                    }
                }
            }
        }
    };
    exports.JsonStringfy = function(o) {
        var r = [];
        if (typeof o == "string")
            return "\"" + o.replace(/([\'\"\\])/g, "\\$1").replace(/(\n)/g, "\\n").replace(/(\r)/g, "\\r").replace(/(\t)/g, "\\t") + "\"";
        if (typeof o == "object") {
            for ( var i in o) {
                r.push(i + ":" + exports.JsonStringfy(o[i]));
            }
            if (!!document.all && !/^\n?function\s*toString\(\)\s*\{\n?\s*\[native code\]\n?\s*\}\n?\s*$/.test(o.toString)) {
                r.push("toString:" + o.toString.toString());
            }
            r = "{" + r.join() + "}";
            return r;
        }
        return o?o.toString():'';
    };
    exports.setCookie = nUI.setCookie = function(name, value, expire) {
    	document.cookie = name + "=" + escape(value) + '; path=/' + (expire ? "; expires=" + expire.toGMTString():"");
	};
	exports.getCookie= nUI.getCookie = function(name) {
		var search = name + "=";
		if (document.cookie.length > 0) {
			offset = document.cookie.indexOf(search);
			if (offset != -1) {
				offset += search.length;
				end = document.cookie.indexOf(";", offset);
				if (end == -1) {
					end = document.cookie.length;
				}
				return unescape(document.cookie.substring(offset, end));
			}
		}
		return undefined;
	};
	exports.showButtons = nUI.showButtons = function(opts,elem){
		opts.owner = elem;
		var target = opts.target?elem.find(opts.target):elem;
		
		
		if(target.find('.nui-pop-toolbar').length > 0){
			var btng = target.find('.nui-pop-toolbar').eq(0);
		}else{
			if(opts.wrapper){
				var btng = $(opts.wrapper).addClass('nui-pop-toolbar');
			}else{
				var btng = $('<div class="nui-pop-toolbar"></div>');
			}
			if(opts.wrapcls){
				btng.css(opts.wrapcls);
			}
			target.css('position','relative');
    		for(var i in opts.buttons){
    			var btn = opts.buttons[i];
    			var bn = $(btn.html);
    			btng.append(bn);
    			if(btn.onClick){
    				bn.click(function(){
    					btn.onClick(elem);    					
    				});
    			}
    		}
    		if(opts.target){
    			target.append(btng);
    		}else{
    			target.append(btng);
    		}
		}
		btng.show();
		opts.elem = btng;
	};
	exports.hideButtons = nUI.hideButtons = function(opts){
		if(opts.elem){
			opts.elem.hide();
		}
	};
    window.checkResponse = function(ajaxReq,rtn) {
        if(!ajaxReq.responseChecked){
            ajaxReq.responseChecked = true;
            var ajaxRes = $.trim(ajaxReq.getResponseHeader('X-AJAX-REDIRECT'));
            var ajaxMsg = $.trim(ajaxReq.getResponseHeader('X-AJAX-MESSAGE'));
            if (ajaxMsg) {
                var msg = '';
                try {
                    eval("res = (" + ajaxReq.responseText + ")");
                    msg = res.message;                    
                } catch (e) {
                    msg = ajaxReq.responseText;                                     
                }
                if(rtn){
                    return msg;
                }else{
                    nUI.errorTip(msg,null,{min_width:800,max_height:600,max_width:1000});
                }   
                return true;
            } else if (ajaxRes) {
                window.location.href = ajaxRes;
                return true;
            }  
            return false;          
        }
        return true;
    };
    // apply nui to selected element
    $.fn.applyNUI = function() {
        $(document).trigger('apply-nui-widgets', [ $(this) ]);
        return $(this);
    };
    // parse option bind to this element
    $.fn.parseArg = function(arg) {
        var argStr = $(this).attr('data-arg'), _arg = arg;
        if (argStr) {
            try {
                argStr = argStr.trim();
                _arg = eval('({' + argStr + '})');
                _arg = $.extend(arg, _arg);
            } catch (e) {
                nUI.log('can not parse args:' + argStr);
            }
        }
        return _arg;
    };
    $.fn.centerMe = function() {
        var win = $(window), ww = win.width(), wh = win.height();
        var lb  = $('#left-panel'),lbw=0;
        if(!$('body').hasClass('menu-on-top') && lb.length>0){
        	lbw = lb.width();
        }
        return $(this).each(function(i, n) {
            var elm = $(n), w = elm.width(), h = elm.height(),top = (wh - h) / 2;
            elm.css({
                left : (ww - lbw - w) / 2 + lbw,
                top : top < 10 ? 10:top
            });
        });
    };

    $.scrollbarWidth = function() {
        var scrollDiv = $('<div></div>').css({
            width : '100px',
            height : '100px',
            overflow : 'scroll',
            position : 'absolute',
            padding : 0,
            margin : 0,
            top : '-9999px'
        });
        $('body').append(scrollDiv);
        scrollD = scrollDiv.get(0);
        var scrollbarWidth = scrollD.offsetWidth - scrollD.clientWidth;;
        scrollDiv.remove();
        return scrollbarWidth;
    };
    /**
     * 扩展String方法
     */
    $.extend(String.prototype, {
        isPositiveInteger : function() {
            return (new RegExp(/^[1-9]\d*$/).test(this));
        },
        isInteger : function() {
            return (new RegExp(/^\d+$/).test(this));
        },
        isNumber : function(value, element) {
            return (new RegExp(/^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/).test(this));
        },
        trim : function() {
            return this.replace(/(^\s*)|(\s*$)|\r|\n/g, "");
        },
        startsWith : function(pattern) {
            return this.indexOf(pattern) === 0;
        },
        endsWith : function(pattern) {
            var d = this.length - pattern.length;
            return d >= 0 && this.lastIndexOf(pattern) === d;
        },
        replaceSuffix : function(index) {
            return this.replace(/\[[0-9]+\]/, '[' + index + ']').replace('#index#', index);
        },
        trans : function() {
            return this.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"');
        },
        encodeTXT : function() {
            return (this).replaceAll('&', '&amp;').replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll(" ", "&nbsp;");
        },
        replaceAll : function(os, ns) {
        	if(typeof(os) == 'string'){
        		return this.replace(new RegExp(os, "gm"), ns);
        	}else{
        		return this.replace(os, ns);
        	}
        },
        replaceTm : function($data) {
            if (!$data)
                return this;
            return this.replace(RegExp("({[A-Za-z_]+[A-Za-z0-9_]*})", "g"), function($1) {
                return $data[$1.replace(/[{}]+/g, "")];
            });
        },
        replaceTmById : function(_box) {
            var $parent = _box || $(document);
            return this.replace(RegExp("({[A-Za-z_]+[A-Za-z0-9_]*})", "g"), function($1) {
                var $input = $parent.find("#" + $1.replace(/[{}]+/g, ""));
                return $input.val() ? $input.val() : $1;
            });
        },
        isFinishedTm : function() {
            return !(new RegExp("{[A-Za-z_]+[A-Za-z0-9_]*}").test(this));
        },
        skipChar : function(ch) {
            if (!this || this.length === 0) {
                return '';
            }
            if (this.charAt(0) === ch) {
                return this.substring(1).skipChar(ch);
            }
            return this;
        },
        isValidPwd : function() {
            return (new RegExp(/^([_]|[a-zA-Z0-9]){6,32}$/).test(this));
        },
        isValidMail : function() {
            return (new RegExp(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(this.trim()));
        },
        isSpaces : function() {
            for ( var i = 0; i < this.length; i += 1) {
                var ch = this.charAt(i);
                if (ch != ' ' && ch != "\n" && ch != "\t" && ch != "\r") {
                    return false;
                }
            }
            return true;
        },
        isPhone : function() {
            return (new RegExp(/(^([0-9]{3,4}[-])?\d{3,8}(-\d{1,6})?$)|(^\([0-9]{3,4}\)\d{3,8}(\(\d{1,6}\))?$)|(^\d{3,8}$)/).test(this));
        },
        isUrl : function() {
            return (new RegExp(/^[a-zA-z]+:\/\/([a-zA-Z0-9\-\.]+)([-\w .\/?%&=:]*)$/).test(this));
        },
        isExternalUrl : function() {
            return this.isUrl() && this.indexOf("://" + document.domain) == -1;
        },
        format : function() {
            var str = this;
            for ( var i = 0; i < arguments.length; i++) {
                str = str.replace('{' + i + '}', arguments[i]);
            }
            return str;
        },
        parseArgs : function() {
            var re = /\$(.+?)\$/g, match, args = [];
            while (match = re.exec(this)) {
                args.push(match[1]);
            }
            return args;
        }
    });
})(window, jQuery);