<?php
$gift_num = count($gift_coupons);
?>

<table <?= $table_style ?>  width="560px">
    <tr>
        <td style="color: #6db381; font-size: 40px;" colspan="3">优惠券信息</td>
    </tr>
    <?php for($a = 0, $b = 1 ;$a < $gift_num; $a++, $b++) { ?>
    <tr>
        <td style="padding: 10px 0;" colspan="3">
            <table <?= $table_style; ?> width="560px;">
                <tr>
                    <?php if($gift_num == 1) { ?>
                    <td colspan="2" style="color: #6db381; font-size: 16px;"></td>
                    <?php } else { ?>
                    <td colspan="2" style="color: #6db381; font-size: 16px;">优惠券<?= $b ?>：</td>
                    <?php } ?>
                </tr>
                <tr>
                    <?php
                    if($gift_coupons[$a]['type'] == 'F') {
                        $coupon_type_str = '优惠金额：';
                        $coupon_discount_str = (int)$gift_coupons[$a]['discount'] . '元';
                    } else {
                        $coupon_type_str = '优惠折扣：';
                        $coupon_discount_str = (int)$gift_coupons[$a]['discount'] . '%';
                    } ?>
                    <td width="160" style="font-size: 18px; line-height: 22px; color: #525252;"><?= $coupon_type_str ?></td>
                    <td width="400" style="font-size: 18px; line-height: 22px; color: #525252;"><?= $coupon_discount_str ?></td>
                </tr>
                <tr>
                    <td style="font-size: 18px; line-height: 22px; color: #525252;">兑换截止日期：</td>
                    <td style="font-size: 18px; line-height: 22px; color: #525252;"><?= $gift_coupons[$a]['date_end']; ?></td>
                </tr>
                <tr>
                    <td style="font-size: 18px; line-height: 22px; color: #525252;">优惠码：</td>
                    <td style="font-size: 18px; line-height: 22px; color: #525252;"><?= $gift_coupons[$a]['code'] ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <?php } ?>
    <?php $homeUrl = Yii::app()->params['urlHome'] ?>
    <tr>
        <td colspan="3" height="30px"></td>
    </tr>
    <tr>
        <td width="200"></td>
        <td width="160" height="50px"><a href="<?= $homeUrl ?>account#coupon" target="_blank" style="color: #fff; background: #6db381; font-size: 22px; display: block; line-height: 50px; text-align: center; text-decoration: none;">查看优惠券</a></td>
        <td width="200"></td>
    </tr>
</table>