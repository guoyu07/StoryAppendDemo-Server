<div id="stock-search-container" ng-controller="StockSearchCtrl" class="container page-container">
    <div class="states-section">
        <div class="section-head">
            <h2 class="section-title">保险码库存（剩余保险码：<span ng-bind="data.insurance.available_amount"></span>）</h2>
            <div class="section-actions">
                <button class="block-action btn btn-inverse" ng-hide="local.uploader_options.insurance.in_progress"
                        ng-click="triggerUpload( 'insurance' )">点击上传
                </button>
                <button class="block-action btn btn-inverse" ng-show="local.uploader_options.insurance.in_progress">
                    上传中...
                </button>
            </div>
        </div>
        <div class="section-body insurance-uploader">
            <hi-uploader options="local.uploader_options.insurance"></hi-uploader>
        </div>
    </div>
    <div class="states-section">
        <div class="section-head">
            <h2 class="section-title">
                商品库存
                <span class="i i-refresh refresh-animate" ng-show="local.product_grid.in_progress"></span>
            </h2>
        </div>
        <div class="section-body product-uploader">
            <hi-uploader options="local.uploader_options.product"></hi-uploader>
            <hi-grid options="local.product_grid"></hi-grid>
        </div>
    </div>
    <?php
    include_once(__DIR__ . '/modules/stock_list/partial.php');
    ?>
</div>