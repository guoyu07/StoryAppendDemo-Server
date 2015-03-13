<?php
//info
$title = "玩途自由行";

//styles
$body_style = "style=\"margin:0;padding:0;border-collapse:collapse;border:0;font-family:'Microsoft YaHei','Hiragino Sans GB','Microsoft YaHei','WenQuanYi Micro Hei',sans-serif;\" ";
$border_grey = 'border-bottom: 1px solid #e9e9e9;';
$table_style = 'align="center" cellpadding="0" cellspacing="0"  border="0"';

//path
$image_url = Yii::app()->request->hostinfo . Yii::app()->params['WEB_PREFIX'] . Yii::app()->params['THEME_BASE_URL'] . '/images/email/new_email/';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?= $title ?></title>
    <meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1.0; user-scalable=no;">
</head>
<body <?= $body_style ?> >
    <center>
        <table <?= $table_style ?> width="640px" style="color: #525252; border: 1px solid #dddddd;">
            <tbody>
                <!-- header -->
                <tr>
                    <td><img height="116px" width="640px" src="<?= $image_url ?>header.jpg" alt="玩途自由行" style="vertical-align: top;" /></td>
                </tr>
                <!-- 订单状态 -->
                <tr>
                    <td style="padding: 36px; background: #f7f7f7; border-bottom: 1px solid #e9e9e9;">
                        <?php include('new_email_status.php'); ?>
                    </td>
                </tr>
                <!-- 订单信息 -->
                <tr>
                    <td style="padding: 35px 20px 50px;">
                        <?php include('new_email_info.php'); ?>
                    </td>
                </tr>
                <!-- 订单详细 -->
                <?php if(!empty($order_products)) { ?>
                <tr>
                    <td style="padding:0 20px;">
                        <?php include('new_email_detail.php'); ?>
                    </td>
                </tr>
                <?php } ?>
                <!-- 订单金额查看订单 -->
                <tr>
                    <td style="padding:50px 20px 30px;">
                        <?php include('new_email_amount.php'); ?>
                    </td>
                </tr>
                <!-- 兑换单 已发货才会出现 -->
                <?php if( $order['status_id'] == 3 || $order['status_id'] == 2 || $order['status_id'] == 6 || $order['status_id'] == 9 ) { ?>
                <tr>
                    <td style="border-top: 1px solid #e9e9e9; border-bottom:1px solid #e9e9e9; padding: 50px 40px;">
                        <?php include('new_email_redeem.php'); ?>
                    </td>
                </tr>
                <?php } ?>
                <!-- 优惠券信息 -->
                <?php if(!empty($gift_coupons)) { ?>
                    <tr>
                        <td style="border-bottom: 1px solid #e9e9e9; padding: 50px 40px 50px;">
                            <?php include('new_email_coupon.php'); ?>
                        </td>
                    </tr>
                <?php } ?>
                <!-- 保险码 -->
                <?php if(!empty($insurance_codes)) { ?>
                <tr>
                    <td style="border-bottom: 1px solid #e9e9e9; padding: 50px 40px 50px;">
                        <?php include('new_email_insurance.php'); ?>
                    </td>
                </tr>
                <?php } ?>

                <!-- 微博活动，发货邮件可见-->
                <?php if( $order['status_id'] == 3 || $order['status_id'] == 2 || $order['status_id'] == 6 || $order['status_id'] == 9 ) { ?>
                    <tr>
                        <td style="border-top: 1px solid #e9e9e9; border-bottom:1px solid #e9e9e9; padding: 50px 40px 20px;">
                            <?php include('new_email_weibo.php'); ?>
                        </td>
                    </tr>
                <?php } ?>

                <!-- help -->
                <tr>
                    <td>
                        <?php include('new_email_help.php'); ?>
                    </td>
                </tr>
                <!-- footer -->
                <tr >
                    <td>
                        <?php include('new_email_footer.php'); ?>
                    </td>
                </tr>
            </tbody>
        </table>

    </center>
</body>