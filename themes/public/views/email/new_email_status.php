<?php
//订单状态
if( $order['status_id'] == 4 || $order['status_id'] == 5 || $order['status_id'] == 21 || $order['status_id'] == 17 ) {
    $order_status_label = '订单正在处理中';
    $status_icon = 'timing.png';
} else if( $order['status_id'] == 3 || $order['status_id'] == 2 || $order['status_id'] == 6 || $order['status_id'] == 9 ) {
    $order_status_label = '您的订单已发货';
    $status_icon = 'success.png';
} else if( $order['status_id'] == 24 ) {
    $order_status_label = '订单退款失败！';
    $status_icon = 'error.png';
} else if( $order['status_id'] == 18 || $order['status_id'] == 19 || $order['status_id'] == 23 ) {
    $order_status_label = '订单退款处理中';
    $status_icon = 'timing.png';
} else if( $order['status_id'] == 11 ) {
    $order_status_label = '您的订单退款成功';
    $status_icon = 'success.png';
}
?>

<table <?= $table_style ?> width="568px">
    <tr>
        <td colspan="2" style="font-size:22px; padding-bottom:42px;">
            亲爱的<?= $order['contacts_name'] ?>，您好!
        </td>
    </tr>
    <tr>
        <td width="180" align="right" style="padding-bottom: 18px;">
            <img src="<?= $image_url ?><?= $status_icon ?>" alt="" width="40px" height="40px" style="vertical-align: top;"/>
        </td>
        <td width="460" style="font-size: 36px; padding-left: 10px; font-weight:bold; padding-bottom: 18px;">
            <?= $order_status_label ?>
        </td>
    </tr>
</table>