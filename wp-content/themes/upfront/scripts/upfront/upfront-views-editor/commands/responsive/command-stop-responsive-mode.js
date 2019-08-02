(function($){
    var l10n = Upfront.Settings && Upfront.Settings.l10n
            ? Upfront.Settings.l10n.global.views
            : Upfront.mainData.l10n.global.views
        ;
    define([
        'scripts/upfront/upfront-views-editor/commands/command'
    ], function ( Command ) {

        return Command.extend({
            enabled: true,
            className: 'exit-responsive sidebar-commands-small-button',
            render: function () {
                this.$el.html("<span title='"+ l10n.exit_responsive  +"'>" + l10n.exit_responsive + "</span>");
            },
            on_click: function () {
                $('li.desktop-breakpoint-activate').trigger('click');
                Upfront.Events.trigger('upfront:exit:responsive');
                Upfront.Application.start_previous();
            }
        });

    });
}(jQuery));
