<div id="import-search-container" class="container page-container" data-ng-controller="ImportGridCtrl">
    <div class="row">
        <div class="col-md-9">
            <h3 class="text-center">
                GTA商品导入 <span class="i i-refresh refresh-animate" ng-show="local.grid_options.in_progress"></span>
            </h3>
        </div>
        <div class="col-md-9">
            <form name="gta_import_form" class="padded-form">
                <div class="row">
                    <div class="col-md-6 text-right">城市代码</div>
                    <div class="col-md-12">
                        <input type="text" class="form-control" ng-model="local.import_data.city_code" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 text-right">Item ID</div>
                    <div class="col-md-12">
                        <input type="text" class="form-control" ng-model="local.import_data.item_id" required
                               ng-blur="goToExisting()" />
                    </div>
                </div>
                <div class="row">
                    <button class="btn btn-inverse block-action pull-right" ng-disabled="gta_import_form.$invalid"
                            ng-click="addImport()">
                        添加任务
                    </button>
                </div>
            </form>
        </div>
    </div>

    <hi-grid options="local.grid_options"></hi-grid>
</div>