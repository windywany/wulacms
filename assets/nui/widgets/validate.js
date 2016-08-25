(function(nUI, $) {
    var nuiValidate = function(form) {
        this.useValidatePlugin = false;
        this.form = form;
        this.showError = form.attr('data-showError') == 'true' ? true : false;
        this.icon = form.attr('data-feedback') == 'true' ? true : false;
        this.validateUrl = form.attr('data-vurl') || $('body').attr('data-validateURL');
        form.data('validateObj', this);
        var errorPlacement = function(error, element) {
            error.insertAfter(element.parent());
        };
        if($.isFunction($.fn.validate)){
            var name = form.attr('name'),rules={};
            if(name && nUI.validateRules[name]){
                rules = nUI.validateRules[name];
                if(rules.rules){
                    for (var i in rules.rules){
                        for(var j in rules.rules[i]){
                            if(j == 'pattern'){                               
                               eval('var rule = '+rules.rules[i][j]+';');                               
                               rules.rules[i][j] = rule;
                            }
                        }
                    }
                }
            }
            this.useValidatePlugin = true;
            rules.errorPlacement = errorPlacement;
            rules.onsubmit = form.attr('data-submit')=='true'?true:false;
            this.validator = form.validate(rules);            
        }
    };
    nuiValidate.prototype.validate = function(errors) {        
        if(this.useValidatePlugin){
            if ( this.validator.form() ) {
            	if(errors){
            		this.validator.showErrors(errors);
            		return;
            	}
                if ( this.validator.pendingRequest ) {
                    this.validator.formSubmitted = true;                    
                    return false;
                }
            }
            return this.form.valid();
        }
        var form = this.form, name = form.attr('name'), me = this;
        if (name && this.validateUrl) {
            var data = form.serializeArray(),valid = false;
            data.push({
                name : '__fn',
                value : name
            });
            $.ajax({
                url : this.validateUrl,
                type : 'post',
                dataType : 'json',
                data : data,
                async : false,
                success : function(data) {
                    if (data.success) {
                        valid =  true;
                    } else if (data.errors) {
                        nUI.setFormError(data.errors, me.showError, me.icon);                        
                    } else {
                        alert(data.msg);                        
                    }
                },
                error : function() {
                   valid = false;
                }
            });
            return valid;
        } else {
            return true;
        }
    };
    $.fn.nuiValidate = function() {
        return $(this).each(function(i, elm) {
            var form = $(elm);
            if (!form.data('validateObj')) {
                new nuiValidate(form);
            }
        });
    };
})(window.nUI, jQuery);
