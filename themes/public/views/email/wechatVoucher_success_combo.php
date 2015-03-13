<?php
$CURRENT_TEMPLATE_URL = HTTP_SERVER . 'catalog/view/theme/' . $this->config->get('config_template') . '/static/image/common/';

$styleTitle = 'color: #FFF; background: #000; font-size: 22px; height: 44px; line-height: 44px; padding: 0 12px; display: block';
$styleDesc = 'color: #636363; line-height: 24px; font-size: 16px; padding: 12px 6px 24px 6px;';
$styleDescHigh = 'color: #EAAB13; height: 24px; line-height: 24px; font-size: 16px; font-weight: bold; padding: 0 8px;';
?>
<html lang="zh">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=540, user-scalable=no, target-densityDpi=device-dpi" />
    <title>恭喜你抢到了优惠码</title>
  </head>
  <body style='margin: 0; padding: 0;'>
    <center>
      <table id="background" style='width: 100%'>
        <tbody>
          <tr style='height: 338px; background-color: #EAAB13; width: 100%;'>
            <td>
            <table style='width: 520px; height: 225px; margin: 0 auto; padding: 55px 0;'>
              <tr>
                <td style='color: #FFF; height: 130px; line-height: 130px; font-size: 80px; vertical-align: bottom;'>
                  <img src="<?= $CURRENT_TEMPLATE_URL ?>gift.png" />
                  <span style='vertical-align: bottom;'>抢到啦！</span>
                </td>
              </tr>
              <tr>
                <td style='color: #FFEDC2; height: 30px; line-height: 30px; font-size: 28px; margin-top: 65px;'>恭喜您抢到“新加坡环球影城组合套票”<br><br>包含：环球影城一日票+24小时隨上随下观光车+船长鸭子船之旅</td>
              </tr>
            </table>
            </td>
          </tr>
          <tr>
            <td>
              <table style='width: 520px; margin: 0 auto;'>
                <tr style='height: 52px;'>
                  <td></td>
                </tr>
                <tr>
                  <td style='<?= $styleTitle; ?>'>您的优惠码号为 <?= $data['voucher_code']; ?></td>
                </tr>
                <tr>
                  <td style='<?= $styleDesc; ?>'>
										该优惠码有效期截止到<?= $data['avail_date']; ?><br /> <br />
										1. 通过以下链接，购买“新加坡环球影城组合套票” （原价624，输入优惠券号码后价格为<?= sprintf('%.0f', $data['special_price']); ?>）<br/></br/>
										<a href="<?=($this->url->link('detail/detail', 'product_id='.$data['product_id']))?>"><?=($this->url->link('detail/detail', 'product_id='.$data['product_id']))?></a><br />
										2. <span style="color: #ff0000; ">请用您抢票的邮件地址登录玩途<?php  if(strlen($data['password'])>0) { echo '，初始密码为：' . $data['password'];} ?></span>，您的优惠券已经与您的账号相绑定。<br /><br />
                    3. 请在支付前，输入以上优惠码（如下图）<br />
                    <img width="510" src="<?= $CURRENT_TEMPLATE_URL ?>instruction1.png" /><br />
                    4. 当您在玩途官网完成支付宝支付后，您的订单即已经完成提交。我们将在一周内把正式的“新加坡环球影城组合票”的兑换确认单（voucher）发送到您的邮箱。<br /><br />
                    注：由于您本次参加了“新加坡环球影城组合票”的<?= sprintf('%.0f', $data['special_price']); ?>元限时特惠，该订单无法享受退货处理，敬请理解。
                  </td>
                </tr>
                <!-- <tr>
                  <td style='<?//= $styleTitle; ?>'>什么？还不知道您已经是会员了？</td>
                </tr>
                <tr>
                  <td style='<?//= $styleDesc; ?>'>
                   请登录玩途官网：<a href="http://hitour.cc">http://hitour.cc</a><br/>
                   <br/>
                   <span style='<?//= $styleTitle; ?>'>使用您的邮箱为自己注册一个玩途的账户吧，我们为您提供丰富的海外地面服务和资讯哦！</span>
                   当您收到这封邮件的时候，您已经具备了玩途的会员资格，一切尊贵服务尽情享受。<br />
                   <b style='<?//= $styleDescHigh; ?>'>用户名</b><?//= $data['email']; ?><br />
                   <b style='< $styleDescHigh; ?>'>密码</b> <?//= $data['password']; ?><br />
                   （您登陆后可在‘我的账户’中修改此密码）
                  </td>
                </tr> -->
                <tr>
                  <td style='margin: 0 auto; width: 245px;'>
<!--                    <a href="http://m.hitour.cc/" style='margin: 36px 130px 50px 130px; font-size: 28px; color: #FFF; height: 45px; width: 240px; background-color: #EAAB13; line-height: 45px; text-align: center; text-decoration: none; display: block;'>点击进入玩途</a>-->
                    <p style='height: 20px; line-height: 20px; font-size: 20px; color: #000; padding-left: 121px; padding-top: 20px;'>关注我们微信吧！每周都有惊喜哦</p>
                    <img src="<?= $CURRENT_TEMPLATE_URL ?>weixin-2d-code.png" style='margin: 0 130px;' />
                  </td>
                </tr>
                <tr>
                  <td></td>
                </tr>
              </table>
            </td>
          </tr>
        </tbody>
      </table>
    </center>
  </body>
</html>
