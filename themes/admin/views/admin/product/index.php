<div id="product-search-container" ng-controller="ProductSearchCtrl" class="container page-container">
    <nav class="row pad-top">
        <div class="col-md-3">
            <select chosen
                    style="width: 100%;"
                    ng-model="local.grid_options.query.query_filter.city_code"
                    ng-change="updateResult()"
                    ng-options="city.city_code as (city.city_name + ' ' + city.city_pinyin) group by city.group for city in data.cities track by city.city_code"
                    no-results-text="'没有找到'"
                    data-placeholder="点击选择城市"
                >
            </select>
        </div>
        <div class="col-md-3">
            <select chosen
                    style="width: 100%;"
                    ng-model="local.grid_options.query.query_filter.supplier_id"
                    ng-change="updateResult()"
                    ng-options="supplier.supplier_id as supplier.name group by supplier.group for supplier in data.suppliers track by supplier.supplier_id"
                    no-results-text="'没有找到'"
                    data-placeholder="点击选择供应商"
                >
            </select>
        </div>
        <div class="col-md-4 product-search-query">
            <input hi-enter="updateResult()" ng-model="local.grid_options.query.query_filter.product_term" type="text"
                   class="form-control" placeholder="商品关键字／商品id" />
            <span class="i i-search" ng-click="updateResult()"></span>
        </div>
        <div class="col-md-6 text-center">
            <span class="i i-refresh refresh-animate" ng-show="local.grid_options.in_progress"></span>
        </div>
        <div class="col-md-2">
            <button class="btn btn-inverse" ng-click="goToImport()">GTA商品导入</button>
        </div>
    </nav>
    <hi-grid options="local.grid_options"></hi-grid>
</div>
