<?php
//info
$title = "玩途自由行-感谢您的注册";

//styles
$body_style = "style=\"margin:0;padding:0;border-collapse:collapse;border:0;font-family:'Microsoft YaHei','Hiragino Sans GB','Microsoft YaHei','WenQuanYi Micro Hei',sans-serif;\" ";
$border_grey = 'border-bottom: 1px solid #e9e9e9;';
$table_style = 'align="center" cellpadding="0" cellspacing="0"  border="0"';

//path
$image_url = Yii::app()->request->hostinfo . Yii::app()->params['WEB_PREFIX'] . Yii::app()->params['THEME_BASE_URL'] . '/images/email/new_email/';
$homeUrl = Yii::app()->params['urlHome'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?= $title; ?></title>
    <meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1.0; user-scalable=no;">
</head>
<body <?= $body_style ?> >
    <center>
        <table <?= $table_style ?> width="640px" style="color: #525252; border: 1px solid #dddddd;">
            <tbody>
                <!-- header -->
                <tr>
                    <td><img height="116px" width="640px" src="<?= $image_url ?>header.jpg" alt="玩途自由行"
                             style="vertical-align: top;" /></td>
                </tr>
                <!-- welcome -->
                <tr>
                    <td style="padding: 36px; background: #f7f7f7; border-bottom: 1px solid #e9e9e9;">
                        <table <?= $table_style ?> width="568px">
                            <tr>
                                <td colspan="2" style="font-size:22px; padding-bottom:42px;">
                                    亲爱的<?= $firstName ?>，您好！
                                </td>
                            </tr>
                            <tr>
                                <td width="140" align="right">
                                    <img src="<?= $image_url ?>success.png" alt="" width="40px" height="40px" style="vertical-align: top; padding-bottom: 18px;"/>
                                </td>
                                <td width="500" style="font-size: 36px; padding-left: 10px; padding-bottom: 18px;">
                                    欢迎您订阅玩途自由行！
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- info -->
                <tr>
                    <td style="padding: 70px 40px 70px; border-bottom: 1px solid #e9e9e9;">
                        <table <?= $table_style ?> width="560px">
                            <tr>
                                <td colspan="3" style="padding-bottom: 70px; font-size: 22px;">
                                    您的优惠券：<span style="font-size: 22px; color: #6db381; padding-right: 5px;">
                                        <?= $coupon['code'] ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="padding-bottom: 70px; font-size: 22px;">
                                    <?php
                                    if($coupon['type'] == 'F') {
                                        $coupon_type_str = '优惠金额：';
                                        $coupon_discount_str = (int)$coupon['discount'] . '元';
                                    } else {
                                        $coupon_type_str = '优惠折扣：';
                                        $coupon_discount_str = (int)$coupon['discount'] . '%';
                                    } ?>
                                    <?php echo $coupon_type_str ?> <span style="font-size: 22px; color: #6db381; padding-right: 5px;"><?php echo $coupon_discount_str ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="padding-bottom: 70px; font-size: 22px;">
                                    有效期： <span style="font-size: 22px; color: #6db381; padding-right: 5px;"><?php echo $coupon['date_end']?></span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="padding-bottom: 70px; font-size: 22px;">
                                    请注意，该优惠券只能够使用一次，可全额抵扣hitour.cc所有非活动产品，且不与其他活动叠加使用！<br><br>
                                    该优惠券的最终解释权归玩途所有，如有优惠券使用规则变动，一切以玩途官网为准，请尽快使用您的优惠券！
                                </td>
                            </tr>
                            <tr>
                                <td width="180"></td>
                                <td width="200" height="50px;" style="background: #6db381;"><a href="<?= $homeUrl; ?>" target="_blank" style="display: block; text-decoration: none; text-align: center; color: #fff; font-size: 24px; line-height: 50px;">立刻踏上旅程</a></td>
                                <td width="180"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- help -->
                <tr>
                    <td>
                        <?php include('new_email_help.php'); ?>
                    </td>
                </tr>
                <!-- footer -->
                <tr>
                    <td>
                        <?php include('new_email_footer.php'); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </center>
</body>
</html>