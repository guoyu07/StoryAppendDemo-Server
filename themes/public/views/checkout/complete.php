<div class="container">
  <div class="dialog-zone">
    <img src="themes/public/images/common/login-pic.png" class="dialog-left">
    <div class="dialog-right">
      <i class="success-icon icon-check-circle"></i>
      <p class="notification-title">支付成功</p>
      <p class="notification-content">您的订单正在处理中，<?= $result_str; ?></p>
      <p class="introduction">您可以在<a onclick="window.location = $request_urls.headerOrders">订单列表</a>查看并跟踪订单状态</p>
    </div>
  </div>
</div>