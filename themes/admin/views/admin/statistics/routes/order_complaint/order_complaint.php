<script type="text/ng-template" id="orderComplaint.html">
    <div class="row grid-top">
        <label class="col-md-2">时间限制</label>
        <div class="col-md-14">
            <div class="one-tag" ng-repeat="duration in local.durations_filters"
                 ng-class="{ 'highlighted' : local.current_duration == $index }"
                 ng-click="setFilter( $index, 'duration' )" ng-bind="duration.name"></div>
        </div>
    </div>

    <div class="row grid-top" ng-show="local.durations_filters[local.current_duration].key == 5">
        <div class="col-md-4 col-md-offset-2">
            <quick-datepicker ng-model='local.duration_from_date' on-change="setFilter( 4, 'duration' )"
                              disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
        </div>
        <div class="col-md-1 text-right">
            —
        </div>
        <div class="col-md-4">
            <quick-datepicker ng-model='local.duration_to_date' on-change="setFilter( 4, 'duration' )"
                              disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
        </div>
    </div>

    <div class="row grid-top">
        <label class="col-md-2">限制条件</label>
        <div class="form-group col-md-3">
            <select chosen
                    style="width: 100%;"
                    ng-model="local.query_filter.country_code"
                    ng-change="fetchChartData()"
                    ng-options="country.country_code as country.cn_name group by country.group for country in local.country_list track by country.country_code"
                    data-placeholder="选择国家"
                    no-results-text="'没有找到'"
                >
            </select>
        </div>
        <div class="form-group col-md-3">
            <select chosen
                    style="width: 100%;"
                    ng-model="local.query_filter.city_code"
                    ng-change="fetchChartData()"
                    ng-options="city.city_code as city.city_name group by city.group for city in local.city_list track by city.city_code"
                    data-placeholder="选择城市"
                    no-results-text="'没有找到'"
                >
            </select>
        </div>
        <div class="form-group col-md-3">
            <select chosen
                    style="width: 100%;"
                    ng-model="local.query_filter.supplier_id"
                    ng-change="fetchChartData()"
                    ng-options="supplier.supplier_id as supplier.name group by supplier.group for supplier in local.supplier_list track by supplier.supplier_id"
                    data-placeholder="选择供应商"
                    no-results-text="'没有找到'"
                >
            </select>
        </div>
        <div class="form-group col-md-3">
            <select chosen
                    style="width: 100%;"
                    ng-model="local.query_filter.product_type_id"
                    ng-change="fetchChartData()"
                    ng-options="type.value as type.label for type in local.product_type_list track by type.value"
                    data-placeholder="选择票种"
                    no-results-text="'没有找到'"
                >
            </select>
        </div>
    </div>

    <div class="row grid-top">
        <div class="col-md-4 col-md-offset-2">
            <input type="text" class="form-control" ng-model="local.query_filter.product_id"
                   placeholder="搜索商品ID" hi-enter="fetchChartData()" />
        </div>
        <div class="col-md-1">
            <button class="btn btn-inverse pull-right" ng-click="fetchChartData()">搜索</button>
        </div>
    </div>

    <div class="row grid-top">
        <label class="col-md-2">纵轴指标</label>
        <div class="one-tag" ng-repeat="yaxis in local.yaxis_filters"
             ng-class="{ 'highlighted' : local.current_yaxis == $index }"
             ng-click="setFilter( $index, 'yaxis' )" ng-bind="yaxis.name"></div>
    </div>

    <div class="row grid-top" id="order_line">
        <svg style="width: 960px; height: 400px;"></svg>
    </div>
</script>