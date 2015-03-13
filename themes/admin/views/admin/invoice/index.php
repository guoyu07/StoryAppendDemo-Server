<div id="invoice-search-container" class="container page-container" ng-controller="InvoiceSearchCtrl">
    <div class="row mt100">
        <div class="form-group col-md-4 col-md-offset-6">
            <select chosen
                    style="width: 100%;"
                    ng-model="local.supplier"
                    ng-change="setSupplierId()"
                    ng-options="vendor.supplier_id as vendor.name group by vendor.group for vendor in data.vendors track by vendor.supplier_id"
                    data-placeholder="选择供应商"
                    no-results-text="'没有找到'"
                >
            </select>
        </div>
        <button class="btn btn-inverse" ng-click="addInvoice()">新增对账单</button>
    </div>
    <h2 class="text-center">产品账单管理</h2>
    <div class="row">
        <hi-grid class="col-md-12 col-md-offset-3" options="local.grid_options"></hi-grid>
    </div>
    <div class="text-center"><span class="i i-refresh refresh-animate" ng-show="local.grid_options.in_progress"></span>
    </div>
    <!--overlay confirm-->
    <div class="overlay confirm" ng-show="local.overlay.has_overlay">
        <div class="notify-container confirm">
            <div class="notify-head">上传对账单</div>
            <div class="notify-body">
                <div class="row">
                    <table class="col-md-14 col-md-offset-4">
                        <tr>
                            <td width="30%">供应商</td>
                            <td width="70%" ng-bind="local.name"></td>
                        </tr>
                        <tr>
                            <td>对账日期</td>
                            <td ng-bind="local.date"></td>
                        </tr>
                        <tr>
                            <td>供应商对账单</td>
                            <td>
                                <button ng-hide="local.file_upload.is_uploaded" class="block-action btn btn-inverse"
                                        data-ng-click="uploadInvoice()">上传对账单
                                </button>
                                <a ng-href="{{local.file_upload.path}}" target="_blank"
                                   ng-show="local.file_upload.is_uploaded"
                                   ng-bind="local.file_upload.name"></a>
                            </td>
                        </tr>
                    </table>
                    <input type="file" id="invoice-upload" class="hidden" nv-file-select uploader="uploader" />
                </div>
            </div>
            <div class="notify-foot">
                <button class="block-action btn btn-inverse" ng-click="cancelAddInvoice()">取消</button>
                <button id="comfirmAddInvoice" class="block-action btn btn-inverse" ng-click="comfirmAddInvoice()">确定
                </button>
            </div>
        </div>
    </div>
</div>