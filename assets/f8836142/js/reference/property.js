/**
 * Created by VILDERR on 16.03.17.
 */

if (typeof jQuery === 'undefined') {
    throw new Error('JavaScript requires jQuery')
}

+function ($) {
    'use strict';

    var Input = function (target, attribute) {
        this.target = target;
        this.attribute = attribute;
    };

    Input.prototype.insert = function (clone) {
        $(clone).insertAfter(this.target);
        $(clone).attr('class', 'form-group');
        $(':input', clone).attr('name', this.attribute).attr('id', '').val('');
    };

    Input.VERSION  = '0.0.1';
    Input.DEFAULTS = {};

    // INPUT PLUGIN DEFINITION
    // =======================
    function Plugin(clone, attribute) {
        return this.each(function() {
            var data = new Input(this, attribute);
            var action = 'insert';

            data[action](clone);
        });
    }

    var old = $.fn.input;

    $.fn.input = Plugin;
    $.fn.input.Constructor = Input;

    // CHOOSER NO CONFLICT
    // =================
    $.fn.input.noConflict = function () {
        $.fn.input = old;
        return this;
    };

    // CHOOSER DATA-API
    // ==============
    $(document).on('click', '.add-property-value-input', function (e) {
        var $this   = $(this);
        var $target = $this.prev();
        var attribute = $this.data('attribute');
        var $clone = $target.clone();
        //console.log($clone);

        if($this.is('a')) e.preventDefault();

        Plugin.call($target, $clone, attribute);
    });

}(jQuery);
