<div class="row">
    <div class="col-md-5">
        <div class="input-group button-select range-or-single">
            <div class="dropdown">
                <button class="btn btn-inverse dropdown-toggle" data-toggle="dropdown">
                    {{ local.close_ranges[tour.current_range] }}
                    <span class="caret"></span>
                </button>
                <span class="dropdown-arrow"></span>
                <ul class="dropdown-menu">
                    <li ng-repeat="(value, label) in local.close_ranges">
                        <a class="status text-center" ng-bind="label" ng-click="changeRange(value, tour)"></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-11" ng-if="tour.current_range == 'weekday'">
        <input class="form-control" type="text" ng-model="tour.added_field.weekday" />
    </div>
    <div class="col-md-11" ng-if="tour.current_range == 'singleday'">
        <quick-datepicker ng-model="tour.added_field.singleday" disable-timepicker="true" date-filter="tour.dateFilter" date-format='yyyy-M-d'></quick-datepicker>
    </div>
    <div class="col-md-11" ng-if="tour.current_range == 'range'">
        <div class="row input-wrapper datepicker-group">
            <div class="col-md-9">
                <quick-datepicker ng-model="tour.added_field.range.from_date" disable-timepicker="true" date-filter="tour.dateFilter" date-format="yyyy-M-d"></quick-datepicker>
            </div>
            <div class="col-md-9">
                <quick-datepicker ng-model="tour.added_field.range.to_date" disable-timepicker="true" date-filter="tour.dateFilter" date-format="yyyy-M-d"></quick-datepicker>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <button class="btn btn-inverse block-action add" ng-click="addCloseItem(tour)">添加</button>
    </div>
</div>

<div class="close-dates-container grid-top">
    <div class="close-date-part with-border clearfix">
        <div class="col-md-4">
            <label>时间段</label>
        </div>
        <div class="col-md-14">
            <div class="one-tag selected" ng-repeat="range in tour.parts.range" ng-click="deleteCloseItem($index, tour_index, 'range')" ng-bind="range"></div>
        </div>
    </div>
    <div class="close-date-part with-border clearfix">
        <div class="col-md-4">
            <label>单独固定日期</label>
        </div>
        <div class="col-md-14">
            <div class="one-tag selected" ng-repeat="single_day in tour.parts.singleday" ng-click="deleteCloseItem($index, tour_index, 'singleday')" ng-bind="single_day"></div>
        </div>
    </div>
    <div class="close-date-part clearfix">
        <div class="col-md-4">
            <label>按周循环</label>
        </div>
        <div class="col-md-14">
            <div class="one-tag selected" ng-repeat="week_day in tour.parts.weekday" ng-click="deleteCloseItem($index, tour_index, 'weekday')" ng-bind="week_day"></div>
        </div>
    </div>
</div>