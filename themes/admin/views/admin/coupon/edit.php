<div id="coupon-edit-container" class="page-container container" ng-controller="CouponEditCtrl">
    <div class="states-section">
        <hi-section-head model="local.section_head.info" options="local.section_head.info"></hi-section-head>
        <div class="section-body coupon-info row" ng-class="local.section_head.info.getClass()"
             ng-show="local.section_head.info.is_edit">
            <form name="coupon_info">
                <table class="forms-table col-md-10">
                    <tr>
                        <td><label for="coupon_name">优惠券名称</label></td>
                        <td>
                            <div class="col-md-18">
                                <input type="text" class="form-control" id="coupon_name" ng-model="data.coupon.name"
                                       required />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="coupon_description">优惠券描述</label></td>
                        <td>
                            <div class="col-md-18">
                                <input type="text" class="form-control" id="coupon_description"
                                       ng-model="data.coupon.description" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="coupon_code">优惠券代码</label></td>
                        <td>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="coupon_code" ng-model="data.coupon.code"
                                       required />
                            </div>
                            <div class="col-md-3 col-md-offset-7">
                                <button class="block-action btn btn-inverse pull-right" ng-click="generateCouponCode()">
                                    生成代码
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label>使用类型</label></td>
                        <td>
                            <div class="col-md-18">
                                <hi-radio-switch model="data.coupon"
                                                 options="local.radio_switch.use_type"></hi-radio-switch>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label>生效日期</label></td>
                        <td>
                            <div class="col-md-8">
                                <quick-datepicker ng-model='data.coupon.date_start' disable-timepicker='true'
                                                  date-format='yyyy-M-d'></quick-datepicker>
                            </div>
                            <div class="col-md-1 text-right">
                                －
                            </div>
                            <div class="col-md-8">
                                <quick-datepicker ng-model='data.coupon.date_end' date-filter='afterStart'
                                                  disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="coupon_discount">优惠券折扣</label></td>
                        <td>
                            <div class="col-md-9">
                                <hi-radio-switch model="data.coupon"
                                                 options="local.radio_switch.discount_type"></hi-radio-switch>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control" ng-model="data.coupon.discount" coupon-discount
                                       required />
                            </div>
                            <div class="col-md-1" ng-bind="data.coupon.type == 'F' ? 'RMB' : '％'"></div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="coupon_discount">优惠券状态</label></td>
                        <td>
                            <div class="col-md-9">
                                <hi-radio-switch model="data.coupon"
                                                 options="local.radio_switch.coupon_status"></hi-radio-switch>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="section-body coupon-info row" ng-class="local.section_head.info.getClass()"
             ng-hide="local.section_head.info.is_edit">
            <button class="btn btn-inverse" id="view_history" ng-show="!local.is_new_coupon"
                    ng-click="toggleHistory( true )">
                优惠券历史记录
            </button>
            <table class="forms-table">
                <tr>
                    <td class="view-title">优惠券名称</td>
                    <td class="view-body" ng-bind="data.coupon.name"></td>
                </tr>
                <tr>
                    <td class="view-title">优惠券描述</td>
                    <td class="view-body" ng-bind="data.coupon.description"></td>
                </tr>
                <tr>
                    <td class="view-title">优惠券代码</td>
                    <td class="view-body" ng-bind="data.coupon.code"></td>
                </tr>
                <tr>
                    <td class="view-title">优惠券使用类型</td>
                    <td class="view-body" ng-bind="local.radio_switch.use_type.items[data.coupon.use_type]"></td>
                </tr>
                <tr>
                    <td class="view-title">优惠券折扣</td>
                    <td class="view-body"
                        ng-bind="local.radio_switch.discount_type.items[data.coupon.type] + ' / ' + data.coupon.discount"></td>
                </tr>
                <tr>
                    <td class="view-title">生效日期</td>
                    <td class="view-body" ng-bind="data.coupon.date_start + ' —— ' + data.coupon.date_end"></td>
                </tr>
                <tr>
                    <td class="view-title">优惠券状态</td>
                    <td class="view-body" ng-bind="local.radio_switch.coupon_status.items[data.coupon.status]"></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="states-section" ng-hide="local.is_new_coupon">
        <hi-section-head model="local.section_head.rules" options="local.section_head.rules"></hi-section-head>
        <div class="section-body coupon-rules row" ng-class="local.section_head.rules.getClass()"
             ng-show="local.section_head.rules.is_edit">
            <form name="coupon_rules">
                <div class="section-ruletitle"><b>优惠券类型</b></div>
                <div class="section-rulebody">
                    <div class="row grid-bottom">
                        <span class="small-desc row-align-left col-md-4">优惠券类型</span>
                        <div class="col-md-14">
                            <hi-radio-switch model="data.coupon" options="local.radio_switch.user_limit"></hi-radio-switch>
                        </div>
                    </div>
                    <div class="row grid-bottom">
                        <div class="col-md-8" ng-if="data.coupon.user_limit == '1' && local.is_search_user">
                            <div class="col-md-12">
                                <input ng-model="local.user_search" class="form-control" placeholder="输入用户ID或者账户邮箱" />
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-inverse block-action" ng-click="toggleUserSearch()">查询</button>
                            </div>
                        </div>
                        <div class="col-md-6" ng-show="data.coupon.user_limit == '1' && !local.is_search_user">
                            <p class="small-desc">
                                用户ID为{{data.coupon.customer_id}}，邮箱为{{data.coupon.customer_email}} <br />
                                <a ng-click="toggleUserSearch()">修改</a>
                            </p>
                        </div>
                    </div>
                    <div class="row grid-bottom" ng-show="data.coupon.user_limit == 0">
                        <span class="row-align-left col-md-4">该优惠券最多使用次数</span>
                        <div class="col-md-4">
                            <hi-radio-switch model="data.coupon"
                                             options="local.radio_switch.max_usage_limit"></hi-radio-switch>
                        </div>
                        <div class="col-md-10" ng-if="data.coupon.max_usage_limit == 1">
                            <div class="col-md-3">最多使用</div>
                            <div class="col-md-3">
                                <input type="number" ng-model="data.coupon.uses_total" class="form-control col-md-2"
                                       required min="0" />
                            </div>
                            <div class="col-md-1">次</div>
                        </div>
                    </div>
                    <div class="row grid-bottom">
                        <span class="row-align-left col-md-4">单个用户最多使用次数</span>
                        <div class="col-md-4">
                            <hi-radio-switch model="data.coupon"
                                             options="local.radio_switch.per_user_usage_limit"></hi-radio-switch>
                        </div>
                        <div class="col-md-10" ng-if="data.coupon.per_user_usage_limit == 1">
                            <div class="col-md-3">最多使用</div>
                            <div class="col-md-3">
                                <input type="number" ng-model="data.coupon.uses_customer" class="form-control" required
                                       min="0" />
                            </div>
                            <div class="col-md-1">次</div>
                        </div>
                    </div>
                </div>

                <div class="section-ruletitle"><b>订单金额限制</b></div>
                <div class="section-rulebody">
                    <div class="row grid-bottom">
                        <span class="row-align-left col-md-18">订单金额最少需要多少，可以使用优惠券</span>
                    </div>
                    <div class="row grid-bottom">
                        <div class="col-md-4">
                            <hi-radio-switch model="data.coupon"
                                             options="local.radio_switch.order_total_limit"></hi-radio-switch>
                        </div>
                        <div class="col-md-10" ng-if="data.coupon.order_total_limit == 1">
                            <div class="col-md-3">最少需要</div>
                            <div class="col-md-3">
                                <input type="number" ng-model="data.coupon.total" class="form-control" required min="0" />
                            </div>
                            <div class="col-md-1">RMB</div>
                        </div>
                    </div>
                </div>

                <div class="section-ruletitle"><b>购买人数限制</b></div>
                <div class="section-rulebody">
                    <div class="row grid-bottom">
                        <span class="row-align-left">订单中至少包含几个人时，可以使用</span>
                    </div>
                    <div class="row grid-bottom">
                        <div class="col-md-4">
                            <hi-radio-switch model="data.coupon"
                                             options="local.radio_switch.min_quantity_limit"></hi-radio-switch>
                        </div>
                        <div class="col-md-10" ng-if="data.coupon.min_quantity_limit == 1">
                            <div class="col-md-3">至少包含</div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" ng-model="data.coupon.product_min" min="0" />
                            </div>
                            <div class="col-md-1">人</div>
                        </div>
                    </div>
                    <div class="row grid-bottom">
                        <span class="row-align-left">订单中最多包含几个人时，可以使用</span>
                    </div>
                    <div class="row grid-bottom">
                        <div class="col-md-4">
                            <hi-radio-switch model="data.coupon"
                                             options="local.radio_switch.max_quantity_limit"></hi-radio-switch>
                        </div>
                        <div class="col-md-10" ng-if="data.coupon.max_quantity_limit == 1">
                            <div class="col-md-3">最多包含</div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" ng-model="data.coupon.product_max" min="0" />
                            </div>
                            <div class="col-md-1">人</div>
                        </div>
                    </div>
                </div>

                <div class="section-ruletitle"><b>生效限制</b></div>
                <div class="section-rulebody">
                    <div class="row grid-bottom">
                        <span class="small-desc row-align-left">选择优惠券的生效类型</span>
                    </div>
                    <div class="row grid-bottom">
                        <div class="col-md-6">
                            <hi-radio-switch model="data.coupon"
                                             options="local.radio_switch.product_allowed_limit_v2"></hi-radio-switch>
                        </div>
                    </div>
                    <!-- 商品券 -->
                    <div class="row grid-bottom" ng-if="data.coupon.valid_type == 1">
                        <p class="small-desc row-align-left">
                            <em ng-if="data.coupon.limit_type == 1">可以</em>
                            <em ng-if="data.coupon.limit_type == 0">不可以</em>
                            使用优惠券的商品
                        </p>
                        <div class="col-md-6" ng-if="data.coupon.valid_type != 0">
                            <hi-radio-switch model="data.coupon"
                                             options="local.radio_switch.product_limit_logic"></hi-radio-switch>
                        </div>
                        <div class="col-md-10" ng-if="data.coupon.limit_type == 1">
                            <hi-input-tag options="local.input_tag.can_use" model="data.coupon.limit_ids"></hi-input-tag>
                        </div>
                        <div class="col-md-10" ng-if="data.coupon.limit_type == 0">
                            <hi-input-tag options="local.input_tag.cant_use" model="data.coupon.limit_ids"></hi-input-tag>
                        </div>
                    </div>
                    <!-- 城市券 -->
                    <div class="row grid-bottom" ng-if="data.coupon.valid_type == 2">
                        <p class="small-desc row-align-left">
                            <em ng-if="data.coupon.limit_type == 1">可以</em>
                            <em ng-if="data.coupon.limit_type == 0">不可以</em>
                            使用优惠券的城市
                        </p>
                        <div class="col-md-6" ng-if="data.coupon.valid_type != 0">
                            <hi-radio-switch model="data.coupon"
                                             options="local.radio_switch.product_limit_logic"></hi-radio-switch>
                        </div>
                        <div class="col-md-10" ng-if="data.coupon.limit_type == 1">
                            <hi-select-tag options="local.input_tag.city_can_use" select="data.cities"
                                           model="data.coupon.limit_ids"></hi-select-tag>
                        </div>
                        <div class="col-md-10" ng-if="data.coupon.limit_type == 0">
                            <hi-select-tag options="local.input_tag.city_cant_use" select="data.cities"
                                           model="data.coupon.limit_ids"></hi-select-tag>
                        </div>
                    </div>
                    <!-- 国家券 -->
                    <div class="row grid-bottom" ng-if="data.coupon.valid_type == 3">
                        <p class="small-desc row-align-left">
                            <em ng-if="data.coupon.limit_type == 1">可以</em>
                            <em ng-if="data.coupon.limit_type == 0">不可以</em>
                            使用优惠券的国家
                        </p>
                        <div class="col-md-6" ng-if="data.coupon.valid_type != 0">
                            <hi-radio-switch model="data.coupon"
                                             options="local.radio_switch.product_limit_logic"></hi-radio-switch>
                        </div>
                        <div class="col-md-10" ng-if="data.coupon.limit_type == 1">
                            <hi-select-tag options="local.input_tag.country_can_use" select="data.countries"
                                           model="data.coupon.limit_ids"></hi-select-tag>
                        </div>
                        <div class="col-md-10" ng-if="data.coupon.limit_type == 0">
                            <hi-select-tag options="local.input_tag.country_cant_use" select="data.countries"
                                           model="data.coupon.limit_ids"></hi-select-tag>
                        </div>
                    </div>
                </div>

                <div class="section-ruletitle"><b>登录限制</b></div>
                <div class="section-rulebody">
                    <div class="row grid-bottom">
                        <span class="row-align-left">是否需要登录后才可以使用优惠券</span>
                    </div>
                    <div class="row grid-bottom">
                        <div class="col-md-6">
                            <hi-radio-switch model="data.coupon" options="local.radio_switch.login_limit"></hi-radio-switch>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="section-body coupon-rules row" ng-class="local.section_head.rules.getClass()"
             ng-hide="local.section_head.rules.is_edit">
            <?php include_once(__DIR__ . '/modules/coupon_rules/view_rule.php'); ?>
        </div>
    </div>
    <div class="overlay confirm" ng-show="local.overlay.has_overlay">
        <div class="notify-container confirm">
            <div class="notify-head">优惠券使用历史</div>
            <div class="notify-body">
                <hi-grid options="local.overlay.grid_options"></hi-grid>
            </div>
            <div class="notify-foot">
                <button class="block-action btn btn-inverse" ng-click="toggleHistory( false )">确定</button>
            </div>
        </div>
    </div>
</div>