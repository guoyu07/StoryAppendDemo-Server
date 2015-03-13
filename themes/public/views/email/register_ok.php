<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>玩途自由行-感谢您的注册！</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<table style="width: 808px; margin: 0 auto; font-size: 12px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
	<tr>
		<td style="text-align: center; font-size: 28px; color: #979898; padding-bottom: 48px; padding-top: 40px;">
			欢迎您加入<span style="color: #72cac4;">玩途自由行</span>！
		</td>
	</tr>
	<tr>
		<td style="padding: 0 150px; font-size: 16px; color: #cbcbcb; line-height: 32px;">
			您可以使用当前的邮箱登录到我们的网站（<a href="http://www.hitour.cc/" style="color: #5b9dd9;">WWW.HITOUR.CC</a>). 希望玩途能让您旅行更简单！<br/>
            <?php if($background_login) { ?>
                初始密码为您手机号码的后6位，登录后可以修改密码。希望玩途能让您旅行更简单！<br/>
            <?php } ?>
			玩途预祝您旅途愉快!
		</td>
	</tr>
	<tr>
		<td style="padding: 14px 150px; color: #e5e5e5; font-size: 14px;">这是一封自动产生的邮件，请勿回复！</td>
	</tr>
	<tr>
		<td style="padding-top: 42px; text-align: center; color: white; font-size: 14px;">
			<a href="<?= $LOGIN_URL ?>"><img src="<?= $BASE_URL ?>/image/email/button_goto_hitour.jpg" style="width: 157px; height: 31px;"></a></td>
	</tr>
	<tr>
		<td><img src="<?= $BASE_URL ?>/image/email/register_ok.png" style="width: 808px; height: 141px;"></td>
	</tr>

	<tr>
		<td style="text-align: center; font-size: 12px; color: #e5e5e5; padding-top: 14px;">本邮件来自于HiTour.cc 版权所有</td>
	</tr>

</table>
</body>
</html>
