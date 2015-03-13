<?php
$order_sub_total = $order['sub_total'];
$order_total = $order['total'];
$order_discount = $order_sub_total - $order_total;
$order_status_processing = 0; //FALSE
if( $order['status_id'] == 4 || $order['status_id'] == 21 || $order['status_id'] == 17 ) {
    $order_status_processing = 1; //TRUE
}
$return_rule = $order_products[0]['product']['return_rule']['return_type'];
$homeUrl = Yii::app()->params['urlHome'];
//TODO:: order from qunar tts
$need_billing = !in_array($order['customer_id'],[798,11164,17348,11153]);
?>

<table <?= $table_style ?> width="600px">
    <tr>
        <td style="padding:5px 0 0 20px; font-size:20px; color: #525252;" width="150">预订人：</td>
        <td style="font-size:20px; color: #525252;" width="450"><?= $order['contacts_name'] ?></td>
    </tr>
    <tr>
        <td style="padding:5px 0 0 20px; font-size:20px; color: #525252;">预定邮箱：</td>
        <td style="font-size:20px; color: #525252;"><?= $order['contacts_email'] ?></td>
    </tr>
    <?php if($need_billing){?>
    <tr>
        <td colspan="2" style="padding-top: 30px;">
            <table  <?= $table_style ?> width="600px">
                <tr>
                    <td width="480" align="right" style="font-size:18px; color: #525252; padding-top: 5px;">总价：</td>
                    <td width="100" style="color: #ff6600; padding-top: 5px; font-size: 18px;">￥<?= $order_sub_total ?></td>
                </tr>
                <?php if($order_discount > 0) { ?>
                <tr>
                    <td align="right" style="font-size:18px; color: #525252; padding-top: 5px;">优惠：</td>
                    <td style="color: #ff6600; padding-top: 5px; font-size: 18px;">￥-<?= $order_discount ?></td>
                </tr>
                <?php } ?>
                <tr>
                    <td align="right" style="font-size:18px; color: #525252; padding-top: 5px;">付款：</td>
                    <td style="color: #ff6600; padding-top: 5px; font-size: 18px;">￥<?= $order_total ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <?php }?>
    <tr>
        <td colspan="2">
            <table  <?= $table_style ?> style="margin: 30px 0 15px;">
                <tr>
                    <td width="210"></td>
                    <td width="180" height="50px;" style="background: #6db381;" align="center">
                        <a href="<?php echo $homeUrl.'account/account#orders' ?>" target="_blank" style="color: #fff; font-size: 24px; text-decoration: none; display: block; line-height: 50px;">查看订单</a>
                    </td>
                    <td width="210"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <table <?= $table_style ?> width="560px" style="margin: 20px;">
                <?php
                if(!empty($order_products[0]['product']['date_rule']['shipping_desc']) && $order_status_processing == 0 && !in_array($order['status_id'],[2,3,6,9])) {
                    $shipping_desc = $order_products[0]['product']['date_rule']['shipping_desc'];
                ?>
                <tr>
                    <td valign="top" width="25px" style="padding-top: 2px;">
                        <img src="<?= $image_url ?>note.png" width="20px" height="20px" style="vertical-align: top;" alt="" />
                    </td>
                    <td valign="top" style="color: #6db381; font-size: 18px;">您的订单将在<?= $shipping_desc ?></td>
                </tr>
                <?php } ?>
                <?php if( $return_rule != 0 ) { ?>
                <tr>
                    <td valign="top" width="25px" style="padding-top: 2px;">
                        <img src="<?= $image_url ?>note.png" width="20px" height="20px" style="vertical-align: top;" alt="" />
                    </td>
                    <td valign="top" style="color: #6db381; font-size: 18px;">此订单可以在<?= $order_products[0]['return_expire_date'] ?>之前免费退订，不会收取额外费用！</td>
                </tr>
                <?php } else { ?>
                <tr>
                    <td valign="top" width="25px" style="padding-top: 2px;">
                        <img src="<?= $image_url ?>warning.png" width="20px" height="20px" style="vertical-align: top;" alt="" />
                    </td>
                    <td valign="top" style="color: #ff6600; font-size: 18px;">此订单不可以退！</td>
                </tr>
                <?php } ?>
            </table>
        </td>
    </tr>
</table>