<div class="states-section" ng-if="local.tab_options.current_tab.path == 'name'">
    <div ng-controller="InfoNameCtrl">
        <hi-section-head options="local.section_head"></hi-section-head>
        <!--编辑态-->
        <div class="section-body clearfix" ng-class="local.section_head.getClass()" ng-show="local.section_head.is_edit">
            <form name="basicinfo_name_form" hi-watch-dirty="local.path_name">
                <table class="forms-table name-table">
                    <tr>
                        <td>
                            <label>商品类型</label>
                        </td>
                        <td>
                            <select chosen required style="width: 200px;" data-placeholder="选择类型"
                                    ng-model="data.info.type"
                                    ng-options="type.value as type.label for type in local.search_list.product_types">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="cn_name">商品名称CH</label>
                        </td>
                        <td>
                            <input type="text" id="cn_name" class="form-control" required ng-model="data.info.cn_name" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="en_name">商品名称EN</label>
                        </td>
                        <td>
                            <input type="text" id="en_name" class="form-control" required ng-model="data.info.en_name" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="cn_origin_name">原供应商商品名称CH</label>
                        </td>
                        <td>
                            <input type="text" id="cn_origin_name" class="form-control" ng-model="data.info.cn_origin_name" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="cn_origin_name">原供应商商品名称EN</label>
                        </td>
                        <td>
                            <input type="text" id="en_origin_name" class="form-control" ng-model="data.info.en_origin_name" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="source_url">供应商商品链接</label>
                        </td>
                        <td>
                            <input type="text" id="source_url" class="form-control" ng-model="data.info.source_url" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="manager_name">商品负责人邮箱</label>
                        </td>
                        <td>
                            <input type="email" id="manager_name" class="form-control" ng-model="data.info.manager_name" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>所属供应商</label>
                        </td>
                        <td>
                            <div ng-show="data.info.supplier_id == 11">
                                <span>GTA</span>
                                <span class="pad-left" ng-bind="data.info.supplier_product_id"></span>
                                <span class="pad-left">导入状态：{{local.import_status[data.info.import.status]}}</span>
                                <button class="btn btn-inverse" ng-click="updateImport()"
                                        ng-show="[2, -1].indexOf(+data.info.import.status)">更新导入
                                </button>
                            </div>
                            <div ng-hide="data.info.supplier_id == 11">
                                <select chosen required ng-model="data.info.supplier_id"
                                        ng-options="supplier.supplier_id as supplier.name group by supplier.group for supplier in local.search_list.suppliers"
                                        data-placeholder="选择供应商"
                                        no-results-text="'没有找到'"
                                    >
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>商品对接专家</label>
                        </td>
                        <td>
                            <div class="expert-chosen">
                                <select chosen required ng-model="data.info.expert_id"
                                        ng-options="expert.id as expert.name for expert in data.experts"
                                        data-placeholder="选择专家"
                                        no-results-text="'没有找到'"
                                    >
                                </select>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <!--显示态-->
        <div class="section-body clearfix" ng-class="local.section_head.getClass()" ng-hide="local.section_head.is_edit">
            <table class="forms-table name-table">
                <tr>
                    <td class="view-title">商品类型</td>
                    <td class="view-body" ng-bind="getLabel(local.search_list.product_types, 'value', data.info.type, 'label')"></td>
                </tr>
                <tr>
                    <td class="view-title">商品名称CH</td>
                    <td class="view-body" ng-bind="data.info.cn_name"></td>
                </tr>
                <tr>
                    <td class="view-title">商品名称EN</td>
                    <td class="view-body" ng-bind="data.info.en_name"></td>
                </tr>
                <tr>
                    <td class="view-title">原供应商商品名称CH</td>
                    <td class="view-body" ng-bind="data.info.cn_origin_name"></td>
                </tr>
                <tr>
                    <td class="view-title">原供应商商品名称EN</td>
                    <td class="view-body" ng-bind="data.info.en_origin_name"></td>
                </tr>
                <tr>
                    <td class="view-title">供应商商品链接</td>
                    <td class="view-body">
                        <a target="_blank" ng-href="{{data.info.source_url}}" ng-bind="data.info.source_url"></a>
                    </td>
                </tr>
                <tr>
                    <td class="view-title">商品负责人邮箱</td>
                    <td class="view-body" ng-bind="data.info.manager_name"></td>
                </tr>
                <tr>
                    <td class="view-title">所属供应商</td>
                    <td class="view-body" ng-bind="getLabel(local.search_list.suppliers, 'supplier_id', data.info.supplier_id, 'name') + '／' + getLabel(local.search_list.suppliers, 'supplier_id', data.info.supplier_id, 'cn_name')"></td>
                </tr>
                <tr>
                    <td class="view-title">商品对接专家</td>
                    <td class="view-body" ng-bind="getLabel(data.experts, 'id', data.info.expert_id, 'name')"></td>
                </tr>
            </table>
        </div>
    </div>
</div>