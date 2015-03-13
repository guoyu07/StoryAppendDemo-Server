<script type="text/ng-template" id="editPriceAttribute.html">
<div class="view-edit-section last clearfix" data-ng-controller="editPriceAttributeCtrl">
<section class="one-section-action">
    <form name="tour_date_form" novalidate>
        <div class="row edit-heading">
            <h2>{{tour_date_title}}</h2>
            <button type="submit" class="btn btn-inverse button-save btn-square" data-ng-click="toggleTour('2')"
                    data-ng-bind="data.edit_label[data.in_edit.tour_date]"
                    data-ng-show="data.in_edit.tour_date == '2'"></button>
            <button type="submit" class="btn btn-inverse button-save btn-square" data-ng-click="toggleTour('1')"
                    data-ng-bind="data.edit_label[data.in_edit.tour_date]"
                    data-ng-show="data.in_edit.tour_date == '1'"></button>
        </div>
        <div class="row edit-body grid-bottom" data-ng-show="data.in_edit.tour_date == '2'">

            <!--need tour date-->
            <div class="row">
                <div class="col-md-6 title-text">客户是否需要填写使用日期？</div>
            </div>
            <div class="row">
                <div class="col-md-12 edit-content last-content">
                    <radio-switch options="radio_options.need_tour_date"
                                  model="data.tour_date.product_date_rule"></radio-switch>
                </div>
            </div>

            <div class="grid-bottom">

                <!--title-->
                <div class="row">
                    <div class="col-md-6 title-text">使用日期的显示名称为：</div>
                </div>
                <div class="row" style="margin-bottom: 12px;">
                    <div class="edit-content clearfix">
                        <label class="col-md-1">
                            中文:
                        </label>
                        <div class="col-md-5">
                            <input class="form-control text-center" type="text"
                                   data-ng-model="data.tour_date.cn_tour_date_title" />
                        </div>
                        <label class="col-md-1">
                            EN:
                        </label>
                        <div class="col-md-5">
                            <input class="form-control text-center" type="text"
                                   data-ng-model="data.tour_date.en_tour_date_title" />
                        </div>
                    </div>
                </div>

                <div>
                    <div class="one-tour-operation" data-ng-repeat="tour in data.tour_date.product_tour_operation">
                        <a class="del-btn text-center" data-ng-click="delTour( $index )">x</a>
                        <div class="input-section price-sale-range">
                            <div class="row" style="padding-left: 120px;">
                                <label class="col-md-4">售卖起止时间</label>
                                <div class="input-group button-select range-or-single col-md-14">
                                    <div class="input-wrapper datepicker-group">
                                        <input type="text" class="form-control datepicker" datepicker-popup="yyyy-MM-dd"
                                               data-ng-model="tour.from_date" is-open="from_opened"
                                               data-ng-click="from_opened = true" close-text="关闭" show-weeks="false"
                                               show-button-bar="false" />
                                        <span class="midline"></span>
                                        <input type="text" class="form-control datepicker" datepicker-popup="yyyy-MM-dd"
                                               data-ng-model="tour.to_date" is-open="to_opened"
                                               data-ng-click="to_opened = true" close-text="关闭" show-weeks="false"
                                               show-button-bar="false" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="input-section">
                            <h4 style="color: #8e8e8e">不可购买日期</h4>
                            <!--<close_any_date model="tour.close_dates" mindate="tour.from_date"-->
                            <!--maxdate="tour.to_date"></close_any_date>-->
                            <div data-ng-include="'close_date.html'"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 title-text">
                            新增一个售卖时间段
                            <button class="btn tagsinput-add" data-ng-click="addTour()"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row edit-body" data-ng-show="data.in_edit.tour_date == '1'">
            <div class="view-section">
                <div class="row grid-bottom" data-ng-show="data.tour_date.product_date_rule.need_tour_date == '0'">
                    客户不需要填写使用日期
                </div>
                <div class="row grid-bottom" data-ng-show="data.tour_date.product_date_rule.need_tour_date == '1'">
                    客户需要填写使用日期
                </div>
                <div class="row">
                    <div class="col-md-3">售卖时间：</div>
                    <div class="col-md-15">
                        <div class="one-tour-operation" data-ng-repeat="tour in data.tour_date.product_tour_operation">
                            <div class="section-head">
                                <label class="text-emphasis">起止时间：</label>
                                {{tour.from_date}} -- {{tour.to_date}}
                            </div>
                            <div class="section-body"
                                 data-ng-show="tour.parts.singleday.length > 0 || tour.parts.weekday.length > 0 || tour.parts.range.length > 0">
                                <div class="text-emphasis">不可售卖时间</div>
                                <div class="row" data-ng-show="tour.parts.singleday.length > 0">
                                    <div class="col-md-3 text-right">单独日期：</div>
                                    <div class="col-md-14 text-emphasis">{{ tour.parts.singleday.join(', ') }}</div>
                                </div>
                                <div class="row" data-ng-show="tour.parts.weekday.length > 0">
                                    <div class="col-md-3 text-right">日期循环：</div>
                                    <div class="col-md-14 text-emphasis">{{ tour.parts.weekday.join(', ') }}</div>
                                </div>
                                <div class="row" data-ng-show="tour.parts.range.length > 0">
                                    <div class="col-md-3 text-right">时间段：</div>
                                    <div class="col-md-14 text-emphasis">{{ tour.parts.range.join(', ') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

<section class="one-section-action">
    <form name="ticket_type_form">
        <div class="row edit-heading">
            <h2>票种设置</h2>
            <button type="submit" class="btn btn-inverse button-save btn-square" data-ng-click="toggleTicket('2')"
                    data-ng-bind="data.edit_label[data.in_edit.ticket_type]"
                    data-ng-show="data.in_edit.ticket_type == '2'"
                    data-ng-disabled="ticket_type_form.$invalid"></button>
            <button type="submit" class="btn btn-inverse button-save btn-square" data-ng-click="toggleTicket('1')"
                    data-ng-bind="data.edit_label[data.in_edit.ticket_type]"
                    data-ng-show="data.in_edit.ticket_type == '1'"></button>
        </div>
        <div class="row edit-body" data-ng-show="data.in_edit.ticket_type == '2'">

            <div class="row">
                <div class="col-md-6 title-text">商品票种选择</div>
            </div>
            <div class="row">
                <div class="col-md-12 edit-content last-content">
                    <radio-switch options="radio_options.ticket_type" model="data.ticket_type"></radio-switch>
                </div>
            </div>

            <div data-ng-if="data.ticket_type.ticket_type == '1'">
                <div class="row">
                    <div class="col-md-6 title-text">填写票种描述</div>
                </div>
                <div class="row">
                    <div class="col-md-10 edit-content last-content">
                        <input class="form-control" type="text"
                               data-ng-model="data.ticket_type.ticket_rules[0].description" />
                    </div>
                </div>
            </div>

            <div data-ng-if="data.ticket_type.ticket_type == '2'">
                <div class="one-ticket-type row" data-ng-repeat="type in data.ticket_type.ticket_rules">
                    <div class="col-md-3">
                        {{local.ticket_type.map[ type.ticket_id ]}}
                    </div>
                    <div class="col-md-6">
                        <label class="col-md-6">年龄范围</label>
                        <div class="col-md-5">
                            <input class="form-control text-center" type="number" data-ng-model="type.age.begin" min="0"
                                   max="100" name="begin{{$index}}" required data-ng-value="type.age.begin" />
                        </div>
                        <div class="col-md-2 mid-line"></div>
                        <div class="col-md-5">
                            <input class="form-control text-center" type="number" data-ng-model="type.age.end" min="0"
                                   max="100" name="end{{$index}}" required data-ng-value="type.age.end" />
                        </div>
                    </div>
                    <div class="col-md-9">
                        <label class="col-md-4 text-right">描述</label>
                        <div class="col-md-14">
                            <input class="form-control" type="text" data-ng-model="type.description" />
                        </div>
                    </div>
                </div>
            </div>

            <div data-ng-if="data.ticket_type.ticket_type == '3'">

                <div class="row grid-bottom">
                    <div class="col-md-6 title-text">
                        新增一个票种
                        <button class="btn tagsinput-add" data-ng-click="addTicket()"></button>
                    </div>
                </div>

                <div class="one-ticket-type row grid-bottom" data-ng-repeat="type in data.ticket_type.ticket_rules">
                    <div class="col-md-3 input-group button-select">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                {{local.ticket_type.map[ type.ticket_id ]}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu pull-right">
                                <li data-ng-repeat="(key, value) in local.ticket_type.map">
                                    <label style="width: 100%;">
                                        <input type="radio" value="{{key}}" data-ng-checked="key == type.ticket_id"
                                               data-ng-model="type.ticket_id" />
                                        {{value}}
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="col-md-6">年龄范围</label>
                        <div class="col-md-5">
                            <input class="form-control text-center" type="number" min="0" max="100"
                                   data-ng-model="type.age.begin" data-ng-value="type.age.begin" required />
                        </div>
                        <div class="col-md-2 mid-line"></div>
                        <div class="col-md-5">
                            <input class="form-control text-center" type="number" min="0" max="100"
                                   data-ng-model="type.age.end" data-ng-value="type.age.end" required />
                        </div>
                    </div>
                    <div class="col-md-7">
                        <label class="col-md-4 text-right">描述</label>
                        <div class="col-md-14">
                            <input class="form-control" type="text" data-ng-model="type.description" />
                        </div>
                    </div>
                    <button class="col-md-2 btn btn-inverse del-btn btn-square" data-ng-click="delTicket( $index )">删除
                    </button>
                </div>
            </div>

        </div>

        <div class="row edit-body" data-ng-show="data.in_edit.ticket_type == '1'">
            <div class="view-section">
                <div class="row">
                    <div class="col-md-8 text-emphasis">
                        商品包含：{{ radio_options.ticket_type.items[data.ticket_type.ticket_type] }} <span
                        data-ng-show="data.ticket_type.ticket_type == 1">{{data.ticket_type.ticket_rules[0].description ? ' ( ' + data.ticket_type.ticket_rules[0].description + ' ) ' : '' }}</span>
                    </div>
                </div>
                <div class="row" data-ng-repeat="type in data.ticket_type.ticket_rules"
                     data-ng-show="data.ticket_type.ticket_type > 1">
                    <div class="col-md-3 text-right">{{local.ticket_type.map[ type.ticket_id ]}}</div>
                    <div class="col-md-15 text-emphasis">
                        {{type.age_range}} {{ type.description ? ' ( ' + type.description + ' ) ' : '' }}
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

<section class="one-section-action">
<form name="special_code_form">
<div class="row edit-heading">
    <h2>{{special_code_title}}</h2>
    <button type="submit" class="btn btn-inverse button-save btn-square" data-ng-click="toggleSpecial('2')"
            data-ng-bind="data.edit_label[data.in_edit.special_code]"
            data-ng-show="data.in_edit.special_code == '2' && product_info.type != '7'"
            data-ng-disabled="data.special_code.need_special_code==1 && special_code_form.$invalid"></button>
    <button type="submit" class="btn btn-inverse button-save btn-square" data-ng-click="toggleSpecial('1')"
            data-ng-bind="data.edit_label[data.in_edit.special_code]"
            data-ng-show="data.in_edit.special_code == '1' && product_info.type != '7'"></button>
</div>
<div class="row edit-body" data-ng-show="data.in_edit.special_code == '2'">

    <div class="row" data-ng-show="!is_GTA">
        <div class="col-md-6 title-text">是否需要Special Code</div>
    </div>
    <div class="row" data-ng-show="!is_GTA">
        <div class="col-md-12 edit-content last-content">
            <radio-switch options="radio_options.need_special_code" model="data.special_code"></radio-switch>
        </div>
    </div>

    <div data-ng-show="data.special_code.need_special_code == '1'">
        <div class="row">
            <div class="col-md-6 title-text">Special Code名称</div>
        </div>
        <div class="row">
            <div class="edit-content clearfix">
                <label class="col-md-2">
                    中文显示：
                </label>
                <div class="col-md-3">
                    <input class="form-control text-center" type="text"
                           data-ng-model="data.special_code.cn_special_title" required />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="edit-content last-content clearfix">
                <label class="col-md-2">
                    英文显示：
                </label>
                <div class="col-md-3">
                    <input class="form-control text-center" type="text"
                           data-ng-model="data.special_code.en_special_title" required />
                </div>
            </div>
        </div>
    </div>

    <div class="row grid-bottom" data-ng-show="data.special_code.need_special_code == '1'">
        <div class="col-md-6 title-text">套餐配置</div>
    </div>

    <div class="multi-square-section" data-ng-show="data.special_code.need_special_code == '1'">
        <div class="section-body padded">
            <div class="row text-center grid-top special-menu">
                <div class="btn-group">
                    <button class="btn btn-primary" ng-click="changeSpecialStatusMenu( item.key )"
                            ng-class="{ 'active': local.special_status == item.key }"
                            ng-repeat="item in local.special_status_menu">
                        <span ng-bind="item.item"></span>
                    </button>
                </div>
            </div>

            <div data-ng-repeat="code in data.special_code.special_codes">
                <div class="clearfix hotel-binding-container" ng-show="product_info.type == '8'">
                    <div class="col-md-3 text-right">
                        <div class="col-md-18">
                            套餐{{$index + 1}} :
                        </div>
                        <div class="col-md-18" data-ng-show="!is_GTA && code.status == '1'">
                            <button class="btn btn-inverse btn-square forbidden-btn" style="width: 100%;"
                                    data-ng-click="toggleSpecialStatus( $index )">禁用
                            </button>
                        </div>
                        <div class="col-md-18" data-ng-show="!is_GTA && code.status == '0'">
                            <button class="btn btn-inverse btn-square forbidden-btn" style="width: 100%;"
                                    data-ng-click="toggleSpecialStatus( $index )">启用
                            </button>
                        </div>
                    </div>
                    <div class="col-md-14">
                        <div class="row title-row">
                            <div class="col-md-9">
                                <label>选择酒店</label>
                            </div>
                            <div class="col-md-9" ng-if="code.bundle_has_special">
                                <label>下属Special Code</label>
                            </div>
                        </div>
                        <div class="row edit-hotel-row">
                            <div class="col-xs-9">
                                <div ng-if="!local.has_bundle">
                                    <label>没有绑定的酒店</label>
                                </div>
                                <div ng-if="local.has_bundle">
                                    <select
                                        ng-model="code.mapping_product_id"
                                        ng-options="value.product_id as value.product_name for (key, value) in data.bundle_hotels"
                                        data-placeholder="选择酒店"
                                        no-results-text="'没有挂接酒店'"
                                        ng-change="changeHotel( $index )"
                                        >
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-9">
                                <div ng-if="code.bundle_has_special">
                                    <select
                                        style="width: 100%;"
                                        ng-model="code.mapping_special_code"
                                        ng-options="value.special_code as value.cn_name group by vendor.group for (key, value) in code.hotel_specials"
                                        data-placeholder="选择Special Code"
                                        no-results-text="'没有Special Code'"
                                        >
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section-row">
                    <div class="col-md-3 text-right">
                        <div class="col-md-18" ng-show="product_info.type != '8'">
                            套餐{{$index + 1}}:
                        </div>
                        <div class="col-md-18" data-ng-show="!is_GTA && code.status == '1' && product_info.type != '8'">
                            <button class="btn btn-inverse btn-square forbidden-btn" style="width: 100%;"
                                    data-ng-click="toggleSpecialStatus( $index )">禁用
                            </button>
                        </div>
                        <div class="col-md-18" data-ng-show="!is_GTA && code.status == '0' && product_info.type != '8'">
                            <button class="btn btn-inverse btn-square forbidden-btn" style="width: 100%;"
                                    data-ng-click="toggleSpecialStatus( $index )">启用
                            </button>
                        </div>
                    </div>
                    <div class="col-md-14">
                        <div class="row half-grid-bottom">
                            <label class="col-md-3">中文名称</label>
                            <div class="col-md-6">
                                <input class="form-control text-center" type="text" required
                                       data-ng-model="code.cn_name" />
                            </div>
                            <label class="col-md-3" for="">英文名称</label>
                            <div class="col-md-6">
                                <input class="form-control text-center" type="text" required
                                       data-ng-model="code.en_name" data-ng-disabled="is_GTA" />
                            </div>
                        </div>
                        <div class="row half-grid-bottom">
                            <label class="col-md-3" for="">描述</label>
                            <div class="col-md-15">
                                <input class="form-control" type="text" data-ng-model="code.description" />
                            </div>
                        </div>
                        <div class="row half-grid-bottom">
                            <label class="col-md-5" for="">供应商原商品名称</label>
                            <div class="col-md-13">
                                <input class="form-control" type="text"
                                       data-ng-model="code.product_origin_name" />
                            </div>
                        </div>
                        <div class="row half-grid-bottom">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-offset-7 col-md-6" data-ng-show="!is_GTA && local.special_status == '1'">
            新增一个套餐
            <button class="btn tagsinput-add" data-ng-click="addSpecial()"></button>
        </div>
    </div>
</div>

<div class="row edit-body" data-ng-show="data.in_edit.special_code == '1'">
    <div class="multi-square-section">
        <div class="section-body padded">
            <div class="row text-center grid-top special-menu">
                <div class="btn-group">
                    <button class="btn btn-primary" ng-click="changeSpecialStatusMenu( item.key )"
                            ng-class="{ 'active': local.special_status == item.key }"
                            ng-repeat="item in local.special_status_menu">
                        <span ng-bind="item.item"></span>
                    </button>
                </div>
            </div>

            <div class="view-section-row" data-ng-repeat="code in data.special_code.special_codes">
                <label class="col-md-2 text-right">套餐{{$index + 1}} :</label>
                <div class="col-md-14 col-md-offset-1">
                    <div ng-if="product_info.type == '8'">
                        <div class="row title-row">
                            <label class="col-md-9">选择酒店</label>
                            <label class="col-md-9" ng-if="code.bundle_has_special">下属Special Code</label>
                        </div>
                        <div class="row info-row">
                            <label class="col-md-9"
                                   ng-bind="data.bundle_hotels[code.mapping_product_id].product_name"></label>
                            <label class="col-md-9" ng-bind="code.hotel_specials[code.mapping_special_code].cn_name"
                                   ng-if="code.bundle_has_special"></label>
                        </div>
                    </div>
                    <div class="row title-row">
                        <label class="col-md-9">中文名称</label>
                        <label class="col-md-9">英文名称</label>
                    </div>
                    <div class="row info-row">
                        <label class="col-md-9" ng-bind="code.cn_name"></label>
                        <label class="col-md-9" ng-bind="code.en_name"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="view-section" data-ng-show="data.special_code.need_special_code == '2'">
        <div class="row grid-bottom">
            <label class="col-md-5 col-md-offset-4">没有Special Code</label>
        </div>
    </div>
</div>
</form>
</section>

</div>
</script>

<script type="text/ng-template" id="close_date.html">
    <div class="row">
        <div class="col-md-4">
            <div class="input-group button-select range-or-single">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default dropdown-toggle"
                            style="border: 1px solid #bdc3c7; width: 168px;" data-toggle="dropdown">
                        {{local.close_ranges[local.current_range]}} <span class="caret"></span></button>
                    <ul class="dropdown-menu pull-right">
                        <li data-ng-repeat='(value, label) in local.close_ranges'>
                            <label style="width: 100%">
                                <input type="radio" value="{{value}}"
                                       data-ng-checked="value == local.current_range"
                                       data-ng-model="local.current_range" />
                                {{label}}
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9" ng-if="local.current_range == 'weekday'">
            <input class="form-control" type="text"
                   data-ng-model="local.added_field.weekday" />
        </div>
        <div class="col-md-9" ng-if="local.current_range == 'singleday'">
            <input type="text" class="form-control datepicker" datepicker-popup="yyyy-MM-dd"
                   min="tour.from_date" max="tour.to_date"
                   data-ng-model="local.added_field.singleday" is-open="from_opened"
                   data-ng-click="from_opened = true" close-text="关闭" show-weeks="false"
                   show-button-bar="false" />
        </div>
        <div class="col-md-11" ng-if="local.current_range == 'range'">
            <div class="row input-wrapper datepicker-group">
                <div class="col-md-8">
                    <input type="text" class="datepicker" style="height: 43px" datepicker-popup="yyyy-MM-dd"
                           min="tour.from_date" max="tour.to_date"
                           data-ng-model="local.added_field.range.from_date" is-open="from_opened"
                           data-ng-click="from_opened = true" close-text="关闭" show-weeks="false"
                           show-button-bar="false" />
                </div>
                <div class="col-md-2" style="text-align: center; line-height: 40px;">
                    <span> —— </span>
                </div>
                <div class="col-md-8">
                    <input type="text" class="datepicker" style="height: 43px" datepicker-popup="yyyy-MM-dd"
                           min="tour.from_date" max="tour.to_date"
                           data-ng-model="local.added_field.range.to_date" is-open="to_opened"
                           data-ng-click="to_opened = true" close-text="关闭" show-weeks="false"
                           show-button-bar="false" />
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <button class="btn btn-inverse" style="width: 100%;"
                    data-ng-click="addCloseItem(tour)">添加
            </button>
        </div>
    </div>

    <div class="row" style="border: 1px solid #bdc3c7; background-color: #f9fafb; margin-top: 20px;">
        <div style="border-bottom: 1px solid #bdc3c7;padding: 10px;">
            <div class="row">
                <div class="col-md-4">
                    <label>时间段</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-18 limitation-contents">
                    <button class="btn one-criteria one-allcriteria close-criteria"
                            style="height: 40px; margin-top: 40px;"
                            data-ng-repeat="range in tour.parts.range"
                            data-ng-click="removeCloseItem($index, tour.parts.range)">
                        {{range}}
                    </button>
                </div>
            </div>
        </div>
        <div style="border-bottom: 1px solid #bdc3c7;padding: 10px;">
            <div class="row">
                <div class="col-md-4">
                    <label>单独固定日期</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-18 limitation-contents">
                    <button class="btn one-criteria one-allcriteria close-criteria close-date-criteria"
                            style="height: 40px; margin-top: 40px;"
                            data-ng-repeat="single_day in tour.parts.singleday"
                            data-ng-click="removeCloseItem($index, tour.parts.singleday)">
                        {{single_day}}
                    </button>
                </div>
            </div>
        </div>
        <div style="padding: 10px;">
            <div class="row">
                <div class="col-md-4">
                    <label>按周循环</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-18 limitation-contents">
                    <button class="btn one-criteria one-allcriteria close-criteria"
                            style="height: 40px; margin-top: 40px;"
                            data-ng-repeat="week_day in tour.parts.weekday"
                            data-ng-click="removeCloseItem($index, tour.parts.weekday)">
                        {{week_day}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</script>