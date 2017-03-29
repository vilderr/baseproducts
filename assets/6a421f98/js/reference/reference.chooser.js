if (typeof jQuery === 'undefined') {
  throw new Error('JavaScript requires jQuery')
}

+function ($) {
    'use strict';
    
    var Chooser = function (element, options) {
        this.$element = $(element);
        this.options = options;
        
        this.$chooserBox = $(this.options.target);
        this.url = this.options.url;
        this.reference_id_name = this.options.name;
        this.reference_select_id = this.options.id;
    };
    
    Chooser.VERSION  = '0.0.1';
    
    Chooser.defaults = {};
    
    Chooser.prototype.insert = function (reference_type) {
        var request = $.post({
            url: this.url,
            data: {
                type: reference_type,
                name: this.reference_id_name,
                id: this.reference_select_id
            }
        });

        request.done($.proxy(this.insertResponse, this));
    };
    
    Chooser.prototype.insertResponse = function (content) {
        this.$chooserBox.html(content);
    };
    
    // CHOOSER PLUGIN DEFINITION
    // =======================
    function Plugin(option, value) {
        return this.each(function() {
            var $this = $(this);
            var options = $.extend({}, Chooser.defaults, option);
            var action = 'insert';
            var data = new Chooser(this, options);
            
            data[action](value);
        });
    }
    
    var old = $.fn.chooser;

    $.fn.chooser = Plugin;
    $.fn.chooser.Constructor = Chooser;
    
    // CHOOSER NO CONFLICT
    // =================
    $.fn.chooser.noConflict = function () {
        $.fn.chooser = old;
        return this;
    };
    
    // CHOOSER DATA-API
    // ==============
    $(document).on('change', '.chooser', function (e) {
        var $this = $(this);
        var $target = $($this.attr('data-target'));
        var value = this.value;
        var option = $this.data();
        
        Plugin.call($target, option, value);
    });
    
}(jQuery);