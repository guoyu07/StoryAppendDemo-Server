<?php
$CURRENT_TEMPLATE_URL = Yii::app()->request->hostinfo . Yii::app()->params['WEB_PREFIX'] . Yii::app()->params['THEME_BASE_URL'];
$need_billing = $order['order_id'] != 798;
foreach ($product['descriptions'] as $pd) {
    if ($pd['language_id'] == 2) {
        $description = $pd;
        break;
    }
}
$description = $product['descriptions'][2];
?>
<html lang="zh">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=640, initial-scale=1">
    <title>玩途自由行 - 预定成功</title>
</head>
<body>
<?php include('order_email_header.php'); ?>
<tr class="notice">
    <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
    <td class="icon" style="<?= $IDcc_EtdCicon ?>"><img
            src="<?php echo $CURRENT_TEMPLATE_URL . '/images/email/check.png' ?>"/></td>
    <td colspan="3">
        您的预定已成功！祝您出行愉快！
    </td>
    <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
</tr>
<tr class="notice">
    <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
    <td class="icon" style="<?= $IDcc_EtdCicon ?>"><img
            src="<?php echo $CURRENT_TEMPLATE_URL . '/images/email/order_success.png' ?>"/></td>
    <td colspan="3" style="<?= $IDcc_Ctall ?>">
        请您打印邮件的附件（兑换单）并随身携带。
    </td>
    <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
</tr>
<?php include('order_info.php'); ?>


<?php if ($product['return_rule']['return_type'] != 0) { ?>
    <tr class="notice">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td class="icon" style="<?= $IDcc_EtdCicon ?>"><img
                src="<?php echo $CURRENT_TEMPLATE_URL . '/images/email/order_success.png' ?>"/></td>
        <td colspan="3" class="green" style="<?= $IDcc_Cgreen . ' ' . $IDcc_Ctall ?>">
            此预定可以在<?= getPrettyDate($order_product['return_expire_date']) ?>之前免费退订，不会收取额外费用。
        </td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
<?php } else { ?>
    <tr class="notice">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td class="icon" style="<?= $IDcc_EtdCicon ?>"><img
                src="<?php echo $CURRENT_TEMPLATE_URL . '/images/email/order_failed.png' ?>"/></td>
        <td colspan="3" class="grey" style="<?= $IDcc_Cgrey . ' ' . $IDcc_Ctall ?>">
            此单不可以退订。
        </td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
<?php } ?>

<tr class="space" style="<?= $IDcc_EtrCspace ?>"></tr>
<tr class="border-row" style="<?= $IDcc_EtrCborder_row ?>">
    <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
    <td colspan="4" class="border" style="<?= $IDcc_EtrCborder_row_EtdCborder ?>"></td>
    <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
</tr>

<tr class="space" style="<?= $IDcc_EtrCspace ?>"></tr>
<tr class="title-row" style="<?= $IDcc_EtrCtitle_row ?>">
    <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
    <td colspan="3">
        <h3><?php echo $need_billing ? '您的账单' : '数量' ?></h3>
    </td>
    <td></td>
    <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
</tr>
<tr class="space" style="<?= $IDcc_EtrCspace ?>"></tr>
<?php foreach ($order_product['prices'] as $pi) { ?>
    <tr class="one-order-record">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="2" class="left-title" style="<?= $IDcc_EtdCleft_title ?>">
            <?php echo $ticket_types[$pi['ticket_id']]['ticket_type']['cn_name'] ?>
            <span class="grey" style="<?= $IDcc_Cgrey ?>"> x <?= $pi['quantity'] ?></span>
            <?php if ($need_billing) { ?>
                <span class="grey" style="<?= $IDcc_Cgrey ?>"> x <?= $pi['price'] ?></span>
            <?php } ?>
        </td>
        <?php if ($need_billing) { ?>
            <td></td>
            <td class="right-title"
                style="<?= $IDcc_EtdCright_title ?>">
                <?php echo((int)$pi['quantity'] * $pi['price']) ?>
            </td>

        <?php } ?>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
<?php } ?>

