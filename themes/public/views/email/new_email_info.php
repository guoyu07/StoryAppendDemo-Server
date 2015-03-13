<?php
//订单信息
$order_product_name = $order_products[0]['product']['description']['name'];
?>

<table <?= $table_style ?> style="<?= $border_grey ?>"" width="600px">
    <tr>
        <td colspan="2" style="font-size: 40px; line-height: 1; color: #6db381; padding: 20px 20px 45px;">订单信息</td>
    </tr>
    <tr>
        <td width="480" style="color: #969696; padding-left: 20px;">产品名称：</td>
        <td width="80" style="color: #969696; padding: 5px 17px;">订单号：</td>
    </tr>
    <tr>
        <td style="color: #3c3c3c; font-size: 26px; padding-left: 20px; line-height: 28px;"><?php echo $order_product_name ?></td>
        <td valign="top" style="border-left: 1px solid #dddddd; font-size: 22px; padding: 0 17px; color: #6db381; "><?php echo $order['order_id']?></td>
    </tr>
    <tr>
        <td colspan="2" width="600px" height="50px"></td>
    </tr>
</table>