var LanguageCsv = Class.create();
LanguageCsv.prototype = {
    initialize: function() {},

    selectModule: function(button) {
        button.replace($('module_selector_div').show());
    }
}