<?php
/*
 * orange: ff6600
price gray: c1c1c1
border gray: c0c0c0
footer note gray: 525252
footer text gray: 999999
footer background gray: f9f9f9
product background gray: efefef
product description gray: 777777
background beige: e9e5da
 */
$table_style = "margin: 0; padding: 0; border-collapse: collapse; border: 0; font-family: 微软雅黑, 'Hiragino Sans GB', 'Microsoft YaHei', 'WenQuanYi Micro Hei', sans-serif;";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>玩途－<?= $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
        }

        img {
            border: 0;
            height: auto;
            outline: none;
            line-height: 100%;
            text-decoration: none;
        }

        table td {
            border-collapse: collapse;
        }

        #outlook a {
            padding: 0;
        }
    </style>
</head>
<body style="background: #e9e5da; <?= $table_style; ?>">
    <center style="background: #e9e5da;">
        <p style="display: none;"><?= $description; ?></p>
        <table id="logo-container" style="width: 640px; <?= $table_style; ?>" border="0" cellspacing="0"
               class="editable">
            <tbody>
                <tr style="height: 100px;">
                    <td style="text-align: center;">
                        <img src="img/logo_big.png" width="162" height="45" alt="玩途自由行" />
                    </td>
                </tr>
            </tbody>
        </table>
        <table id="head-container"
               style="<?= $table_style; ?> width: 640px; background: #ffffff url('img/top.png') no-repeat top center;"
               border="0" cellspacing="0">
            <tbody>
                <tr style="height: 160px;">
                    <td style="text-align: center;">
                        <a href="<?= $title_link; ?>" style="text-decoration: none; color: #000000;">
                            <span style="font-size: 54px; line-height: 74px;"><?= $title; ?></span>
                            <br />
                            <span style="font-size: 26px; line-height: 26px;"><?= $small_title; ?></span>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left;">
                        <p style="margin: 40px 0; padding: 0 40px; font-size: 20px; line-height: 48px;">
                            <?= $description; ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
        foreach($groups as $key => $group) {
            $bg_color = ($key % 2 == 0) ? '#ffffff' : '#efefef';
            ?>
            <table class="set-container"
                   style="<?= $table_style; ?> width: 640px; background: <?= $bg_color; ?>; border-bottom: 1px solid #c1c1c1;"
                   border="0" cellspacing="0">
                <tbody>
                    <tr>
                        <td style="text-align: center;">
                            <p style="margin: 45px 0 60px 0; font-size: 32px; line-height: 32px;">
                                <a href="<?= $group['title_link']; ?>"
                                   style="text-decoration: none; color: #000000;"><?= $group['title']; ?></a>
                            </p>
                        </td>
                    </tr>
                    <?php
                    foreach($group['group_products'] as $product) {
                        ?>
                        <tr>
                            <td>
                                <table class="set-products-container" style="<?= $table_style; ?> width: 640px;"
                                       border="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 0 30px;">
                                            <a style="text-decoration: none;" title="<?= $product['product_name'] ?>"
                                               href="<?= $product['product_link'] ?>" target="email">
                                                <img
                                                    src="img/g<?= $group['group_id'] ?>p<?= $product['product_id'] ?>.jpg"
                                                    width="580" height="250" alt="<?= $product['product_name'] ?>"
                                                    style="width: 580px; height: 250px;" />
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0 40px 20px 40px;">
                                            <p style="color: #000000; font-size: 26px; font-weight: bolder; line-height: 32px; margin: 12px 0; padding: 0;">
                                                <a href="<?= $product['product_link'] ?>"
                                                   style="text-decoration: none; color: #000000;">
                                                    <?= $product['product_name'] ?>
                                                </a>
                                            </p>
                                            <p style="color: #777777; font-size: 20px; line-height: 30px; margin: 0; padding: 0;">
                                                <a href="<?= $product['product_link'] ?>"
                                                   style="text-decoration: none; color: #777777;">
                                                    <?= $product['product_description'] ?>
                                                </a>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0 30px 40px 30px; text-align: right;">
                                    <span style="vertical-align: middle;">
                                        <span
                                            style="font-size: 14px; line-height: 14px; color: #c1c1c1; text-decoration: line-through; vertical-align: baseline;"><?= $product['orig_price'] ?>
                                            元</span>
                                        <span
                                            style="font-size: 16px; line-height: 30px; color: #ff6600; vertical-align: baseline;">
                                            <span style="font-size: 30px;"><?= $product['price'] ?></span>
                                            元起
                                        </span>
                                    </span>
                                            &nbsp;&nbsp;
                                            <a href="<?= $product['product_link'] ?>"
                                               style="color: #ffffff; padding: 10px 24px; background: #ff6600; text-decoration: none;">去看看</a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        <?php
        }
        ?>
        <table id="foot-container"
               style="<?= $table_style; ?> width: 640px; background: #f9f9f9; border-bottom: 1px solid #c1c1c1;"
               border="0" cellspacing="0">
            <tbody>
                <tr style="height: 32px;">
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td style="width: 90px;"></td>
                    <td style="width: 150px; text-align: center;">
                        <img src="img/wechat.png" width="139" height="139" alt="玩途官方微信" />
                        <p style="color: #525252; font-size: 24px; line-height: 24px; margin: 20px 0 15px 0; padding: 0;">
                            微信客服</p>
                        <p style="color: #525252; font-size: 18px; line-height: 30px; margin: 0; padding: 0;">
                            关注玩途微信<br />
                            获得最新优惠资讯
                        </p>
                    </td>
                    <td style="width: 60px;"></td>
                    <td style="width: 340px;">
                        <table id="contact-container" style="<?= $table_style; ?> width: 340px;" border="0"
                               cellspacing="0">
                            <tbody>
                                <tr>
                                    <td style="width: 60px;">
                                        <img src="img/weibo.png" width="61" height="60" alt="玩途官方微博" />
                                    </td>
                                    <td style="width: 20px;">
                                    </td>
                                    <td style="width: 260px;">
                                        <a href="http://weibo.com/u/3211176940" title="玩途官方微博"
                                           style="text-decoration: none;">
                                            <span style="font-size: 22px; color: #000000;">@玩途</span>
                                            <br />
                                            <span style="font-size: 18px; color: #999999;">关注玩途获取最新动态</span>
                                        </a>
                                    </td>
                                </tr>
                                <tr style="height: 25px;">
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="width: 60px;">
                                        <img src="img/phone.png" width="60" height="60" alt="玩途官方电话" />
                                    </td>
                                    <td style="width: 20px;">
                                    </td>
                                    <td style="width: 260px;">
                                        <a href="tel:4000101900" title="玩途官方电话" style="text-decoration: none;">
                                            <span style="font-size: 22px; color: #000000;">400-010-1900</span>
                                            <br />
                                            <span style="font-size: 18px; color: #999999;">400-010-1900</span>
                                        </a>
                                    </td>
                                </tr>
                                <tr style="height: 25px;">
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="width: 60px;">
                                        <img src="img/email.png" width="60" height="60" alt="玩途官方邮箱" />
                                    </td>
                                    <td style="width: 20px;">
                                    </td>
                                    <td style="width: 260px;">
                                        <a href="mailto:service@hitour.cc" title="玩途官方邮箱"
                                           style="text-decoration: none;">
                                            <span style="font-size: 22px; color: #000000;">service@hitour.cc</span>
                                            <br />
                                            <span style="font-size: 18px; color: #999999;">发送意见反馈</span>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr style="height: 32px;">
                    <td colspan="4"></td>
                </tr>
            </tbody>
        </table>
        <table id="subscribe-container" style="<?= $table_style; ?> width: 640px;" border="0" cellspacing="0">
            <tbody>
                <tr>
                    <td style="font-size: 16px; line-height: 24px; color: #525252; text-align: center; padding: 30px 0;">
                        这是一封自动产生的邮件，请勿回复！<br />
                        如果您对玩途的邮件内容感兴趣可以
                        <a style="color: #525252;" title="订阅玩途邮件" href="{$PLUGINLINK=subscribe}"
                           target="email">点击这里订阅</a>。<br />
                        如果您不想再收到此类邮件，请<a style="color: #525252;" href="{$PLUGINLINK=unsubscribe}"
                                         target="email">点击这里退订</a>。
                    </td>
                </tr>
            </tbody>
        </table>
    </center>
</body>