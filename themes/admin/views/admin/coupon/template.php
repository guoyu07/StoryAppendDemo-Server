<div id="coupon-template-container" data-ng-controller="CouponTemplateCtrl" class="container page-container">
    <div class="states-section">
        <hi-section-head model="local.section_head.info" options="local.section_head.info"></hi-section-head>
        <div class="section-body template-info row" ng-class="local.section_head.info.getClass()"
             ng-show="local.section_head.info.is_edit">
            <form name="template_info">
                <table class="forms-table col-md-12">
                    <tr>
                        <td><label for="status">模版状态</label></td>
                        <td>
                            <div class="col-md-18">
                                <hi-radio-switch model="data.template"
                                                 options="local.radio_switch.status"></hi-radio-switch>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="coupon_quantity">生成优惠券数量</label></td>
                        <td>
                            <div class="col-md-6">
                                <input type="number" class="form-control" id="coupon_quantity"
                                       ng-model="data.template.quantity" required />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="coupon_date">优惠券使用日期</label></td>
                        <td class="pad-bottom pad-top">
                            <div class="col-md-18 pad-bottom">
                                <hi-radio-switch model="data.template"
                                                 options="local.radio_switch.date_type"></hi-radio-switch>
                            </div>
                            <div class="type_select" ng-show="data.template.date_type == 0">
                                <div class="col-md-8">
                                    <quick-datepicker ng-model='data.template.date_start' disable-timepicker='true'
                                                      date-format='yyyy-M-d'></quick-datepicker>
                                </div>
                                <div class="col-md-1 text-right">
                                    －
                                </div>
                                <div class="col-md-8">
                                    <quick-datepicker ng-model='data.template.date_end' date-filter='afterStart'
                                                      disable-timepicker='true'
                                                      date-format='yyyy-M-d'></quick-datepicker>
                                </div>
                            </div>
                            <div class="type_select" ng-show="data.template.date_type == 1">
                                <div class="row" style="margin-bottom: 10px;">
                                    <label class="col-md-6">自下单日起生效</label>
                                    <div class="col-md-12">
                                        <div hi-input-dropdown options="local.dropdown.start_offset.options"
                                             model="local.dropdown.start_offset.values"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-md-6">有效日期范围</label>
                                    <div class="col-md-12">
                                        <div hi-input-dropdown options="local.dropdown.end_range.options"
                                             model="local.dropdown.end_range.values"></div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="customer_limit">优惠券使用人</label></td>
                        <td>
                            <div class="col-md-18">
                                <hi-radio-switch model="data.template"
                                                 options="local.radio_switch.customer_limit"></hi-radio-switch>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="template_coupon">模版优惠券</label></td>
                        <td>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="template_coupon" placeholder="请填写模板优惠券ID"
                                       ng-model="data.template.coupon_id" required />
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-inverse" ng-click="selectCoupon( false )">
                                    查看现有优惠券
                                </button>
                                <button class="btn btn-inverse" ng-click="selectCoupon( true )">
                                    新增优惠券
                                </button>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="section-body template-info row" ng-class="local.section_head.info.getClass()"
             ng-hide="local.section_head.info.is_edit">
            <button class="btn btn-inverse" id="view_history" ng-click="toggleHistory( true )"
                    ng-show="!local.new_template">
                优惠券生成历史
            </button>
            <table class="forms-table">
                <tr>
                    <td class="view-title">来源商品</td>
                    <td class="view-body"
                        ng-bind="data.template.product_id + '［' + data.template.product_name + '］'"></td>
                </tr>
                <tr>
                    <td class="view-title">挂接优惠券ID</td>
                    <td class="view-body">
                        <a ng-href="{{ local.edit_coupon }}{{ data.template.coupon_id }}">{{ data.template.coupon_id }}
                                                                                          / {{ data.coupon.name }}</a>
                    </td>
                </tr>
                <tr>
                    <td class="view-title">优惠券状态</td>
                    <td class="view-body" ng-bind="local.radio_switch.status.items[data.template.status]"></td>
                </tr>
                <tr>
                    <td class="view-title">生成优惠券数量</td>
                    <td class="view-body" ng-bind="data.template.quantity"></td>
                </tr>
                <tr>
                    <td class="view-title">优惠券使用日期</td>
                    <td class="view-body">
                        <span ng-bind="local.radio_switch.date_type.items[data.template.date_type]"></span>
                        <span ng-show="data.template.date_type == 0">
                            （{{ data.template.date_start | date : yyyy-MM-dd }} － {{ data.template.date_end | date : yyyy-MM-dd }}）
                        </span>
                        <span ng-show="data.template.date_type == 1">
                            （优惠券自下单日期{{ formatDropdown('start_offset') }}后可使用；优惠券有效期为{{ formatDropdown('end_range') }}）
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="view-title">优惠券使用人</td>
                    <td class="view-body"
                        ng-bind="local.radio_switch.customer_limit.items[data.template.customer_limit]"></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="states-section" ng-show="data.coupon">
        <div class="section-head">
            <h2 class="section-title">优惠券规则</h2>
        </div>
        <div class="section-body coupon-rules row view"
             ng-class="{ 'hide-customer' : data.template.customer_limit == 1 }">
            <?php include_once(__DIR__ . '/modules/coupon_rules/view_rule.php'); ?>
        </div>
    </div>
    <div class="overlay confirm" ng-show="local.overlay.has_overlay" ng-if="!local.new_template">
        <div class="notify-container confirm">
            <div class="notify-head">优惠券生成历史</div>
            <div class="notify-body">
                <hi-grid options="local.overlay.grid_options"></hi-grid>
            </div>
            <div class="notify-foot">
                <button class="block-action btn btn-inverse" ng-click="toggleHistory( false )">确定</button>
            </div>
        </div>
    </div>
</div>