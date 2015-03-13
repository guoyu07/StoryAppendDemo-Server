<div class="states-section" ng-if="local.tab_options.current_tab.path == 'rule'">
    <div ng-controller="NoticeRuleCtrl">
        <hi-section-head options="local.section_head"></hi-section-head>
        <!--编辑态-->
        <div class="section-body" ng-class="local.section_head.getClass()" ng-show="local.section_head.is_edit">
            <form name="notice_rule_form" hi-watch-dirty="local.path_name">
                <table class="forms-table rule-table">
                    <tr>
                        <td>
                            <h4>是否提前购买</h4>
                        </td>
                        <td class="pad-bottom pad-left">
                            <div class="pad-top pad-bottom">
                                <hi-radio-switch options="local.radio_options.buy_in_advance" model="data.date_rules"></hi-radio-switch>
                            </div>
                            <div ng-if="data.date_rules.buy_in_advance_radio == 1">
                                客户需要提前
                                <input type="number" class="form-control inline-input" min="{{data.date_rules.buy_in_advance.min}}" ng-model="data.date_rules.buy_in_advance.qty" />
                                个
                                <hi-radio-switch options="local.radio_options.advance_day_type" model="data.date_rules"></hi-radio-switch>
                                购买
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4>是否立即发货</h4>
                        </td>
                        <td class="pad-bottom pad-left">
                            <div class="pad-top pad-bottom">
                                <hi-radio-switch options="local.radio_options.ship_immediately" model="data.date_rules"></hi-radio-switch>
                            </div>
                            <div ng-if="data.date_rules.lead_time_radio == 1">
                                将会在
                                <input type="number" class="form-control inline-input" min="{{data.date_rules.lead_time.min}}" ng-model="data.date_rules.lead_time.qty" />
                                个
                                <hi-radio-switch options="local.radio_options.advance_day_type" model="data.date_rules"></hi-radio-switch>
                                发货
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4>最远购买时间</h4>
                        </td>
                        <td class="pad-bottom pad-left">
                            <div class="pad-top pad-bottom">
                                <hi-radio-switch options="local.radio_options.sale_range" model="data.date_rules"></hi-radio-switch>
                            </div>
                            <div ng-if="data.date_rules.sale_range_type == 1">
                                <hi-input-dropdown options="local.dropdown_options.sale_range" model="data.date_rules.sale_range_duration"></hi-input-dropdown>
                                内可购买本产品
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4>兑换有效时间</h4>
                        </td>
                        <td class="pad-bottom pad-left">
                            <div class="pad-top pad-bottom">
                                <hi-radio-switch options="local.radio_options.redeem_type" model="data.redeem_limit"></hi-radio-switch>
                            </div>
                            <div ng-if="data.redeem_limit.redeem_type == 2">
                                从购买日起，
                                <hi-input-dropdown options="local.dropdown_options.fixed_duration" model="data.redeem_limit.fixed_duration"></hi-input-dropdown>
                                之内兑换有效
                            </div>
                            <div ng-if="data.redeem_limit.redeem_type == 3">
                                用户需要在
                                <div class="inline-input redeem-datepicker">
                                    <quick-datepicker ng-model="data.redeem_limit.expire_date" disable-timepicker="true" date-format="yyyy-M-d" date-filter="afterStart"></quick-datepicker>
                                </div>
                                之前兑换有效
                            </div>
                            <div ng-if="data.redeem_limit.redeem_type == 4">
                                在用户选择的使用日期后
                                <hi-input-dropdown options="local.dropdown_options.range_duration" model="data.redeem_limit.range_duration"></hi-input-dropdown>
                                兑换有效
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4>使用有效时间</h4>
                        </td>
                        <td class="pad-bottom pad-left">
                            <div class="pad-top pad-bottom">
                                用户兑换后，
                                <input type="text" class="form-control inline-input" ng-model="data.redeem_limit.usage_limit" placeholder="24小时" />
                                内使用有效
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4>是否可以退换</h4>
                        </td>
                        <td class="pad-bottom pad-left">
                            <div class="pad-top pad-bottom">
                                <hi-radio-switch options="local.radio_options.return_type" model="data.return_limit"></hi-radio-switch>
                            </div>
                            <div ng-if="data.return_limit.return_type == 1 || data.return_limit.return_type == 2">
                                使用日期前
                                <hi-input-dropdown options="local.dropdown_options.offset_duration" model="data.return_limit.offset_duration"></hi-input-dropdown>
                                可退换
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <!--展示态-->
        <div class="section-body" ng-class="local.section_head.getClass()" ng-hide="local.section_head.is_edit">
            <table class="forms-table">
                <tr>
                    <td class="view-title">兑换规则</td>
                    <td class="view-body" ng-bind-html="data.rule_desc.redeem_desc"></td>
                </tr>
                <tr>
                    <td class="view-title">购买时间</td>
                    <td class="view-body" ng-bind-html="data.rule_desc.sale_desc"></td>
                </tr>
                <tr>
                    <td class="view-title">退款限制</td>
                    <td class="view-body" ng-bind-html="data.rule_desc.return_desc"></td>
                </tr>
                <tr>
                    <td class="view-title">发货限制</td>
                    <td class="view-body" ng-bind-html="data.rule_desc.shipping_desc"></td>
                </tr>
            </table>
        </div>
    </div>
</div>