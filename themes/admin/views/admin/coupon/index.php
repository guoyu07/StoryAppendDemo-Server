<div id="coupon-search-container" data-ng-controller="CouponGridCtrl" class="container page-container">
    <div class="row" style="margin-bottom: 40px;">
        <div class="col-md-12">
            <div class="col-md-5">
                <h3 class="text-center" style="margin: 0;">
                    搜索优惠券 <span class="i i-refresh refresh-animate" ng-show="local.grid_options.in_progress"></span>
                </h3>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control" ng-model="local.search_text" placeholder="搜索优惠券名称／代码"
                       hi-enter="searchCoupon()" />
            </div>
            <div class="col-md-3">
                <button class="block-action btn btn-inverse" ng-click="searchCoupon()">搜索</button>
            </div>
        </div>
        <div class="col-md-2 col-md-offset-4">
            <button class="btn btn-inverse pull-right" ng-click="goToCoupon()">添加优惠券</button>
        </div>
    </div>

    <hi-grid options="local.grid_options"></hi-grid>
</div>