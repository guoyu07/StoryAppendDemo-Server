<div class="main-wrap">
    <div class="header"></div>
    <div id="box1" class="box">
        <div class="title"></div>
    </div>
    <div id="box2" class="box">
        <div class="title"></div>
        <div class="content"></div>
    </div>
    <div id="box3" class="box">
        <div class="left"></div>
        <div class="right"></div>
    </div>
    <div id="box4" class="box">
        <div class="title"></div>
        <div class="content"></div>
    </div>
    <div class="main-content">
        <div class="benefit-ctn clearfix">
            <div class="heart-wrap">
                <div class="heart-block">
                    <div class="heart-block-img"></div>
                    <div class="heart-block-title"><i class="icon-thin-heart"></i>安心 +</div>
                    <div class="heart-block-text">玩途Hotel+，为您提供最完备的服务，更有24小时当地管家全程服务，赠送百万保额保险更无后顾之忧。Hotel+，“加”可以保证您出行安心！</div>
                </div>
                <div class="benefit-title"><i class="icon-thin-heart"></i>安心 +</div>
                <div class="benefit-content">24小时管家无忧体验<br>机场-酒店接送机服务</div>
            </div>
            <div class="crown-wrap">
                <div class="crown-block">
                    <div class="crown-block-img"></div>
                    <div class="crown-block-title"><i class="icon-thin-crown"></i>超值 +</div>
                    <div class="crown-block-text">做套餐并非难事，但套餐价格比单品更低20%+，也只有Hotel+能够做到。付出4星酒店的价格，享受5星酒店的住宿和服务。这正是Hotel+能提供给您最超值的“加”！</div>
                </div>
                <div class="benefit-title"><i class="icon-thin-crown"></i>超值 +</div>
                <div class="benefit-content">4星价格享受5星体验<br>比单独订房多省20%</div>
            </div>
            <div class="time-wrap">
                <div class="time-block">
                    <div class="time-block-img"></div>
                    <div class="time-block-title"><i class="icon-thin-time"></i>自由 +</div>
                    <div class="time-block-text">Hotel+不是套餐，不想要的可乐为什么一定得为之掏钱？Hotel+，为您提供的是自由的选择组合，当地游玩项目自由选购，想玩什么就选什么，完全自由！</div>
                </div>
                <div class="benefit-title"><i class="icon-thin-time"></i>自由 +</div>
                <div class="benefit-content">当地必玩，一站式服务<br>游玩自由选择，不捆绑</div>
            </div>
        </div>
        <? foreach($data['promotion_group'] as $index=>$group) {?>
        <div <? if($index%2 != 0) {?>style="background-color: #ffffff;" <?}?>>
            <div class="group-block">
                <div class="group-title"><?=$group['name']?></div>
                <div class="group-desc"><?=$group['description']?></div>
                <div class="hi-carousel scroll hotel-wrap"
                     id="group_<?= $group['group_id'] ?>">
                    <div class="overflow-hidden">
                        <div class="carousel-list">
                            <? foreach($group['promotion_product'] as $hotel) {?>
                                <div class="carousel-item normal-item">
                                    <?php include(dirname(__FILE__) . '/module/normal_hotel.php'); ?>
                                </div>
                            <?}?>
                        </div>
                    </div>
                    <div class="to to-prev icon-arrow-left"></div>
                    <div class="to to-next icon-arrow-right"></div>
                </div>
                <a class="group-more" href="<?= $group['attach_url'] ?>" target="_blank">了解更多</a>
            </div>
        </div>
        <?}?>

    </div>
    <script>
        $(function(){
            var carousel_objects = [];
            var $sub_groups = $('.hi-carousel.hotel-wrap');
            for(var i = 0; i < $sub_groups.length; i++) {
                var id = $sub_groups.eq(i).attr('id');
                var item_length = $('#' + id).find('.carousel-item').length;
                if(item_length > 3) {
                    carousel_objects.push(new HiCarousel({
                        dom  : '#' + id,
                        type : 'scroll',
                        time : 400
                    }));
                } else {
                    $('#' + id).find('.to').hide();
                }
            }
        });
    </script>
<!--    <script>-->
<!--        var $box1=$('#box1'),$box2=$('#box2'),$box3=$('#box3'),$box4=$('#box4');-->
<!--        var $box3Left=$box3.find('.left'),$box3Right=$box3.find('.right');-->
<!--        //$box1.parallax("50%", 0.4);-->
<!--        var $box2Text = $box2.find('.title,.content');-->
<!--        $box2.parallax("50%", 0.2, 10, {-->
<!--            enterPoint:40,-->
<!--            leavePoint:-140,-->
<!--            enter:function(){-->
<!--                $box2Text.removeClass('outer');-->
<!--            },-->
<!--            leave:function(){-->
<!--                $box2Text.addClass('outer');-->
<!--            },-->
<!--            outer:function(){-->
<!--                $box2Text.addClass('outer');-->
<!--            }-->
<!---->
<!--        });-->
<!--        $box2.find('.title').parallax("82%",0.10,85);-->
<!--        $box2.find('.content').parallax("82%",0.10,200);-->
<!--        $box3.find('.title').parallax("22%",0.20,85);-->
<!--        $box3.find('.content').parallax("22%",0.20,200);-->
<!--        $box3.parallax("50%",1);-->
<!--        $box4.parallax("50%",0.5,0);-->
<!--        var p1=$box3.offset().top-20,-->
<!--            p2=$box3.offset().top+250;-->
<!--        var trigger=0;-->
<!--        $(window).on('scroll',function(){-->
<!--            var pos=$(window).scrollTop();-->
<!--            if(pos<p1&&trigger!=1){-->
<!--                $box3Left.css('left','-50%');-->
<!--                $box3Right.css('right','-50%');-->
<!--                trigger=1;-->
<!--            }-->
<!--            else if(pos>=p1&&pos<=p2&&trigger!=2){-->
<!--                $box3Left.css('left','0');-->
<!--                $box3Right.css('right','0');-->
<!--                $box3.find('.left,.right').removeClass('abs');-->
<!--                trigger=2;-->
<!--            }-->
<!--            else if(pos>=p2&&pos<=2750&&trigger!=3){-->
<!--                console.log(111);-->
<!--                $box3.find('.left,.right').addClass('abs');-->
<!--                trigger=3;-->
<!--                $box4.find('.title,.content').removeClass('show');-->
<!---->
<!--            }-->
<!--            else if(pos>2750&&pos<2940&&trigger!=4){-->
<!--                $box4.find('.title,.content').addClass('show');-->
<!--                $box4.find('.title,.content').removeClass('show2');-->
<!--                trigger=4;-->
<!--            }-->
<!--            else if(pos>2940&&pos<3352&&trigger!=5){-->
<!--                $box4.find('.title').addClass('show2')-->
<!--                $box4.find('.content').addClass('show2')-->
<!--                $box4.find('.title,.content').removeClass('show3');-->
<!--                trigger=5-->
<!--            }-->
<!--            else if(pos>3352&&trigger!=6){-->
<!--                $box4.find('.title,.content').addClass('show3');-->
<!--                trigger=6;-->
<!--            }-->
<!--        })-->
<!--    </script>-->
</div>