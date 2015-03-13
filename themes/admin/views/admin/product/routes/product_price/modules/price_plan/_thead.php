<tr>
    <th ng-repeat="group in local.plan_info.special_info.groups" ng-bind="group.title" ng-if="local.config.has_special_code"></th>
    <th ng-if="local.config.has_special_code">可售卖时间</th>
    <th style="width: 10%;" ng-if="!has_default_tickets">票种</th>
    <th style="width: 10%;" ng-if="!has_default_tickets && plan.need_tier_pricing == '1'">人数</th>
    <th style="width: 10%;">售卖价</th>
    <th style="width: 10%;">成本价</th>
    <th style="width: 10%;">门市价</th>
</tr>