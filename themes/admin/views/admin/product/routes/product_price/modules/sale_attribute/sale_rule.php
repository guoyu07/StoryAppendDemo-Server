<div class="states-section" ng-controller="priceSaleRuleCtrl" ng-if="!local.has_default_tickets">
    <hi-section-head options="local.section_head.sale_pattern"></hi-section-head>
    <!--展示态-->
    <div class="section-body clearfix" ng-class="local.section_head.sale_pattern.getClass()" ng-hide="local.section_head.sale_pattern.is_edit">
        <p class="text-emphasis grid-bottom">
            此商品<span ng-show="data.sale_rule.sale_in_package == 0">不</span>按套售卖
        </p>

        <p class="grid-bottom" ng-bind="local.label.summary"></p>

        <h4>
            此票{{ data.sale_rule.min_num }}{{ local.label.unit }}起定,
            <span ng-show="data.sale_rule.sale_in_package == 0 && data.ticket_rule[child_index].is_independent == 0">最少包含{{ data.ticket_rule[adult_index].min_num }}个成人,</span>
            最多一次性购买{{ data.sale_rule.max_num }}{{ local.label.unit }}
        </h4>
    </div>
    <!--编辑态-->
    <div class="section-body clearfix" ng-class="local.section_head.sale_pattern.getClass()" ng-show="local.section_head.sale_pattern.is_edit">
        <form name="sale_pattern_form">
            <div class="row grid-bottom">
                <div class="col-md-6">此商品是否按套出售？</div>
                <div class="col-md-12">
                    <hi-radio-switch options="local.radio_options.is_packaged" model="data.sale_rule"></hi-radio-switch>
                </div>
            </div>

            <div ng-if="data.sale_rule.sale_in_package == 1">
                <div class="row">
                    <div class="col-md-8 title-text">您需要定义每套商品包含下列票种的数量</div>
                </div>

                <div class="row" ng-repeat="ticket in data.package_rule">
                    <label class="col-md-3">
                        包含
                        <span class="text-emphasis">{{ticket.ticket_type.cn_name}}票</span>
                    </label>
                    <div class="col-md-2">
                        <input type="text" class="form-control" ng-model="ticket.quantity" />
                    </div>
                    <label class="col-md-10">张</label>
                </div>

                <div class="row">
                    <label class="col-md-3">起定套数为</label>
                    <div class="col-md-2">
                        <input type="text" class="form-control" ng-model="data.sale_rule.min_num" />
                    </div>
                    <label class="col-md-3">最大购买套数</label>
                    <div class="col-md-2">
                        <input type="text" class="form-control" ng-model="data.sale_rule.max_num" />
                    </div>
                </div>
            </div>

            <div ng-if="data.sale_rule.sale_in_package == 0">
                <div class="row grid-bottom" ng-show="data.ticket_type == 2">
                    <div class="col-md-6">是否可以单独售卖成人票？</div>
                    <div class="col-md-12">
                        <hi-radio-switch options="local.radio_options.adult_only" model="data.ticket_rule[adult_index]"></hi-radio-switch>
                    </div>
                    <div class="col-md-6">是否可以单独售卖儿童票？</div>
                    <div class="col-md-12">
                        <hi-radio-switch options="local.radio_options.child_only" model="data.ticket_rule[child_index]"></hi-radio-switch>
                    </div>
                </div>

                <div class="grid-bottom" ng-if="data.ticket_type == 3">
                    <p>请勾选下列可单独购买的票种</p>
                    <label class="hi-checkbox" ng-repeat="ticket in data.ticket_rule">
                        <input type="checkbox" ng-checked="ticket.is_independent == '1'" ng-click="toggleIndependent($index)" />
                        <span class="inner-text">可单独购买&nbsp;&nbsp;{{ticket.ticket_type.cn_name}}票</span>
                    </label>
                </div>

                <div class="row grid-bottom">
                    <label class="col-md-2">起定人数</label>
                    <div class="col-md-2">
                        <input type="text" class="form-control" ng-model="data.sale_rule.min_num" />
                    </div>
                    <label class="col-md-3">最大购买人数</label>
                    <div class="col-md-2">
                        <input type="text" class="form-control" ng-model="data.sale_rule.max_num" />
                    </div>
                </div>
                <div class="row grid-bottom" ng-show="data.ticket_rule[child_index].is_independent == 0">
                    <label class="col-md-2">最少包含</label>
                    <div class="col-md-2">
                        <input type="text" class="form-control" ng-model="data.ticket_rule[adult_index].min_num" />
                    </div>
                    <label class="col-md-2">个成人</label>
                </div>
                <div class="row grid-bottom" ng-show="data.ticket_rule[adult_index].is_independent == 0">
                    <label class="col-md-2">最少包含</label>
                    <div class="col-md-2">
                        <input type="text" class="form-control" ng-model="data.ticket_rule[child_index].min_num" />
                    </div>
                    <label class="col-md-2">个儿童</label>
                </div>
            </div>
        </form>
    </div>
</div>