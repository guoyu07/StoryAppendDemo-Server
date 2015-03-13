<?php
$homeUrl = Yii::app()->params['urlHome'];
$CURRENT_TEMPLATE_URL = Yii::app()->request->hostinfo . Yii::app()->params['WEB_PREFIX'] . Yii::app()->params['THEME_BASE_URL'];
$need_billing = $order['order_id'] != 798;
foreach ($product['descriptions'] as $pd) {
    if ($pd['language_id'] == 2) {
        $description = $pd;
        break;
    }
}
$description = $product['descriptions'][2];
$is_redeem =  $gift_coupons[0]['type']=='P' && $gift_coupons[0]['discount'] == 100;
?>

<?php
/*#top-container*/
$IDtc = 'width: 100%; color: #000; margin: 0 auto; font-size: 20px; background: #ffffff; font-family: "Microsoft YaHei", "Hiragino Sans GB", sans-serif;';
$IDtc_Ea = 'color: #00BA8E; font-size: 20px; text-decoration: none;';
$IDtc_Eh3 = 'color: #00BA8E; font-size: 24px; font-weight: bold;';
$IDtc_Cheader_n_Cfooter = 'width: 640px; background: #f26d62;height:120px;';

/*#content-start*/
$email_bg_white = 'background-color:white';
$email_intro = 'font-size:24px;color:#666666;line-height:40px';
$email_large_font = 'font-size:30px;color:#f26d62;';
$email_sub_intro = 'font-size:22px;color:#666666;line-height:35px';

//layout
$email_center_odd = 'width: 640px;margin: 0px auto;border-bottom:1px solid #a0a0a0;background-color:#ffffff';
$email_center_even = 'width: 640px;margin: 0px auto;border-bottom:1px solid #a0a0a0;background-color:#f8f8f8';

//words-style
$block_title = 'color:#000000;font-size:40px;padding-left:20px;';
$block_mark = 'background-color:#000000;height:40px;width:14px;';

$order_title = 'color:#666666;font-size:24px;line-height:40px;';
$order_content = 'background-color:#eeeeee;color:#000000;font-size:30px;line-height:46px;padding-left:30px;padding-right:30px;';
$order_date = 'color:#ff6600;font-size:30px;';
$order_detail = 'text-align:center;';
$order_link = 'border: 1px solid #399253;color:#ffffff;font-size:30px;background-color:#6db381;padding:10px 20px;text-decoration:none;';

$bill_words = 'font-size:22px;color:#525252;line-height:40px;vertical-align:top;';

$large_words = 'font-size:30px;color:#525252;text-align:center;';

/*#footer-container*/
$IDfc = 'color: #FFF; font-size: 20px;';
$IDfc_Etd = 'padding: 10px;';

/*#content-container*/
$IDcc = 'width: 640px; border: 1px solid #EBEBEB; background: #FFF;';
$IDcc_EtrCspace = 'height: 35px;';
$IDcc_EtrCtitle_row = 'color: #00BA8E';
$IDcc_EtrCborder_row = 'height: 1px;';
$IDcc_EtrCborder_row_EtdCborder = 'border-top: 4px solid #F0F0F0;';
$IDcc_EtrCdash_border_row_EtdCborder = 'border-top: 1px dashed #EBEBEB;';

$IDcc_Etd = 'padding: 5px;';
$IDcc_EtdCpad_left = 'width: 1px; padding-left: 40px;';
$IDcc_EtdCpad_right = 'width: 1px; padding-right: 40px;';
$IDcc_EtdCicon = 'width: 40px;';
$IDcc_EtdCleft_title = 'width: 210px;';
$IDcc_EtdCright_title = 'width: 140px; text-align: right;';
$IDcc_EtdCmain_content = 'width: 300px;';

$IDcc_Ctall = 'line-height: 30px;';
$IDcc_Csmall = 'font-size: 14px; line-height: 18px;';
$IDcc_Corder = 'font-size: 26px;';
$IDcc_Cgrey = 'color: #939393;';
$IDcc_Cgreen = 'color: #00BA8E;';

