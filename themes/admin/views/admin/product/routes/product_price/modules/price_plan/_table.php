<table class="table table-hover plan-list grid-bottom">
    <thead>
        <tr>
            <th colspan="{{plan.config.total_columns}}" class="text-center">
                <span ng-class="{ 'invalid' : plan.config.is_invalid }">
                    {{local.title}}计划{{$index + 1}} &nbsp;&nbsp; {{getPlanRangeLabel(plan)}}
                </span>
                <br />
                <span ng-show="local.config.is_special_plan">
                    <br />
                    渠道：{{plan.reseller}} 口号：{{plan.slogan}}
                </span>
                <div class="plan-action">
                    <button class="btn btn-danger block-action pull-right grid-left" ng-click="deletePlan($index)">
                        删除
                    </button>
                    <button class="btn btn-inverse block-action pull-right" ng-click="editPlan($index)">
                        编辑
                    </button>
                </div>
            </th>
        </tr>
        <?php include('_thead.php') ?>
    </thead>
    <tbody>
        <tr ng-repeat="item in plan.items" ng-init="all_codes = getRowSpanByCode(item.special_code); item_index = $index">
            <td class="span-border" ng-repeat="code in all_codes" ng-if="local.config.has_special_code && item_index % code.row_span == 0" rowspan="{{ code.row_span }}" ng-bind="code.label"></td>
            <td class="span-border" ng-if="local.config.has_special_code && item_index % plan.config.row_span_map.frequency == 0" rowspan="{{ plan.config.row_span_map.frequency }}" ng-bind="getFrequencyLabel(item.frequency)"></td>
            <td ng-bind="local.result.ticket_types_obj[item.ticket_id].cn_name" ng-class="{ 'span-border' : plan.need_tier_pricing == '1' }" ng-if="!has_default_tickets && item_index % plan.config.row_span_map.ticket == 0" rowspan="{{ plan.config.row_span_map.ticket }}"></td>
            <td ng-bind="item.quantity" ng-if="!has_default_tickets && plan.need_tier_pricing == '1'"></td>
            <td ng-bind="item.price"></td>
            <td ng-bind="item.cost_price"></td>
            <td ng-bind="item.orig_price"></td>
        </tr>
    </tbody>
</table>