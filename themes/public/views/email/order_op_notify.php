<?php
$language_id = 2;
$pd = $order_product['product']['descriptions'][$language_id];
$departure_time = $order_product['departure_time'];
$departures = $order_product['departures'];

$special_value = '';
if(!empty($order_product['special_info'])) {
    foreach ($order_product['special_info'][0]['items'] as $special) {
        $special_value .= $special['cn_name'] . ' ';
    }
}
$departure_value = '';
if ($departure_time <> '00:00:00') {
    $departure_value .= date('H:i', strtotime($departure_time));
}

if (isset($departures[$language_id]['departure_point'])) {
    if ($departure_value) {
        $departure_value .= '  ';
    }
    $departure_value .= $order_product['departures'][$language_id]['departure_point'];
}

$pax_rule = $order_product['product']['pax_rule'];
$pax_meta = $order_product['product']['pax_meta'];

?>

<html>
<body>
<table border="1px" border-collapse="collapse">
    <tr>订单信息如下:</tr>
    <tr>
        <td>玩途订单号</td>
        <td><?php echo $order_product['order_id'] ?></td>
    </tr>
    <?php if ($order_product['supplier_order']['hitour_booking_ref']) { ?>
        <tr>
            <td>订单确认码</td>
            <td><?php echo $order_product['supplier_order']['hitour_booking_ref'] ?></td>
        </tr>
    <?php } ?>
    <tr>
        <td>产品名称</td>
        <td><?php echo $pd['name'] . ($special_value?' ('. $special_value.')':'') ?></td>
    </tr>
    <?php if (!empty($order_product['special_info'])) {
        foreach($order_product['special_info'][0]['items'] as $special){?>
        <tr>
            <td><?php echo $special['group_title'] ?></td>
            <td><?php echo $special['cn_name'] ?></td>
        </tr>
    <?php }
    } ?>

    <?php if ($order_product['product']['date_rule']['need_tour_date']) { ?>
        <tr>
            <td>使用日期</td>
            <td><?php echo date('Y-m-d', strtotime($order_product['tour_date'])) ?></td>
        </tr>
    <?php } ?>

    <?php if ($departure_value) { ?>
        <tr>
            <td><?php echo $pd['departure_title'] ?></td>
            <td><?php echo $departure_value ?></td>
        </tr>
    <?php } ?>

    <tr>
        <td>旅客数量</td>
        <td>
            <?php foreach ($order_product['quantities'] as $k => $v) {
                echo sprintf('%s × %s  ', $language_id == 1 ? $order_product['ticket_types'][$k]['ticket_type']['en_name'] : $order_product['ticket_types'][$k]['ticket_type']['cn_name'], $v);
            }?>
        </td>
    </tr>
    <?php if (!empty($order_product['passengers']) && is_array($order_product['passengers'])) {
        foreach ($order_product['passengers'] as $k => $p) {
            ?>
            <tr>
                <td>
                    <?php $pax_title = '出行人' . ($k + 1);
                    if ($pax_rule['need_lead'] && $k == 0) {
                        $pax_title .= '(领队)';
                    } else if ($p['is_child']) {
                        $pax_title .= '(儿童)';
                    }
                    echo $pax_title;
                    ?>
                </td>
                <td>
                    <table>
                        <?php
                        if (isset($pax_rule['id_map'][$p['ticket_id']])) {
                            $ids = $pax_rule['id_map'][$p['ticket_id']];
                        } else if (isset($pax_rule['id_map'][1])) {
                            $ids = $pax_rule['id_map'][1];
                        } else {
                            $ids = [1, 2];
                        }
                        if ($k == 0 && $pax_rule['need_lead']) {
                            $ids = $pax_rule['lead_ids'];
                        }


                        ?>
                        <tr>
                            <td><?php echo '姓名' ?></td>
                            <td><?php echo($p['zh_name'] . '  ' . $p['en_name']) ?></td>
                        </tr>
                        <?php
                        if (!empty($ids) && is_array($ids)) {
                            foreach($pax_meta as $id=>$meta){
                                if(!in_array($id,$ids)) continue;
                                if ($id == 1 || $id == 2) continue;
                                $title = $language_id == 1 ? $meta['en_label'] : $meta['label'];
                                $store_field = $meta['storage_field'];
                                if ($store_field == 'gender') {
                                    $value = $p[$store_field] ? '男': '女';
                                } else {
                                    $value = isset($p[$store_field])?$p[$store_field]:'';
                                }
                                ?>
                                <tr>
                                    <td><?php echo $title ?></td>
                                    <td><?php echo $value ?></td>
                                </tr>
                            <?php
                            }
                        } ?>
                    </table>
                </td>
            </tr>
        <?php
        }
    } ?>
</table>
</body>
</html>