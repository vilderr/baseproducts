/**
 * yii distribution
 * this javascript using with Distribution views
 * @author <developer@vilderr.ru>
 */
(function ($) {
    'use strict';

    $.fn.distribution = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Метод с именем ' + method + ' не существует для jQuery.distribution');
        }
    };

    var defaults = {
        addCondition: '#part-condition-btn',
        addOperation: '#part-action-btn',
        obCondition: '#part-conditions-box',
        obOperation: '#part-actions-box',
        removePart: '#part-remove-btn',
        url: '/distribution/part-service',
        id: undefined,
        reference_id: undefined
    };

    var methods = {
        init: function (options) {
            return this.each(function () {
                var settings = $.extend({}, defaults, options || {});
                var lines = $('.line', this);

                lines.each(function (index, element) {
                    var $element = $(element);
                    methods.initLine.call($element, settings.id, settings.url, settings.reference_id);
                });

                $(this).on('click', settings.addCondition, function (event) {
                    methods.addLine.call($(settings.obCondition), 'add-condition-line', settings.url, settings.id, settings.reference_id);
                });

                $(this).on('click', settings.addOperation, function (event) {
                    methods.addLine.call($(settings.obOperation), 'add-action-line', settings.url, settings.id, settings.reference_id);
                });

                $(this).on('click', settings.removePart, $.proxy(function () {
                    this.remove();
                }, this));
            });
        },

        initLine: function (id, url, reference) {
            var chooser = $('select.chooser', this);
            var valueBox = $('.value-box', this);
            var removeBtn = $('.remove-line', this);
            var content = this.data('content');

            removeBtn.on('click', $.proxy(function () {
                this.remove();
            }, this));

            chooser.on('change', $.proxy(function (event) {
                var value = event.target.value;
                var request = $.ajax({
                    url: url,
                    data: {
                        'action': content,
                        'value': value,
                        'part_id': id,
                        'reference_id': reference
                    },
                    type: 'post'
                });

                request.done(function (result) {
                    valueBox.html(result);
                });
            }, this));
        },

        addLine: function (action, url, id, reference) {
            var request = $.ajax({
                url: url,
                data: {
                    'action': action,
                    'part_id': id,
                    'reference_id': reference
                },
                type: 'post'
            });

            request.done($.proxy(function (result) {
                var line = $(result);

                this.append(line);
                methods.initLine.call(line, id, url, reference);
            }, this));
        }
    };

    $.addPart = function (options) {
        if (typeof options === "object") {
            var parent = $("#" + options.id);
            var request = $.ajax({
                url: options.url,
                data: {'reference_id': options.reference_id},
                type: 'post'
            });

            request.done(function (result) {
                parent.append(result);
            });
        }
    };

})(jQuery);