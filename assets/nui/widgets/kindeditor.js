(function(nUI, $) {
    var colorpicker = null;
    var nuiKindEditor = function(editor) {
        this.editor = editor;
        this.widget = editor.attr('data-plugin');        
        if(this.widget == 'colorpicker'){
            this.colorpicker();
        }
    };
    nuiKindEditor.prototype.colorpicker = function(){
        elm = this.editor;
        if (colorpicker) {
            colorpicker.remove();
            colorpicker = null;
        }
        var forinput = elm.attr('data-for');
        var color = elm.attr('data-color') || ''; 
        if(!color){
            if(forinput){
                color = $(forinput).val();
            }
        }  
        if(color){
            elm.css('color',color);
        }
        elm.bind('click', function(e) {
            e.stopPropagation();
            if (colorpicker) {
                colorpicker.remove();
                colorpicker = null;
                return;
            } 
            var colorpickerPos = elm.offset();
            colorpicker = KindEditor.colorpicker({
                x : parseInt(colorpickerPos.left,10),
                y : parseInt(colorpickerPos.top + elm.height(),10),
                z : 19811214,
                selectedColor : color,
                noColor : '无颜色',
                click : function(colorx) {
                    if(forinput){
                        $(forinput).val(colorx);
                    }       
                    color = colorx;             
                    elm.css('color',color);
                    colorpicker.remove();
                    colorpicker = null;
                }
            });
        });     
    };
    $.fn.nuiKindEditor = function() {
        return $(this).each(function(i, elm) {
            var editor = $(elm);
            if (!editor.data('kindObj')) {
                editor.data('kindObj', new nuiKindEditor(editor));
            }
        });
    };    
})(window.nUI, jQuery);
