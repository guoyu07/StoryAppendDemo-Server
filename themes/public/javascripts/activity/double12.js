var Double12 = avalon.define("double12Ctrl", function(vm) {
    vm.data = {};
    vm.local = {
        timer           : {
            'hours'   : '00',
            'minutes' : '00',
            'seconds' : '00'
        },
        activity_status : ''
    };

    vm.DataInitializer = {
        'get1212Data'    : function() {
            $.ajax({
                url      : "activity/double12data",
                dataType : "json",
                success  : function(res) {
                    if(res.code == 200) {
                        var data = res.data;
                        data.groups = Double12.DataInitializer.reformProducts(data.groups, data.status);
                        Double12.data = data;
                        PageInitializer.initPage(data);
                    } else {
                        alert(res.msg);
                    }
                }
            });
        },
        'reformProducts' : function(groups, status) {
            for(var i = 0; i < groups.length; i++) {
                for(var j = 0; j < groups[i].products.length; j++) {
                    if(status == 2) {
                        groups[i].products[j].buy_info = '准备开抢';
                        groups[i].products[j]['class'] = 'ready';
                    } else if(status == 3) {
                        if(groups[i].products[j].stock_info.current_stock_num > 0) {
                            groups[i].products[j].buy_info = '立即秒杀';
                            groups[i].products[j]['class'] = 'on';
                        } else {
                            groups[i].products[j].buy_info = '已售罄';
                            groups[i].products[j]['class'] = 'disable';
                            groups[i].products[j].link_url = 'javascript:;'
                        }
                    } else {
                        groups[i].products[j].buy_info = '秒杀结束';
                        groups[i].products[j]['class'] = 'disable';
                        groups[i].products[j].link_url = 'javascript:;'
                    }
                }
            }
            return groups;
        }
    };

    var PageInitializer = {
        'initPage'       : function(data) {
            Timer.initTimer(data.countdown);
            Timer.getActivityStatus(data.status);
        },
        'toggleNavFixed' : function($nav, $nav_scrollTop) {
            var scrollTop = $(document).scrollTop();
            if(scrollTop >= $nav_scrollTop) {
                $nav.addClass('fixed');
            } else {
                $nav.removeClass('fixed');
            }
        }
    };

    vm.renderCallback = function() {
        // 滚动监听
        var $nav = $('.activity-nav');
        var $nav_scrollTop = $nav.offset().top;
        $(window).on('scroll', function() {
            // 导航栏 fixed判断
            PageInitializer.toggleNavFixed($nav, $nav_scrollTop);
            // 底部邮箱记录
            if(document.body.scrollTop > 800 &&
               document.body.scrollTop < (document.body.scrollHeight - window.innerHeight - 800)) {
                $('.bottom-fixed-bar').fadeIn(600);
            } else {
                $('.bottom-fixed-bar').fadeOut(200);
            }
        })

        // init TAB 组件
        var $hi_nav = $('.hi-nav');
        if($hi_nav) {
            $.each($hi_nav, function(i, val) {
                new HINav($hi_nav[i]);
            });
        }

        // 查看活动规则
        $(document).on('click', '.activity-rule-btn', function() {
            $('html, body').animate({ 'scrollTop' : $('.activity-footer').offset().top - 50 });
        })


        // 关闭遮罩
        $('.loading-mask').hide();
    };

    var Timer = {
        'TimerCache'         : {
            'hours'   : 0,
            'minutes' : 0,
            'seconds' : 0
        },
        'timer_interval'     : {},
        'initTimer'          : function(countdown) {
            if(countdown <= 0) {

            } else {
                Timer.TimerCache = {
                    'hours'   : Math.floor(countdown / 3600),
                    'minutes' : Math.floor(countdown % 3600 / 60),
                    'seconds' : countdown % 60
                }
                Timer.runTimer();
                Timer.timer_interval = setInterval(function() {
                    Timer.runTimer();
                }, 1000);
            }
        },
        'runTimer'           : function() {
            Timer.TimerCache.seconds--;
            if(Timer.TimerCache.seconds == -1) {
                Timer.TimerCache.minutes--;
                Timer.TimerCache.seconds = 59;
            }
            if(Timer.TimerCache.minutes == -1) {
                Timer.TimerCache.hours--;
                Timer.TimerCache.seconds = 59;
                Timer.TimerCache.minutes = 59;
            }
            if(Timer.TimerCache.hours == -1) {
                $('html, body').animate({'scrollTop' : $('.timer-title').offset().top}, 300);
                clearInterval(Timer.timer_interval);
                Double12.DataInitializer.get1212Data();
                return;
            }
            Timer.updateTimerDisplay();
        },
        'updateTimerDisplay' : function() {
            var tempHours = "00" + Timer.TimerCache.hours;
            var tempMinutes = "00" + Timer.TimerCache.minutes;
            var tempSeconds = "00" + Timer.TimerCache.seconds;
            if(Timer.TimerCache.hours >= 100) {
                Double12.local.timer = {
                    'hours'   : tempHours.substring(tempHours.length - 3, tempHours.length),
                    'minutes' : tempMinutes.substring(tempMinutes.length - 2, tempMinutes.length),
                    'seconds' : tempSeconds.substring(tempSeconds.length - 2, tempSeconds.length),
                };
            } else {
                Double12.local.timer = {
                    'hours'   : tempHours.substring(tempHours.length - 2, tempHours.length),
                    'minutes' : tempMinutes.substring(tempMinutes.length - 2, tempMinutes.length),
                    'seconds' : tempSeconds.substring(tempSeconds.length - 2, tempSeconds.length),
                };
            }
        },
        'getActivityStatus'  : function(status) {
            switch(status) {
                case 2 :
                    Double12.local.activity_status = '距开始还有';
                    break;
                case 3 :
                    Double12.local.activity_status = '秒杀进行中！离结束还有';
                    break;
                case 4 :
                    Double12.local.activity_status = '秒杀已结束 下期秒杀 敬请期待';
                    break;
            }
        }
    }


});

$(function() {
    Double12.DataInitializer.get1212Data();

    $('.bottom-fixed-bar .close-btn').click(function() {
        $('.bottom-fixed-bar').fadeOut(200).remove();
        ;
    })
    $('#submitEmail').on('click', function() {
        var email = $('#email').val();
        if(email) {
            if(/^([\w\d]+[-\w\d.]*@[-\w\d.]+\.[a-zA-Z]{2,10}|1[358]\d{9})$/.test(email)) {
                $.ajax({
                    url     : 'activity/subscribe/email/' + email,
                    success : function() {
                        $('.email-holder').hide();
                        $('.lcd').show();
                    }
                });
            } else {
                alert('邮箱格式不对');
            }
        }
    });
});
