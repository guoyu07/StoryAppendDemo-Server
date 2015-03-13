<div ng-if="['edit_price_plan', 'edit_special_price_plan'].indexOf(local.price_type) > -1" ng-controller="priceEditPlanCtrl" class="section-body view">
    <hi-section-head options="local.section_head"></hi-section-head>
    <form name="edit_plan_form" hi-watch-dirty="local.path_name">
        <div ng-if="local.config.is_special_plan" class="clearfix pad-top">
            <label for="reseller" class="col-md-4">渠道名称</label>
            <div class="col-md-5">
                <input id="reseller" ng-model="data.current_plan.reseller" class="form-control" />
            </div>
            <label for="slogan" class="col-md-4">口号</label>
            <div class="col-md-5">
                <input id="slogan" ng-model="data.current_plan.slogan" class="form-control" />
            </div>
        </div>

        <div class="section-subtitle">此价格是否应用于整个生效区域？</div>
        <div class="section-subbody">
            <hi-radio-switch options="local.radio_options.valid_region" model="data.current_plan"></hi-radio-switch>
            <div class="row pad-top" ng-show="data.current_plan.valid_region == 1">
                <label class="col-md-5">此价格应用的生效时间为</label>
                <div class="col-md-6">
                    <quick-datepicker ng-model='data.current_plan.from_date' date-filter='fromDateFilter' disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
                </div>
                <div class="col-md-1 text-center">
                    －
                </div>
                <div class="col-md-6">
                    <quick-datepicker ng-model='data.current_plan.to_date' date-filter='toDateFilter' disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
                </div>
            </div>
        </div>

        <div ng-if="!$parent.local.has_default_tickets">
            <div class="section-subtitle">是否需要设置阶梯价格</div>
            <div class="section-subbody pad-bottom">
                <hi-radio-switch options="local.radio_options.need_tier_pricing" model="data.current_plan"></hi-radio-switch>
            </div>
        </div>

        <hr />

        <div class="pad-top pad-bottom">
            <h4 class="text-center">编辑{{local.plan_title}}</h4>
        </div>

        <table class="table table-hover plan-list grid-bottom" ng-init="plan = data.current_plan">
            <thead>
                <?php include('_thead.php') ?>
            </thead>
            <tbody>
                <tr ng-repeat="item in plan.items" ng-init="all_codes = getRowSpanByCode(item.special_code); item_index = $index;">
                    <div ng-if="local.config.has_special_code">
                        <td class="span-border" ng-repeat="code in all_codes" ng-if="item_index % code.row_span == 0" rowspan="{{code.row_span}}" ng-bind="code.label"></td>
                    </div>
                    <td class="span-border frequency" ng-if="local.config.has_special_code && item_index % plan.config.row_span_map.frequency == 0" rowspan="{{ plan.config.row_span_map.frequency }}" ng-click="toggleEditFrequency(item.special_code)">
                        {{ getFrequencyLabel(data.current_plan.special_code_frequency[item.special_code]) }}
                    </td>
                    <td ng-bind="local.result.ticket_types_obj[item.ticket_id].cn_name" ng-class="{ 'span-border' : plan.need_tier_pricing == '1' }" ng-if="!has_default_tickets && item_index % plan.config.row_span_map.ticket == 0" rowspan="{{ plan.config.row_span_map.ticket }}"></td>
                    <td ng-bind="item.quantity" ng-if="!has_default_tickets && plan.need_tier_pricing == '1'"></td>
                    <td>
                        <input required ng-model="item.price" class="form-control" onclick="this.select()" />
                    </td>
                    <td>
                        <input required ng-model="item.cost_price" class="form-control" onclick="this.select()" />
                    </td>
                    <td>
                        <input required ng-model="item.orig_price" class="form-control" onclick="this.select()" />
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

    <hr />

    <div class="states-section">
        <div class="section-head" ng-show="data.plans.length > 1">
            <h2 class="section-title">其他价格计划</h2>
        </div>

        <div class="other-plans-list grid-top" ng-repeat="plan in data.plans" ng-show="data.current_plan.price_plan_id != plan.price_plan_id">
            <?php include( __DIR__ . '/_table.php' ); ?>
        </div>
    </div>

    <div class="overlay confirm" ng-show="local.overlay.has_overlay">
        <div class="notify-container confirm">
            <div class="notify-head">编辑可售卖范围</div>
            <div class="notify-body">
                <form name="edit_frequency_form" hi-watch-dirty="local.path_name">
                    <div class="clearfix">
                        <label class="hi-radio col-md-4" ng-repeat="rule in local.frequency_sale_rule" ng-click="changeRule(rule)">
                            <input type="radio" name="frequency_sale_rule" value="{{ rule.value }}" ng-model="local.overlay.frequency.rule" />
                            <span class="inner-text" ng-bind="rule.label"></span>
                        </label>
                    </div>
                    <hr />
                    <div class="clearfix" ng-show="local.overlay.frequency.rule == '1'">
                        <label class="hi-checkbox col-md-4" ng-repeat="(value, label) in local.weekdays" ng-show="value != 'wdall'">
                            <input type="checkbox" name="frequency_sale_days" value="{{ value }}" ng-checked="local.overlay.frequency.days.indexOf(value) > -1" ng-click="toggleSelection(value, local.overlay.frequency.days)" />
                            <span class="inner-text" ng-bind="label"></span>
                        </label>
                    </div>
                </form>
            </div>
            <div class="notify-foot">
                <button class="block-action btn btn-default" ng-click="toggleEditFrequency()">取消</button>
                <button class="block-action btn btn-inverse" ng-click="toggleEditFrequency(false, true)">确定</button>
            </div>
        </div>
    </div>
</div>