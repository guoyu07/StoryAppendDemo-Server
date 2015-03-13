<div class="main-wrap account-container" ms-controller="account">

  <div class="container">
    <div class="tab-title">
      <ul class="tab-nav">
        <li ms-class="active: header_tab == 'account'">
          <a ms-href="linkAccount" ms-click="switchHeadTab( 'account' )">我的账户</a>
        </li>

        <li ms-class="active: header_tab == 'orders'">
          <a ms-href="linkOrders" ms-click="switchHeadTab( 'orders' )">我的订单</a>
        </li>

        <li ms-class="active: header_tab == 'coupon'">
          <a ms-href="linkCoupon" ms-click="switchHeadTab( 'coupon' )">我的优惠券</a>
        </li>
      </ul>
    </div>
    <!--Tab Content-->
    <div class="tab-content">

      <!--Profile Tab-->
      <div class="tab-pane" ms-if="header_tab == 'account'" ms-class="active: header_tab == 'account'">
        <?php include_once( 'account.partial.php' ); ?>
      </div>

      <!--Order List Tab-->
      <div class="tab-pane" ms-if="header_tab == 'orders'" ms-class="active: header_tab == 'orders'">
        <?php include_once( 'orderList.partial.php' ); ?>
      </div>

      <!--Coupon List Tab-->
      <div class="tab-pane" ms-if="header_tab == 'coupon'" ms-class="active: header_tab == 'coupon'">
        <?php include_once( 'couponList.partial.php' ); ?>
      </div>

    </div>
  </div>

</div>