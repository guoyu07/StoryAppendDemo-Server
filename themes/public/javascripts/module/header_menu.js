/**
 * Created by zhifan on 15/1/19.
 * plugin address :https://github.com/kamens/jQuery-menu-aim
 */
(function($) {
    $.fn.menuAim = function(opts) {
        this.each(function() {
            init.call(this, opts);
        });
        return this;
    };

    function init(opts) {
        var $menu = $(this),
            activeRow = null,
            mouseLocs = [],
            lastDelayLoc = null,
            timeoutId = null,
            options = $.extend({
                rowSelector     : "> li",
                submenuSelector : "*",
                tolerance       : 75,  // bigger = more forgivey when entering submenu
                enter           : $.noop,
                exit            : $.noop,
                activate        : $.noop,
                deactivate      : $.noop,
                exitMenu        : $.noop
            }, opts);

        var MOUSE_LOCS_TRACKED = 3,  // number of past mouse locations to track
            DELAY = 200;  // ms delay when user appears to be entering submenu

        var mousemoveDocument = function(e) {
            mouseLocs.push({x : e.pageX, y : e.pageY});

            if(mouseLocs.length > MOUSE_LOCS_TRACKED) {
                mouseLocs.shift();
            }
        };

        var mouseleaveMenu = function() {
            if(timeoutId) {
                clearTimeout(timeoutId);
            }
            options.exitMenu(this);
            if(activeRow) {
                options.deactivate(activeRow);
            }
            activeRow = null;
        };

        var mouseenterRow = function() {
                if(timeoutId) {
                    clearTimeout(timeoutId);
                }
                options.enter(this);
                possiblyActivate(this);
            },
            mouseleaveRow = function() {
                options.exit(this);
            };

        var clickRow = function() {
            activate(this);
        };

        var activate = function(row) {
            if(row == activeRow) {
                return;
            }
            if(activeRow) {
                options.deactivate(activeRow);
            }
            options.activate(row);
            activeRow = row;
        };

        var possiblyActivate = function(row) {
            var delay = activationDelay();

            if(delay) {
                timeoutId = setTimeout(function() {
                    possiblyActivate(row);
                }, delay);
            } else {
                activate(row);
            }
        };

        var activationDelay = function() {
            if(!activeRow || !$(activeRow).is(options.submenuSelector)) {
                return 0;
            }

            var offset = $menu.offset(),
                upperLeft = {
                    x : offset.left,
                    y : offset.top - options.tolerance
                },
                upperRight = {
                    x : offset.left + $menu.outerWidth(),
                    y : upperLeft.y
                },
                lowerLeft = {
                    x : offset.left,
                    y : offset.top + $menu.outerHeight() + options.tolerance
                },
                lowerRight = {
                    x : offset.left + $menu.outerWidth(),
                    y : lowerLeft.y
                },
                loc = mouseLocs[mouseLocs.length - 1],
                prevLoc = mouseLocs[0];

            if(!loc) {
                return 0;
            }

            if(!prevLoc) {
                prevLoc = loc;
            }

            if(prevLoc.x < offset.left || prevLoc.x > lowerRight.x ||
               prevLoc.y < offset.top || prevLoc.y > lowerRight.y) {
                return 0;
            }

            if(lastDelayLoc &&
               loc.x == lastDelayLoc.x && loc.y == lastDelayLoc.y) {
                return 0;
            }

            function slope(a, b) {
                return (b.y - a.y) / (b.x - a.x);
            }

            var decreasingCorner = upperRight,
                increasingCorner = lowerRight;

            var decreasingSlope = slope(loc, decreasingCorner),
                increasingSlope = slope(loc, increasingCorner),
                prevDecreasingSlope = slope(prevLoc, decreasingCorner),
                prevIncreasingSlope = slope(prevLoc, increasingCorner);

            if(decreasingSlope < prevDecreasingSlope &&
               increasingSlope > prevIncreasingSlope) {
                lastDelayLoc = loc;
                return DELAY;
            }

            lastDelayLoc = null;
            return 0;
        };

        $menu
            .mouseleave(mouseleaveMenu)
            .find(options.rowSelector)
            .mouseenter(mouseenterRow)
            .mouseleave(mouseleaveRow)
            .click(clickRow);

        //只在洲的那一列移动计算 减少需要计算区域
        $menu.find(options.rowSelector).children(':first-child').mousemove(mousemoveDocument);
    }
})(jQuery);

$(function() {
    var $menu = $('.navigate-overlay');
    $menu.menuAim({
        activate   : activateSubmenu,
        deactivate : deactivateSubmenu
    });

    function activateSubmenu(row) {
        var $row = $(row),
            submenuId = $row.data("submenuId"),
            $submenu = $("#" + submenuId),
            height = $menu.outerHeight(),
            width = $menu.outerWidth();

        $submenu.css({
            'display' : "block",
            'min-height' : height
        });
        if(!$row.hasClass("maintain-hover")) {
            $row.addClass("maintain-hover");
        }
    }

    function deactivateSubmenu(row) {
        var $row = $(row),
            submenuId = $row.data("submenuId"),
            $submenu = $("#" + submenuId);
        $submenu.css("display", "none");
        $row.removeClass("maintain-hover");
    }

    $('.navigate-ctn')
        .hover(function() {
            $menu
                .css('display', 'block')
                .children(':first-child')
                .trigger('click')
                .addClass("maintain-hover");
        }, function() {
            $menu.css('display', 'none');
            $(".maintain-hover").removeClass("maintain-hover");
        });

    $(document)
        .on('click', '.account-menu', function() {
            $(this).find('.action-list').toggleClass('active');
        });
});
