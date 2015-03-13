<?php
$language_id = $product['shipping_rule']['language_id'];
$pd = $product['descriptions'][$language_id];

$pax_rule = $product['pax_rule'];
$pax_meta = $product['pax_meta'];

//special value
if(!empty($pd['origin_name'])){
    $pd['name'] = $pd['origin_name'];
}

$special_value = '';
if (!empty($special_info)) {
    foreach($special_info[0]['items'] as $special) {
        $special_value .= ($special['en_name']!=$special['cn_name']?$special['en_name'] .' '.$special['cn_name']:$special['cn_name']).'  ';
    }
    if (!empty($special_info[0]['items'][0]['product_origin_name'])) {
        $pd['name'] = $special_info[0]['items'][0]['product_origin_name'];
        $special_value = '';
    }
}

//departure value
$departure_value = '';
if ($departure_time <> '00:00:00') {
    $departure_value .= date('H:i', strtotime($departure_time));
}
if (!empty($departures[$language_id]['departure_point'])) {
    if ($departure_value) {
        $departure_value .= '  ';
    }
    $departure_value .= $departures[$language_id]['departure_point'];
}


?>

<html>
<body>
<table border="1px" border-collapse="collapse">
    <tr><?php echo $label['introduction'] ?></tr>
    <tr>
        <td><?php echo $label['order_id'] ?></td>
        <td><?php echo $order_id ?></td>
    </tr>
    <?php if ($supplier_order['hitour_booking_ref']) { ?>
        <tr>
            <td><?php echo $label['hitour_booking_ref'] ?></td>
            <td><?php echo $supplier_order['hitour_booking_ref'] ?></td>
        </tr>
    <?php } ?>
    <tr>
        <td><?php echo $label['product_name'] ?></td>
        <td><?php echo $pd['name']  ?></td>
    </tr>
    <?php if (!empty($special_info)) {
        foreach($special_info[0]['items'] as $special){?>
        <tr>
            <td><?php echo $special['group_title'] ?></td>
            <td><?php echo $special['en_name']!=$special['cn_name']?$special['en_name'].' '.$special['cn_name']:$special['cn_name']?></td>
        </tr>
    <?php }
    }?>

    <?php if ($product['date_rule']['need_tour_date']) { ?>
        <tr>
            <td><?php echo $pd['tour_date_title'] ?></td>
            <td><?php echo date('Y-m-d', strtotime($tour_date)) ?></td>
        </tr>
    <?php } ?>

    <?php if ($departure_value) { ?>
        <tr>
            <td><?php echo $pd['departure_title'] ?></td>
            <td><?php echo $departure_value ?></td>
        </tr>
    <?php } ?>

    <tr>
        <td><?php echo $label['quantity'] ?></td>
        <td>
            <?php foreach ($quantities as $k => $v) {
                echo sprintf('%s × %s  ', $language_id == 1 ? $ticket_types[$k]['ticket_type']['en_name'] : $ticket_types[$k]['ticket_type']['cn_name'], $v);
            }?>
        </td>
    </tr>

    <?php if(!empty($pax_num) && $product['type'] == HtProduct::T_HOTEL) { ?>
        <tr>
            <td><?php echo $label['pax_num'] ?></td>
            <td><?php echo $pax_num ?></td>
        </tr>
    <?php } ?>

    <?php if (!empty($passengers) && is_array($passengers)) {
        foreach ($passengers as $k => $p) {
            ?>
            <tr>
                <td>
                    <?php $pax_title = $label['pax'] . ($k + 1);
                    if ($pax_rule['need_lead'] && $k == 0) {
                        $pax_title .= '(' . $label['lead'] . ')';
                    } else if ($p['is_child']) {
                        $pax_title .= '(' . $label['child'] . ')';
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
                            <td><?php echo $label['name'] ?></td>
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
                                    $value = $p[$store_field] ? $label['male'] : $label['female'];
                                } else {
                                    $value = empty($p[$store_field]) ? '' : $p[$store_field];
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