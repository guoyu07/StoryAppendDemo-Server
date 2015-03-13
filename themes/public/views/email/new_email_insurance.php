<?php
$redeem_code_str ='';
foreach($insurance_codes as $ic) {
    $redeem_code_str = $redeem_code_str . $ic['redeem_code'] . '<br />';
}
$redeem_url = $insurance_codes[0]['company']['policy_url'];
?>

<table <?= $table_style ?>  width="560px">
    <tr>
        <td colspan="2" style="color: #6db381; font-size: 40px;">保险信息</td>
    </tr>
    <tr>
        <td colspan="2" style="padding: 10px 0 30px; font-size: 22px;">玩途给每位出行的成人旅客（18周岁以上）赠送一份为期10天，保额15万的境外旅游意外保险（由中国太平洋保险股份有限公司承担）。每位旅客的保单兑换码如下：</td>
    </tr>
    <tr>
        <td width="120" valign="top" style="font-size: 22px;">兑换码：</td>
        <td width="440" style="font-size: 22px; color: #6db381;"><?= $redeem_code_str ?></td>
    </tr>
    <tr>
        <td width="120" style="font-size: 22px;">兑换网址：</td>
        <td width="440" style="font-size: 22px;"><a href="<?= $redeem_url ?>" target="_blank" style="font-size: 22px; color: #6db381;"><?= $redeem_url ?></a></td>
    </tr>
</table>