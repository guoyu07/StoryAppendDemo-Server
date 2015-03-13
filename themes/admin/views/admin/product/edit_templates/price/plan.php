<script type="text/ng-template" id="editPricePlan.html">
    <div class="view-edit-section clearfix" data-ng-controller="editPricePlanCtrl">
        <div data-ng-include="'editPricePlanEdit.html'" data-ng-show="local.this_plan_index > -1"></div>
        <div data-ng-include="'editPricePlanList.html'"></div>
    </div>
</script>

<script type="text/ng-template" id="editPricePlanList.html">
    <section class="one-section-action">
        <div class="row edit-heading">
            <h2 data-ng-show = "local.this_plan_index == -1">{{local.price_plan_name}}</h2>
            <h2 data-ng-show = "local.this_plan_index > -1">其他计划</h2>
        </div>
        <div class="row edit-body">
            <h2 class="text-center grid-bottom" data-ng-show="!(data.length==1 && data[0].valid_region==0) && local.this_plan_index == -1">
                <button class="btn tagsinput-add" data-ng-click="addPlan()"></button>
                添加一个{{local.price_plan_name}}
            </h2>

            <div data-ng-repeat="plan in data">
                <table class="table table-hover pricing-table grid-bottom" data-ng-hide = "local.this_plan_index == $index">
                    <thead>
                        <tr>
                            <th colspan="{{ plan.colspan }}" class="text-center">
                                <span data-ng-show="plan.out_of_date == 1" style="color: red">{{local.price_plan_name}}{{ $index + 1 }} &nbsp;&nbsp; {{ getDisplayDate($index) }}</span>
                                <span data-ng-hide="plan.out_of_date == 1">{{local.price_plan_name}}{{ $index + 1 }} &nbsp;&nbsp; {{ getDisplayDate($index) }}</span>
                                <br />
                <span data-ng-if="local.is_special_price"><br />渠道：{{ plan.reseller }} 口号：{{ plan.slogan }}
                          </span>
                                <button class="btn btn-square btn-inverse"
                                        style="height: 20px; padding: 0 8px; float: right;"
                                        data-ng-click="editPlan( $index )"
                                        ng-disabled = "local.this_plan_index > -1">编辑
                                </button>
                                <button class="btn btn-square btn-inverse"
                                        style="margin-right: 12px; height: 20px; padding: 0 8px; float: right;"
                                        data-ng-click="deletePlan( $index )"
                                        ng-disabled = "local.this_plan_index > -1">删除
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 10%;">票种</th>
                            <th style="width: 10%;" data-ng-show="plan.need_tier_pricing == '1'">人数</th>
                            <th style="width: 10%;">售卖价</th>
                            <th style="width: 10%;">成本价</th>
                            <th style="width: 10%;">门市价</th>
                            <th style="width: 20%;" data-ng-show="local.has_special">Special Code</th>
                            <th data-ng-show="local.has_special">约束条件</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-ng-repeat="item in plan.items">
                            <td>{{ getTicketName(item.ticket_id) }}</td>
                            <td data-ng-show="plan.need_tier_pricing == '1'">{{ item.quantity }}</td>
                            <td>{{ item.price }}</td>
                            <td>{{ item.cost_price }}</td>
                            <td>{{ item.orig_price }}</td>
                            <td data-ng-if="local.has_special && $index % plan.row_span == 0 " rowspan="{{plan.row_span}}"
                                style="border-left: 1px solid #ddd;">{{ getCodeName(item.special_code) }}
                            </td>
                            <td data-ng-if="local.has_special && $index % plan.row_span == 0 " rowspan="{{plan.row_span}}"
                                style="border-left: 1px solid #ddd;">{{ getFrequencyLabel(item.frequency) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</script>

<script type="text/ng-template" id="editPricePlanEdit.html">
    <section class="one-section-action">
        <div class="row">
            <div class="col-md-16">
                <h3 style="margin: 0px;">{{local.price_plan_name}}{{local.this_plan_index + 1}} &nbsp;
          <span>
            {{formatDate( local.this_plan.from_date )}} - {{formatDate( local.this_plan.to_date )}}
          </span>
          <span data-ng-if="local.is_special_price">渠道：{{ plan.reseller }} 口号：{{ plan.slogan }}
          </span>
                </h3>
            </div>
            <button class="col-md-2 btn btn-inverse btn-square" data-ng-click="updatePlan()">保存</button>
        </div>
        <div class="row edit-heading">
            <h2>此价格是否应用于整个生效区域？</h2>
        </div>
        <div class="row edit-body">

            <div>
                <radio-switch options="radio_options.valid_region" model="local.this_plan"></radio-switch>
            </div>

            <div class="row input-group button-select" data-ng-show="local.this_plan.valid_region == '1'">
                <div class="col-md-5" style="line-height: 20px;">此价格应用的生效时间为</div>
                <div class="col-md-4">
                    <input type="text" class="form-control datepicker" datepicker-popup="yyyy-MM-dd"
                           min="min_date" max="max_date"
                           data-ng-model="local.this_plan.from_date" is-open="some_picker_opened_1"
                           data-ng-click="some_picker_opened_1 = true" close-text="关闭" show-weeks="false"
                           show-button-bar="false" />
                </div>
                <div class="col-md-2 mid-line"></div>
                <div class="col-md-4">
                    <input type="text" class="form-control datepicker" datepicker-popup="yyyy-MM-dd"
                           min="min_date" max="max_date"
                           data-ng-model="local.this_plan.to_date" is-open="some_picker_opened_2"
                           data-ng-click="some_picker_opened_2 = true" close-text="关闭" show-weeks="false"
                           show-button-bar="false" />
                </div>
            </div>

            <div class="row" data-ng-if="local.is_special_price">
                <div class="col-md-4">渠道名称</div>
                <div class="col-md-5">
                    <input data-ng-model="local.this_plan.reseller" type="text" class="form-control" />
                </div>
                <div class="col-md-4">口号</div>
                <div class="col-md-5">
                    <input data-ng-model="local.this_plan.slogan" type="text" class="form-control" />
                </div>
            </div>
        </div>

        <div data-ng-show="local.product_info.special_codes.length > 0">
            <div class="row edit-heading">
                <h2>选择此计划中包含的Special Code</h2>
            </div>
            <div class="row edit-body">
                <div class="row">
                    <label class="one-special-code" data-ng-repeat="code in local.product_info.special_codes">
                        <input type="checkbox" data-ng-model="local.this_plan.special_codes[ code.special_code ]"
                               value="{{code.special_code}}"/> {{code.cn_name}} / {{code.en_name}}
                    </label>
                </div>
            </div>
        </div>

        <div class="row edit-heading">
            <h2>是否需要设置阶梯价格</h2>
        </div>
        <div class="row edit-body">
            <radio-switch options="radio_options.need_tier_pricing" model="local.this_plan"></radio-switch>

            <table class="table table-hover pricing-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">票种</th>
                        <th style="width: 10%;" data-ng-show="local.this_plan.need_tier_pricing == '1'">人数</th>
                        <th style="width: 10%;">售卖价</th>
                        <th style="width: 10%;">成本价</th>
                        <th style="width: 10%;">门市价</th>
                        <th style="width: 20%;" data-ng-show="local.has_special">Special Code</th>
                        <th data-ng-show="local.has_special">约束条件</th>
                    </tr>
                </thead>
                <tbody>
                    <tr data-ng-repeat="item in local.this_plan.items">
                        <td>{{ getTicketName(item.ticket_id) }}</td>
                        <td data-ng-show="local.this_plan.need_tier_pricing == '1'">{{item.quantity}}</td>
                        <td><input type="text" class="form-control" data-ng-value="item.price"
                                   data-ng-model="item.price" /></td>
                        <td><input type="text" class="form-control" data-ng-value="item.cost_price"
                                   data-ng-model="item.cost_price" /></td>
                        <td><input type="text" class="form-control" data-ng-value="item.orig_price"
                                   data-ng-model="item.orig_price" /></td>
                        <td data-ng-if="local.has_special && $index % local.this_plan.row_span == 0 "
                            rowspan="{{local.this_plan.row_span}}" style="border-left: 1px solid #ddd;">{{
                                                                                                        getCodeName(item.special_code)
                                                                                                        }}
                        </td>
                        <td data-ng-if="local.has_special && $index % local.this_plan.row_span == 0 "
                            rowspan="{{local.this_plan.row_span}}" style="border-left: 1px solid #ddd;">
                            <select chosen multiple
                                    style="min-width: 200px;"
                                    ng-model="local.this_plan.special_code_frequency[item.special_code]"
                                    ng-options="value as label for (value, label) in local.weekdays"
                                    data-placeholder="全部为周一至周日">
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</script>