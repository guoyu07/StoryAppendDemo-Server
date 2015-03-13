<?php $homeUrl = Yii::app()->params['urlHome'] ?>
<table <?= $table_style ?> width="560px">
    <tr>
        <td colspan="4" style="color: #6db381; font-size: 40px;">兑换单下载</td>
    </tr>
    <tr>
        <td colspan="4" style="padding: 10px 0 30px; font-size: 22px;">请务必您下载打印兑换单并随身携带</td>
    </tr>
    <tr>
        <td width="100"></td>
        <td width="180">
            <table <?= $table_style ?> width="180px;">
                <tr>
                    <td style="font-size: 18px; line-height: 22px; color: #525252;">兑换单提取码：</td>
                </tr>
                <tr>
                    <td style="font-size: 24px; line-height: 28px; color: #6db381;"><?= $order['extract_code']; ?></td>
                </tr>
            </table>
        </td>
        <td width="180" height="50px" style="background: #6db381;" align="center">
            <a href="<?= $homeUrl ?>account/download/order_id/<?= $order['order_id'] ?>" target="_blank" style="color: #fff; font-size: 22px; text-decoration: none; display: block; line-height: 50px; ">下载兑换单</a>
        </td>
        <td width="100"></td>
    </tr>
</table>
