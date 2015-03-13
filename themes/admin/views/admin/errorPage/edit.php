<div id="error-page-edit-container" class="container page-container" ng-controller="ErrorPageEditCtrl">
    <div class="states-section">
        <hi-section-head model="local.detail_section" options="local.detail_section"></hi-section-head>
        <div class="section-body clearfix" ng-show="local.detail_section.is_edit">
            <form name="error_page">
                <div class="row grid-bottom">
                    <div class="col-md-3">错误页面挂接商品</div>
                    <div class="col-md-6">
                        <input class="form-control" placeholder="请输入商品id" type="text"
                               ng-model="data.page_info.product_id" required />
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-inverse block-action"
                                ng-disabled="!data.page_info.product_id || data.page_info.product_id == local.product_id_copy"
                                ng-click="bindingErrorProduct()">确认
                        </button>
                    </div>
                </div>
                <p class="small-desc grid-bottom">PC背景图：尺寸为1024 x 800；Mobile背景图：尺寸为768 x 1024（手机站）</p>
                <div class="row grid-bottom">
                    <div class="col-md-14">
                        <p class="small-desc">PC背景图</p>
                        <div hi-uploader options="local.uploader_options.pc_bg"></div>
                    </div>
                    <div class="col-md-4">
                        <p class="small-desc">Mobile背景图</p>
                        <div hi-uploader options="local.uploader_options.mobile_bg"></div>
                    </div>
                </div>
                <div class="row grid-bottom">
                    <div class="col-md-2">商品名称</div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" ng-model="data.page_info.product_name"
                               ng-required="data.page_info.status == 1" />
                    </div>
                </div>
                <div class="row grid-bottom">
                    <div class="col-md-2">商品描述</div>
                    <div class="col-md-12">
                        <textarea class="form-control" ng-model="data.page_info.product_description"
                                  ng-required="data.page_info.status == 1"></textarea>
                    </div>
                </div>
                <div class="row grid-bottom">
                    <div class="col-md-2">错误描述</div>
                    <div class="col-md-12">
                        <textarea class="form-control" ng-model="data.page_info.error_description"
                                  ng-required="data.page_info.status == 1"></textarea>
                    </div>
                </div>
                <div class="row grid-bottom">
                    <div class="col-md-2">状态</div>
                    <div class="col-md-6">
                        <hi-radio-switch options="local.radio_switch.options" model="data.page_info"></hi-radio-switch>
                    </div>
                </div>
            </form>
        </div>
        <div class="section-body clearfix" ng-hide="local.detail_section.is_edit">
            <div class="row grid-bottom">
                <div class="col-md-3">错误页面挂接商品id</div>
                <div class="col-md-3" ng-bind="data.page_info.product_id"></div>
            </div>
            <div class="error-bg">
                <div class="col-md-14">
                    <p class="small-desc">PC背景图</p>
                    <img ng-src="{{ data.page_info.bg_image_url }}" class="pc-img" />
                </div>
                <div class="col-md-4">
                    <p class="small-desc">mobile背景图</p>
                    <img ng-src="{{ data.page_info.mobile_image_url }}" class="mobile-img" />
                </div>
            </div>
            <div class="col-md-14">
                <h4>商品信息</h4>
                <table class="error-forms-table">
                    <tr>
                        <td class="item-title col-md-2">商品名称</td>
                        <td class="item-body" ng-bind="data.page_info.product_name"></td>
                    </tr>
                    <tr>
                        <td class="item-title col-md-2">商品描述</td>
                        <td class="item-body" ng-bind="data.page_info.product_description"></td>
                    </tr>
                    <tr>
                        <td class="item-title col-md-2">错误描述</td>
                        <td class="item-body" ng-bind="data.page_info.error_description"></td>
                    </tr>
                    <tr>
                        <td class="item-title col-md-2">状态</td>
                        <td class="item-body" ng-bind="data.page_info.status == 1 ? '启用' : '禁用'"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>