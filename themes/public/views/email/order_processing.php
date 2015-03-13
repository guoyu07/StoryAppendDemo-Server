<html lang="zh">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=640, initial-scale=1">
    <title>玩途自由行 - 预定处理中</title>
</head>
<body>
    <?php include('order_email_header.php');
    $order_product = $order_products[0];
    $product = $order_product['product'];
    ?>
    <tr class="notice">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td class="icon" style="<?= $IDcc_EtdCicon ?>"><img
                src="<?php echo $CURRENT_TEMPLATE_URL; ?>/images/email/order_processing.png" /></td>
        <td colspan="3">
            您的预定请求已收到！
        </td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
    <tr class="notice">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td class="icon" style="<?= $IDcc_EtdCicon ?>"></td>
        <td colspan="3">
            <?php

            if ($product['type'] == HtProduct::T_COUPON) {
                echo '抵用券将随后发送到您的邮箱。';
            } else {
                echo $order['status_id'] != HtOrderStatus::ORDER_WAIT_CONFIRMATION ?
                    '我们会在' . $product['date_rule']['shipping_desc'] :
                    '该商品预定名额有限，玩途会第一时间帮您预定，并以邮件的形式通知您预定结果。<br/> 如遇预定失败，玩途会48小时内为您协调门票时间或处理退款，让您后顾无忧。';
            }
            ?>
        </td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
    <?php include('order_info.php'); ?>
    <?php include('order_email_footer.php'); ?>
</body>
</html>