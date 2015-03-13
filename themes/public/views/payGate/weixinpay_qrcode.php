<style type="text/css">
    .wx-page {
        text-align: center;
        color: #525252;
        background-color: #f7f7f7;
        border-top: 1px solid #dddddd;
    }
    .container {
        background-color: white;
    }
    .order-table {
        border-collapse: collapse;
        margin: 50px auto;
        font-size: 16px;
    }
    .order-table tr th,
    .order-table tr td {
        border: 1px solid #dddddd;
        padding: 5px;
    }
    .order-table thead {
        background-color: #f7f7f7;
        text-align: center;
    }
    .last-row {
        text-align: right;
    }
    .last-row td span {
        color: #ff6600;
    }
    .qr-code {
        width: 305px;
        height: 306px;
    }
    .qr-code img {
        width: 100%;
    }
    .qr-title {
        color: #6db381;
        font-size: 20px;
    }
    .qr-desc {
        width: 250px;
        color: #787878;
        font-size: 14px;
        line-height: 18px;
        text-align: left;
        margin: 0px auto 30px auto;
    }
    .buy-ctn {
        float: right;
        margin-top: -160px;
        margin-right: 130px;
    }
    .buy-ctn .back-btn {
        border: 1px solid #DDDDDD;
        color: #A8A8A8;
        background: #FFF;
    }
    .buy-ctn .aside-btn {
        font-size: 18px;
        color: #A8A8A8;
        outline: 0px;
        height: 50px;
        width: 186px;
        margin-top: 10px;
        padding: 8px 12px;
        display: inline-block;
        text-align: center;
        margin-right: 6px;
    }
</style>

<div id="wx_qrcode" class="wx-page"">
    <div class="container wx-content">
        <table class="order-table">
            <thead class="first-row">
                <tr>
                    <th style="width: 720px;">商品名称</th>
<!--                    <th style="width: 140px;">价格（实付款）</th>-->
                </tr>
            </thead>
            <tbody>
                <tr class="table-content">
                    <td style="text-align: left;padding-left: 10px;"><?php echo $order['product_name']?></td>
<!--                    <td style="text-align: center;width: 140px;"></td>-->
                </tr>
                <tr class="last-row">
                    <td colspan="2">合计：<span>￥<?php echo $order['total']?></span></td>
                </tr>
            </tbody>
        </table>
        <div>
        <p class="qr-title">微信扫码支付</p>
        <img class="qr-code" src="<?=$qrcode_url?>">
        <p class="qr-desc">请使用微信扫描此二维码以完成支付您的订单。</p>
        </div>
        <div class="buy-ctn">
            <button class="aside-btn back-btn" onclick="location.href='/account/account#orders'">已完成支付</button><br>
            <button class="aside-btn back-btn" onclick="location.href='/account/account#orders'">支付遇到问题</button>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $(".loading-mask").css("display", "none");
        setInterval(function(){
            $.ajax({
                url: $request_urls.orderStatus,
                data: {
                    order_id:'<?=$order['order_id']?>'
                },
                type: 'post',
                dataType: 'json',
                success: function (res) {
                    if (res.code == 200) {
                        window.location.href = $request_urls.paySuccess;
                    }
                },
                error: function (xhr, type, msg) {
                    console.log(arguments);
                }
            });
        },1000);
    });
</script>