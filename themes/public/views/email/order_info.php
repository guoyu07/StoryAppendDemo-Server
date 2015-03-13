<tr class="space" style="<?= $IDcc_EtrCspace ?>"></tr>
<tr class="border-row" style="<?= $IDcc_EtrCborder_row ?>">
    <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
    <td colspan="4" class="border" style="<?= $IDcc_EtrCborder_row_EtdCborder ?>"></td>
    <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
</tr>
<tr class="space" style="<?= $IDcc_EtrCspace ?>"></tr>
<tr class="title-row" style="<?= $IDcc_EtrCtitle_row ?>">
    <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
    <td colspan="2" class="left-title" style="<?= $IDcc_EtdCleft_title ?>">
        <h3>预定信息</h3>
    </td>
    <td colspan="2">
        <a href="<?= $order['account_order_url']; ?>" target="_blank">查看订单详情 >></a>
    </td>
    <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
</tr>
<tr class="space" style="<?= $IDcc_EtrCspace ?>"></tr>
<tr class="one-order-record">
    <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
    <td colspan="2" class="left-title" style="<?= $IDcc_EtdCleft_title ?>">产品名称：</td>
    <td colspan="2" class="grey main-content"
        style="<?= $IDcc_Cgrey . ' ' . $IDcc_EtdCmain_content ?>"><?php echo $product['description']['name'] ?></td>
    <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
</tr>
<?php if (isset($order_product['special']['cn_name'])) { ?>
    <tr class="one-order-record">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="2" class="left-title"
            style="<?= $IDcc_EtdCleft_title ?>"><?php echo $product['description']['special_title'] . ':' ?></td>
        <td colspan="2" class="grey main-content"
            style="<?= $IDcc_Cgrey . ' ' . $IDcc_EtdCmain_content ?>"><?= $order_product['special']['cn_name'] ?></td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
<?php } ?>
<tr class="one-order-record">
    <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
    <td colspan="2" class="left-title" style="<?= $IDcc_EtdCleft_title ?>">订单号：</td>
    <td colspan="2" class="grey main-content"
        style="<?= $IDcc_Cgrey . ' ' . $IDcc_EtdCmain_content ?>"><?= $order['order_id'] ?></td>
    <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
</tr>
<tr class="one-order-record">
    <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
    <td colspan="2" class="left-title" style="<?= $IDcc_EtdCleft_title ?>">预定人：</td>
    <td colspan="2" class="grey main-content"
        style="<?= $IDcc_Cgrey . ' ' . $IDcc_EtdCmain_content ?>"><?= $order['contacts_name'] ?></td>
    <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
</tr>
<tr class="one-order-record">
    <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
    <td colspan="2" class="left-title" style="<?= $IDcc_EtdCleft_title ?>">预定邮箱：</td>
    <td colspan="2" class="grey main-content"
        style="<?= $IDcc_Cgrey . ' ' . $IDcc_EtdCmain_content ?>"><?= $order['contacts_email'] ?></td>
    <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
</tr>
<?php
if ($product['date_rule']['need_tour_date']) {
    ?>
    <tr class="one-order-record">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="2" class="left-title" style="<?= $IDcc_EtdCleft_title ?>">出行日期：</td>
        <td colspan="2" class="grey main-content"
            style="<?= $IDcc_Cgrey . ' ' . $IDcc_EtdCmain_content ?>"><?php echo getPrettyDate($order_product['tour_date']); ?></td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
<?php
} else if ($order_product['redeem_expire_date'] != '0000-00-00') {
    ?>
    <tr class="one-order-record">
        <td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
        <td colspan="2" class="left-title" style="<?= $IDcc_EtdCleft_title ?>">兑换截止日：</td>
        <td colspan="2" class="grey main-content"
            style="<?= $IDcc_Cgrey . ' ' . $IDcc_EtdCmain_content ?>"><?php echo getPrettyDate($order_product['redeem_expire_date']); ?></td>
        <td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
    </tr>
<?php
}
?>
