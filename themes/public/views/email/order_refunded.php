<html lang="zh">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=640, initial-scale=1">
	<title>玩途自由行 - 退订成功！</title>
</head>
<body>
	<?php include('order_email_header.php'); ?>
	<tr class="notice">
		<td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
		<td class="icon" style="<?= $IDcc_EtdCicon ?>"><img src="themes/public/images/email/check.png" /></td>
		<td colspan="3">
			您的退订已生效！
		</td>
		<td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
	</tr>
	<tr class="notice">
		<td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
		<td class="icon" style="<?= $IDcc_EtdCicon ?>"></td>
		<td colspan="3">
			请查看退款是否到账。
		</td>
		<td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
	</tr>
	<?php include('order_email_footer.php'); ?>
</body>
</html>