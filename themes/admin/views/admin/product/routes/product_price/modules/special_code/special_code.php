<div class="special-code-section" ng-if="local.tab_options.current_tab.path == 'special_code'"
     ng-controller="priceSpecialCodeCtrl">

    <div ng-if="!local.is_hotel_plus">
        <hi-section-head options="local.section_head.special_code"></hi-section-head>
    </div>
    <div ng-if="local.is_hotel_plus">
        <div class="section-head">
            <h2 class="section-title">房型名称 (Room Type)</h2>
        </div>
    </div>
    <div class="section-body" ng-class="local.section_head.special_code.getClass()">
        <div class="one-special-code-group" ng-repeat="group in data.groups" data-index="{{ $index }}" hi-dnd
             item="group" callback="local.dnd.callback(info, dst_index)" options="local.dnd.options"
             ng-class="local.status_map[group.status].class">
            <div ng-show="local.section_head.special_code.is_edit">
                <div class="clearfix cn-name">
                    <label class="col-md-2">中文名称</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" ng-model="group.cn_title" />
                    </div>

                    <div class="col-md-4 col-md-offset-6">
                        <div class="one-status btn" ng-repeat="(status_id, status) in local.status_map"
                             ng-click="setGroupStatus($parent.$index, status_id)"
                             ng-class="{ 'btn-default' : status_id != group.status, 'btn-inverse' : status_id == group.status }"
                             ng-bind="status.action_label"></div>
                    </div>
                </div>
                <div class="clearfix pad-bottom">
                    <label class="col-md-2">英文名称</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" ng-model="group.en_title" />
                    </div>

                    <div class="col-md-4 col-md-offset-6" ng-show="!group.group_id">
                        <button class="btn btn-danger" ng-click="deleteGroup($index)">取消添加</button>
                    </div>
                </div>
            </div>
            <div ng-show="!local.section_head.special_code.is_edit">
                <div class="clearfix pad-bottom">
                    <div class="col-md-1 text-center group-index" ng-bind="$index + 1"></div>
                    <div class="col-md-8 group-name" ng-bind="group.cn_title + ' － ' + group.en_title"></div>
                </div>
                <div class="group-status" ng-bind="local.status_map[group.status].label"></div>
            </div>

            <table class="special-codes-table table table-striped">
                <thead>
                    <tr>
                        <th style="width: 10%;">排序</th>
                        <th>中文名称</th>
                        <th>英文名称</th>
                        <th style="width: 18%;" ng-if="local.is_package">酒店</th>
                        <th style="width: 12%;" ng-if="local.is_package">房型</th>
                        <th style="width: 10%;">状态</th>
                        <th style="width: 12%;" ng-show="local.section_head.special_code.is_edit">动作</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="item in group.special_items">
                        <td>
                            <span ng-show="!local.section_head.special_code.is_edit" ng-bind="$index + 1"></span>
                            <input class="form-control" ng-model="item.display_order"
                                   ng-blur="updateItemsOrder($parent.$index, $index)"
                                   ng-show="local.section_head.special_code.is_edit" />
                        </td>
                        <td ng-bind="item.cn_name"></td>
                        <td ng-bind="item.en_name"></td>
                        <td ng-if="local.is_package"
                            ng-bind="local.package.description[item.mapping_product_id].product_name"></td>
                        <td ng-if="local.is_package"
                            ng-bind="local.package.special_code[item.mapping_product_id].special_codes[item.mapping_special_code].cn_name"></td>
                        <td class="item-status" ng-bind="local.status_map[item.status].label"
                            ng-class="local.status_map[item.status].class"></td>
                        <td class="item-action" ng-show="local.section_head.special_code.is_edit">
                            <a ng-click="toggleItemStatus($parent.$index, $index)"
                               ng-bind="local.status_map[item.status].toggle_label"></a> | <a
                                ng-click="editItem($parent.$index, $index)">编辑</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-inverse add-item-btn" ng-click="addItem($index)" ng-show="canAddItem($index)">
                添加Code
            </button>
        </div>

        <button class="btn btn-inverse col-md-offset-7 col-md-4" ng-click="addGroup()" ng-show="canAddGroup()">
            添加Group
        </button>
    </div>

    <div class="overlay confirm" ng-show="local.overlay.has_overlay">
        <div class="notify-container confirm">
            <div class="notify-head">编辑Special Code</div>
            <div class="notify-body">
                <form name="price_special_code_form" hi-watch-dirty="local.path_name">
                    <div class="clearfix" ng-if="local.overlay.item.special_code">
                        <label class="col-md-6 col-md-offset-1" for="item_special_code">Special Code</label>
                        <div class="col-md-10" ng-if="!local.is_cpic" ng-bind="local.overlay.item.special_code"></div>
                        <div class="col-md-10" ng-if="local.is_cpic">
                            <input id="item_special_code" type="text" class="form-control" required
                                   ng-model="local.overlay.item.special_code" />
                        </div>
                    </div>
                    <div class="clearfix">
                        <label class="col-md-6 col-md-offset-1" for="item_cn_name">中文名称</label>
                        <div class="col-md-10">
                            <input id="item_cn_name" type="text" class="form-control"
                                   ng-model="local.overlay.item.cn_name" required />
                        </div>
                    </div>
                    <div class="clearfix">
                        <label class="col-md-6 col-md-offset-1" for="item_en_name">英文名称</label>
                        <div class="col-md-10">
                            <input id="item_en_name" type="text" class="form-control"
                                   ng-model="local.overlay.item.en_name" />
                        </div>
                    </div>
                    <div class="clearfix" ng-if="!local.is_charter">
                        <label class="col-md-6 col-md-offset-1" for="item_product_origin_name">供应商原始名称</label>
                        <div class="col-md-10">
                            <input id="item_product_origin_name" type="text" class="form-control"
                                   ng-model="local.overlay.item.product_origin_name" />
                        </div>
                    </div>
                    <div class="clearfix">
                        <label class="col-md-6 col-md-offset-1" for="item_description">Special Code描述</label>
                        <div class="col-md-10">
                            <textarea id="item_description" class="form-control"
                                      ng-model="local.overlay.item.description"></textarea>
                        </div>
                    </div>
                    <div class="clearfix package" ng-if="local.is_package">
                        <hr />
                        <label class="col-md-3 col-md-offset-1">选择酒店</label>
                        <div class="col-md-7">
                            <select
                                ng-model="local.overlay.item.mapping_product_id"
                                ng-options="value.product_id as value.product_name for (key, value) in local.package.description"
                                data-placeholder="选择酒店"
                                ng-change="onSelectHotel()"
                                >
                            </select>
                        </div>
                        <label class="col-md-3" ng-show="showHotelRoom()">酒店房型</label>
                        <div class="col-md-4" ng-show="showHotelRoom()">
                            <select
                                ng-model="local.overlay.item.mapping_special_code"
                                ng-options="key as value.cn_name for (key, value) in local.package.special_code[local.overlay.item.mapping_product_id].special_codes"
                                data-placeholder="选择Special Code"
                                >
                            </select>
                        </div>
                    </div>
                    <div class="clearfix" ng-if="showCharterPassengerLimit()">
                        <hr />
                        <label class="col-md-6 col-md-offset-1" for="item_description">填写可乘坐人数</label>
                        <div class="col-md-10 pad-bottom">
                            <hi-radio-switch options="local.radio_options.limit_pax_num"
                                             model="local.overlay.item.item_limit"></hi-radio-switch>
                            <div class="row pad-top" ng-if="local.overlay.item.item_limit.limit_pax_num == '1'">
                                <div class="col-md-5">
                                    <input type="number" class="form-control" min="1"
                                           ng-attr-max="{{ local.overlay.item.item_limit.max_pax_num }}" required
                                           ng-model="local.overlay.item.item_limit.min_pax_num" />
                                </div>
                                <div class="col-md-2">人</div>
                                <div class="col-md-2 text-center">
                                    -
                                </div>
                                <div class="col-md-5">
                                    <input type="number" class="form-control" max="20"
                                           ng-attr-min="{{ local.overlay.item.item_limit.min_pax_num }}" required
                                           ng-model="local.overlay.item.item_limit.max_pax_num" />
                                </div>
                                <div class="col-md-2">人</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="notify-foot">
                <button class="block-action btn btn-default" ng-click="toggleEditItem(false)">取消</button>
                <button class="block-action btn btn-inverse" ng-click="toggleEditItem(false, true)">确定</button>
            </div>
        </div>
    </div>
</div>