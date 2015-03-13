<form name="shipping_form" novalidate>
    <div class="one-shipping-product grid-bottom" ng-class="{ 'shipping-info-border': data.shipping.shipping_info[pid].is_edit}" ng-repeat="(pid, product) in data.shipping.shipping_info">
        <h3 class="grid-bottom">
            <a ng-href="{{local.product_url + product.shipping_rule.product_id}}" target="_blank">{{product.shipping_rule.product_id}} － {{product.shipping_rule.product_name}}</a>
            <button class="block-action btn btn-inverse pull-right" ng-show="data.shipping.shipping_info[pid].is_edit" ng-click="operationSave(pid)">
                <span class="save-edit-font">保存</span>
            </button>
            <button class="block-action btn btn-inverse pull-right" ng-hide="data.shipping.shipping_info[pid].is_edit" ng-click="operationEdit(pid)" ng-disabled="!data.shipping.shipping_info[pid].allow_edit">
                编辑
            </button>
        </h3>

        <!--供应商-->
        <div class="row">
            <div class="section-subtitle col-md-5">供应商：</div>
            <div class="col-md-12 section-subbody" ng-bind="product.baseInfo.supplier_name_en"></div>
        </div>

        <!--商品类型-->
        <div class="row" ng-if="data.shipping.product_type == 'package'">
            <div class="section-subtitle col-md-5">商品类型：</div>
            <div class="col-md-12 section-subbody" ng-bind="product.baseInfo.bundle_type_name"></div>
        </div>

        <!--酒店房间数-->
        <div class="row" ng-if="data.shipping.product_type == 'package' && product.baseInfo.type == '7'">
            <div class="section-subtitle col-md-5">酒店房间数量：</div>
            <div class="col-md-12 section-subbody">
                <span ng-bind="product.ticket_num"></span>间
            </div>
        </div>

        <!--出行人-->
        <div class="row">
            <div class="section-subtitle col-md-5">出行人：</div>
            <div class="col-md-12 section-subbody">
                <div class="row">
                    <div class="col-md-4" ng-repeat="(key, name) in product.baseInfo.passengers"
                         ng-bind="name"></div>
                </div>
            </div>
        </div>

        <!--Special Code-->
        <div class="row" ng-if="product.baseInfo.need_special == '1'" ng-repeat="code in shipping_product_info[product.pid].special_code">
            <div class="section-subtitle col-md-5" ng-bind="code.label"></div>
            <div class="col-md-12 section-subbody">
                <span ng-bind="code.value"></span>
            </div>
        </div>

        <hr />

        <!--Tour Date-->
        <div class="row" ng-if="product.baseInfo.need_tour_date == '1'">
            <div class="section-subtitle col-md-5" ng-bind="shipping_product_info[pid].tour_date.label"></div>
            <div class="col-md-12 section-subbody">
                <span ng-hide="data.shipping.shipping_info[pid].is_edit">
                    {{ shipping_product_info[pid].tour_date.value | date : "yyyy-MM-dd" }}
                </span>
                <div class="meta-value-container"
                     ng-if="data.shipping.shipping_info[pid].is_edit">
                    <quick-datepicker ng-model='shipping_product_info[pid].tour_date.value'
                                      disable-timepicker='true' date-format='yyyy-M-d'
                                      on-change='shipping_product_info[pid].tour_date.fetchDepartureFromTourDate(product.baseInfo.tour_date)'
                                      date-filter='shipping_product_info[pid].tour_date.dateFilter'></quick-datepicker>
                </div>
            </div>
        </div>

        <!--Departure Point-->
        <div class="row" ng-if="product.baseInfo.need_departure == '1'">
            <div class="section-subtitle col-md-5" ng-bind="shipping_product_info[pid].departure.label"></div>
            <div class="col-md-12 section-subbody">
                <span ng-bind="shipping_product_info[pid].departure.value" ng-hide="data.shipping.shipping_info[pid].is_edit"></span>
                <div class="meta-value-container" ng-if="data.shipping.shipping_info[pid].is_edit">
                    <select chosen
                            style="width: 250px;"
                            ng-model="shipping_product_info[pid].departure.value"
                            ng-options="dep for dep in shipping_product_info[pid].departure.list"
                            no-results-text="'没有找到'"
                            data-placeholder="选择一个departure">
                    </select>
                    <span class="pad-left" ng-show="shipping_product_info[pid].departure.list.length == 0">
                        没有可用的{{shipping_product_info[pid].departure.label}}
                    </span>
                </div>
            </div>
        </div>

        <!--Supplier Booking Reference-->
        <div class="row" ng-if="product.shipping_rule.need_supplier_booking_ref == '1'">
            <div class="section-subtitle col-md-5">Supplier Booking ID：</div>
            <div class="section-subbody col-md-12">
                <div class="row">
                    <div class="col-md-9 shipping-booking">
                        <span ng-bind="product.supplier_order.supplier_booking_ref" ng-hide="data.shipping.shipping_info[pid].is_edit"></span>
                        <div class="meta-value-container" ng-if="data.shipping.shipping_info[pid].is_edit">
                            <input type="text" class="form-control" ng-model="product.supplier_order.supplier_booking_ref" ng-disabled="!data.shipping.shipping_info[pid].supplier_order.supplier_order_id" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Hitour Booking Reference-->
        <div class="row" ng-if="product.shipping_rule.need_hitour_booking_ref == '1'">
            <div class="section-subtitle col-md-5">玩途订单号：</div>
            <div class="section-subbody col-md-12">
                <div class="row">
                    <div class="col-md-9 shipping-hitour">
                        <span ng-bind="product.supplier_order.hitour_booking_ref" ng-hide="data.shipping.shipping_info[pid].is_edit"></span>
                        <div class="meta-value-container" ng-if="data.shipping.shipping_info[pid].is_edit">
                            <input type="text" ng-model="product.supplier_order.hitour_booking_ref" class="form-control" ng-disabled="!data.shipping.shipping_info[pid].supplier_order.supplier_order_id" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Confirmation Code-->
        <div class="row" ng-if="product.shipping_rule.supplier_feedback_type == '1' && product.shipping_rule.confirmation_type != '0'">
            <div class="section-subtitle col-md-5">Confirmation Code：</div>
            <div class="section-subbody col-md-12">
                <div class="row">
                    <div class="col-md-9 shipping-confirmation" ng-repeat="code in product.confirmation_ref track by $index">
                        <span ng-bind="code" ng-hide="data.shipping.shipping_info[pid].is_edit"></span>
                        <div class="meta-value-container" ng-if="data.shipping.shipping_info[pid].is_edit">
                            <input type="text" ng-model="product.confirmation_ref[$index]" class="form-control" ng-disabled="!data.shipping.shipping_info[pid].supplier_order.supplier_order_id" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Generated PDF-->
        <div class="row" ng-if="!(product.shipping_rule.booking_type != 'STOCK' && product.shipping_rule.supplier_feedback_type == '2' && product.shipping_rule.confirmation_type != 0) && product.supplier_order.voucher_ref.length">
            <div class="section-subtitle col-md-5">兑换单：</div>
            <div class="section-subbody col-md-12">
                <div class="row">
                    <div class="col-md-9 shipping-voucher" ng-repeat="voucher in product.supplier_order.voucher_ref">
                        <a ng-href="{{ voucher.voucher_url }}" target="_blank">
                            <span ng-bind="voucher.voucher_name"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!--Uploaded PDF-->
        <div class="row" ng-if="product.shipping_rule.booking_type != 'STOCK' && product.shipping_rule.supplier_feedback_type == '2' && product.shipping_rule.confirmation_type > 0">
            <div class="section-subtitle col-md-5">
                兑换单：
                <button class="block-action add btn btn-inverse" ng-click="addVoucher(pid)" ng-show="data.shipping.shipping_info[pid].is_edit && canAddVoucher(pid)" ng-disabled="!data.shipping.shipping_info[pid].supplier_order.supplier_order_id">
                    新增PDF
                </button>
                <div ng-show="data.shipping.shipping_info[pid].is_edit && data.shipping.shipping_info[pid].supplier_order.supplier_order_id">
                    <span class="small-desc" ng-hide="product.shipping_rule.confirmation_type == '3'">(需要上传{{ shipping_product_info[pid].shipping.max_count }}个PDF文件)</span>
                    <span class="small-desc" ng-show="product.shipping_rule.confirmation_type == '3'">(需要上传至少1个PDF文件)</span>
                </div>
            </div>
            <div class="section-subbody col-md-12" ng-show="data.shipping.shipping_info[pid].is_edit">
                <div class="row">
                    <div class="col-md-9 shipping-voucher" ng-repeat="voucher in product.supplier_order.voucher_ref">
                        <div class="meta-value-container">
                            <a class="meta-value" ng-href="{{ voucher.voucher_url }}" ng-bind="voucher.voucher_name" target="_blank"></a>
                            <span class="i i-close" ng-click="delVoucher(pid, $index)"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-subbody col-md-12" ng-hide="data.shipping.shipping_info[pid].is_edit">
                <div class="row">
                    <div class="col-md-9 shipping-voucher" ng-repeat="voucher in product.supplier_order.voucher_ref">
                        <a ng-href="{{ voucher.voucher_url }}" target="_blank">
                            <span ng-bind="voucher.voucher_name"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!--Supplier Additional Info-->
        <div class="row" ng-if="product.shipping_rule.display_additional_info == '1'">
            <div class="section-subtitle col-md-5">供应商附加信息：</div>
            <div class="section-subbody col-md-12">
                <div class="row">
                    <div class="col-md-16 shipping-booking">
                        <span class="meta-value" ng-bind="product.supplier_order.additional_info" ng-hide="data.shipping.shipping_info[pid].is_edit"></span>
                        <div class="meta-value-container" ng-if="data.shipping.shipping_info[pid].is_edit">
                            <input type="text" class="form-control" ng-model="product.supplier_order.additional_info" ng-disabled="!data.shipping.shipping_info[pid].supplier_order.supplier_order_id" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>