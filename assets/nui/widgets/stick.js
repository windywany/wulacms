(function(nUI, $) {
    var nuiStick = function(stick) {
        this.placeholder = stick.clone();
        this.placeholder.removeAttr('data-widget');
        this.stickE = stick;
        this.stickY = stick.attr('data-stick') || 0;
        this.offset = stick.offset();
        this.pos = this.offset.top - this.stickY;
        this.stickE.css('top', this.stickY + 'px');
        this.placeholder.hide();
        stick.after(this.placeholder);
    };

    nuiStick.prototype.stick = function(y) {
        if (y >= this.pos) {
            if (!this.stickE.hasClass('nui-stick')) {
                this.stickE.addClass('nui-stick');
                this.placeholder.show();
            }
        } else {
            this.placeholder.hide();
            this.stickE.removeClass('nui-stick');
        }
    };

    $.fn.nuiStick = function() {
        return $(this).each(function(i, elm) {
            var stick = $(elm);
            if (!stick.data('stickObj')) {
                stick.data('stickObj', new nuiStick(stick));
            }
        });
    };

    $(window).scroll(function() {
        var me = $(this), y = me.scrollTop();
        $('body').find('div[data-widget=nuiStick]').each(function(i, e) {
            var stick = $(e).data('stickObj');
            if (stick) {
                stick.stick(y);
            }
        });
    });
})(window.nUI, jQuery);
