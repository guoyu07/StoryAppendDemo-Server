/*
 Plugin: jQuery Parallax
 Version 1.1.3
 Author: Ian Lunn
 Twitter: @IanLunn
 Author URL: http://www.ianlunn.co.uk/
 Plugin URL: http://www.ianlunn.co.uk/plugins/jquery-parallax/

 Dual licensed under the MIT and GPL licenses:
 http://www.opensource.org/licenses/mit-license.php
 http://www.gnu.org/licenses/gpl.html
 */

(function ($) {
    var $window = $(window);
    var windowHeight = $window.height();

    $window.resize(function () {
        windowHeight = $window.height();
    });

    $.fn.enableParallax=function(flag){
        $(this).each(function () {
            $(this).data('enableParallax',flag);
        });
        return $(this);
    }

    $.fn.parallax = function (xpos, speedFactor, offsetTop, trigger) {
        var $this = $(this);
        var getHeight;
        var firstTop;
        var paddingTop = 0;
        if($this.data('enableParallax')===undefined) {
            $this.data('enableParallax', true);
        }
        //get the starting position of each element to have parallax applied to it
        $this.each(function () {
            firstTop = $this.offset().top;
        });
        var outerHeight = true;
        if (outerHeight) {
            getHeight = function (jqo) {
                return jqo.outerHeight(true);
            };
        } else {
            getHeight = function (jqo) {
                return jqo.height();
            };
        }

        // setup defaults if arguments aren't specified
        if (arguments.length < 1 || xpos === null) xpos = "50%";
        if (arguments.length < 2 || speedFactor === null) speedFactor = 0.1;
        if (arguments.length < 3 || outerHeight === null) outerHeight = true;
        if(arguments.length==1&&xpos>0){
            $this.data('speedFactor',xpos);
            return;
        }
        $this.data('speedFactor',speedFactor);

        // function to be called whenever the window is scrolled or resized
        var lastPos = $window.scrollTop();

        var triggerState = 0;
        function update() {
            var pos = $window.scrollTop();

            $this.each(function () {
                var $element = $(this);
                var top = $element.offset().top;
                var height = getHeight($element);
                var speedFactor=$element.data('speedFactor');
                // Check if totally above or totally below viewport
                if (top + height < pos || top > pos + windowHeight) {
                    return;
                }
                var offset = Math.round((firstTop - pos) * speedFactor);

                if (trigger) {
                    if(offset>trigger.enterPoint&&triggerState!=1){
                        trigger.outer();
                        triggerState=1;
                    }
                    else if(offset<=trigger.enterPoint&&offset>trigger.leavePoint&&triggerState!=2){
                        trigger.enter();
                        triggerState=2;
                    }
                    else if(offset<=trigger.leavePoint&&triggerState!=3){
                        trigger.leave();
                        triggerState=3;
                    }
                }

                if($this.data('enableParallax')) {
                    $this.css('backgroundPosition', xpos + " " + ((offsetTop || 0) + offset) + "px");
                }
            });
        }

        $window.bind('scroll', update).resize(update);
        update();
        return $this;
    };
})(jQuery);
