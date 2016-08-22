(function(nUI, $) {
	var searchFormData = {};
    var nuiSearchForm = function(form) {
        var targetId = form.attr('data-for');
        if (targetId) {
            targetId = targetId.trim();
            if (targetId) {      
            	var te = $(targetId);
                var keep   = te.attr('data-keep');
                var target = $(targetId).data('formTarget');
                if (target || !$.isFunction(target.form) || !$.isFunction(target.reload)) {                                 
                    var data = keep?searchFormData[keep]:false;
                    if(data){                    	
                    	for (d in data){
                    		var wd = form.find('[name="'+data[d].name+'"]');
                    		if(wd){
                    			if(wd.eq(0).is('[type="radio"]')){
                    				form.find('[name="'+data[d].name+'"][value="'+data[d].value+'"]').attr('checked',true);
                        		}else if(wd.eq(0).is('[type="checkbox"]')){
                        			if(data[d].name.endsWith('[]')){
                        				form.find('[name="'+data[d].name+'"][value="'+data[d].value+'"]').attr('checked',true);
                        			}else{
                        				form.find('[name="'+data[d].name+'"]').attr('checked',true);
                        			}
                        		}else{
                        			wd.val(data[d].value);
                        		}
                    		}
                    	}
                    }
                    target.form(form);
                    form.submit(function(event) {
                        event.preventDefault();
                        if(keep){
                        	searchFormData[keep] = form.serializeArray();
                        }
                        target.reload(null,true);
                        return false;
                    });
                }
            }
        }
    };
    $.fn.nuiSearchForm = function() {
        return $(this).each(function(i, elm) {
            var form = $(elm);
            if (!form.data('formObj')) {
                form.data('formObj', new nuiSearchForm(form));
            }
        });
    };
})(window.nUI, jQuery);
