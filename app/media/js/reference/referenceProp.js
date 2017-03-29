/**
 * Created by VILDERR on 13.03.17.
 */

$(function () {
    ReferenceProp = function (params) {

        this.property = {
            ID: 0
        };
        this.visual = {
            ADVANCED_SETTINGS_BOX: ''
        };
        this.url = '';

        this.obProperty = null;
        this.obAdvanced = null;
        this.additional = [];

        this.errorCode = 0;

        if ("object" === typeof params) {
            this.property = params.PROP;
            this.additional = params.ADDITIONAL;
            this.visual = params.VISUAL;
            this.url = params.URL;

        }
        else {
            this.errorCode = -1;
        }

        if (this.errorCode === 0) {
            this.Init();
        }
    };

    ReferenceProp.prototype.Init = function () {
        this.obProperty = $("#ReferenceProperty-" + this.property.ID + "-settings-line", document);
        this.obAdvanced = this.obProperty.find("#" + this.visual.ADVANCED_SETTINGS_BOX);

        if (!this.obProperty) {
            this.errorCode = -2;
        }

        modSettingsOb = this.obProperty.find(".mod-setting");

        if (this.errorCode === 0) {
            for (var i = 0; i < modSettingsOb.length; i++) {
                $(modSettingsOb[i]).on('change', $.proxy(this.SetFieldValue, this));
            }
        }
    };

    ReferenceProp.prototype.SetFieldValue = function (e) {
        var ob = e.target;
        var obValue = ob.value;
        var obDataId = ob.getAttribute('data-id');
        var obTarget = document.getElementById(obDataId);

        switch (ob.type) {
            case 'checkbox':
                if ($(ob).prop('checked')) {
                    $(obTarget).prop('checked', true);
                }
                else {
                    $(obTarget).prop('checked', false);
                }
                break;
            default:
                obTarget.value = obValue;
                break;
        }

        if ('property-type' === ob.getAttribute('data-type')) {
            this.SetAdditionalFields(obValue);
        }
    };

    ReferenceProp.prototype.SetAdditionalFields = function (value) {

        if (this.obAdvanced) {
            var request = $.post({
                url: this.url,
                data: {
                    property_id:this.property.ID,
                    type:value
                }
            });

            request.done($.proxy(this.InsertAdditionalContent, this));
        }
    };

    ReferenceProp.prototype.in_array = function (needle, haystack) {
        for (var i = 0; i < haystack.length; i++) {
            if (haystack[i] == needle) {
                return true;
            }
        }
        return false;
    };

    ReferenceProp.prototype.InsertAdditionalContent = function (content) {
        this.obAdvanced.html(content);
    }
});

