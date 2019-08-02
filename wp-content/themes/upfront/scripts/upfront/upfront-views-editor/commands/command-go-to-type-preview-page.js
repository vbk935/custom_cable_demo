(function($){
    var l10n = Upfront.Settings && Upfront.Settings.l10n
            ? Upfront.Settings.l10n.global.views
            : Upfront.mainData.l10n.global.views
        ;
    define([
        'scripts/upfront/upfront-views-editor/commands/command'
    ], function ( Command ) {

        return Command.extend({
            tagName: 'div',
            className: "command-go-to-type-preview",
            render: function () {
                this.$el.text(l10n.go_to_preview_page);
            },
            on_click: function () {
                alert('This is just placeholder :)');
            }
        });

    });
}(jQuery));