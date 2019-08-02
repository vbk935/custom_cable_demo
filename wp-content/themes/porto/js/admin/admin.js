/*
 * jQuery.appear
 * https://github.com/bas2k/jquery.appear/
 * http://code.google.com/p/jquery-appear/
 * http://bas2k.ru/
 *
 * Copyright (c) 2009 Michael Hixson
 * Copyright (c) 2012-2014 Alexander Brovikov
 * Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
 */
(function($) {
    'use strict';
    $.fn.appear = function(fn, options) {
        var settings = $.extend({
            //arbitrary data to pass to fn
            data: undefined,
            //call fn only on the first appear?
            one: true,
            // X & Y accuracy
            accX: 0,
            accY: 0
        }, options);

        return this.each(function() {
            var t = $(this);
            //whether the element is currently visible
            t.appeared = false;
            if (!fn) {
                //trigger the custom event
                t.trigger('appear', settings.data);
                return;
            }
            var w = $(window);
            //fires the appear event when appropriate

            var check = function() {
                //is the element hidden?
                if (!t.is(':visible')) {
                    //it became hidden
                    t.appeared = false;
                    return;
                }

                //is the element inside the visible window?
                var a = w.scrollLeft();
                var b = w.scrollTop();
                var o = t.offset();
                var x = o.left;
                var y = o.top;
                var ax = settings.accX;
                var ay = settings.accY;
                var th = t.height();
                var wh = w.height();
                var tw = t.width();
                var ww = w.width();

                if (y + th + ay >= b &&
                    y <= b + wh + ay &&
                    x + tw + ax >= a &&
                    x <= a + ww + ax) {
                    //trigger the custom event
                    if (!t.appeared) t.trigger('appear', settings.data);
                } else {
                    //it scrolled out of view
                    t.appeared = false;
                }
            };

            //create a modified fn with some additional logic
            var modifiedFn = function() {
                //mark the element as visible
                t.appeared = true;

                //is this supposed to happen only once?
                if (settings.one) {
                    //remove the check
                    w.unbind('scroll', check);
                    var i = $.inArray(check, $.fn.appear.checks);
                    if (i >= 0) $.fn.appear.checks.splice(i, 1);
                }

                //trigger the original fn
                fn.apply(this, arguments);
            };

            //bind the modified fn to the element
            if (settings.one) t.one('appear', settings.data, modifiedFn);
            else t.bind('appear', settings.data, modifiedFn);
            //check whenever the window scrolls
            w.scroll(check);
            //check whenever the dom changes
            $.fn.appear.checks.push(check);
            //check now
            (check)();
        });
    };

    //keep a queue of appearance checks
    $.extend($.fn.appear, {
        checks: [],
        timeout: null,
        //process the queue
        checkAll: function() {
            var length = $.fn.appear.checks.length;
            if (length > 0) while (length--) {
                if (typeof $.fn.appear.checks[length] == 'function')
                    ($.fn.appear.checks[length])();
            }
        },
        //check the queue asynchronously
        run: function() {
            if ($.fn.appear.timeout) clearTimeout($.fn.appear.timeout);
            $.fn.appear.timeout = setTimeout($.fn.appear.checkAll, 20);
        }
    });

    //run checks when these methods are called
    $.each(['append', 'prepend', 'after', 'before', 'attr',
        'removeAttr', 'addClass', 'removeClass', 'toggleClass',
        'remove', 'css', 'show', 'hide'], function(i, n) {
        var old = $.fn[n];
        if (old) {
            $.fn[n] = function() {
                var r = old.apply(this, arguments);
                $.fn.appear.run();
                return r;
            }
        }
    });
})(jQuery);