if (!function_exists('getPrettyDate')) {
    function getPrettyDate($str) {
        $strdate = strtotime($str);
        $newdate = date('Y年m月d日 ', $strdate);
        $week_array = array("日","一","二","三","四","五","六");
        return $newdate . '星期' . $week_array[date('w', $strdate)];
    }
} ?>

<html lang="zh">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=640, initial-scale=1">
    <title>玩途自由行 - 预定成功</title>
</head>
<body>
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

        #content-container td {
            padding: 5px;
        }

        #footer-container td {
            padding: 8px;
        }
    </style>
    <table id="top-container" align="center" style="<?= $IDtc; ?>">
        <tr class="top-header" style="<?= $IDtc_Cheader_n_Cfooter; ?>">
            <td>
                <center>
                    <img src="<?php echo $CURRENT_TEMPLATE_URL; ?>/images/email/coupon_header.png" />
                </center>
            </td>
        </tr>

        <tr class="top-content">
            <td>
                <table style="<?= $email_center_odd ?>">
                    <tr style="height: 40px"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td style="<?= $email_intro ?>"><?= $order['contacts_name'] ?>，您好！</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td style="<?= $email_intro ?>">恭喜您成功抢购<span style="<?= $email_large_font?>">玩途周五5时5折限时秒杀</span>活动产品。请核实以下订单信息，如遇问题请联系我们，我们会竭诚为您服务。</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 40px"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td><span style="width: 350px;border-top:1px solid #a0a0a0;display:block;"></span></td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 30px"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td style="<?= $email_sub_intro ?>">感谢您选择玩途伴您旅行！</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td style="<?= $email_sub_intro ?>">玩途自由行</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td style="<?= $email_sub_intro ?>">服务热线：400-010-1900</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 30px"></tr>
                </table>
            </td>
        </tr>

        <tr class="order-info">
            <td>
                <table style="<?= $email_center_even?>">
                    <tr style="height: 50px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td style="<?= $block_mark?>"></td>
                        <td style="<?= $block_title?>">订单信息</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 35px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $order_title?>">商品名称：</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $order_content?>"><?php echo $product['description']['name'] ?></td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 35px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $order_title?>">订单号：</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $order_content?>"><?= $order['order_id'] ?></td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 35px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $order_title?>">预订人：</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $order_content?>"><?= $order['contacts_name'] ?></td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 35px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $order_title?>">预订邮箱：</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $order_content?>"><?= $order['contacts_email'] ?></td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 60px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="border-top: 1px dashed #a0a0a0;"></td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 60px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $order_date ?>">兑换截止日期：<?php echo $gift_coupons[0]['date_end']; ?></td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 20px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $order_date ?>"><img src="<?php echo $CURRENT_TEMPLATE_URL; ?>/images/email/boss.png">此订单不可退订</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 30px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $order_detail ?>"><a href="<?= $order['account_order_url']; ?>" target="_blank" style="<?= $order_link ?>">查看订单详情 >></a></td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 50px;"></tr>
                </table>
            </td>
        </tr>

        <tr class="bill-info">
            <td>
                <table style="<?= $email_center_odd?>">
                    <tr style="height: 50px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td style="<?= $block_mark?>"></td>
                        <td style="<?= $block_title?>">您的账单</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 35px;"></tr>
                    <?php foreach ($order_product['prices'] as $pi) { ?>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td style="<?= $bill_words?>" colspan="2">
<!--                        --><?php //echo $ticket_types[$pi['ticket_id']]['ticket_type']['cn_name'] ?>
                        <span> x <?= $pi['quantity'] ?></span>
                        <?php if ($need_billing) { ?>
                            <span> x <?= $pi['price'] ?></span>
                        <?php } ?>
                        </td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <? } ?>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="border-top: 2px solid #a0a0a0;"></td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="text-align: right;">
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $bill_words?>">总额 <?php echo $order_product['total'] ?> RMB</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="text-align: right;">
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2"style="<?= $bill_words?>">实际支付 <?php echo $order['total'] ?> RMB</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 60px;"></tr>
                </table>
            </td>
        </tr>

        <tr class="coupon-info">
            <td>
                <table style="<?= $email_center_even?>">
                    <tr style="height: 50px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td style="<?= $block_mark?>"></td>
                        <td style="<?= $block_title?>"><?php echo $is_redeem?'一年有效抵用券':'优惠券'?></td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 35px;"></tr>
                    <?php if($is_redeem){ ?>
                        <tr>
                            <td style="<?= $IDcc_EtdCpad_left?>"></td>
                            <td style="<?= $bill_words?>">1.</td>
                            <td style="<?= $bill_words?>">您所购买的是该产品的全额抵用券，可在一年内任何时间在玩途官网预定<a href="<?php echo Yii::app()->request->hostinfo .$gift_coupons[0]['limit_ids'][0]['link_url'] ?>" target="_blank" style=""><?php echo $gift_coupons[0]['limit_ids'][0]['name'] ?></a>，无需另外付费。</td>
                            <td style="<?= $IDcc_EtdCpad_right?>"></td>
                        </tr>
                        <tr>
                            <td style="<?= $IDcc_EtdCpad_left?>"></td>
                            <td style="<?= $bill_words?>">2.</td>
                            <td style="<?= $bill_words?>">确定出行计划后，到指定商品预定支付，在“输入优惠券”处输入该抵用券，无需另外支付即可预定该商品。</td>
                            <td style="<?= $IDcc_EtdCpad_right?>"></td>
                        </tr>
                    <?php } else{?>
                        <tr>
                            <td style="<?= $IDcc_EtdCpad_left?>"></td>
                            <td style="<?= $bill_words?>">1.</td>
                            <td style="<?= $bill_words?>">您所购买的是该产品的现金抵用券，价值<?php echo (int)$gift_coupons[0]['discount']?>元，可以在三个月内任意时间在玩途官网预定<a href="<?php echo Yii::app()->request->hostinfo .$gift_coupons[0]['limit_ids'][0]['link_url'] ?>" target="_blank" style=""><?php echo $gift_coupons[0]['limit_ids'][0]['name'] ?></a>，并使用现金抵用券，同时要遵守该产品的购买和使用规则，特殊热门时间以及可选游玩产品的选购，需要用户自行补差价购买。</td>
                            <td style="<?= $IDcc_EtdCpad_right?>"></td>
                        </tr>
                        <tr>
                            <td style="<?= $IDcc_EtdCpad_left?>"></td>
                            <td style="<?= $bill_words?>">2.</td>
                            <td style="<?= $bill_words?>">确定出行计划后，到指定商品页面使用现金抵用券购买，在“输入优惠券”处输入该抵用券，如遇热门时间酒店涨价或自行选择更多可选游玩内容，超出抵用券金额，需要您资金支付差价完成购买。</td>
                            <td style="<?= $IDcc_EtdCpad_right?>"></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td style="<?= $bill_words?>">3.</td>
                        <td style="<?= $bill_words?>">您可在您的账户里“我的优惠券”中查到该抵用券的详细情况。</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td style="<?= $bill_words?>">4.</td>
                        <td style="<?= $bill_words?>">此抵用券仅限本人使用，不可转让。</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 35px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $bill_words?>">抵用券码</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $bill_words?>"><?php echo $gift_coupons[0]['code'] ?></td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 20px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $bill_words?>">有效期</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $bill_words?>"><?php echo $gift_coupons[0]['date_start'] . '——' . $gift_coupons[0]['date_end'] ?></td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 20px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $bill_words?>">优惠金额</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $bill_words?>"><?php echo $is_redeem?'全额抵用':'￥'.(int)$gift_coupons[0]['discount']?></td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 50px;"></tr>
                </table>
            </td>
        </tr>

        <tr class="coupon-info">
            <td>
                <table style="<?= $email_center_even ?>">
                    <tr style="height: 50px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left ?>"></td>
                        <td style="<?= $block_mark ?>"></td>
                        <td style="<?= $block_title ?>">微博活动</td>
                        <td style="<?= $IDcc_EtdCpad_right ?>"></td>
                    </tr>
                    <tr style="height: 35px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left ?>"></td>
                        <td style="<?= $bill_words ?>"></td>
                        <td style="<?= $bill_words ?>">玩途君希望与你分享你的旅途故事，并传达给更多热爱旅行的人。</td>
                        <td style="<?= $IDcc_EtdCpad_right ?>"></td>
                    </tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left ?>"></td>
                        <td style="<?= $bill_words ?>"></td>
                        <td style="<?= $bill_words ?>">在旅途中，分享见闻和感受，并在微博上@玩途 就能获得奖励哦！</td>
                        <td style="<?= $IDcc_EtdCpad_right ?>"></td>
                    </tr>
                    <tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left ?>"></td>
                        <td colspan="2"
                            style="<?= $bill_words?> text-align: center;"><a href="<?= $homeUrl ?>ad/index?url=http://t.cn/RZL4s1N" target="_blank" style="<?= $order_link ?>">查看活动详情</a></td>
                        <td style="<?= $IDcc_EtdCpad_right ?>"></td>
                    </tr>
                    <tr style="height: 50px;"></tr>
                </table>
            </td>
        </tr>
        <tr class="footer-info">
            <td>
                <table style="<?= $email_center_odd?>">
                    <tr style="height: 40px;">
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td style="width: 275px;"></td>
                        <td style="width: 275px;"></td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td colspan="2" style="<?= $large_words ?>">感谢您选择玩途伴您旅行，祝您出行愉快</td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height: 40px;"></tr>
                    <tr>
                        <td style="<?= $IDcc_EtdCpad_left?>"></td>
                        <td>
                            <table style="text-align: center;color:#000000;">
                            <tr style="text-align: center;">
                                <td><img src="<?php echo $CURRENT_TEMPLATE_URL; ?>/images/email/hitour-cc.png"></td>
                            </tr>
                            <tr style="font-size:18px;color:#525252;line-height: 30px;"><td>服务号：hitour-cc</td></tr>
                            <tr style="font-size:24px;line-height: 30px;"><td>关注玩途微信</td></tr>
                            <tr style="font-size:24px;line-height: 30px;"><td>随时跟踪订单状态</td></tr>
                            <tr style="height: 30px;"></tr>
                            <tr><td style="font-size:20px;">咨询电话：400-010-1900</td></tr>
                            </table>
                        </td>
                        <td>
                            <table style="text-align: center;color:#000000;">
                            <tr>
                                <td><img src="<?php echo $CURRENT_TEMPLATE_URL; ?>/images/email/hitour_weibo.png"></td>
                            </tr>
                            <tr style="font-size:18px;color:#525252;line-height: 30px;"><td><a href="http://weibo.com/hitourwantu?topnav=1&wvr=5&topsug=1" target="_blank">新浪微博：@玩途</a></td></tr>
                            <tr style="font-size:24px;line-height: 30px;"><td>关注玩途微博</td></tr>
                            <tr style="font-size:24px;line-height: 30px;"><td>获得最新优惠资讯</td></tr>
                            <tr style="height: 30px;"></tr>
                            <tr><td style="font-size:20px;">官方QQ群：171941349</td></tr>
                            </table>
                        </td>
                        <td style="<?= $IDcc_EtdCpad_right?>"></td>
                    </tr>
                    <tr style="height:40px;"></tr>
                </table>
            </td>
        </tr>

        <tr class="top-header" style="<?= $IDtc_Cheader_n_Cfooter; ?>">
            <td>
                <center>
                    <img src="<?php echo $CURRENT_TEMPLATE_URL; ?>/images/email/coupon_header.png" />
                </center>
            </td>
        </tr>
    </table>

</body>
</html>
