<div class="states-section" ng-controller="priceTourOperationCtrl">
    <hi-section-head options="local.section_head.tour_date"></hi-section-head>
    <div class="section-body">
        <div class="row edit-body" ng-show="!local.section_head.tour_date.is_edit">
            <div ng-show="data.tour_date.product_date_rule.need_tour_date == '0'">
                客户不需要填写使用日期
            </div>
            <div ng-show="data.tour_date.product_date_rule.need_tour_date == '1'">
                客户需要填写使用日期
            </div>
            <div class="row" ng-show="data.tour_date.product_date_rule.need_tour_date == '1'">
                <div class="section-subtitle">售卖时间：</div>
                <div class="col-md-14 section-subbody">
                    <div class="one-tour-operation clearfix" ng-repeat="tour in data.tour_date.product_tour_operation" ng-init="operationIndex = $index">
                        <div class="section-head">
                            <label class="text-emphasis">起止时间：</label>
                            {{ tour.from_date }} -- {{ tour.to_date }}
                        </div>
                        <div class="content-body" ng-show="tour.parts.singleday.length > 0 || tour.parts.weekday.length > 0 || tour.parts.range.length > 0">
                            <div class="text-emphasis">不可售卖时间</div>
                            <div class="row" ng-show="tour.parts.range.length > 0">
                                <div class="col-md-3 text-right">时间段：</div>
                                <div class="col-md-14 text-emphasis">{{ tour.parts.range.join(', ') }}</div>
                            </div>
                            <div class="row" ng-show="tour.parts.singleday.length > 0">
                                <div class="col-md-3 text-right">单独日期：</div>
                                <div class="col-md-14 text-emphasis">{{ tour.parts.singleday.join(', ') }}</div>
                            </div>
                            <div class="row" ng-show="tour.parts.weekday.length > 0">
                                <div class="col-md-3 text-right">日期循环：</div>
                                <div class="col-md-14 text-emphasis">{{ tour.parts.weekday.join(', ') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row edit-body grid-bottom" ng-show="local.section_head.tour_date.is_edit">
            <form name="tour_date_form" novalidate>
                <div class="row">
                    <div class="col-md-6 section-subtitle">客户是否需要填写使用日期？</div>
                </div>
                <div class="row">
                    <div class="col-md-12 section-subbody">
                        <hi-radio-switch options="local.radio_options.need_tour_date" model="data.tour_date.product_date_rule"></hi-radio-switch>
                    </div>
                </div>

                <div class="grid-bottom" ng-show="data.tour_date.product_date_rule.need_tour_date == '1'">
                    <div class="row">
                        <div class="col-md-6 section-subtitle">使用日期的显示名称为：</div>
                    </div>
                    <div class="row pad-bottom">
                        <div class="section-subbody clearfix">
                            <label class="col-md-2 text-right">
                                中文:
                            </label>
                            <div class="col-md-4">
                                <input class="form-control text-center" type="text" ng-model="data.tour_date.cn_tour_date_title" />
                            </div>
                            <label class="col-md-2 text-right col-md-offset-2">
                                EN:
                            </label>
                            <div class="col-md-4">
                                <input class="form-control text-center" type="text" ng-model="data.tour_date.en_tour_date_title" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="one-tour-operation col-md-15" ng-repeat="tour in data.tour_date.product_tour_operation" ng-init="tour_index = $index">
                            <a class="del-btn text-center" ng-click="deleteTour( $index )">x</a>
                            <div class="input-section price-sale-range">
                                <div class="row">
                                    <label class="col-md-4" style="font-size: 18px;">售卖起止时间</label>
                                    <div class="col-md-13">
                                        <div class="col-md-8">
                                            <quick-datepicker ng-model='tour.from_date' disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
                                        </div>
                                        <div class="col-md-1 text-center">
                                            －
                                        </div>
                                        <div class="col-md-8">
                                            <quick-datepicker ng-model='tour.to_date' disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="input-section">
                                <h4>不可购买日期</h4>
                                <?php include_once(__DIR__ . '/_close_date.php'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row text-center">
                        <button class="btn btn-primary" ng-click="addTour()">
                            新增一个售卖时间段
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>