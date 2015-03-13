<?php $CURRENT_TEMPLATE_URL = Yii::app()->request->hostinfo . Yii::app()->params['WEB_PREFIX'] . Yii::app()->params['THEME_BASE_URL']; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?php echo $CURRENT_TEMPLATE_URL; ?>/stylesheets/voucher_product.css"/>
    <title><?php echo $desc['name'] ."辅助信息"?></title>
</head>
<body>
<div class="main-wrap">
    <header>
        <div class="content-ctn">
            <p class="voucher-head">Voucher<span>兑换辅助信息</span></p>
        </div>
    </header>

    <section class="main-content">
        <p class="main-title"><?php echo $desc['name']?></p>
        <p class="main-subtitle"><?php echo $desc['en_name']?></p>

        <!--location-->
        <div class="location">
            <?php foreach($pick_landinfo_groups as $places) { ?>
                <div class="single-location">
                    <div class="header-zone">
                        <div class="black-block"></div>
                        <div class="location-title"><?php echo $places["title"] ?></div>
                        <div class="location-notice">如果您的谷歌地图无法打开，请在非限制网络环境下尝试。</div>
                    </div>
                    <?php foreach($places["landinfos"] as $landinfo) { ?>
                        <div class="location-content">
                            <div class="qr-zone">
                                <img src="<?php echo $landinfo["map_qrcode"] ?>">
                                <p>扫码查看地图</p>
                            </div>
                            <div class="info-zone">
                                <div class="info-title">
                                    <?php echo $landinfo["name"] ?>
                                </div>
                                <?php if ($landinfo["address"] != null) { ?>
                                    <div class="info-each">
                                        <div class="info-left">地&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;址：</div>
                                        <div class="info-right">
                                            <p><?php echo $landinfo["address"] ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($landinfo["communication"] != null) { ?>
                                    <div class="info-each">
                                        <div class="info-left">到达方式：</div>
                                        <div class="info-right">
                                            <p><?php echo $landinfo["communication"] ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($landinfo["phone"] != null) { ?>
                                    <div class="info-each">
                                        <div class="info-left">电&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;话：</div>
                                        <div class="info-right">
                                            <p><?php echo $landinfo["phone"] ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($landinfo["open_time"] != null) { ?>
                                    <div class="info-each">
                                        <div class="info-left">开放时间：</div>
                                        <div class="info-right">
                                            <p><?php echo $landinfo["open_time"] ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($landinfo["close_time"] != null) { ?>
                                    <div class="info-each">
                                        <div class="info-left">关闭时间：</div>
                                        <div class="info-right">
                                            <p><?php echo $landinfo["close_time"] ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($landinfo["website"] != null) { ?>
                                    <div class="info-each">
                                        <div class="info-left">网&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;站：</div>
                                        <div class="info-right">
                                            <p><?php echo $landinfo["website"] ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <!--Product Information-->
        <div class="product-info">
            <div class="header-zone">
                <div class="black-block"></div>
                <div class="location-title">商品信息</div>
            </div>
            <?php if(!empty($introduction) && isset($introduction)) { ?>
                <div class="markdown-text">
                    <?php foreach( $introduction['service_include'] as $service_include ) { ?>
                        <div>
                            <h2><?php echo $service_include['title'] ?></h2>
                            <?php echo $service_include['detail'] ?>
                        </div>
                    <?php } ?>

                    <?php echo $introduction['redeem_usage']['usage']?>
                </div>
                <div class="markdown-text">
                    <?php echo $introduction['please_read']['buy_note']?>
                </div>
            <?php } else { ?>
                <div class="intro-zone">
                    <?php echo $desc["how_it_works"]?>
                    <?php if(!empty($desc["service_include"]) && isset($desc["service_include"])) { echo $desc["service_include"]; }?>
                </div>
            <?php } ?>
        </div>

    </section>

    <footer>
        <div class="content-ctn">
            <div class="hitour"></div>
            <div class="email">service@hitour.cc</div>
            <div class="tel">+8610-53344380(海外) &nbsp;&nbsp;&nbsp;&nbsp;400-010-1900(国内)</div>
        </div>
    </footer>
</div>
<script type="text/javascript" src="<?php echo $CURRENT_TEMPLATE_URL; ?>/bower_components/jquery/jquery.min.js"></script>
<script>

    var headerHTML = $("header").prop("outerHTML");
    var footerHTML = $("footer").prop("outerHTML");

    var windowHeight = parseInt($(".main-content").css('height'));
    console.log(windowHeight);

    if(windowHeight > 1391) {
        var productHTML = $(".product-info").prop("outerHTML");
        $(".product-info").remove();
        var finalHTML = "<div class='main-wrap block-top'>" + headerHTML + productHTML + footerHTML + "</div>";
        $("body").append(finalHTML);
    }

</script>
</body>
</html>
