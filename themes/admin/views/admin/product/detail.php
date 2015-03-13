<div id="product-edit-container" class="page-container clearfix" ng-controller="ProductEditCtrl">
    <div id="menu" class="col-md-3 side-menu" hi-after-load="finishLoad(10)">
        <div class="list-group-item">
            <h4 class="list-group-item-heading">
                商品状态
            </h4>
        </div>
        <div class="list-group-item">
            <div class="dropdown" id="product_status">
                <button class="btn btn-inverse dropdown-toggle" data-toggle="dropdown" ng-class="local.current_status.class_name">
                    {{ local.current_status.label }}
                    <span class="caret"></span>
                </button>
                <span class="dropdown-arrow"></span>
                <ul class="dropdown-menu">
                    <li ng-repeat="status in local.product_status track by $index">
                        <a class="status text-center"
                           ng-bind="status.label"
                           ng-hide="status.value == local.current_status.value"
                           ng-class="status.class_name"
                           ng-click="changeProductStatus($index)"
                            ></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="separator"></div>
        <div class="list-group-item" ng-repeat="item in local.menu_items"
             ng-class="{ active: item.value && (item.value == local.current_menu.value) }">
            <h4 ng-if="!item.value" class="list-group-item-heading" ng-bind="item.label"></h4>
            <a ng-if="!!item.value && item.is_visible" class="list-group-item-text" ng-bind="item.label"
               ng-click="changeMenu( $index )"></a>
        </div>
    </div>
    <div id="edit-section" class="col-md-12 col-md-offset-4 clearfix" ng-class="local.current_menu_class">
        <div ng-view hi-after-load="finishLoad(20)"></div>
    </div>
    <!--加载菊花-->
    <div class="loading-indicator" ng-hide="!local.load_in_progress"></div>
    <!--用于页面跳转以及tab跳转-->
    <div class="overlay confirm" ng-show="local.overlay.has_overlay">
        <div class="notify-container confirm">
            <div class="notify-head" ng-show="!local.overlay.is_tab_navigate">
                你有未保存内容，确定离开页面？
            </div>
            <div class="notify-head" ng-show="local.overlay.is_tab_navigate">
                {{ local.overlay.tab_label }}分组页面有未保存内容，确定离开？
            </div>
            <div class="notify-foot">
                <button class="block-action btn btn-primary" ng-click="confirmSave( false )">继续编辑</button>
                <button class="block-action btn btn-inverse" ng-click="confirmSave( true )">保存并离开</button>
            </div>
        </div>
    </div>

    <?php
    $path = __DIR__ . '/routes/';
    //商品规则
    include_once $path . 'product_basic_info/product_basic_info.php';
    //商品说明
    include_once $path . 'product_service/product_service.php'; //服务说明
    include_once $path . 'product_redeem/product_redeem.php'; //兑换及使用
    include_once $path . 'product_notice/product_notice.php'; //购买须知
    include_once $path . 'product_qna/product_qna.php'; //qna
    include_once $path . 'product_comment/product_comment.php'; //商品评论
    include_once $path . 'product_bundle/product_bundle.php'; //商品挂接
    include_once $path . 'product_feedback/product_feedback.php'; //商品回访
    include_once $path . 'hotel_room/hotel_room.php'; //酒店房型
    //商品运营
    include_once $path . 'product_seo/product_seo.php';
    include_once $path . 'related_product/related_product.php';
    include_once $path . 'coupon_template/coupon_template.php';
    include_once $path . 'product_price/product_price.php';
    ?>
</div>