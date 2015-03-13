<?php
//date_default_timezone_set('Asia/Chongqing');
$CURRENT_TEMPLATE_URL = HTTP_SERVER . 'catalog/view/theme/' . $this->config->get('config_template') . '/static/image/common/';
//$CURRENT_TEMPLATE_URL = 'http://192.168.1.107:8000/catalog/view/theme/hitour/static/image/common/';
/*
$data = array(
  'label' => '悉尼',
  'product' => '悉尼通票',
  'value' => 100,
  'city' => '悉尼',
  'link' => 'http://hitour.cc',
  'email' => 'abc@abc.me',
  'password' => 'helloworld',
  'coupon_code' => '6sHDT'
);*/

$styleTitle = 'color: #FFF; background: #BB0F19; font-size: 22px; height: 44px; line-height: 44px; padding: 0 10px; display: block; margin: 40px 0 10px 0; text-align: left;';
$styleDesc = 'color: #636363; line-height: 24px; font-size: 16px;';
$styleDescHigh = 'color: #BB0F19; height: 24px; line-height: 24px; font-size: 16px; font-weight: bold; padding: 0 8px;';
?>
<html lang="zh">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=943, user-scalable=no, target-densityDpi=device-dpi" />
    <title>恭喜你抢到了<?= $data['product']; ?></title>
  </head>
  <body style='margin: 0; padding: 0;'>
    <div id="header" style="background: #C3BFBE;">
      <img src="<?= $CURRENT_TEMPLATE_URL ?>wechatVoucher_2014.png" style="width: 100%;" />
    </div>
    <div id="header-border" style="margin-top: 2px; background: #D9252D; height: 12px; width: 100%;"></div>
    <table id="content" style="width: 500px; margin: 15px auto; padding-right: 40px;">
      <tbody>
        <tr>
          <th style="<?= $styleTitle; ?>">您的免票兑换码 <?= $data['coupon_code']; ?></th>
        </tr>
        <tr>
          <td style="<?= $styleDesc; ?>">
            <p>恭喜您刮到—————<b style="<?= $styleDescHigh; ?>"><?= $data['product']; ?></b></p>
            <p>价值<?= $data['value']; ?>元，仅限用于兑换一张“<?= $data['product']; ?>”，2014年12月31日前有效。</p>
            <p><?= $data['city']; ?>走起！！</p>
            <p>您可到玩途官网免费领取该奖品</p>
          </td>
        </tr>
        <tr>
          <th style="<?= $styleTitle; ?>">免费领取奖品方法</th>
        </tr>
        <tr>
          <td style="<?= $styleDesc; ?>">
            <ol>
              <li>点击链接进入玩途官方网站<a href="<?= $data['link']; ?>" style="<?= $styleDescHigh; ?>"><br /><?= $data['link']; ?></a></li>
              <li>点击购买</li>
              <li>完成订单填写，并在“支付方式”右侧输入“免费兑换码”</li>
              <li>完成0元订购</li>
              <li>若有任何疑问或问题，请拨打我们的服务热线400-010-1900</li>
            </ol>
          </td>
        </tr>
        <?php if(!empty($data['email'])&&!empty($data['password'])){?>
        <tr>
          <th style="<?= $styleTitle; ?>">什么？还不知道您已经是会员了？</th>
        </tr>
        <tr>
          <td style="<?= $styleDesc; ?>">
            <p>当您收到这封邮件的时候，您已经具备了玩途的会员资格，一切尊贵服务尽情享受。</p>
            <p><b style='<?= $styleDescHigh; ?> padding: 0 15px 0 0;'>用户名</b><?= $data['email']; ?></p>
            <p><b style='<?= $styleDescHigh; ?> padding: 0 15px 0 0;'>密码</b> <?= $data['password']; ?></p>
            <p>（您登陆后可在‘我的账户’中修改此密码）</p>
          </td>
        </tr>
        <tr>
            <?php } else{?>
        <tr>
            <th style="<?= $styleTitle; ?>">您已经是玩途会员，请登录玩途使用您的兑换码</th>
        </tr>
        <?php }?>

          <td>
            <a href="http://hitour.cc/" style='margin: 36px 130px 50px 130px; font-size: 28px; color: #FFF; height: 45px; width: 240px; background-color: #BB0F19; line-height: 45px; text-align: center; text-decoration: none; display: block;'>点击进入玩途</a>
            <p style='height: 20px; line-height: 20px; font-size: 20px; color: #000; padding-left: 121px; padding-top: 20px;'>关注我们微信吧！每周都有惊喜哦</p>
            <img src="<?= $CURRENT_TEMPLATE_URL ?>weixin-2d-code.png" style='margin: 0 180px;' />
          </td>
        </tr>
      </tbody>
    </table>
  </body>
</html>