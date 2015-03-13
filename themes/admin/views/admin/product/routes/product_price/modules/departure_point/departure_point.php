<div class="states-section departure-point" ng-if="local.tab_options.current_tab.path == 'departure_point'" ng-controller="priceDeparturePointCtrl">
    <hi-section-head options="local.section_head.departure_point"></hi-section-head>
    <hi-uploader options="local.uploader_options.excel"></hi-uploader>

    <div class="section-body" ng-show="!local.section_head.departure_point.is_edit">
        <div ng-show="data.departure.has_departure == 1">
            <p ng-show="data.departure.valid_region == 1">
                以下计划应用于自定义区间
            </p>
            <p ng-show="data.departure.valid_region == 0">
                以下计划应用于整个区间
            </p>
            <div class="one-tour-operation" ng-repeat="plan in data.departure.plan_list">
                <div class="section-head" ng-show="data.departure.valid_region == 1">
                    <span class="text-emphasis">有效区间：</span>{{ plan.from_date }} － {{ plan.to_date }}
                </div>
                <div class="section-body" ng-show="plan.plans">
                    <ul>
                        <li ng-repeat="item in plan.plans">
                            &nbsp;&nbsp; {{item.departures[0].departure_point}}, &nbsp;&nbsp;{{item.time}}&nbsp;&nbsp;
                            <span ng-show="item.additional_limit.length">
                                （{{item.additional_limit.toString()}}）
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div ng-show="data.departure.has_departure == '0'">
            <div class="section-subtitle">
                此商品不需要Departure Point。
            </div>
        </div>
    </div>

    <div class="section-body" ng-show="local.section_head.departure_point.is_edit">
        <div class="clearfix">
            <label class="col-md-6">客户是否需要填写Departure Point</label>
            <div class="col-md-12">
                <hi-radio-switch options="local.radio_options.has_departure" model="data.departure"></hi-radio-switch>
            </div>
        </div>

        <form name="departure_limits_form" ng-show="data.departure.has_departure == '1'">
            <div class="section-subtitle">
                显示名称
            </div>
            <div class="section-subbody clearfix">
                <label class="col-md-2">中文：</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" ng-model="data.departure.cn_departure_title" />
                </div>
                <label class="col-md-2">英文：</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" ng-model="data.departure.en_departure_title" />
                </div>
            </div>

            <div class="section-subtitle">
                生效区间设置
            </div>
            <div class="section-subbody">
                <hi-radio-switch options="local.radio_options.valid_region" model="data.departure"></hi-radio-switch>
            </div>

            <div class="section-subtitle">
                增加生效区间
                <button class="btn btn-inverse block-action" ng-show="data.departure.valid_region == 1" ng-click="addDeparturePlan()">
                    新增
                </button>
            </div>

            <div class="one-block" ng-repeat="plan in data.departure.plan_list" ng-init="plan_index = $index">
                <div class="delete-block" ng-click="deleteDeparturePlan($index)" ng-show="data.departure.valid_region == 1">
                    <span class="i i-close"></span>
                </div>

                <div class="section-head clearfix" ng-show="data.departure.valid_region == '1'">
                    <div class="col-md-6 col-md-offset-2">
                        <quick-datepicker ng-model="plan.from_date" disable-timepicker="true" date-filter="dateFilter" date-format="yyyy-M-d"></quick-datepicker>
                    </div>
                    <div class="col-md-2 text-center">
                        －
                    </div>
                    <div class="col-md-6">
                        <quick-datepicker ng-model="plan.to_date" disable-timepicker="true" date-filter="dateFilter" date-format="yyyy-M-d"></quick-datepicker>
                    </div>
                </div>
                <div class="section-body">
                    <div class="pad-top pad-bottom">
                        <div class="col-md-7 col-md-offset-1">
                            添加地点及约束条件
                            <button class="btn btn-inverse block-action" ng-click="addDeparturePlanItem(plan_index)">
                                新增
                            </button>
                        </div>
                        <button class="btn btn-inverse col-md-offset-5" ng-click="triggerUpload(plan_index)">
                            导入Departure Points
                        </button>
                    </div>

                    <div class="one-plan pad-top" ng-repeat="item in plan.plans" ng-init="item_index = $index">
                        <div class="clearfix pad-bottom">
                            <label class="col-md-2 text-right">中文地点</label>
                            <div class="col-md-5">
                                <input class="form-control" type="text" required ng-model="item.departures[0].departure_point" />
                            </div>
                            <label class="col-md-2 text-right">英文地点</label>
                            <div class="col-md-5">
                                <input class="form-control" type="text" required ng-model="item.departures[1].departure_point" />
                            </div>
                            <div class="col-md-2 col-md-offset-1">
                                <button class="btn btn-inverse block-action" ng-click="deleteDeparturePlanItem(plan_index, $index)">
                                    删除
                                </button>
                            </div>
                        </div>

                        <div class="clearfix pad-bottom">
                            <label class="col-md-2 text-right">出发时间</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" ng-model="item.time" ng-pattern="/[0-2][0-9]:[0-6][0-9]/" placeholder="格式为00:00，冒号之间不能空格" />
                            </div>

                            <label class="col-md-2 text-right">约束条件</label>
                            <div class="col-md-7">
                                <div class="one-tag" ng-repeat="day in local.weekday" ng-click="toggleDay(day, plan_index, item_index)" ng-bind="day" ng-class="{ 'selected' : item.additional_limit.indexOf(day) > -1 }"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>