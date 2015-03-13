<?php
//订单详情
$order_product_type =  $order_products[0]['product']['type'];
$order_product_combo = !!$order_products[0]['product']['is_combo'];

?>
<?php if($order_product_type == 8 || $order_product_combo == 1 ) { ?>
    <table <?= $table_style ?> style="<?= $border_grey ?>" width="600px">
        <?php for( $i=1; $i < count($order_products); $i++) {
            //第一个是壳商品，不显示$i从1开始
            $product = $order_products[$i];
            $product_desc = $product['product']['description'];
        ?>
        <tr>
            <td valign="top"
                style="background: url('<?= $image_url ?>circle.png') 20px 3px no-repeat; padding-top: 5px; width: 80px; height: 40px; text-align: center; font-size: 22px; color: #c5c5c5;">
                <?= $i ?>
            </td>
            <td style="padding-bottom: 20px; width: 520px;">
                <table <?= $table_style ?> width="520px;">
                    <tr>
                        <td colspan="2" style="font-size: 24px; color: #525252; padding-top: 5px;"><?= $product_desc['name'] ?></td>
                    </tr>
                    <!-- special,departure,tour_date -->
                    <?php if(!empty($product['special_info'])) {

                        foreach($product['special_info'][0]['items'] as $special){?>
                            <tr>
                                <td width="100" style="font-size: 20px; line-height: 22px; color: #969696;"><?= $special['group_title'] ?>：</td>
                                <td width="390" style="font-size: 20px; line-height: 22px; color: #6db381;"><?= $special['cn_name'] ?></td>
                            </tr>
                        <?php }
                    } ?>
                    <?php if(!empty($product['departures'])) { ?>
                    <tr>
                        <td width="100" style="font-size: 20px; line-height: 22px; color: #969696;"><?= $product_desc['departure_title'] ?>：</td>
                        <td width="390" style="font-size: 20px; line-height: 22px; color: #6db381;"><?= $product['departures']['2']['departure_point'] ?></td>
                    </tr>
                    <?php } ?>
                    <?php if($product['tour_date'] <> '0000-00-00') { ?>
                    <tr>
                        <td width="100" style="font-size: 20px; line-height: 22px; color: #969696;"><?= $product_desc['tour_date_title'] ?>：</td>
                        <td width="390" style="font-size: 20px; line-height: 22px;color: #6db381;"><?= $product['tour_date'] ?></td>
                    </tr>
                    <?php } ?>
                    <?php if(!empty($product['passengers'])) {
                        $passenger_str = '';
                        foreach($product['passengers'] as $one_passenger) {
                            $passenger_str = $passenger_str . $one_passenger['zh_name'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        }
                        $passenger_str = trim($passenger_str);
                    ?>
                    <tr>
                        <td width="100" valign="top" style="font-size: 20px; line-height: 22px; color: #969696;">出行人：</td>
                        <td width="390" style="font-size: 20px; line-height: 22px; color: #969696;"><?= $passenger_str ?></td>
                    </tr>
                    <tr>
                        <td heght="50px"></td>
                        <td heght="50px"></td>
                    </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
        <?php } ?>
    </table>
<?php } else { ?>
    <table <?= $table_style ?> style="<?= $border_grey ?>" width="600px">
        <?php
        $product = $order_products[0];
        $product_desc = $product['product']['description'];
        ?>
        <?php if(!empty($product['special_info'])) {

            foreach($product['special_info'][0]['items'] as $special){?>
                <tr>
                    <td width="100" style="font-size: 20px; line-height: 22px; color: #969696;padding-left: 20px;"><?= $special['group_title'] ?>：</td>
                    <td width="390" style="font-size: 20px; line-height: 22px; color: #6db381;"><?= $special['cn_name'] ?></td>
                </tr>
            <?php }
        } ?>
        <?php if(!empty($product['departures'])) { ?>
            <tr>
                <td width="130" style="font-size: 20px; line-height: 22px; color: #969696; padding-left: 20px;"><?= $product_desc['departure_title'] ?>：</td>
                <td width="370" style="font-size: 20px; line-height: 22px; color: #6db381;"><?= $product['departures']['2']['departure_point'] ?></td>
            </tr>
        <?php } ?>
        <?php if($product['tour_date'] <> '0000-00-00') { ?>
            <tr>
                <td width="130" style="font-size: 20px; line-height: 22px; color: #969696; padding-left: 20px;"><?= $product_desc['tour_date_title'] ?>：</td>
                <td width="370" style="font-size: 20px; line-height: 22px; color: #6db381;"><?= $product['tour_date'] ?></td>
            </tr>
        <?php } ?>
        <?php if(!empty($product['passengers'])) {
            $passenger_str = '';
            foreach($product['passengers'] as $one_passenger) {
                $passenger_str = $passenger_str . $one_passenger['zh_name'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            $passenger_str = trim($passenger_str);
            ?>
            <tr>
                <td width="130" valign="top" style="font-size: 20px; line-height: 22px; color: #969696; padding-left: 20px;">出行人：</td>
                <td width="370" style="font-size: 20px; line-height: 22px; color: #969696;"><?= $passenger_str ?></td>
            </tr>
            <tr>
                <td height="50px"></td>
                <td height="50px"></td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>
