<script type="text/ng-template" id="editDeparturePoint.html">
    <div class="view-edit-section clearfix" data-ng-controller="editDeparturePointCtrl">
        <div data-ng-include="'editLimitationsInfo.html'"></div>
    </div>
</script>

<script type="text/ng-template" id="editLimitationsInfo.html">
    <input id="excel-upload" type="file" class="hidden" data-ng-file-select />
    <section class="one-section-action ">
        <div class="row edit-heading">
            <div class="col-md-15">
                <h2>{{title_str}}</h2>
            </div>
            <button class="col-xs-3 btn btn-sharp"
                    data-ng-click="editDeparturePlans()"
                    data-ng-show="departure_limits_editing == false" ng-disabled="departure_point_list == {}">
                编辑
            </button>
            <button type="submit" class="col-xs-3 btn btn-sharp"
                    data-ng-click="submitDeparturePlanChanges()"
                    data-ng-show="departure_limits_editing == true">
                保存
            </button>
        </div>
    </section>

    <section class="one-section-action">
        <div class="row edit-body departure-point-Info"
             data-ng-show="departure_limits_editing == false">
            <div data-ng-show="departure_status.needed == 1">
                <h4 class="departure-detail" ng-show="valid_region.valid_region == '1'">
                    此计划应用于&nbsp;自定义区间
                </h4>

                <div data-ng-repeat="plan in planList">
                    <div data-ng-include="'effectsDuration.html'"></div>
                </div>
            </div>

            <div data-ng-show="departure_status.needed == '0'">
                <h4 class="departure-detail">
                    此商品不需要Departure Point.
                </h4>
            </div>
        </div>
    </section>
    <section class="one-section-action">
        <form name="departure_limits_form" novalidate>
            <div class="row edit-body" data-ng-hide="departure_limits_editing == false">
                <div class="row">
                    <div class="col-md-7 title-text">客户是否需要填写Departure Point</div>
                </div>
                <div class="row">
                    <div class="col-md-12 edit-content last-content">
                        <radio-switch options="radio_options.need_departure"
                                      model="departure_status"></radio-switch>
                    </div>
                </div>

                <div data-ng-show="departure_status.needed == 1">
                    <div class="row">
                        <div class="col-md-7 title-text">显示名称</div>
                    </div>
                    <div class="row">
                        <div class="edit-content">
                            <label class="col-md-2">中文：</label>
                            <div class="col-md-4 input_margin">
                                <input type="text" class="form-control" data-ng-model="titles.title_zh" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="edit-content last-content clearfix">
                            <label class="col-md-2">英文：</label>
                            <div class="col-md-4 input_margin">
                                <input type="text" class="form-control" data-ng-model="titles.title_en" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7 title-text">生效区间设置</div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 edit-content last-content">
                            <radio-switch options="radio_options.effects_duration"
                                          model="valid_region"></radio-switch>
                        </div>
                    </div>

                    <div class="row" data-ng-hide="valid_region.valid_region == 0">
                        <div class="col-md-7 title-text">增加生效区间
                            <button class="btn btn-sharp tagsinput-add col-md-offset-1"
                                    data-ng-click="addDeparturePlan('1')"></button>
                        </div>
                    </div>

                    <div data-ng-repeat="departurePlan in planList">
                        <div class="square-input-section" style="float: left; width: 100%">
                            <a class="del-btn text-center" data-ng-show="valid_region.valid_region == '1'"
                               data-ng-click="deleteOneDuration($index)">x</a>
                            <div class="section-head" data-ng-show="valid_region.valid_region == '1'">
                                <div class="col-md-14 col-md-offset-2">
                                    <div class="input-group button-select range-or-single col-md-11">
                                        <div class="input-wrapper datepicker-group">
                                            <input type="text" class="form-control datepicker"
                                                   datepicker-popup="yyyy-MM-dd"
                                                   data-ng-model="departurePlan.from_date" is-open="from_opened"
                                                   data-ng-click="from_opened = true" close-text="关闭" show-weeks="false"
                                                   show-button-bar="false" min="local.min_date" max="local.max_date" />
                                            <span class="midline"></span>
                                            <input type="text" class="form-control datepicker"
                                                   datepicker-popup="yyyy-MM-dd"
                                                   data-ng-model="departurePlan.to_date" is-open="to_opened"
                                                   data-ng-click="to_opened = true" close-text="关闭" show-weeks="false"
                                                   show-button-bar="false" min="local.min_date" max="local.max_date" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-body" style="overflow: visible;">
                                <div class="section-row">
                                    <div class="row half-grid-bottom">
                                        <div class="col-md-7 col-md-offset-1 title-text last-content">
                                            添加地点及约束条件
                                            <button class="btn btn-sharp tagsinput-add"
                                                    data-ng-click="addDeparturePlanItem(departurePlan)"></button>
                                        </div>
                                        <button class="btn btn-darkflat col-md-offset-5"
                                                data-ng-click="triggerUpload(departurePlan)"> 导入Departure Points
                                        </button>
                                    </div>
                                </div>

                                <div class="one-plan" data-ng-repeat="onePlan in departurePlan.plans">
                                    <div class="section-row">
                                        <div class="col-md-17 col-md-offset-1">
                                            <div class="row half-grid-bottom">
                                                <label class="col-md-2 text-right">中文地点：</label>
                                                <div class="col-md-5">
                                                    <input class="form-control" type="text"
                                                           data-ng-model="onePlan.departures[0].departure_point"
                                                           required />
                                                </div>
                                                <label class="col-md-2 text-right">英文地点：</label>
                                                <div class="col-md-5">
                                                    <input class="form-control" type="text"
                                                           data-ng-model="onePlan.departures[1].departure_point"
                                                           required />
                                                </div>
                                                <div class="col-md-2 col-md-offset-1">
                                                    <button class="btn btn-inverse btn-square delete-row"
                                                            style="width: 100%;"
                                                            data-ng-click="deleteDeparturePlanItem(departurePlan, onePlan)">
                                                        删除
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row half-grid-bottom">
                                                <label class="col-md-1" style="height: 120px; line-height: 120px;">时间:</label>
                                                <div class="col-md-2">
                                                    <timepicker ng-model="onePlan.time" class="time-picker"
                                                                hour-step="time_picker_rule.hstep" minute-step="time_picker_rule.mstep"
                                                                show-meridian="time_picker_rule.ismeridian"></timepicker>
                                                </div>

                                                <label class="col-md-2 text-right" style="height: 120px; line-height: 120px; margin-left: 30px;">约束条件：</label>

                                                <div class="col-md-12 limitation-contents">
                                                    <button class="btn one-criteria one-allcriteria"
                                                            style="height: 40px; margin-top: 40px;"
                                                            data-ng-repeat="day in local.weekday"
                                                            data-ng-click="toggleItem(day.key, onePlan.additional_limit)"
                                                            data-ng-class="{ checked: onePlan.additional_limit.indexOf(day.key) == -1 }">
                                                        {{day.name}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>

</script>

<script type="text/ng-template" id="effectsDuration.html">
    <div class="clearfix one_limits_duration_container col-xs-16"
         data-ng-hide="departure_limits_editing == true">
        <div class="titleContainer">
            <div class="col-xs-3 durationRule">
                生效区间
            </div>
            <div ng-show="valid_region.valid_region==1">
                <div class="col-xs-4 date">{{plan.from_date}}</div>
                <div class="col-xs-2"> ——</div>
                <div class="col-xs-4 date">{{plan.to_date}}</div>
            </div>
            <div ng-show="valid_region.valid_region==0">
                <div class="col-xs-10">整个区间</div>
            </div>
        </div>
        <div class="contentContainer">
            <div class="col-xs-18 constrantRule">
                约束条件
            </div>
            <div data-ng-repeat="detail in plan.plans">
                <div class="col-xs-18 ruleItem">
                    &bull;&nbsp;&nbsp; {{detail.departures[0].departure_point.toString()}},&nbsp;&nbsp;{{getTimeFromDate(detail.time)}}&nbsp;&nbsp;
                    <span data-ng-show="detail.additional_limit.toString().length > 0">
                        ({{detail.additional_limit.toString()}})
                    </span>
                </div>
            </div>
        </div>
    </div>
</script>


