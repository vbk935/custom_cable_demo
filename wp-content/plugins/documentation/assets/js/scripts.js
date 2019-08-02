/**
 * JavaScript for Bootstrap's docs (http://getbootstrap.com)
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under the Creative Commons Attribution 3.0 Unported License. For
 * details, see http://creativecommons.org/licenses/by/3.0/.
 */
(function($) {
    "use strict";

    $(document).ready(function() {

        var hash = location.hash || null;

        if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
            var b = document.createElement("style");
            b.appendChild(document.createTextNode("@-ms-viewport{width:auto!important}")), document.querySelector("head").appendChild(b)
        }
        var c = $(window),
            d = $(document.body);
        $(".navbar").outerHeight(!0) + 10;
        d.scrollspy({
            target: ".bs-docs-sidebar"
        });
        c.on("load", function() {
            d.scrollspy("refresh");
        });
        $(".bs-docs-container [href=#]").click(function(a) {
            a.preventDefault()
        });

        function sidebaraffix() {
            var b = $(".bs-docs-sidebar");
            b.affix({
                offset: {
                    top: function() {
                        var c = b.offset().top,
                            d = parseInt(b.children(0).css("margin-top"), 10),
                            e = $(".bs-docs-nav").height();
                        return this.top = c - e - d
                    },
                    bottom: function() {
                        return this.bottom = $(".bs-docs-footer").outerHeight(!0)
                    }
                }
            });
        }

        setTimeout(sidebaraffix, 100);



        if (hash) {
            location.hash = hash;
        }

        $(window).on("resize", function(e) {
            var b = $(".bs-docs-sidebar");
            b.removeClass('affix affix-top affix-bottom');
            b.removeData("bs.affix");
            $(window).off(".affix");
            sidebaraffix();
        })

    });
})(jQuery);