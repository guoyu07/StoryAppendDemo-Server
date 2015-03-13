<?php
$image_url = Yii::app()->request->hostinfo . Yii::app()->params['WEB_PREFIX'] . Yii::app()->params['THEME_BASE_URL'] . '/images/email/new_email/';
$customer_str = '';
?>
<style>
    .loading-mask {
        display: none !important;
    }
    #voucher-download {
        width: 1000px;
        min-height: 450px;
        margin: 0 auto;
        background: url(<?= $image_url?>voucher_download_bg.png) center bottom no-repeat;
    }
    .voucher-ctn {
        width: 450px;
        height: 50px;
        margin: 50px auto;
        color: #525252;
    }
    .voucher-ctn .input {
        width: 180px;
        height: 40px;
        padding: 4px 8px;
        outline: none;
        border: 1px solid #aaa;
        border-radius: 4px;
    }
    .voucher-ctn .input:focus {
        border: 1px solid #1abc9c;
    }
    .voucher-ctn .download {
        padding: 8px 15px;
        border: 1px solid #e9e9e9;
        background: #fff;
        font-size: 18px;
        border-radius: 4px;
        text-decoration: none;
        color: #525252;
    }
    .voucher-ctn .download:hover {
        background: #f7f7f7;
    }
    #mobile-tip {
        display: none;
        text-align: center;
        padding-top: 100px;
        font-size: 18px;
        min-height: 400px;
    }
</style>
<?php if ($is_mobile) { ?>
    <script>
        $( function() {
            var voucherDown = $( '#voucher-download' );
            var mobileTip = $( '#mobile-tip' );
            voucherDown.hide();
            mobileTip.show();
        } )
    </script>
<?php } ?>
<?php if( Yii::app()->customer->customerName != '') {
    $customer_str = '亲爱的' . Yii::app()->customer->customerName . '，';
} ?>


<div id="voucher-download">
    <p style="padding-top: 50px; font-size: 30px;"><?= $customer_str ?>您好！</p>
    <p style="font-size: 30px; padding-top: 60px; text-align: center;">感谢您选择玩途！祝您一路顺风！</p>
    <div class="voucher-ctn" >
        <label style="font-size: 18px;">请输入提取码：<input type="text" class="input" /></label>
        <a href="<?= $download_url; ?>" class="download" onclick="replaceHref()">下载兑换单</a>
    </div>
</div>
<p id="mobile-tip">
    下载的兑换单暂时不配适在移动设备上完成，请登录到PC端网站下载！
</p>
<script>
    function replaceHref() {
        var anchor = $('.download')[0];
        anchor.href = anchor.href + $('.input' ).val();
    }
</script>