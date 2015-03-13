<div id="error-search-container" class="container page-container" ng-controller="ErrorPageGridCtrl">
    <div class="row">
        <h3 class="text-center">
            错误页面列表
            <button class="btn btn-inverse add-page" ng-disabled="local.grid_options.in_progress" ng-click="addErrorPage()">
                添加错误页面
            </button>
            <span class="i i-refresh refresh-animate" ng-show="local.grid_options.in_progress"></span>
        </h3>
    </div>
    <div class="row">
        <div class="col-md-12 col-md-offset-3">
            <hi-grid options="local.grid_options"></hi-grid>
        </div>
    </div>
</div>