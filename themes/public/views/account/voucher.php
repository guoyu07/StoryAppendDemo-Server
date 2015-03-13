<?php $CURRENT_TEMPLATE_URL = Yii::app()->request->hostinfo . Yii::app()->params['WEB_PREFIX'] . Yii::app()->params['THEME_BASE_URL']; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?php echo $CURRENT_TEMPLATE_URL; ?>/stylesheets/voucher.css"/>
    <title><?php echo $product_info['name'] ?></title>
</head>
<body>
<div class="main-wrap">
    <header>
        <div class="content-ctn">
            <p class="voucher-head">VOUCHER<span>兑换单（打印有效）</span></p>

            <p class="order-num">玩途订单号:<span><?php echo $order_info['order_id']; ?></span></p>
        </div>
    </header>

    <div class="content-ctn" id="body_ctn">
        <div class="supplierlogo">
            <img src="<?php echo $confirmation_info['logo'] ?>">
<!--            <img class="qrcode" src="--><?php //echo $confirmation_info['verify_qrcode_url'] ?><!--">-->
        </div>
        <!-- order info start -->
        <section class="head-section">
            <h1><?php echo $product_info['name'] ?></h1>

            <h2><?php echo($product_info['name'] != $product_info['sub_name'] ? $product_info['sub_name'] : '') ?></h2>
            <ul class="order-info-ctn">
                <?php
                foreach ($order_info['user_data'] as $item) { ?>
                    <li>
                        <span class="order-info-title"><?php echo $item['title'] ?></span>
                        <span class="order-info-content"><?php echo $item['value'] ?></span>
                    </li>
                <?php } ?>
            </ul>
            <?php if (isset($confirmation_info['additional_info']) && !empty($confirmation_info['additional_info'])) { ?>
                <p class="note"><?php echo $confirmation_info['additional_info'] ?></p>
            <?php } ?>
        </section>
        <!-- order info end -->

        <!-- bar code start -->
        <section class="bar-code">
            <h3><i class="decoration"></i>Confirmation/兑换信息</h3>

            <?php if (isset($confirmation_info['payable_by']) && $confirmation_info['payable_by']) { ?>
                <p class="note"><?php echo $confirmation_info['payable_by'] ?></p>
            <?php } ?>

            <?php if(!empty($confirmation_info['codes'])){ ?>
            <?php foreach ($confirmation_info['codes'] as $item) { ?>
                <div class="bar-code-order">
                    <div class="bar-code-title"><?php echo $item['title']; ?></div>
                    <?php if (!is_array($item['value'])) { ?>
                        <div class="bar-code-content"><?php echo $item['value']; ?></div>
                    <?php } else { ?>

                        <!-- is an array -->
                        <ul class="barcode">
                            <?php foreach ($item['value'] as $ci) { ?>
                                <li>
                                    <?php if (isset($ci["barcode_url"])) { ?>
                                        <img src="<?php echo $ci['barcode_url'] ?>"/>
                                        <p class="textcode"><?php echo $ci['code'] ?></p>
                                    <?php } else { ?>
                                        <p class="textcode"><?php echo $ci['code'] ?></p>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php } ?>


            <!--please note -->
            <?php if (isset($product_info['please_note']) && $product_info['please_note']) { ?>
                <p class="note"><?php echo $product_info['please_note'] ?></p>
            <?php } ?>

        </section>
        <!-- bar code end -->

        <!-- start tourist-info-->
        <?php if (isset($pax_info['detail']) && $pax_info['detail']['need_table']) {
            $pax_class = 'layout-table';
        } else {
            $pax_class = 'layout-group';
        }?>
        <section class="tourist-info  <?php echo $pax_class ?>">
            <h3><i class="decoration"></i>Tourist Information/旅客详情</h3>

            <div class="tourist-detail-ctn">
                <?php foreach ($pax_info['total'] as $item) { ?>
                    <span class="tourist-num"><?php echo $item['title'] ?>
                        :&nbsp;<strong><?php echo $item['value'] ?></strong></span>
                <?php } ?>


                <?php if (isset($pax_info['lead']) && !empty($pax_info['lead'])) { ?>
                    <div class="tourist">
                        <p><i class="tourist-decoration"></i>Lead Contact/主要出行联系人</p>
                        <ul class="lead-tourist-info">
                            <?php foreach ($pax_info['lead'] as $item) { ?>
                                <li><?php echo $item['title'] ?><span><?php echo $item['value'] ?></span></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>

                <!--其他出行人-->
                <?php if (isset($pax_info['detail']) && $pax_info['detail']['has_everyone'] == 1) { ?>
                <div class="tourist">
                    <?php if (isset($pax_info['lead']) && !empty($pax_info['lead'])) { ?><p><i
                            class="tourist-decoration"></i>Other tourist info/其他出行联系人</p><?php } ?>
                    <!-- case 1-->
                    <?php if ($pax_info['detail']['need_table'] ){ if(count($pax_info['detail']['table']['value'])>0) { ?>
                        <table>
                            <thead>
                            <tr>
                                <?php foreach ($pax_info['detail']['table']['title'] as $td) { ?>
                                    <td><?php echo $td ?></td>
                                <?php } ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php for ($i = 0; $i < count($pax_info['detail']['table']['value']); $i++) {
                                $p = $pax_info['detail']['table']['value'][$i];?>
                                <tr <?php echo $i % 2 ? 'class="even"' : '' ?>>
                                    <?php foreach ($p as $pv) { ?>
                                        <td><?php echo $pv ?></td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    <?php
                    }} else {
                    if (!empty($pax_info['detail']['flat']) && is_array($pax_info['detail']['flat'])) {
                    foreach ($pax_info['detail']['flat'] as $group) {
                        ?>
                        <!-- case 2 layout-group -->
                        <!--            <p class="tourist-num">--><?php //echo $group['title']['title']?><!--&nbsp;x<strong>--><?php //echo $group['title']['value']?><!--</strong></p>-->
                        <ul class="tourist-detail">
                            <?php foreach ($group['value'] as $p) { ?>
                                <li>
                                    <span class="tourist-name"> <?php echo $p[0] ?> </span>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php }} ?>
                </div>
            <?php } ?>


            </div>
            <?php } ?>
        </section>

        <!-- end tourist-info -->

        <!-- start customer-service-->
        <?php if (isset($confirmation_info['local_support'])) { ?>
            <section class="customer-service">
                <h3><i class="decoration"></i>Customer Service/客服电话</h3>
                <ul class="customer-service-ctn">
                    <?php foreach ($confirmation_info['local_support'] as $aot) { ?>
                        <li>
                            <span class="customer-service-title"><?php echo $aot['title'] ?></span>
                            <span class="customer-service-content"><?php echo $aot['value'] ?></span>
                        </li>
                    <?php } ?>
                </ul>
            </section>
        <?php } ?>
        <!-- end customer-service -->

        <!-- signature -->
        <?php if (isset($confirmation_info['need_signature']) && $confirmation_info['need_signature'] == 1) { ?>
            <p class="signature">Signature/签名：<span class="btm-line"></span></p>
        <?php } ?>

    </div>

    <footer>
        <div class="content-ctn">
            <div class="hitour"></div>
            <div class="email">service@hitour.cc</div>
            <div class="tel">+8610-53344380(海外) &nbsp;&nbsp;&nbsp;&nbsp;400-010-1900(国内)</div>
        </div>
    </footer>
</div>

<script type="text/javascript"
        src="<?php echo $CURRENT_TEMPLATE_URL; ?>/bower_components/jquery/jquery.min.js"></script>
<script>

    var headerHTML = $("header").prop("outerHTML");
    var footerHTML = $("footer").prop("outerHTML");

    var mainContentHeight = parseInt($("#body_ctn").css("height"));
    var tourInfoHeight = parseInt($(".tourist-info").css("height"));
    var cstSrvcHeight = parseInt($(".customer-service").css("height"));

    var hasSignature = false;

    if ($(".signature") == "[]") {
        var signatureHeight = parseInt($(".signature").css("height"));
        hasSignature = true;
    }
    else {
        var signatureHeight = 0;
        hasSignature = false;
    }

    var classArray = new Array(".signature", ".customer-service", ".tourist-info", ".bar-code");
    var heightArray = new Array();
    heightArray[0] = mainContentHeight;
    heightArray[1] = mainContentHeight - signatureHeight;
    heightArray[2] = mainContentHeight - signatureHeight - cstSrvcHeight;
    heightArray[3] = mainContentHeight - signatureHeight - cstSrvcHeight - tourInfoHeight;

    var flagPos;
    for (var i = heightArray.length - 1; i >= 0; i--) {
        if (heightArray[i] > 1391) {
            flagPos = i;
            break;
        }
    }

    if (flagPos != undefined) {
        var tempHTML = "";
        for (var i = flagPos; i >= 0; i--) {
            if (i == 0 && !hasSignature)
                break;
            tempHTML = tempHTML + $(classArray[i]).prop("outerHTML");
            $(classArray[i]).remove();
        }
        tempHTML = "<div class='content-ctn'>" + tempHTML + "</div>";
        var finalHTML = "<div class='main-wrap block-top'>" + headerHTML + tempHTML + footerHTML + "</div>";
        $("body").append(finalHTML);
    }

</script>
</body>
</html>