// Easy Responsive Tabs Plugin
// Author: Samson.Onna <Email : samson3d@gmail.com>
(function ($) {
    'use strict';
    $.fn.extend({
        easyResponsiveTabs: function (options) {
            //Set the default values, use comma to separate the settings, example:
            var defaults = {
                type: 'default', //default, vertical, accordion;
                width: 'auto',
                fit: true,
                closed: false,
                activate: function(){}
            }
            //Variables
            options = $.extend(defaults, options);
            var opt = options, jtype = opt.type, jfit = opt.fit, jwidth = opt.width, vtabs = 'vertical', accord = 'accordion';
            var hash = window.location.hash;
            var historyApi = !!(window.history && history.replaceState);

            //Events
            $(this).bind('tabactivate', function(e, currentTab) {
                if(typeof options.activate === 'function') {
                    options.activate.call(currentTab, e);
                }
            });

            //Main function
            this.each(function () {
                var $respTabs = $(this);
                var $respTabsList = $respTabs.find('ul.resp-tabs-list');
                var respTabsId = $respTabs.attr('id');
                $respTabs.find('ul.resp-tabs-list li').addClass('resp-tab-item');
                $respTabs.css({
                    'display': 'block',
                    'width': jwidth
                });

                $respTabs.find('.resp-tabs-container > div').addClass('resp-tab-content');
                jtab_options();
                //Properties Function
                function jtab_options() {
                    if (jtype == vtabs) {
                        $respTabs.addClass('resp-vtabs');
                    }
                    if (jfit == true) {
                        $respTabs.css({ width: '100%' });
                    }
                    if (jtype == accord) {
                        $respTabs.addClass('resp-easy-accordion');
                        $respTabs.find('.resp-tabs-list').css('display', 'none');
                    }
                }

                //Assigning the h2 markup to accordion title
                var $tabItemh2;
                $respTabs.find('.resp-tab-content').before("<h2 class='resp-accordion' role='tab'><span class='resp-arrow'></span></h2>");

                var itemCount = 0;
                $respTabs.find('.resp-accordion').each(function () {
                    $tabItemh2 = $(this);
                    var $tabItem = $respTabs.find('.resp-tab-item:eq(' + itemCount + ')');
                    var $accItem = $respTabs.find('.resp-accordion:eq(' + itemCount + ')');
                    $accItem.append($tabItem.html());
                    $accItem.data($tabItem.data());
                    $tabItemh2.attr('aria-controls', 'tab_item-' + (itemCount));
                    itemCount++;
                });

                //Assigning the 'aria-controls' to Tab items
                var count = 0,
                    $tabContent;
                $respTabs.find('.resp-tab-item').each(function () {
                    var $tabItem = $(this);
                    $tabItem.attr('aria-controls', 'tab_item-' + (count));
                    $tabItem.attr('role', 'tab');

                    //Assigning the 'aria-labelledby' attr to tab-content
                    var tabcount = 0;
                    $respTabs.find('.resp-tab-content').each(function () {
                        $tabContent = $(this);
                        $tabContent.attr('aria-labelledby', 'tab_item-' + (tabcount));
                        tabcount++;
                    });
                    count++;
                });

                // Show correct content area
                var tabNum = 0;
                if(hash!='') {
                    var matches = hash.match(new RegExp(respTabsId+"([0-9]+)"));
                    if (matches!==null && matches.length===2) {
                        tabNum = parseInt(matches[1],10)-1;
                        if (tabNum > count) {
                            tabNum = 0;
                        }
                    }
                }

                //Active correct tab
                $($respTabs.find('.resp-tab-item')[tabNum]).addClass('resp-tab-active');

                //keep closed if option = 'closed' or option is 'accordion' and the element is in accordion mode
                if(options.closed !== true && !(options.closed === 'accordion' && !$respTabsList.is(':visible')) && !(options.closed === 'tabs' && $respTabsList.is(':visible'))) {
                    $($respTabs.find('.resp-accordion')[tabNum]).addClass('resp-tab-active');
                    $($respTabs.find('.resp-tab-content')[tabNum]).addClass('resp-tab-content-active').attr('style', 'display:block');
                }
                //assign proper classes for when tabs mode is activated before making a selection in accordion mode
                else {
                    $($respTabs.find('.resp-tab-content')[tabNum]).addClass('resp-tab-content-active resp-accordion-closed');
                }

                //Tab Click action function
                $respTabs.find("[role=tab]").each(function () {

                    var $currentTab = $(this);
                    $currentTab.click(function () {

                        var $currentTab = $(this);
                        var $tabAria = $currentTab.attr('aria-controls');

                        if ($currentTab.hasClass('resp-accordion') && $currentTab.hasClass('resp-tab-active')) {
                            $respTabs.find('.resp-tab-content-active').slideUp('', function () { $(this).addClass('resp-accordion-closed'); });
                            $currentTab.removeClass('resp-tab-active');
                            return false;
                        }
                        if (!$currentTab.hasClass('resp-tab-active') && $currentTab.hasClass('resp-accordion')) {
                            $respTabs.find('.resp-tab-active').removeClass('resp-tab-active');
                            $respTabs.find('.resp-tab-content-active').slideUp().removeClass('resp-tab-content-active resp-accordion-closed');
                            $respTabs.find("[aria-controls=" + $tabAria + "]").addClass('resp-tab-active');

                            $respTabs.find('.resp-tab-content[aria-labelledby = ' + $tabAria + ']').slideDown().addClass('resp-tab-content-active');
                        } else {
                            $respTabs.find('.resp-tab-active').removeClass('resp-tab-active');
                            $respTabs.find('.resp-tab-content-active').removeAttr('style').removeClass('resp-tab-content-active').removeClass('resp-accordion-closed');
                            $respTabs.find("[aria-controls=" + $tabAria + "]").addClass('resp-tab-active');
                            $respTabs.find('.resp-tab-content[aria-labelledby = ' + $tabAria + ']').addClass('resp-tab-content-active').attr('style', 'display:block');
                        }
                        //Trigger tab activation event
                        $currentTab.trigger('tabactivate', $currentTab);
                    });

                });

                //Window resize function
                $(window).resize(function () {
                    $respTabs.find('.resp-accordion-closed').removeAttr('style');
                });
            });
        }
    });
})(jQuery);

jQuery(document).ready(function($) {
    'use strict';

    // content type meta tab
    $('.porto-meta-tab').easyResponsiveTabs({
        type: 'vertical'//, //default, vertical, accordion;
    });

    // taxonomy meta tab
    $('.porto-tab-row').hide();
    $('.porto-tax-meta-tab').on('click', function(e) {
        e.preventDefault();
        var tab = $(this).attr('data-tab');
        $('.porto-tab-row[data-tab="' + tab + '"]').toggle();
        return false;
    });

    // color field
    $(document).on('plugin_init', '.porto-meta-color', function() {
        var $el = $(this),
            $c = $el.find('.porto-color-field'),
            $t = $el.find('.porto-color-transparency');

        $c.wpColorPicker({
            change: function( e, ui ) {
                $( this ).val( ui.color.toString() );
                $t.removeAttr( 'checked' );
            },
            clear: function( e, ui ) {
                $t.removeAttr( 'checked' );
            }
        });
        $t.on('click', function() {
            if ( $( this ).is( ":checked" ) ) {
                $c.attr('data-old-color', $c.val());
                $c.val( 'transparent' );
                $el.find( '.wp-color-result' ).css('background-color', 'transparent');
            } else {
                if ( $c.val() === 'transparent' ) {
                    var oc = $c.attr('data-old-color');
                    $el.find( '.wp-color-result' ).css('background-color', oc);
                    $c.val(oc);
                }
            }
        });
    });
    $('.porto-meta-color').each(function() {
        $(this).trigger('plugin_init');
    });

    // meta required filter
    var filters = ['.postoptions .metabox', '.form-table .form-field'];
    $.each(filters, function(index, filter) {
        $(filter + '[data-required]').each(function() {
            var $el = $(this),
                id = $el.data('required'),
                value = $el.data('value'),
                $required = $(filter + ' [name="' + id + '"]'),
                type = $required.attr('type');
            if ($required.prop('type') == 'select-one') {
                $required.change(function() {
                    if ($.inArray($required.val(), value.split(',')) !== -1) {
                        $el.show();
                    } else {
                        $el.hide();
                    }
                });
                $required.change();
            } else {
                if (type == 'checkbox') {
                    $required.change(function() {
                        if ($(this).is(':checked')) {
                            if (value) {
                                $el.show();
                            } else {
                                $el.hide();
                            }
                        } else {
                            if (!value) {
                                $el.show();
                            } else {
                                $el.hide();
                            }
                        }
                    });
                    $required.change();
                } else if (type == 'radio') {
                    $required.click(function() {
                        if ($(this).is(':checked')) {
                            if ($.inArray($(this).val(), value.split(',')) !== -1) {
                                $el.show();
                            } else {
                                $el.hide();
                            }
                        }
                    });
                    $(filter + ' [name="' + id + '"]:checked').click();
                }
            }
        });
    });

    // codemirror
    if (typeof CodeMirror != 'undefined') {
        if (document.getElementById("custom_css")) CodeMirror.fromTextArea(document.getElementById("custom_css"), { lineNumbers: true, mode: 'css' });
        if (document.getElementById("custom_js_head")) CodeMirror.fromTextArea(document.getElementById("custom_js_head"), { lineNumbers: true, mode: 'javascript' });
        if (document.getElementById("custom_js_body")) CodeMirror.fromTextArea(document.getElementById("custom_js_body"), { lineNumbers: true, mode: 'javascript' });
    }
});

