<div id="supplier-search-container" class="container page-container" ng-controller="SupplierGridCtrl">
    <div class="row">
        <h2 class="text-center page-title">
            编辑供应商
            <button class="toggle-btn btn btn-inverse" ng-class="{ 'expanded': local.show_add_vendor }"
                    ng-click="local.show_add_vendor = !local.show_add_vendor"></button>
        </h2>
        <div class="row add-row">
            <div class="col-md-6 col-md-offset-6" ng-show="local.show_add_vendor">
                <div class="col-md-12">
                    <input type="text" class="form-control" ng-model="local.vendor_en_name" placeholder="供应商英文名" />
                </div>
                <button class="block-action btn btn-inverse col-md-6" ng-click="addVendor()"
                        ng-disabled="!local.vendor_en_name">
                    添加
                    <span class="i i-refresh refresh-animate" ng-show="local.add_in_progress"></span>
                </button>
            </div>
        </div>
        <div class="form-group col-xs-4 col-xs-offset-7 grid-top">
            <select chosen
                    style="width: 100%;"
                    ng-model="local.supplier"
                    ng-change="goEditVendor()"
                    ng-options="vendor.supplier_id as vendor.name group by vendor.group for vendor in data.vendors"
                    data-placeholder="选择供应商"
                    no-results-text="'没有找到'"
                >
            </select>
        </div>
    </div>
</div>