<?php if ($need_billing) { ?>
    <tr class="one-order-record dash-border-row">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td class="border" style="<?= $IDcc_EtrCdash_border_row_EtdCborder ?>"></td>
        <td class="border" style="<?= $IDcc_EtrCdash_border_row_EtdCborder ?>"></td>
        <td class="border"
            style="<?= $IDcc_EtrCdash_border_row_EtdCborder ?> text-align: right; padding-left: 10px;">总额
        </td>
        <td class="right-title border"
            style="<?= $IDcc_EtrCdash_border_row_EtdCborder . ' ' . $IDcc_EtdCright_title ?>">
            <?php echo $order_product['total'] ?> RMB
        </td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>

    <?php if ($order_product['total'] > $order['total']) { ?>
        <tr class="one-order-record dash-border-row">
            <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
            <td class="border" style="<?= $IDcc_EtrCdash_border_row_EtdCborder ?>"></td>
            <td class="border" style="<?= $IDcc_EtrCdash_border_row_EtdCborder ?>"></td>
            <td class="border"
                style="<?= $IDcc_EtrCdash_border_row_EtdCborder ?> text-align: right; padding-left: 10px;">
                优惠抵扣
            </td>
            <td class="right-title border"
                style="<?= $IDcc_EtrCdash_border_row_EtdCborder . ' ' . $IDcc_EtdCright_title ?>">
                <?php echo $order_product['total'] - $order['total'] ?> RMB
            </td>
            <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
        </tr>
    <?php } ?>


    <tr class="one-order-record dash-border-row">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td class="border" style="<?= $IDcc_EtrCdash_border_row_EtdCborder ?>"></td>
        <td class="border" style="<?= $IDcc_EtrCdash_border_row_EtdCborder ?>"></td>
        <td class="border" style="<?= $IDcc_EtrCdash_border_row_EtdCborder ?> text-align: right; padding-left: 10px;">
            实际支付
        </td>
        <td class="right-title border"
            style="<?= $IDcc_EtrCdash_border_row_EtdCborder . ' ' . $IDcc_EtdCright_title ?>">
            <?php echo $order['total'] ?> RMB
        </td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
<?php } ?>
<?php if (isset($insurance_codes) && ($code_count = count($insurance_codes)) > 0) { ?>
    <tr class="space" style="<?= $IDcc_EtrCspace ?>"></tr>
    <tr class="border-row" style="<?= $IDcc_EtrCborder_row ?>">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="4" class="border" style="<?= $IDcc_EtrCborder_row_EtdCborder ?>"></td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
    <tr class="space" style="<?= $IDcc_EtrCspace ?>"></tr>
    <tr class="title-row" style="<?= $IDcc_EtrCtitle_row ?>">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="3">
            <h3>保险信息</h3>
        </td>
        <td></td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
    <tr class="space" style="<?= $IDcc_EtrCspace ?>"></tr>

    <tr>
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="4">玩途给每位出行的成人旅客（18周岁以上）赠送一份为期10天, 保额15万的境外旅游意外保险（由中国太平洋保险股份有限公司承保）. 每位旅客的保单兑换码如下：</td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
    <tr>
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="2" class="left-title" style="<?= $IDcc_EtdCleft_title ?>">兑换码：</td>
        <td class="small"
            style="<?= $IDcc_Csmall ?>"><?php if (isset($insurance_codes[0])) echo $insurance_codes[0]['redeem_code'] ?></td>
        <td class="small"
            style="<?= $IDcc_Csmall ?>"><?php if (isset($insurance_codes[1])) echo $insurance_codes[1]['redeem_code'] ?></td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
    <?php
    if ($code_count > 2) {
        for ($i = 2;
             $i < $code_count;
             $i++) {
            if ($i % 2 == 0) {
                echo '<tr><td class="pad-left" style="' . $IDcc_EtdCpad_left . '"></td><td colspan="2"></td>';
            } //2, 4, 6
            ?>
            <td class="small" style="<?= $IDcc_Csmall ?>"><?= $insurance_codes[$i]['redeem_code'] ?></td>
            <?php
            if ($i % 2 == 1) {
                echo '</tr><td class="pad-right" style="' . $IDcc_EtdCpad_right . '"></td>';
            } //3, 5, 7
        }
    }
    ?>
    <tr>
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="2" class="left-title" style="<?= $IDcc_EtdCleft_title ?>">兑换网址：</td>
        <td colspan="2"><a
                href="<?= $insurance_codes[0]['company']['policy_url'] ?>"><?= $insurance_codes[0]['company']['policy_url'] ?></a>
        </td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
<?php } ?>

