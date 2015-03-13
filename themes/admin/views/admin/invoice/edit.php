<div id="invoice-edit-container" class="container page-container" ng-controller="InvoiceEditCtrl">
    <!-- base_info -->
    <div class="states-section">
        <div class="section-head">
            <div class="section-title" ng-bind="local.section_head.base_info.title"></div>
        </div>
        <div class="section-body">
            <div class="row col-md-offset-2 margin10">
                <div class="col-md-3">供应商</div>
                <div class="col-md-3" ng-bind="data.invoice_data.supplier_name"></div>
                <div class="col-md-3" ng-bind="data.invoice_data.invoice_date"></div>
                <div class="col-md-3" ng-bind="data.invoice_data.invoice_sn"></div>
            </div>
            <div class="row col-md-offset-2 margin10">
                <div class="col-md-3">对账状态</div>
                <div class="col-md-3">正确 <span class="gray-bg" id="filterRight" ng-bind="data.invoice_data.success"
                                               ng-click="filterInvoiceList('right')"></span></div>
                <div class="col-md-3">有问题 <span class="gray-bg warning-red" id="filterProblem"
                                                ng-bind="data.invoice_data.fail"
                                                ng-click="filterInvoiceList('problem')"></span></div>
            </div>
            <div class="row col-md-offset-2 margin10">
                <div class="col-md-3">对账单</div>
                <div class="col-md-6"><a ng-href="{{data.invoice_data.invoice_doc}}"
                                         ng-bind="data.invoice_data.invoice_name"></a></div>
                <div class="col-md-2">
                    <button class="btn block-action btn-inverse" ng-click="reUpload()">重新上传 <span
                            class="i i-refresh refresh-animate" ng-show="local.file_uploading"></span></button>
                </div>
                <input type="file" id="invoice-reupload" class="hidden" nv-file-select uploader="uploader" />
            </div>
            <div class="row col-md-offset-2 margin10">
                <div class="col-md-3">备注</div>
                <div class="col-md-9 remark-ctn">
                    <div ng-hide="local.invoice_data.is_edit" ng-bind="data.invoice_data.remark"></div>
                    <div ng-show="local.invoice_data.is_edit">
                        <textarea style="width: 100%" ng-model="data.invoice_data.remark"></textarea>
                    </div>
                </div>
                <div class="col-md-1" ng-hide="local.invoice_data.is_edit"><span class="i i-edit"
                                                                                 ng-click="local.invoice_data.is_edit = true"></span>
                </div>
                <div class="col-md-1" ng-show="local.invoice_data.is_edit"><span class="i i-save"
                                                                                 ng-click="saveRemarkEdit()"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- invoice_list -->
    <div class="states-section">
        <div class="section-head">
            <div class="section-title" ng-bind="local.section_head.invoice_list.title"></div>
        </div>
        <div class="section-body">
            <div class="row">
                <!-- supplier_id = '11' means GTA -->
                <div class="col-md-offset-2 col-md-14" ng-hide="data.invoice_data.supplier_id == '11' ">
                    <div class="row margin10">
                        <div class="col-md-3">订单号：</div>
                        <div class="col-md-6">
                            <input class="form-control" type="text"
                                   ng-model="data.search_content.query_filter.search_order_id"
                                   ng-keyup="triggerSearch($event)" />
                        </div>
                        <div class="col-md-2">确认码：</div>
                        <div class="col-md-6">
                            <input class="form-control" type="text"
                                   ng-model="data.search_content.query_filter.search_confirmation_ref"
                                   ng-keyup="triggerSearch($event)" />
                        </div>
                    </div>
                    <div class="row margin10">
                        <div class="col-md-3">商品名称/ID:</div>
                        <div class="col-md-6">
                            <input class="form-control" type="text"
                                   ng-model="data.search_content.query_filter.search_product_text"
                                   ng-keyup="triggerSearch($event)" />
                        </div>
                    </div>
                    <div class="row margin10">
                        <div class="col-md-3">下单日期</div>
                        <div class="col-md-6">
                            <quick-datepicker ng-model='data.search_content.query_filter.search_added_from_date'
                                              disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
                        </div>
                        <div class="col-md-1 text-center">——</div>
                        <div class="col-md-6">
                            <quick-datepicker ng-model='data.search_content.query_filter.search_added_to_date'
                                              disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
                        </div>
                    </div>
                    <div class="row margin10">
                        <div class="col-md-3">使用日期</div>
                        <div class="col-md-6">
                            <quick-datepicker ng-model='data.search_content.query_filter.search_tour_from_date'
                                              disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
                        </div>
                        <div class="col-md-1 text-center">——</div>
                        <div class="col-md-6">
                            <quick-datepicker ng-model='data.search_content.query_filter.search_tour_to_date'
                                              disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-inverse block-action" ng-click="searchInvoiceOrder()">
                                搜索
                        <span class="i i-refresh refresh-animate"
                              ng-show="local.invoice_order_search.search_status"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-offset-5 col-md-8" ng-show="data.invoice_data.supplier_id == '11' ">
                    <div class="col-md-14">
                        <input class="form-control" type="text" ng-model="data.search_content.confirmation_ref"
                               ng-keyup="triggerSearch($event)" />
                    </div>
                    <div class="col-md-4">
                        <button class="block-action btn btn-inverse" ng-click="searchInvoiceOrder()">
                            搜索
                            <span class="i i-refresh refresh-animate"
                                  ng-show="local.invoice_order_search.search_status"></span>
                        </button>
                    </div>
                </div>
            </div>


            <table class="table" style="margin-top: 30px">
                <thead>
                    <tr ng-show="data.invoice_data.supplier_id == '11' ">
                        <th ng-repeat="item in local.invoice_order_table.GTA" ng-bind="item.title"
                            width="{{item.width}}"></th>
                    </tr>
                    <tr ng-hide="data.invoice_data.supplier_id == '11' ">
                        <th ng-repeat="item in local.invoice_order_table.Other" ng-bind="item.title"
                            width="{{item.width}}"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- GTA -->
                    <tr ng-repeat="invoice_order in data.invoice_order"
                        ng-show="data.invoice_data.supplier_id == '11' ">
                        <td>
                            <a ng-href="<?= Yii::app()->params['WEB_PREFIX'] ?>admin/order/edit/order_id/{{invoice_order.order_id}}"
                               ng-bind="invoice_order.order_id"></a></td>
                        <td ng-bind="invoice_order.confirmation_ref"></td>
                        <td ng-bind="invoice_order.product_name"></td>
                        <td>
                            <div ng-show="invoice_order.invoice_status == 0">未对账</div>
                            <div ng-show="invoice_order.invoice_status == 1">正确</div>
                            <div ng-show="invoice_order.invoice_status == 2" class="warning-red">有问题</div>
                        </td>
                        <td ng-bind="invoice_order.order_status"></td>
                        <td ng-bind="invoice_order.leader_name"></td>
                        <td ng-bind="invoice_order.tour_date"></td>
                        <td ng-bind="invoice_order.cost_total"></td>
                        <td>
                            <button class="block-action btn btn-inverse" ng-click="setInvoiceOrderRight($index)"
                                    ng-hide="invoice_order.invoice_status == 1">正确
                            </button>
                            <button class="block-action btn btn-inverse" ng-click="setInvoiceOrderProblem($index)">有问题
                            </button>
                            <button class="block-action btn btn-inverse" ng-click="showLog($index)"
                                    ng-hide="invoice_order.invoice_status == 0">日志
                            </button>
                        </td>
                    </tr>
                    <!-- other -->
                    <tr ng-repeat="invoice_order in data.invoice_order"
                        ng-hide="data.invoice_data.supplier_id == '11' ">
                        <td><a ng-href="/admin/order/edit/order_id/{{invoice_order.order_id}}"
                               ng-bind="invoice_order.order_id"></a></td>
                        <td ng-bind="invoice_order.product_name"></td>
                        <td ng-bind="invoice_order.special_name"></td>
                        <td ng-bind="invoice_order.date_added"></td>
                        <td ng-bind="invoice_order.tour_date"></td>
                        <td ng-bind="invoice_order.confirmation_ref"></td>
                        <td ng-bind="invoice_order.passenger_quantity_str"></td>
                        <td>
                            <div ng-show="invoice_order.invoice_status == 0">未对账</div>
                            <div ng-show="invoice_order.invoice_status == 1">正确</div>
                            <div ng-show="invoice_order.invoice_status == 2" class="warning-red">有问题</div>
                        </td>
                        <td ng-bind="invoice_order.order_status"></td>
                        <td ng-bind="invoice_order.leader_name"></td>
                        <td ng-bind="invoice_order.cost_total"></td>
                        <td>
                            <button class="block-action btn btn-inverse" ng-click="setInvoiceOrderRight($index)"
                                    ng-hide="invoice_order.invoice_status == 1">正确
                            </button>
                            <button class="block-action btn btn-inverse" ng-click="setInvoiceOrderProblem($index)">有问题
                            </button>
                            <button class="block-action btn btn-inverse" ng-click="showLog($index)"
                                    ng-hide="invoice_order.invoice_status == 0">日志
                            </button>
                        </td>
                    </tr>
                </tbody>

            </table>

        </div>

        <!--有退款记录确认-->
        <div class="overlay confirm" ng-show="local.overlay.refund_confirm_overlay.has_overlay">
            <div class="notify-container confirm">
                <div class="notify-head">有退款订单确认</div>
                <div class="notify-body">
                    该订单有 <span class="warning-red">退款记录</span>，请确认操作！
                </div>
                <div class="notify-foot">
                    <button class="btn block-action btn-inverse" ng-click="cancelRefundConfirm()">取消操作</button>
                    <button class="btn block-action btn-inverse" ng-click="confirmRefundConfirm()">确认对账</button>
                </div>
            </div>
        </div>
        <!--对账状态有问题转正确-->
        <div class="overlay confirm" ng-show="local.overlay.right_confirm_overlay.has_overlay">
            <div class="notify-container confirm">
                <form novalidate name="right_confirm_form">
                    <div class="notify-head">订单对账正确</div>
                    <div class="notify-body">
                        <div class="row">
                            <div class="col-md-2">备注</div>
                            <div class="col-md-16"><textarea name="remark" style="width: 100%"
                                                             ng-model="data.invoice_status_data.remark"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="notify-foot">
                        <button class="btn btn-inverse block-action" ng-click="cancelInvoiceOrderRight()">取消</button>
                        <button class="btn btn-inverse block-action" ng-click="confirmInvoiceOrderRight()"
                                ng-disabled="right_confirm_form.remark.$pristine">确认<span
                                class="i i-refresh refresh-animate"
                                ng-show="local.overlay.right_confirm_overlay.is_post"></span></button>
                    </div>
                </form>

            </div>
        </div>
        <div class="overlay confirm" ng-show="local.overlay.problem_overlay.has_overlay">
            <div class="notify-container confirm">
                <div class="notify-head">订单对账有问题</div>
                <div class="notify-body">
                    <div class="row">
                        <div class="col-md-2">原因</div>
                        <div class="col-md-16">
                            <label class="hi-radio" ng-repeat="reason in local.overlay.problem_overlay.reason">
                                <input
                                    type="radio"
                                    name="{{reason.name}}"
                                    value="{{ reason.code }}"
                                    ng-model="data.invoice_status_data.reason"
                                    >
                                <span class="inner-text" ng-bind="reason.name"></span>
                            </label>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px">
                        <div class="col-md-2">备注</div>
                        <div class="col-md-16">
                    <textarea style="width: 100%; height: 100px;" class="form-control"
                              ng-model="data.invoice_status_data.remark"></textarea>
                        </div>
                    </div>
                </div>
                <div class="notify-foot">
                    <button class="btn btn-inverse block-action" ng-click="cancelInvoiceOrderProblem()">取消</button>
                    <button class="btn btn-inverse block-action" ng-disabled=" data.invoice_status_data.reason == '' "
                            ng-click="confirmInvoiceOrderProblem()">确认<span class="i i-refresh refresh-animate"
                                                                            ng-show="local.overlay.problem_overlay.is_post"></span>
                    </button>
                </div>
            </div>
        </div>
        <div class="overlay confirm" ng-show="local.overlay.log_overlay.has_overlay">
            <div class="notify-container confirm" style="width: 90%">
                <div class="notify-head">订单对账日志</div>
                <div class="notify-body height-control">
                    <div class="text-center"><span class="i i-refresh refresh-animate"
                                                   ng-show="local.overlay.log_overlay.grid_options.in_progress"></span></span>
                    </div>
                    <hi-grid options="local.overlay.log_overlay.grid_options"></hi-grid>
                </div>
                <div class="notify-foot">
                    <button class="block-action btn btn-inverse" ng-click="closeLogOverlay()">确定</button>
                </div>
            </div>
        </div>
    </div>
</div>
