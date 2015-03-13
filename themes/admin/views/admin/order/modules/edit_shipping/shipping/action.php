<div class="row grid-bottom">
    <div class="col-md-18 grid-bottom" ng-show="shipping.needBooking == 1 || shipping.needReBooking == 1 || shipping.canShipping == 1 || shipping.canReShipping == 1">
        <div class="col-md-2">
            <button class="btn btn-danger block-action" ng-show="shipping.needBooking == 1" ng-click="handleShipping( 'booking' )" ng-disabled="local.shipping.progress.booking">
                预定
                <span class="i i-refresh refresh-animate" ng-show="local.shipping.progress.rebooking"></span>
            </button>
            <button class="btn btn-danger block-action" ng-show="shipping.needReBooking == 1" ng-click="handleShipping( 'rebooking' )" ng-disabled="local.shipping.progress.rebooking">
                再次预定
                <span class="i i-refresh refresh-animate" ng-show="local.shipping.progress.rebooking"></span>
            </button>
            <button class="btn btn-danger block-action" ng-show="shipping.canShipping == 1" ng-click="handleShipping( 'ship' )" ng-disabled="local.shipping.progress.ship">
                发货
                <span class="i i-refresh refresh-animate" ng-show="local.shipping.progress.ship"></span>
            </button>
            <button class="btn btn-danger block-action" ng-show="shipping.canReShipping == 1" ng-click="handleShipping( 'reship' )" ng-disabled="local.shipping.progress.reship">
                再次发货
                <span class="i i-refresh refresh-animate" ng-show="local.shipping.progress.reship"></span>
            </button>
            <!--<button class="btn btn-danger block-action" ng-show="shipping.canReShipping == 1" ng-click="handleShipping( 'ship-pdf' )" ng-disabled="local.shipping.progress.ship_pdf">-->
                <!--只发pdf-->
                <!--<span class="i i-refresh refresh-animate" ng-show="local.shipping.progress.reship"></span>-->
            <!--</button>-->
        </div>
    </div>

    <div class="col-md-18" ng-show="shipping.canReturn == 1 || shipping.canReturn == 2">
        <div class="col-md-2">
            <button class="btn btn-danger block-action" ng-click="toggleOverlay( true )" ng-disabled="local.shipping.progress.return">
                退货／退款
            </button>
        </div>
        <div class="col-md-14 order-desc">
            <!--已退货-->
            <span ng-show="shipping.canReturn == 2">
                商品已退货。{{shipping.statusHistory[0].status_name + ' ' + shipping.statusHistory[0].date_added}}
            </span>
            <!--未退货-->
            <div ng-show="shipping.canReturn == 1">
                退货信息：
                <!--不可以退-->
                <span ng-show="parent_product.product.return_rule.return_type == '0'">
                    此商品不可以退
                </span>
                <!--可以退-->
                <span ng-show="parent_product.product.return_rule.return_type != '0'">
                    <span ng-show="local.shipping.date <= local.shipping.return_expire_date">
                        {{local.shipping.return_expire_date + '前可以退货以及全额退款'}}
                    </span>
                    <span ng-show="local.shipping.date > local.shipping.return_expire_date">
                        {{'不可以退货，超过退订期限' + local.shipping.return_expire_date}}
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>
