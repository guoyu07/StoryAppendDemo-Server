<html lang="zh">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=640, initial-scale=1">
	<title>玩途自由行 - 预定失败</title>
</head>
<body>
	<?php include('order_email_header.php'); ?>
	<tr class="notice">
		<td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
		<td class="icon" style="<?= $IDcc_EtdCicon ?>"><img src="themes/public/images/email/order_failed.png" /></td>
		<td colspan="3">
			您的预定出现异常！
		</td>
		<td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
	</tr>
	<tr class="notice">
		<td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
		<td class="icon" style="<?= $IDcc_EtdCicon ?>"></td>
		<td colspan="3" style="<?= $IDcc_Ctall ?>">
			建议您拨打客服电话咨询<br />
			我们很高兴为您服务
		</td>
		<td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
	</tr>
	<?php include('order_info.php'); ?>
	<?php include('order_email_footer.php'); ?>
</body>
</html>