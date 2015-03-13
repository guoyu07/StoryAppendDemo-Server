<!--乘客信息-->
<div class="states-section passenger-info clearfix">
    <hi-section-head options="local.section_head.passenger_info"></hi-section-head>
    <div class="section-body row" ng-class="local.section_head.passenger_info.getClass()">
        <form name="passenger_info">
            <div ng-show="data.shipping.passenger_info.has_lead">
                <div ng-repeat="pax in data.shipping.passenger_info.lead" ng-init="is_leader = 1">
                    <div ng-include="'meta.html'"></div>
                </div>
            </div>
            <div ng-show="data.shipping.passenger_info.has_all">
                <div ng-repeat="pax in data.shipping.passenger_info.all" ng-init="is_leader = 0">
                    <div ng-include="'meta.html'"></div>
                </div>
            </div>
        </form>
    </div>
</div>