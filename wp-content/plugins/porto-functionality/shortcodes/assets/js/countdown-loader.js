/* Count Down */
jQuery(document).ready(function(){
    'use strict';
    jQuery('.porto_countdown-dateAndTime').each(function(){
        var t = new Date(jQuery(this).data('terminal-date'));
        var tz = jQuery(this).data('time-zone')*60;
        var tfrmt = jQuery(this).data('countformat');
        var labels_new = jQuery(this).data('labels');
        var new_labels = labels_new.split(",");
        var labels_new_2 = jQuery(this).data('labels2');
        var new_labels_2 = labels_new_2.split(",");
        var server_time = function(){
          return new Date(jQuery(this).data('time-now'));
        }
        
        var ticked = function (a){
            var count_amount = jQuery(this).find('.porto_countdown-amount');
            var count_period = jQuery(this).find('.porto_countdown-period');

            var tick_color          = jQuery(this).data('tick-col'),
                tick_p_size         = jQuery(this).data('tick-p-size'),
                tick_fontfamily     = jQuery(this).data('tick-font-family'),
                count_amount_css    = '',
                count_amount_font   = '',
                tick_br_color       = jQuery(this).data('br-color'),
                tick_br_size        = jQuery(this).data('br-size'),
                tick_br_style       = jQuery(this).data('br-style'),
                tick_br_radius      = jQuery(this).data('br-radius'),
                tick_bg_color       = jQuery(this).data('bg-color'),
                tick_padd           = jQuery(this).data('padd');
            
            // Applied CSS for Count Amount & Period
            count_amount.css({
                // 'color'         : tick_color,
                'font-family'   : tick_fontfamily,
                'border-width'  : tick_br_size,
                'border-style'  : tick_br_style,
                'border-radius' : tick_br_radius,
                'background'    : tick_bg_color,
                'padding'       : tick_padd,
                'border-color'  : tick_br_color
            });
        }

        if(jQuery(this).hasClass('porto-usrtz')){
            jQuery(this).porto_countdown({labels: new_labels, labels1: new_labels_2, until : t, format: tfrmt, padZeroes:true,onTick:ticked});
        }else{
            jQuery(this).porto_countdown({labels: new_labels, labels1: new_labels_2, until : t, format: tfrmt, padZeroes:true,onTick:ticked , serverSync:server_time});
        }
    });
});