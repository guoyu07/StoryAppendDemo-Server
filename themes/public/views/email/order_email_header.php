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
		padding:0;
	}

	#content-container td {
		padding: 5px;
	}
	#footer-container td {
		padding: 8px;
	}
</style>
<?php
/*#top-container*/
$IDtc = 'width: 100%; color: #000; margin: 0 auto; font-size: 20px; background: #F9F9F9; font-family: "Microsoft YaHei", "Hiragino Sans GB", sans-serif;';
$IDtc_Ea = 'color: #00BA8E; font-size: 20px; text-decoration: none;';
$IDtc_Eh3 = 'color: #00BA8E; font-size: 24px; font-weight: bold;';
$IDtc_Cheader_n_Cfooter = 'width: 640px; background: #00BA8E;';

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
}

// = HTTP_SERVER . 'catalog/view/theme/' . $this->config->get('config_template');
$CURRENT_TEMPLATE_URL = Yii::app()->request->hostinfo . Yii::app()->params['WEB_PREFIX'] . Yii::app()->params['THEME_BASE_URL'];
?>
<table id="top-container" align="center" style="<?= $IDtc; ?>">
	<tr class="top-header" style="<?= $IDtc_Cheader_n_Cfooter; ?>">
		<td></td>
		<td>
      <center>
        <img src="<?php echo $CURRENT_TEMPLATE_URL;?>/images/email/header.png" />
      </center>
		</td>
		<td></td>
	</tr>
	<tr style="height: 35px;"></tr>
	<tr>
		<td></td>
		<td>
			<center>
				<table id="content-container" cellpadding="5" style="<?= $IDcc; ?>">
					<tr style="height: 40px;"></tr>
					<tr>
						<td class="pad-left" style="<?= $IDcc_EtdCpad_left ?>"></td>
						<td colspan="3" style="font-size: 28px;"><?= $order['contacts_name'] ?> 您好，</td>
						<td class="grey order right-title" style="<?= $IDcc_Cgrey . ' ' . $IDcc_Corder . ' ' . $IDcc_EtdCright_title; ?>"><span class="small" style="<?= $IDcc_Csmall ?>">订单号：</span><?= $order['order_id'] ?></td>
						<td class="pad-right" style="<?= $IDcc_EtdCpad_right ?>"></td>
					</tr>
					<tr style="height: 40px;"></tr>