<script type="text/ng-template" id="activitiesOrder.html">
    <div class="states-section">
        <div class="row">
            <div class="col-md-3 col-md-offset-1">
                <label>订单总量</label>
                <div class="total-static" ng-bind="data.summary.total_orders"></div>
            </div>
            <div class="col-md-3">
                <label>成功订单量</label>
                <div class="total-static" ng-bind="data.summary.total_success_orders"></div>
            </div>
            <div class="col-md-3">
                <label>异常订单量</label>
                <div class="total-static" ng-bind="data.summary.problem_order_counts"></div>
            </div>
            <div class="col-md-3">
                <label>异常订单占比</label>
                <div class="total-static" ng-bind="data.summary.problem_order_rate"></div>
            </div>
            <div class="col-md-3">
                <label>统计销售金额</label>
                <div class="total-static" ng-bind="data.summary.total_success_amount"></div>
            </div>
        </div>

        <div class="row activity-search-ctn">
            <div class="row search-row">
                <div class="col-md-2 col-md-offset-1 search-title">
                    活动名称
                </div>
                <div class="form-group col-md-4">
                    <select chosen
                            style="width: 100%;"
                            ng-model="local.grid_options.query.query_filter.activity_id"
                            ng-change="changeActivities()"
                            ng-options="activity.activity_id as activity.title for activity in local.activities_list track by activity.activity_id"
                            data-placeholder="选择活动"
                            no-results-text="'没有找到'"
                        >
                    </select>
                </div>
                <div class="col-md-3">
                    ( {{data.activity_duration.start_date}}
                </div>
                <div class="col-md-1 text-center">
                    ——
                </div>
                <div class="col-md-3">
                    {{data.activity_duration.end_date}} )
                </div>
            </div>
            <div class="row search-row">
                <div class="col-md-2 col-md-offset-1 search-title">
                    统计时间
                </div>
                <div class="duration-ctn">
                    <div class="col-md-4">
                        <quick-datepicker ng-model='local.grid_options.query.query_filter.date_start'
                                          on-change='filterActivity()' disable-timepicker='true'
                                          date-format='yyyy-M-d'></quick-datepicker>
                    </div>
                    <div class="col-md-1 text-right">
                        —
                    </div>
                    <div class="col-md-4">
                        <quick-datepicker ng-model='local.grid_options.query.query_filter.date_end'
                                          on-change='filterActivity()' disable-timepicker='true'
                                          date-format='yyyy-M-d'></quick-datepicker>
                    </div>
                </div>
            </div>
            <div class="row search-row">
                <div class="col-md-2 col-md-offset-1 search-title">
                    商品名称/ID
                </div>
                <div class="duration-ctn">
                    <div class="col-md-4">
                        <input type="text" class="form-control"
                               ng-model="local.grid_options.query.query_filter.product_id" placeholder="搜索商品ID"
                               hi-enter="filterActivity()" />
                    </div>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-inverse pull-right" ng-click="filterActivity()">搜索</button>
                </div>
            </div>
        </div>
        <div class="col-md-offset-1">
            <hi-grid options="local.grid_options"></hi-grid>
        </div>
    </div>
</script>