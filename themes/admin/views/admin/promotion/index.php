<div id="promotion-search-container" class="container page-container" ng-controller="PromotionGridCtrl">
    <div class="row">
        <h3 class="text-center">
            活动页面
            <button class="btn btn-inverse block-action add" ng-disabled="local.grid_options.in_progress" ng-click="toggleOverlay( true )">
                添加活动
            </button>
            <span class="i i-refresh refresh-animate" ng-show="local.grid_options.in_progress"></span>
        </h3>
    </div>
    <div class="row">
        <div class="col-md-12 col-md-offset-3">
            <hi-grid options="local.grid_options"></hi-grid>
        </div>
    </div>
    <div class="overlay confirm" ng-show="local.has_overlay">
        <div class="notify-container confirm">
            <div class="notify-head">活动名称</div>
            <div class="notify-body">
                <input type="text" class="form-control" placeholder="活动名称（必填）" ng-model="local.promotion_name" />
            </div>
            <div class="notify-foot">
                <button class="block-action btn btn-inverse" ng-click="toggleOverlay( false )">取消</button>
                <button class="block-action btn btn-inverse" ng-click="confirmAdd()" ng-disabled="!local.promotion_name">
                    确定
                </button>
            </div>
        </div>
    </div>
</div>