(function() {
    'use strict';
    // Uploading files
    var file_frame;
    var clickedID;

    jQuery(document).off( 'click', '.button_upload_image').on( 'click', '.button_upload_image', function( event ){

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( !file_frame ) {
            // Create the media frame.
            file_frame = wp.media.frames.downloadable_file = wp.media({
                title: 'Choose an image',
                button: {
                    text: 'Use image'
                },
                multiple: false
            });
        }

        file_frame.open();
        
        clickedID = jQuery(this).attr('id');
        
        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();

            jQuery('#' + clickedID).val( attachment.url );
            if (jQuery('#' + clickedID).attr('data-name'))
                jQuery('#' + clickedID).attr('name', jQuery('#' + clickedID).attr('data-name'));

            file_frame.close();
        });
    });

    jQuery(document).off( 'click', '.button_attach_image').on( 'click', '.button_attach_image', function( event ){

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( !file_frame ) {
            // Create the media frame.
            file_frame = wp.media.frames.downloadable_file = wp.media({
                title: 'Choose an image',
                button: {
                    text: 'Use image'
                },
                multiple: false
            });
        }

        file_frame.open();

        clickedID = jQuery(this).attr('id');

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();

            jQuery('#' + clickedID).val( attachment.id );
            jQuery('#' + clickedID + '_thumb').html('<img src="' + attachment.url + '"/>');
            if (jQuery('#' + clickedID).attr('data-name'))
                jQuery('#' + clickedID).attr('name', jQuery('#' + clickedID).attr('data-name'));

            file_frame.close();
        });
    });

    jQuery(document).on( 'click', '.button_remove_image', function( event ){
        
        var clickedID = jQuery(this).attr('id');
        jQuery('#' + clickedID).val( '' );
        jQuery('#' + clickedID + '_thumb').html('');

        return false;
    });
})();

