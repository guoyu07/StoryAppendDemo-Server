<div id="edm-search-container" class="container page-container" ng-controller="EDMGridCtrl">
    <div class="row">
        <h3 class="text-center">
            EDM模版
            <button class="btn btn-inverse block-action add" ng-disabled="local.grid_options.in_progress"
                    ng-click="toggleOverlay( true )">
                添加EDM
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
            <div class="notify-head">EDM模版名称</div>
            <div class="notify-body">
                <input type="text" class="form-control" placeholder="EDM模版名称（必填）" ng-model="local.edm_name" />
            </div>
            <div class="notify-foot">
                <button class="block-action btn btn-inverse" ng-click="toggleOverlay( false )">取消</button>
                <button class="block-action btn btn-inverse" ng-click="confirmAdd()" ng-disabled="!local.edm_name">确定
                </button>
            </div>
        </div>
    </div>
</div>