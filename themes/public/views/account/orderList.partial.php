<div class="order-tab-title-container clearfix">
  <div class="tab-title tab-sub">
    <ul class="tab-nav">
      <li ms-class="active: order_tab == 'all_orders'">
        <a ms-click="switchOrderTab( 'all_orders' )">全部</a>
      </li>
      <li ms-class="active: order_tab == 'unpaid_orders'">
        <a ms-click="switchOrderTab( 'unpaid_orders' )">未支付订单</a>
      </li>
      <li ms-class="active: order_tab == 'shipped_orders'">
        <a ms-click="switchOrderTab( 'shipped_orders' )">已发货订单</a>
      </li>
      <li ms-class="active: order_tab == 'processing_orders'">
        <a ms-click="switchOrderTab( 'processing_orders' )">处理中订单</a>
      </li>
      <li ms-class="active: order_tab == 'refunded_orders'">
        <a ms-click="switchOrderTab( 'refunded_orders' )">已退订订单</a>
      </li>
    </ul>
  </div>
</div>
<div class="tab-content">
  <div class="tab-pane active">
    <table class="table table-order table-striped">
      <thead>
        <tr>
          <th style="width: 120px;">订单号</th>
          <th style="width: 330px;">商品名称</th>
          <th style="width: 140px;">订单日期</th>
          <th style="width: 130px;">订单状态</th>
          <th>动作</th>
        </tr>
      </thead>
      <tbody>
        <tr ms-repeat-order="orders" ms-visible="isVisible( order.status_id )">
          <td class="text-center numeric-text">{{ order.order_id }}</td>
          <td class="product-name">
            <a ms-href="{{ order.product_url }}" ms-attr-alt="{{ order.product_name }}">{{ order.product_name }}</a>
          </td>
          <td class="text-center numeric-text">{{ order.date_added }}</td>
          <td class="text-center" ms-class="{{ getStatusClass( order.status_id ) }}">{{ order.status_name }}</td>
          <td class="text-center order-action">
            <a class="link-download" ms-href="{{ order.download_voucher_url }}" ms-visible="status_action[ 'download' ].indexOf( order.order_id ) > -1">下载兑换单</a>
            <ul class="action-list">
              <li ms-if="order.payment_url">
                <a ms-href="{{ order.payment_url }}" class="link-action">支付</a>
              </li>
              <li ms-if="order.cancel_url ">
                <a ms-click="confirmCancel( $index )" href="javascript:;"class="link-action">取消</a>
              </li>
              <li ms-if="order.return_url">
                <a ms-click="confirmRefund( $index )" href="javascript:;" class="link-action">退订</a>
              </li>
              <li>
                <a class="link-action" ms-click="buildOverlay( $index )">查看</a>
              </li>
              <li ms-if="order.download_voucher_url">
                <a class="link-action" ms-attr-href="order.download_voucher_url">下载兑换单</a>
              </li>
            </ul>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>