<?php if (isset($gift_coupons) && $gift_coupons) { ?>
    <tr class="space" style="<?= $IDcc_EtrCspace ?>"></tr>
    <tr class="border-row" style="<?= $IDcc_EtrCborder_row ?>">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="4" class="border" style="<?= $IDcc_EtrCborder_row_EtdCborder ?>"></td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
    <tr class="space" style="<?= $IDcc_EtrCspace ?>"></tr>
    <tr class="title-row" style="<?= $IDcc_EtrCtitle_row ?>">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="3">
            <h3>优惠券</h3>
        </td>
        <td></td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
    <tr class="space" style="<?= $IDcc_EtrCspace ?>"></tr>

    <tr>
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="4">感谢您对玩途的信任，附送<?php echo count($gift_coupons) ?>张玩途优惠券，您可以在下次预订时使用。该优惠券您也可以送给朋友使用。</td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>

    <tr style="height: 30px">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <th colspan="1">优惠码</th>
        <th colspan="1"></th>
        <th colspan="1">有效期</th>
        <th colspan="1">优惠金额</th>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
    <?php foreach ($gift_coupons as $coupon) { ?>
        <tr class="dash-border-row" style="height: 30px">
            <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
            <td class="border" colspan="1" style="<?= $IDcc_EtrCdash_border_row_EtdCborder ?>text-align: center">
                <?php echo $coupon['code'] ?>
                <?php
                if($coupon['limit_type'] != 0) echo '*' ?>
            </td>
            <td class="border" colspan="1" style="<?= $IDcc_EtrCdash_border_row_EtdCborder ?>text-align: center"></td>
            <td class="border" colspan="1"
                style="<?= $IDcc_EtrCdash_border_row_EtdCborder ?>text-align: center"><?php echo $coupon['date_start'] . '——' . $coupon['date_end'] ?></td>
            <td class="border" colspan="1"
                style="<?= $IDcc_EtrCdash_border_row_EtdCborder ?>text-align: center"><?php echo sprintf('￥%d', $coupon['discount']) ?></td>
            <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
        </tr>
    <?php } ?>

    <tr style="height:30px">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="4">* 含有星号的优惠券有使用限制，请登录玩途查看详情</td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
    <tr style="height:55px">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="4">登录玩途可查看您的优惠券：<a href="http://www.hitour.cc/account/account#coupon" target="_blank">hitour.cc</a></td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>

<?php } ?>

<?php if (!empty($email_ad) && count($email_ad) > 0) { ?>
    <tr class="space" style="<?= $IDcc_EtrCspace ?>"></tr>
    <tr class="border-row" style="<?= $IDcc_EtrCborder_row ?>">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="4" class="border" style="<?= $IDcc_EtrCborder_row_EtdCborder ?>"></td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
    <tr class="space" style="<?= $IDcc_EtrCspace ?>"></tr>
    <tr class="title-row" style="<?= $IDcc_EtrCtitle_row ?>">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="3">
            <h3>优惠信息</h3>
        </td>
        <td></td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
    <tr class="space" style="<?= $IDcc_EtrCspace ?>"></tr>


    <?php foreach ($email_ad as $ad) { ?>
        <tr>
            <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
            <?php if (!empty($ad['image_url'])) { ?>
                <td colspan="2" class="left-title" style="<?= $IDcc_EtdCleft_title ?>"><img
                        src="<?php echo $CURRENT_TEMPLATE_URL . $ad['image_url'] ?>"/></td>
                <td colspan="2"><a href="<?= $ad['link_url'] ?>"><?= $ad['title'] ?></a></td>
            <?php } else { ?>
                <td colspan="4"><a href="<?= $ad['link_url'] ?>"><?= $ad['title'] ?></a></td>
            <?php } ?>
            <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
        </tr>
    <?php } ?>

<?php } ?>

<?php include('order_email_footer.php'); ?>
</body>
</html>