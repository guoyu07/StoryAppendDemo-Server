<script type="text/ng-template" id="userAnalysis.html">
    <div id="userAnalysis">
        <div class="row grid-top">
            <div class="col-md-2">时间</div>
            <div class="col-md-16">
                <div class="one-tag" ng-repeat="duration in local.durations_filters"
                     ng-class="{ 'highlighted' : local.current_duration == $index }"
                     ng-click="setFilter($index)" ng-bind="duration.name"></div>
            </div>
            <div class="col-md-16 col-md-offset-2 mb10">
                <div class="col-md-5">
                    <quick-datepicker ng-model='local.query_filter.from_date' on-change="setFilter(4)"
                                      disable-timepicker='true'
                                      date-format='yyyy-M-d'></quick-datepicker>
                </div>
                <div class="col-md-1 text-center">——</div>
                <div class="col-md-5">
                    <quick-datepicker ng-model='local.query_filter.to_date' on-change="setFilter(4)"
                                      disable-timepicker='true'
                                      date-format='yyyy-M-d'></quick-datepicker>
                </div>
                <div class="col-md-2">
                    <button class="btn block-action" ng-class="{ 'btn-inverse' : local.contrast_status == 1 }"
                            ng-click="compare()">对比
                    </button>
                </div>
            </div>
            <div class="col-md-16 col-md-offset-2" ng-show="local.contrast_status == 1">
                <div class="col-md-5">
                    <quick-datepicker ng-model='local.query_filter.compare_from_date'
                                      disable-timepicker='true' on-change="setDurationDate(6)"
                                      date-format='yyyy-M-d'></quick-datepicker>
                </div>
                <div class="col-md-1 text-center">——</div>
                <div class="col-md-5">
                    <quick-datepicker ng-model='local.query_filter.compare_to_date'
                                      disable-timepicker='true' on-change="setDurationDate(7)"
                                      date-format='yyyy-M-d'></quick-datepicker>
                </div>
            </div>
        </div>
        <div class="row grid-top">
            <div class="col-md-2">指标</div>
            <div class="col-md-15">
                <div class="target_select">
                    <label ng-bind="local.targets_filters[local.query_filter.target-1].name"></label>
                    <button class="toggle-btn btn btn-inverse" ng-class="{'expanded' : local.target_expanded == 1}"
                            ng-click="local.target_expanded = local.target_expanded % 2 == 1 ? 0 : 1"></button>
                    <span class="i i-refresh refresh-animate col-md-offset-2"
                          ng-show="local.chart_loading_status"></span>
                </div>
                <div class="target_set" ng-show="local.target_expanded == 1">
                    <div class="row grid-top" ng-repeat="t_set in local.targets_set">
                        <div class="col-md-3" ng-bind="t_set.target_name"></div>
                        <div class="col-md-15">
                            <label class="hi-radio" ng-repeat="target in local.targets_filters"
                                   ng-if="target.key == t_set.key">
                                <input
                                    type="radio"
                                    name=""
                                    value="{{target.target}}"
                                    ng-model="local.query_filter.target"
                                    >
                                <span class="inner-text" ng-bind="target.name"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row grid-top">
            <nvd3-line-chart
                data="chart_data"
                id="userAnalysisChart"
                width="1000"
                height="400"
                showXAxis="true"
                showYAxis="true"
                tooltips="true"
                interactive="true"
                useInteractiveGuideline="true"
                xAxisTickFormat="xAxisTickFormat()"
                showLegend="true"
                color="local.chart_color"
                legendColor="local.chart_color">
                <svg></svg>
            </nvd3-line-chart>
        </div>
        <div class="grid-top">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>统计指标</th>
                        <th ng-show="local.contrast_status == 1">{{local.query_filter.compare_from_date | date:
                                                                 'yyyy-MM-dd'}}到{{local.query_filter.compare_to_date |
                                                                 date: 'yyyy-MM-dd'}}
                        </th>
                        <th>{{local.query_filter.from_date | date: 'yyyy-MM-dd'}}到{{local.query_filter.to_date | date:
                            'yyyy-MM-dd'}}
                        </th>
                        <th ng-show="local.contrast_status == 1">对比趋势(B-A)/A</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="table in table_data">
                        <td ng-bind="table[0]"></td>
                        <td ng-show="local.contrast_status == 1" ng-bind="table[2]"></td>
                        <td ng-bind="table[1]"></td>
                        <td ng-show="local.contrast_status == 1" ng-bind="table[3]"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</script>