jQuery(function($) {
    'use strict';
    function updatePortoMenuOptions(elem, shift) {
        var current_elem = elem;
        var depth_shift = shift;
        var classNames = current_elem.attr('class').split(' ');

        for (var i = 0; i < classNames.length; i+=1) {
            if (classNames[i].indexOf('menu-item-depth-') >= 0) {
                var depth = classNames[i].split('menu-item-depth-');
                var id = current_elem.attr('id');

                depth = parseInt(depth[1]) + depth_shift;
                id = id.replace('menu-item-', '');

                if (depth == 0) {
                    current_elem.find('.edit-menu-item-level1-' + id).hide().find('select, input, textarea').each(function() {
                        $(this).removeAttr('name');
                    });
                    current_elem.find('.edit-menu-item-level0-'+id).show().find('select, input[type="text"], textarea').each(function() {
                        if ($(this).val()) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level0-'+id).find('input[type="checkbox"]').each(function() {
                        if ($(this).is(':checked')) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level01-'+id).show().find('select, input[type="text"], textarea').each(function() {
                        if ($(this).val()) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level01-'+id).find('input[type="checkbox"]').each(function() {
                        if ($(this).is(':checked')) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                } else if (depth == 1) {
                    current_elem.find('.edit-menu-item-level0-' + id).hide().find('select, input, textarea').each(function() {
                        $(this).removeAttr('name');
                    });
                    current_elem.find('.edit-menu-item-level1-'+id).show().find('select, input[type="text"], textarea').each(function() {
                        if ($(this).val()) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level1-'+id).find('input[type="checkbox"]').each(function() {
                        if ($(this).is(':checked')) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level01-'+id).show().find('select, input[type="text"], textarea').each(function() {
                        if ($(this).val()) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level01-'+id).find('input[type="checkbox"]').each(function() {
                        if ($(this).is(':checked')) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                } else {
                    current_elem.find('.edit-menu-item-level0-'+id).hide().find('select, input, textarea').each(function() {
                        $(this).removeAttr('name');
                    });
                    current_elem.find('.edit-menu-item-level1-'+id).hide().find('select, input, textarea').each(function() {
                        $(this).removeAttr('name');
                    });
                    current_elem.find('.edit-menu-item-level01-'+id).hide().find('select, input, textarea').each(function() {
                        $(this).removeAttr('name');
                    });
                }
            }
        }
    }

    $(document).on('change', '.menu-item select, .menu-item textarea, .menu-item input[type="text"]', function() {
        var that = $('body #' + $(this).attr('id'));
        var value = $(this).val();
        var name = $(this).attr('data-name');
        if (value) {
            that.attr('name', name);
        } else {
            that.removeAttr('name');
        }
    });

    $(document).on('change', '.menu-item input[type="checkbox"]', function() {
        var that = $('body #' + $(this).attr('id'));
        var value = $(this).is(':checked');
        var name = $(this).attr('data-name');
        if (value) {
            that.attr('name', name);
        } else {
            that.removeAttr('name');
        }
    });

    $('#update-nav-menu').bind('click', function(e) {
        if ( e.target && e.target.className ) {
            if ( -1 != e.target.className.indexOf('item-delete') ) {
                var clickedEl = e.target;
                var itemID = parseInt(clickedEl.id.replace('delete-', ''), 10);
                var menu_item = $('#menu-item-' + itemID);
                var children = menu_item.childMenuItems();
                children.each(function() {
                    updatePortoMenuOptions($(this), -1);
                });
            }
        }
    });

    $( "#menu-to-edit" ).on( "sortstop", function( event, ui ) {
        var menu_item = ui.item;
        setTimeout(function() {
            updatePortoMenuOptions(menu_item, 0);
            var children = menu_item.childMenuItems();
            children.each(function() {
                updatePortoMenuOptions($(this), 0);
            });
        }, 200);
    } );

    // Remove import success values
    if ($('#redux-form-wrapper').length) {
        var $referer = $('#redux-form-wrapper input[name="_wp_http_referer"]');
        var value = $referer.val();
        value = value.replace('&import_success=true', '');
        value = value.replace('&import_masterslider_success=true', '');
        value = value.replace('&import_widget_success=true', '');
        value = value.replace('&import_options_success=true', '');
        value = value.replace('&compile_theme_success=true', '');
        value = value.replace('&compile_theme_rtl_success=true', '');
        value = value.replace('&compile_plugins_success=true', '');
        value = value.replace('&compile_plugins_rtl_success=true', '');
        $referer.val(value);
    }

    function alertLeavePage(e) {
        var dialogText = "Are you sure you want to leave?";
        e.returnValue = dialogText;
        return dialogText;
    }

    function addAlertLeavePage() {
        $('.porto-import-yes.btn-primary').attr('disabled', 'disabled');
        $('.mfp-bg, .mfp-wrap').unbind('click');
        $(window).bind('beforeunload', alertLeavePage);
    }

    function removeAlertLeavePage() {
        $('.porto-import-yes.btn-primary').removeAttr('disabled');
        $('.mfp-bg, .mfp-wrap').bind('click', function(e) {
            if ($(e.target).is('.mfp-wrap .mfp-content *')) {
                return;
            }
            e.preventDefault();
            $.magnificPopup.close();
        });
        $(window).unbind('beforeunload', alertLeavePage);
    }

    function showImportMessage(selected_demo, message, count, index) {
        var html = '';
        if (selected_demo) {
            html += '<h3 class="porto-demo-install"><i class="porto-ajax-loader"></i> Installing ' + $('#' + selected_demo).html() + '</h3>';
        }
        if (message) {
            html += '<strong>' + message + '</strong>';
        }
        if (count && index) {
            var percent = index / count * 100;
            if (percent > 100)
                percent = 100;
            html += '<div class="import-progress-bar"><div style="width:' + percent + '%;"></div></div>';
        }
        $('.porto-install-demo #import-status').stop().show().html(html);
    }

    $(window).load(function() {
        // filter demos
        if ($('#theme-install-demos').length) {
            var $install_demos = $('#theme-install-demos').isotope(),
                $demos_filter = $('.demo-sort-filters');

            $demos_filter.find('.sort-source li').click(function(e) {
                e.preventDefault();
                var $this = $(this),
                    filter = $this.data('filter-by');
                $install_demos.isotope({
                    filter: (filter == '*' ? filter : ('.' + filter))
                });
                $demos_filter.find('.sort-source li').removeClass('active');
                $this.addClass('active');
                return false;
            });
            $demos_filter.find('.sort-source li[data-active="true"]').click();
        }

        // porto studio
        if ($('.blocks-wrapper .blocks-list .block').length) {
            $('#vc_templates-editor-button, [data-vc-ui-element-target="[data-tab=porto_studio]"]').on('click', function(e) {
                if ($('.blocks-wrapper .blocks-list').hasClass('initialized')) {
                    return;
                }
                $('.blocks-wrapper .blocks-list').addClass('initialized');
                var $blocks = $('.blocks-wrapper .blocks-list').isotope(),
                    $blocks_filter = $('.blocks-wrapper .category-list ul');

                $blocks_filter.find('li > a').click(function(e) {
                    e.preventDefault();
                    var $this = $(this),
                        filter = $this.data('filter-by');
                    $blocks.isotope({
                        filter: (filter == '*' ? filter : ('.' + filter))
                    });
                    $blocks_filter.children().removeClass('active');
                    $this.parent().addClass('active');
                    return false;
                });
                setTimeout(function() {
                    $blocks.isotope('layout');
                }, 100);
            });

            $('.blocks-wrapper .blocks-list .import').on('click', function(e) {
                e.preventDefault();
                var $this = $(this),
                    block_id = $this.data('id');
                $this.attr('disabled', 'disabled');
                $this.closest('.block').addClass('importing');
                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    dataType: 'json',
                    data: { action: 'porto_studio_import', block_id: block_id },
                    success: function(response) {
                        $this.removeAttr('disabled');
                        $this.closest('.block').removeClass('importing');
                        if (response && response.content) {
                            vc.storage.append(response.content);
                            vc.shortcodes.fetch({
                                reset: !0
                            }), _.delay(function() {
                                window.vc.undoRedoApi.unlock();
                            }, 50);
                            $('.vc_ui-panel.vc_active .vc_ui-panel-header-controls [data-vc-ui-element="button-close"]').trigger('click');
                        } else if (response && response.error) {
                            alert(response.error);
                        }
                    }
                });
            });

            $('#porto-studio-editor-button').on('click', function(e) {
                e.preventDefault();
                $('#vc_templates-editor-button').trigger('click');
            });
        }
    });

    // cancel import button
    $('#porto-import-no').click(function() {
        $.magnificPopup.close();
        removeAlertLeavePage();
    });

    // import
    $('.porto-import-yes').click(function() {
        addAlertLeavePage();
        var demo = $('#porto-install-demo-type').val(),
            options = {
                demo: demo,
                reset_menus: $('#porto-reset-menus').is(':checked'),
                reset_widgets: $('#porto-reset-widgets').is(':checked'),
                import_dummy: $('#porto-import-dummy').is(':checked'),
                import_shortcodes: $('#porto-import-shortcodes').is(':checked'),
                import_widgets: $('#porto-import-widgets').is(':checked'),
                //import_sliders: $('#porto-import-sliders').is(':checked'),
                import_options: $('#porto-import-options').is(':checked'),
                import_icons: $('#porto-import-icons').is(':checked'),
                override_contents: $('#porto-override-contents').is(':checked')
            };
        if ($(this).hasClass('alternative')) {
            options.dummy_action = 'porto_import_dummy_step_by_step';
        } else {
            options.dummy_action = 'porto_import_dummy';
        }

        if (options.demo) {
            showImportMessage(demo, '');
            var data = {'action': 'porto_download_demo_file', 'demo': demo, 'wpnonce': porto_setup_wizard_params.wpnonce};
            $.post(ajaxurl, data, function(response) {
                try {
                    response = $.parseJSON(response);
                } catch (e) {}
                if (response && response.process && response.process == 'success') {
                    porto_import_options(options);
                } else if (response && response.process && response.process == 'error') {
                    porto_import_failed(demo, response.message);
                } else {
                    porto_import_failed(demo);
                }
            }).fail(function(response) {
                porto_import_failed(demo);
            });
        }
        $('#porto-install-options').slideUp();
    });

    // import options
    function porto_import_options(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.import_options) {
            var demo = options.demo,
                data = {'action': 'porto_import_options', 'demo': demo, 'wpnonce': porto_setup_wizard_params.wpnonce};

            showImportMessage(demo, 'Importing theme options');

            $.post(ajaxurl, data, function(response) {
                if (response) showImportMessage(demo, response);
                porto_reset_menus(options);
            }).fail(function(response) {
                porto_reset_menus(options);
            });
        } else {
            porto_reset_menus(options);
        }
    }

    // reset_menus
    function porto_reset_menus(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.reset_menus) {
            var demo = options.demo,
                data = {'action': 'porto_reset_menus', 'import_shortcodes': options.import_shortcodes, 'wpnonce': porto_setup_wizard_params.wpnonce};

            $.post(ajaxurl, data, function(response) {
                if (response) showImportMessage(demo, response);
                porto_reset_widgets(options);
            }).fail(function(response) {
                porto_reset_widgets(options);
            });
        } else {
            porto_reset_widgets(options);
        }
    }

    // reset widgets
    function porto_reset_widgets(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.reset_widgets) {
            var demo = options.demo,
                data = {'action': 'porto_reset_widgets', 'wpnonce': porto_setup_wizard_params.wpnonce};

            $.post(ajaxurl, data, function(response) {
                if (response) showImportMessage(demo, response);
                porto_import_dummy(options);
            }).fail(function(response) {
                porto_import_dummy(options);
            });
        } else {
            porto_import_dummy(options);
        }
    }

    // import dummy content
    var dummy_index = 0, dummy_count = 0, dummy_process = 'import_start';
    function porto_import_dummy(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.import_dummy) {
            var demo = options.demo,
                data = {'action': options.dummy_action, 'process':'import_start', 'demo': demo, 'override_contents': options.override_contents, 'wpnonce': porto_setup_wizard_params.wpnonce};
            dummy_index = 0;
            dummy_count = 0;
            dummy_process = 'import_start';
            porto_import_dummy_process(options, data);
            showImportMessage(demo, 'Importing posts');
        } else {
            porto_import_widgets(options);
        }
    }

    // import dummy content process
    function porto_import_dummy_process(options, args) {
        var demo = options.demo;
        $.post(ajaxurl, args, function(response) {
            if (response && /^[\],:{}\s]*$/.test(response.replace(/\\["\\\/bfnrtu]/g, '@').
                replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
                replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                response = $.parseJSON(response);
                if (response.process != 'complete') {
                    var requests = {'action': args.action, 'wpnonce': porto_setup_wizard_params.wpnonce};
                    if (response.process) requests.process = response.process;
                    if (response.index) requests.index = response.index;

                    requests.demo = demo;
                    requests.override_contents = options.override_contents;
                    porto_import_dummy_process(options, requests);

                    dummy_index = response.index;
                    dummy_count = response.count;
                    dummy_process = response.process;

                    showImportMessage(demo, response.message, dummy_count, dummy_index);
                } else {
                    showImportMessage(demo, response.message);
                    porto_import_revsliders(options);
                }
            } else {
                porto_import_failed(demo);
            }
        }).fail(function(response) {
            if (args.action == 'porto_import_dummy') {
                porto_import_failed(demo);
            } else {
                var requests;
                if (dummy_index < dummy_count) {
                    requests = {'action': args.action, 'wpnonce': porto_setup_wizard_params.wpnonce};
                    requests.process = dummy_process;
                    requests.index = ++dummy_index;
                    requests.demo = demo;

                    porto_import_dummy_process(options, requests);
                } else {
                    requests = {'action': args.action, 'wpnonce': porto_setup_wizard_params.wpnonce};
                    requests.process = dummy_process;
                    requests.demo = demo;

                    porto_import_dummy_process(options, requests);
                }
            }
        });
    }

    // import rev sliders
    function porto_import_revsliders(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.import_dummy) {
            var demo = options.demo,
                data = {'action': 'porto_import_revsliders', 'demo': demo, 'wpnonce': porto_setup_wizard_params.wpnonce};
            if (options.import_options) {
                data.import_options_too = 'true';
            }

            $.post(ajaxurl, data, function(response) {
                if (response) showImportMessage(demo, response);
                porto_import_widgets(options);
            }).fail(function(response) {
                porto_import_widgets(options);
            });
        } else {
            porto_import_widgets(options);
        }
    }

    // import widgets
    function porto_import_widgets(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.import_widgets) {
            var demo = options.demo,
                data = {'action': 'porto_import_widgets', 'demo': demo, 'wpnonce': porto_setup_wizard_params.wpnonce};

            showImportMessage(demo, 'Importing widgets');

            $.post(ajaxurl, data, function(response) {
                if (response) showImportMessage(demo, response);
                porto_import_icons(options);
            }).fail(function(response) {
                porto_import_icons(options);
            });
        } else {
            porto_import_icons(options);
        }
    }

    // import icons
    function porto_import_icons(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.import_icons) {
            var demo = options.demo,
                data = {'action': 'porto_import_icons', 'wpnonce': porto_setup_wizard_params.wpnonce};

            showImportMessage(demo, 'Importing icons');

            $.post(ajaxurl, data, function(response) {
                if (response) showImportMessage(demo, response);
                porto_import_shortcodes(options);
            }).fail(function(response) {
                porto_import_shortcodes(options);
            });
        } else {
            porto_import_shortcodes(options);
        }
    }

    // import shortcode pages
    function porto_import_shortcodes(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.import_shortcodes) {
            var demo = options.demo,
                data = {'action': options.dummy_action, 'process':'import_start', 'demo': 'shortcodes', 'wpnonce': porto_setup_wizard_params.wpnonce};

            dummy_index = 0;
            dummy_count = 0;
            dummy_process = 'import_start';
            var data_download = {'action': 'porto_download_demo_file', 'demo': demo, 'wpnonce': porto_setup_wizard_params.wpnonce};
            $.post(ajaxurl, data_download, function(response) {
                try {
                    response = $.parseJSON(response);
                } catch (e) {}
                if (response && response.process && response.process == 'success') {
                    porto_import_shortcodes_process(options, data);
                } else if (response && response.process && response.process == 'error') {
                    porto_import_failed(demo, response.message);
                } else {
                    porto_import_failed(demo);
                }
            }).fail(function(response) {
                porto_import_failed(demo);
            });
        } else {
            porto_import_finished(options);
        }
    }

    function porto_import_shortcodes_process(options, args) {
        var demo = options.demo;
        $.post(ajaxurl, args, function(response) {
            if (response && /^[\],:{}\s]*$/.test(response.replace(/\\["\\\/bfnrtu]/g, '@').
                replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
                replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                response = $.parseJSON(response);
                if (response.process != 'complete') {
                    var requests = {'action': args.action, 'wpnonce': porto_setup_wizard_params.wpnonce};
                    if (response.process) requests.process = response.process;
                    if (response.index) requests.index = response.index;

                    requests.demo = 'shortcodes';
                    porto_import_shortcodes_process(options, requests);

                    dummy_index = response.index;
                    dummy_count = response.count;
                    dummy_process = response.process;

                    showImportMessage(demo, "Importing element pages");
                } else {
                    porto_import_finished(options);
                }
            } else {
                porto_delete_tmp_dir('shortcodes');
                porto_import_failed(demo);
            }
        }).fail(function(response) {
            if (args.action == 'porto_import_dummy') {
                porto_import_failed(demo);
            } else {
                var requests;
                if (dummy_index < dummy_count) {
                    requests = {'action': args.action, 'wpnonce': porto_setup_wizard_params.wpnonce};
                    requests.process = dummy_process;
                    requests.index = ++dummy_index;
                    requests.demo = 'shortcodes';

                    porto_import_shortcodes_process(options, requests);
                } else {
                    requests = {'action': args.action, 'wpnonce': porto_setup_wizard_params.wpnonce};
                    requests.process = dummy_process;
                    requests.demo = 'shortcodes';

                    porto_import_shortcodes_process(options, requests);
                }
            }
        });
    }

    function porto_delete_tmp_dir(demo) {
        var data = {'action': 'porto_delete_tmp_dir', 'demo': demo, 'wpnonce': porto_setup_wizard_params.wpnonce};
        $.post(ajaxurl, data, function(response) {
        });
    }

    function porto_import_failed(demo, message) {
        porto_delete_tmp_dir(demo);
        if (typeof message == 'undefined') {
            showImportMessage(demo, 'Failed importing! Please check the <a href="' + window.location.href.replace('?page=porto-demos', '?page=porto-system') + '" target="_blank">"System Status"</a> tab to ensure your server meets all requirements for a successful import. Settings that need attention will be listed in red. If your server provider does not allow to update settings, please try using alternative import mode.');
        } else {
            showImportMessage(demo, message);
        }
        removeAlertLeavePage();
        jQuery('.porto-install-demo .porto-demo-install .porto-ajax-loader').remove();
        jQuery('.porto-install-demo .porto-demo-install').html(jQuery("#porto-install-options .theme-name").text() + ' installation is failed!');
        jQuery('.porto-install-demo .porto-demo-install').css('padding-left', 0);
        jQuery('#porto-install-options').show();
    }

    // import finished
    function porto_import_finished(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        var demo = options.demo;
        porto_delete_tmp_dir(demo);
        setTimeout(function() {
            if (jQuery('#wp-admin-bar-view-site').length) {
                showImportMessage(demo, '<a href="'+ jQuery('#wp-admin-bar-view-site a').attr('href') +'" target="_blank">Visit your site.</a>');
            } else if (jQuery('#current_site_url').length) {
                showImportMessage(demo, '<a href="'+ jQuery('#current_site_url').val() +'" target="_blank">Visit your site.</a>');
            } else {
                showImportMessage(demo, '');
            }
            jQuery('.porto-install-demo .porto-demo-install .porto-ajax-loader').remove();
            jQuery('.porto-install-demo .porto-demo-install').html($('#' + demo).html() + ' installation is finished!');
            jQuery('.porto-install-demo .porto-demo-install').css('padding-left', 0);
            removeAlertLeavePage();
        }, 3000);
    }

    if ( jQuery( 'body' ).hasClass( 'porto_page_porto-plugins' ) ) {
        var $confirm;
        jQuery( '.porto-install-plugins .theme-actions .button-primary.disabled' ).on( 'click', function( e ) {
            e.preventDefault();
            $confirm = window.alert( 'ERROR:\n\nThis plugin can only be installed or updated, after you have successfully completed the Porto Registration on the "Registration" tab.' );
        });
    }

    jQuery('body').on('click', '.button-load-plugins', function(e) {
        e.preventDefault();
        jQuery(this).closest('.porto-setup-wizard-plugins').children('.hidden').hide();
        jQuery(this).closest('.porto-setup-wizard-plugins').children('.hidden').fadeIn();
        jQuery(this).closest('.porto-setup-wizard-plugins').children('.hidden').removeClass('hidden');
        jQuery(this).hide();
    });

    // import demos
    jQuery(document).on('click', '.porto-install-demos .theme .theme-wrapper', function(e) {
        e.preventDefault();
        if (jQuery(this).closest('.theme').hasClass('open-classic')) {
            jQuery(this).closest('.porto-install-demos').find('.demo-sort-filters [data-filter-by="classic"] a').click();
        } else if (jQuery(this).closest('.theme').hasClass('open-shop')) {
            jQuery(this).closest('.porto-install-demos').find('.demo-sort-filters [data-filter-by="shop"] a').click();
        } else if (jQuery(this).closest('.theme').hasClass('open-blog')) {
            jQuery(this).closest('.porto-install-demos').find('.demo-sort-filters [data-filter-by="blog"] a').click();
        } else {
            jQuery('#porto-install-options').show();
            jQuery(this).closest('.porto-install-demos').find('.porto-install-demo .theme-img').html(jQuery(this).find('.theme-screenshot').children().clone());
            jQuery(this).closest('.porto-install-demos').find('.porto-install-demo .theme-name').html(jQuery(this).find('.theme-name').text());
            jQuery(this).closest('.porto-install-demos').find('.porto-install-demo .live-site').attr('href', jQuery(this).find('.theme-name').data('live-url'));
            jQuery(this).closest('.porto-install-demos').find('.porto-install-demo .more-options').removeClass('opened');
            jQuery(this).closest('.porto-install-demos').find('.porto-install-demo .porto-install-options-section').hide();
            jQuery(this).closest('.porto-install-demos').find('.porto-install-demo .plugins-used').remove();
            jQuery('#porto-install-demo-type').val(jQuery(this).find('.theme-name').attr('id'));
            if (jQuery(this).find('.plugins-used').length) {
                jQuery(this).find('.plugins-used').clone().insertAfter(jQuery(this).closest('.porto-install-demos').find('.porto-install-section'));
                jQuery(this).closest('.porto-install-demos').find('.porto-install-demo .porto-install-section').hide();
                jQuery(this).closest('.porto-install-demos').find('.porto-install-demo .more-options').hide();
            } else {
                jQuery(this).closest('.porto-install-demos').find('.porto-install-demo .porto-install-section').show();
                jQuery(this).closest('.porto-install-demos').find('.porto-install-demo .more-options').show();
            }
            if (jQuery('.porto-import-yes:not(:disabled)').length) {
                jQuery('.porto-install-demo #import-status').html('');
            }
            jQuery.magnificPopup.open({
                items: {
                    src: '.porto-install-demo'
                },
                type: 'inline',
                mainClass: 'mfp-with-zoom',
                zoom: {
                    enabled: true,
                    duration: 300
                }
            });
        }
    });
    jQuery('.porto-install-demo .more-options').click(function(e) {
        e.preventDefault();
        jQuery(this).toggleClass('opened');
        jQuery(this).closest('.porto-install-demo').find('.porto-install-options-section').stop().toggle('slide');
    });

    // init theme options
    jQuery(document).ready(function($) {
        function portoAdminLazyLoadImages(element) {
            var $element = $(element);
            if ( $element.hasClass( 'lazy-load-active' ) ) return;
            var src = $element.data( 'original' );
            if ( src ) $element.attr( 'src', src );
            $element.addClass('lazy-load-active');
        }
        $('.redux-container .redux-group-tab:visible').find('.redux-image-select [data-original]').each(function(index, element) {
            if ($.fn.waypoint) {
                $(element).waypoint(function (direction) {
                  portoAdminLazyLoadImages(element);
                }, { offset: '140%' });
            } else {
                portoAdminLazyLoadImages(element);
            }
        });
        $('.redux-group-tab-link-a').click(function() {
            if (typeof $(this).data('rel') == 'undefined') {
                return;
            }
            var id = $(this).data( 'rel' ) + '_section_group';
            if (!$('#' + id).length) {
                return;
            }
            $('#' + id).find('.redux-image-select [data-original]').each(function(index, element) {
                if ($.fn.waypoint) {
                    $(element).waypoint(function (direction) {
                      portoAdminLazyLoadImages(element);
                    }, { offset: '140%' });
                } else {
                    portoAdminLazyLoadImages(element);
                }
            });
        });

        // header type make 2 columns
        if (!$('#customize-controls').length) {
            $('#porto_settings-header-type ul.redux-image-select').append('<li class="header-types-split classic-demos"></li><li class="header-types-split ecommerce-demos"></li>');
            $('#porto_settings-header-type ul.redux-image-select').children().each(function(index) {
                var $this = $(this);
                if ($this.hasClass('classic-demos') || $this.hasClass('ecommerce-demos')) {
                    return;
                }
                if (index < 8) {
                    $this.appendTo($this.closest('ul.redux-image-select').children('.classic-demos'));
                } else {
                    $this.appendTo($this.closest('ul.redux-image-select').children('.ecommerce-demos'));
                }
            });
        }
    });

    jQuery(document).ready(function($) {
        // images / colors swatch
        $('#porto_swatches').on('change', 'select.swatch_option_type', function () {
            var $parent = $(this).closest('.porto_swatches_section');
            $parent.find('[class*="swatch_field_"]').hide();
            $parent.find('.swatch_field_' + $(this).val()).show();
        });


        var _custom_media = false,
            _orig_send_attachment = wp.media.editor.send.attachment;

        $('#porto_swatches').on('click', '.remove_swatch_image_button', function (e) {
            e.preventDefault();

            $(this).parent().find('.upload_image_id').val('');
            $(this).closest('td').find('img').attr('src', porto_swatches_params.placeholder_src);
        });

        var frame = null;

        $('#porto_swatches').on('click', '.upload_swatch_image_button', function (e) {
            e.preventDefault();
            var $button = $(this);

            if (frame) {
                frame.porto_swatches_btn = $button;
                frame.open();
                return;
            }

            frame = wp.media({
                title: 'Select or Upload an Image',
                button: {
                    text: 'Use this media'
                },
                multiple: false
            });

            frame.porto_swatches_btn = $button;

            frame.on('select', function () {
                if (frame.porto_swatches_btn) {
                    var attachment = frame.state().get('selection').first().toJSON(),
                        $input = frame.porto_swatches_btn.parent().find('.upload_image_id');
                    $input.val(attachment.id);
                    frame.porto_swatches_btn.closest('td').find('img').attr('src', attachment.url);
                }
            });

            frame.open();
            return false;
        });

        $('#woocommerce-product-data').on('woocommerce_variations_loaded', function() {
            var wrapper = $('#porto_swatches');
            if (!wrapper.length) {
                return;
            }
            wrapper.block({
                message: null,
                overlayCSS: {
                    opacity: 0.1
                }
            });
            $.ajax({
                url: porto_swatches_params.ajax_url,
                data: {
                    action:     'porto_load_swatches',
                    wpnonce:   porto_swatches_params.wpnonce,
                    product_id: porto_swatches_params.post_id
                },
                type: 'POST',
                success: function( response ) {
                    wrapper.empty().append( response );
                    wrapper.find('.porto-meta-color').each(function() {
                        $(this).trigger('plugin_init');
                    });
                    $('.woocommerce-help-tip', wrapper).tipTip({
                        'attribute': 'data-tip',
                        'fadeIn':    50,
                        'fadeOut':   50,
                        'delay':     200
                    });
                }
            });
        });


        // switch theme options panel
        $('body').on('click', '.switch-live-option-panel', function(e) {
            e.preventDefault();
            if ($(this).hasClass('disabled')) {
                return false;
            }
            var type = $(this).hasClass('porto-theme-link') ? 'customizer' : 'redux';
            $(this).attr('disabled', 'disabled').addClass('disabled');
            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {action: 'porto_switch_theme_options_panel', type: type },
                success: function() {
                    if ('customizer' == type) {
                        window.location.href = ajaxurl.replace('admin-ajax.php', 'customize.php');
                    } else {
                        window.location.href = ajaxurl.replace('admin-ajax.php', 'themes.php?page=porto_settings');
                    }
                }
            });
        });

        // set default options for header types
        if (typeof js_porto_admin_vars != 'undefined' && typeof js_porto_admin_vars.header_default_options != 'undefined') {
            js_porto_admin_vars.header_default_options = $.parseJSON(js_porto_admin_vars.header_default_options);
            $('#porto_settings-header-type input[type="radio"]').on('change', function() {
                var selected_header = $('#porto_settings-header-type input[type="radio"]:checked').val();
                $.each(js_porto_admin_vars.header_default_options, function(key, default_options) {
                    if ($.inArray(selected_header, key.replace(/ /g, '').split(',')) != -1) {
                        $.each(default_options, function(name, value) {
                            if ($('#porto_settings-' + name).length) {
                                if (value) {
                                    $('#porto_settings-' + name).find('input[value="' + value + '"]').trigger('click');
                                } else {
                                    $('#porto_settings-' + name).find('input[value]:first-child').trigger('click');
                                }
                            }
                        });
                        return false;
                    }
                });
            });
        }

        $('body').on('change', '[data-vc-shortcode="porto_blog"] select[name="post_layout"]', function(e) {
            var $trigger = $('[data-vc-shortcode="porto_blog"] select[name="post_style"]');
            if (!$trigger.length || $trigger.is(':hidden')) {
                return;
            }
            $trigger.children().removeAttr('disabled');
            if ( 'creative' == $(this).val() ) {
                $trigger.children().attr('disabled', 'disabled');
                $trigger.children('.default, .hover_info, .hover_info2').removeAttr('disabled');
            } else if ( 'timeline' == $(this).val() || 'masonry-creative' == $(this).val() ) {
                $trigger.children('.grid, .list, .widget').attr('disabled', 'disabled');
            }
        });
    });

});
