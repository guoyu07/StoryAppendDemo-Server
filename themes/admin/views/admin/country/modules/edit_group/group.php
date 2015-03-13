<div class="states-section country-group" ng-show="local.current_menu == '2'">
    <button class="btn btn-inverse add-btn" ng-click="addGroup()">添加分组</button>
    <div class="section-head">
        <h2 class="section-title">分组管理</h2>
    </div>
    <div class="section-body" id="citygroup_watershed">
        <div class="section-subbody row">
            <div class="citygroups-list-container col-md-6">
                <div class="one-block" ng-repeat="group in data.all_group" ng-click="setCurrentGroup( $index )"
                     item="group" data-index="{{ $index }}" ng-init="group_index = $index"
                     ng-class="{ 'selected' : local.current_group_i == $index }">
                    <div class="delete-block" ng-click="deleteGroup( $index )">
                        <span class="i i-close"></span>
                    </div>
                    <div class="ordering" ng-bind="group.group_id"></div>
                    <div class="group-name" ng-bind="group.name"></div>
                    <div>
                        <span class="group-type">[{{local.group_switch.options.items[group.type]}}]</span>
                        <div class="small-desc">
                            此分组含<span ng-bind="group.refs.length"></span>个元素
                        </div>
                    </div>
                    <div class="btn-group all-status">
                        <button class="btn dropdown-toggle" data-toggle="dropdown"
                                ng-class="{ 'btn-default' : group.status == '1', 'btn-inverse' : group.status == '2' }"
                                ng-bind="local.group_status[ group.status ].label">
                            <span class="caret"></span>
                        </button>
                        <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                        <ul class="dropdown-menu dropdown-inverse drop-menu">
                            <li ng-repeat="(status_id, status) in local.group_status">
                                <a ng-click="updateGroupState( group_index, status_id )" ng-bind="status.label"></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="citygroups-edit-container col-md-12" ng-show="local.current_group_i > -1">
                <div class="one-block">
                    <span class="col-md-4 type-text">分组内条目类型:</span>
                    <span class="col-md-4 type-text" ng-show="data.all_group[local.current_group_i].type_set">{{local.group_switch.options.items[data.all_group[local.current_group_i].type]}}</span>
                    <div class="col-md-12 type-label" ng-hide="data.all_group[local.current_group_i].type_set">
                        <div hi-radio-switch options="local.group_switch.options" model="local.group_switch.value"></div>
                        <button class="btn btn-inverse type-btn" ng-click="saveGroupType()" ng-hide="data.all_group[local.current_group_i].type_set && local.group_switch.value < 1">保存</button>
                    </div>
                </div>
                <div class="citygroup-edit-info one-block grid-bottom" ng-show="data.all_group[local.current_group_i].type_set">
                    <form name="group_form">
                        <div class="row" ng-hide="data.all_group[local.current_group_i].type == '4'">
                            <div class="citygroup-cover col-md-5">
                                <div hi-uploader options="local.uploader_options.group_cover"></div>
                                (图片大小:200*200)
                            </div>
                            <div class="col-md-13">
                                <div class="row">
                                    <table class="forms-table">
                                        <tr>
                                            <td class="col-md-4">
                                                分组名称:
                                            </td>
                                            <td>
                                                <input type="text" ng-model="data.all_group[local.current_group_i].name"
                                                       class="form-control disabled-text" required
                                                       ng-disabled="local.edit_group == false" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-4">
                                                <div class="group-summary">短描述:</div>
                                            </td>
                                            <td>
                                                <textarea ng-model="data.all_group[local.current_group_i].summary" maxlength="20"
                                                          class="form-control grid-top disabled-text" required rows="1"
                                                          placeholder="用于分组的提示性文字(20字以内)"
                                                          ng-disabled="local.edit_group == false"></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-4">
                                                长描述:
                                            </td>
                                            <td>
                                                <textarea ng-model="data.all_group[local.current_group_i].description"
                                                          class="form-control grid-top disabled-text" required rows="2"
                                                          placeholder="分组的详细介绍"
                                                          ng-disabled="local.edit_group == false"></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-4">
                                                关联城市:{{data.all_group[local.current_group_i].city_code}}
                                            </td>
                                            <td>
                                                <div class="city-chosen-name" ng-show="local.edit_group == false" ng-bind="getCityName(data.all_group[local.current_group_i].city_code) || '未关联'"></div>
                                                <div class="city-chosen-tag" ng-hide="local.edit_group == false">
                                                    <select
                                                        chosen
                                                        style="width: 150px;"
                                                        ng-model="data.all_group[local.current_group_i].city_code"
                                                        ng-options="city.city_code as (city.cn_name) group by city.group for city in data.cities"
                                                        data-placeholder="点击选择城市"
                                                        no-results-text="'没有找到'"
                                                        >
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                                <span class="i block-btn"
                                      ng-class="{ 'i-edit': local.edit_group == false, 'i-save': local.edit_group == true }"
                                      ng-click="toggleGroupEdit()"
                                      ng-disabled="group_form.$invalid && local.edit_group == true"></span>
                        </div>
                        <div class="row" ng-show="data.all_group[local.current_group_i].type == '4'">
                            <div class="col-md-18">
                                <div class="row">
                                    <table class="forms-table">
                                        <tr>
                                            <td class="col-md-3">
                                                分组名称:
                                            </td>
                                            <td>
                                                <input type="text" ng-model="data.all_group[local.current_group_i].name"
                                                       class="form-control disabled-text" required
                                                       ng-disabled="local.edit_group == false" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-3">
                                                <div class="group-summary">短描述:</div>
                                            </td>
                                            <td>
                                                <textarea ng-model="data.all_group[local.current_group_i].summary" maxlength="20"
                                                          class="form-control grid-top disabled-text" required rows="1"
                                                          placeholder="用于分组的提示性文字(20字以内)"
                                                          ng-disabled="local.edit_group == false"></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-3">
                                                长描述:
                                            </td>
                                            <td>
                                                <textarea ng-model="data.all_group[local.current_group_i].description"
                                                          class="form-control grid-top disabled-text" required rows="2"
                                                          placeholder="分组的详细介绍"
                                                          ng-disabled="local.edit_group == false"></textarea>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                                <span class="i block-btn"
                                      ng-class="{ 'i-edit': local.edit_group == false, 'i-save': local.edit_group == true }"
                                      ng-click="toggleGroupEdit()"
                                      ng-disabled="group_form.$invalid && local.edit_group == true"></span>
                        </div>
                    </form>
                </div>
                <div ng-show="data.all_group[local.current_group_i].type == '1' && data.all_group[local.current_group_i].type_set">
                    <div class="grid-bottom clearfix">
                        <div class="col-md-6 col-md-offset-5">
                            <input type="text" class="form-control" ng-model="local.search_text" placeholder="输入商品ID" />
                        </div>
                        <div class="col-md-1">
                            <button class="block-action add btn btn-inverse"
                                    ng-click="addProductToGroup( local.search_text )">
                                添加
                            </button>
                        </div>
                    </div>
                    <div class="col-md-18 one-block side-block product-block">
                        <div class="item-list-container ingroup">
                            <h4 class="list-title">分组内商品</h4>
                            <ul class="item-list">
                                <li ng-repeat="product in data.all_group[local.current_group_i].refs_order">
                                    <input class="item-order form-control" ng-model="product.display_order"
                                           ng-value="{{ $index }} + 1" ng-blur="updateProductGroupOrder(product)" />
                                    <span class="item-name" ng-bind="product.id + ' - ' + product.product_name"></span>
                                    <a href="" ng-click="deleteProductFromGroup( product.id )"><span class="i i-close"></span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div ng-show="data.all_group[local.current_group_i].type == '2' && data.all_group[local.current_group_i].type_set">
                    <div class="grid-bottom clearfix">
                        <div class="col-md-6 col-md-offset-5">
                            <input type="text" class="form-control" ng-model="local.search_text" placeholder="输入线路ID" />
                        </div>
                        <div class="col-md-1">
                            <button class="block-action add btn btn-inverse"
                                    ng-click="addLineToGroup( local.search_text )">
                                添加
                            </button>
                        </div>
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10%;">排序</th>
                                <th style="width: 8%;">商品ID</th>
                                <th style="width: 40%;">商品名称</th>
                                <th style="width: 30%;">商品图片(340*290)</th>
                                <th style="width: 12%;">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="line in data.all_group[local.current_group_i].refs_order">
                                <td>
                                    <input class="item-order form-control" ng-model="line.display_order"
                                           ng-value="{{ $index }} + 1" ng-blur="updateLineGroupOrder(line)" />
                                </td>
                                <td>
                                    <span ng-bind="line.id"></span>
                                </td>
                                <td>
                                    <span ng-bind="line.product_name"></span>
                                    <textarea class="form-control" ng-model="line.name" placeholder="商品展示名称"
                                              ng-blur="updateLineInfo( line )"></textarea>
                                </td>
                                <td ng-if="data.all_group[local.current_group_i].type == '2'">
                                    <div hi-uploader options="line.uploader"></div>
                                </td>
                                <td class="article-button">
                                    <button class="block-action add btn btn-inverse"
                                            ng-click="deleteLineFromGroup( line.id )">
                                        删除
                                    </button>
                                    <div class="article-status all-status">
                                        <button class="btn article-status-btn" data-toggle="dropdown"
                                                ng-class="{ 'btn-default' : line.status == '1', 'btn-inverse' : line.status == '2' }"
                                                ng-bind="local.group_status[line.status].label">
                                            <span class="caret"></span>
                                        </button>
                                        <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                                        <ul class="dropdown-menu dropdown-inverse drop-menu article-status-menu">
                                            <li ng-repeat="(status_id, status) in local.group_status">
                                                <a ng-click="updateLineInfo( line, status_id )" ng-bind="status.label"></a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div ng-show="data.all_group[local.current_group_i].type == '3' && data.all_group[local.current_group_i].type_set">
                    <div class="col-md-18 one-block article-block">
                        <div class="item-list-container ungrouped">
                            <h4 class="list-title">未分组文章</h4>
                            <ul class="item-list">
                                <li ng-repeat="article in data.articles" ng-show="!isArticleInAllGroups( article.article_id )">
                                    <a class="item-name" href="" ng-click="addArticleToGroup( article.article_id )">
                                        <span ng-bind="article.article_id + '-' + article.title"></span>
                                        <div class="i i-enter article-btn"></div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="item-list-container grouped">
                            <h4 class="list-title">已分组文章</h4>
                            <ul class="item-list">
                                <li ng-repeat="article in data.articles"
                                    ng-show="isArticleInAllGroups( article.article_id ) && !isArticleInGroup( article.article_id )">
                                    <a class="item-name" href="" ng-click="addArticleToGroup( article.article_id )">
                                        <span ng-bind="article.article_id + '-' + article.title"></span>
                                        <div class="i i-enter article-btn"></div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10%;">排序</th>
                                <th style="width: 8%;">文章ID</th>
                                <th style="width: 30%;">文章图片(340*220)</th>
                                <th style="width: 40%;">文章名称</th>
                                <th style="width: 12%;">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="article in data.all_group[local.current_group_i].refs_order">
                                <td>
                                    <input class="form-control"
                                           ng-value="{{$index + 1}}"
                                           ng-model="article.display_order"
                                           ng-blur="updateArticleGroupOrder(article)" />
                                </td>
                                <td>
                                    <span ng-bind="article.id"></span>
                                </td>
                                <td ng-if="data.all_group[local.current_group_i].type == '3'">
                                    <div hi-uploader options="article.uploader"></div>
                                </td>
                                <td>
                                    <span ng-show="article.status == 1">★</span>
                                    <span ng-bind="article.article_name"></span>
                                </td>
                                <td class="article-button">
                                    <button class="block-action add btn btn-inverse"
                                            ng-click="deleteArticleFromGroup( article.id )">
                                        删除
                                    </button>
                                    <div class="article-status all-status">
                                        <button class="btn article-status-btn" data-toggle="dropdown"
                                                ng-class="{ 'btn-default' : article.status == '1', 'btn-inverse' : article.status == '2' }"
                                                ng-bind="local.group_status[ article.status ].label">
                                            <span class="caret"></span>
                                        </button>
                                        <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                                        <ul class="dropdown-menu dropdown-inverse drop-menu article-status-menu">
                                            <li ng-repeat="(status_id, status) in local.group_status">
                                                <a ng-click="updateArticleState( article, status_id )" ng-bind="status.label"></a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div ng-show="data.all_group[local.current_group_i].type == '4' && data.all_group[local.current_group_i].type_set">
                    <div class="col-md-9 one-block side-block margin">
                        <div class="item-list-container ungrouped">
                            <h4 class="list-title">未分组城市</h4>
                            <ul class="item-list">
                                <li ng-repeat="city in data.cities" ng-show="!isCityInAllGroups( city.city_code )">
                                    <span class="item-name" ng-bind="city.cn_name"></span>
                                    <a href="" ><span class="i i-enter" ng-click="addCityToGroup( city.city_code )"></span></a>
                                </li>
                            </ul>
                        </div>
                        <div class="item-list-container grouped">
                            <h4 class="list-title">已分组城市</h4>
                            <ul class="item-list">
                                <li ng-repeat="city in data.cities"
                                    ng-show="isCityInAllGroups( city.city_code ) && !isCityInGroup( city.city_code )">
                                    <span class="item-name" ng-bind="city.cn_name"></span>
                                    <a href="" ><span class="i i-enter" ng-click="addCityToGroup( city.city_code )"></span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-9 one-block side-block">
                        <div class="item-list-container ingroup">
                            <h4 class="list-title">分组内的城市</h4>
                            <ul class="item-list">
                                <li ng-repeat="city in data.all_group[local.current_group_i].refs_order">
                                    <input class="item-order form-control" ng-model="city.display_order"
                                           ng-value="{{ $index }} + 1" ng-blur="updateCityGroupOrder(city)" />
                                    <span class="item-name" ng-bind="getCityName( city.id )"></span>
                                    <a href="" ><span class="i i-close" ng-click="deleteCityFromGroup( city.id )"></span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div ng-show="data.all_group[local.current_group_i].type == '5' && data.all_group[local.current_group_i].type_set">
                    <div class="col-md-9 one-block side-block margin">
                        <div class="item-list-container ungrouped">
                            <h4 class="list-title">未分配分组</h4>
                            <ul class="item-list">
                                <li ng-repeat="group in data.all_group" ng-show="!isGroupInAllGroups( group.group_id ) && group.group_id != data.all_group[local.current_group_i].group_id && group.type != '4' ">
                                    <span class="item-name" ng-bind="group.group_id + '-' + group.name"></span>
                                    <a href="" ><span class="i i-enter" ng-click="addGroupToGroup( group.group_id )"></span></a>
                                </li>
                            </ul>
                        </div>
                        <div class="item-list-container grouped">
                            <h4 class="list-title">已分配分组</h4>
                            <ul class="item-list">
                                <li ng-repeat="group in data.all_group"
                                    ng-show="isGroupInAllGroups( group.group_id ) && !isGroupInGroup( group.group_id ) && group.group_id != data.all_group[local.current_group_i].group_id && group.type != '4' ">
                                    <span class="item-name" ng-bind="group.group_id + '-' + group.name"></span>
                                    <a href="" ><span class="i i-enter" ng-click="addGroupToGroup( group.group_id )"></span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-9 one-block side-block">
                        <div class="item-list-container ingroup">
                            <h4 class="list-title">包含分组</h4>
                            <ul class="item-list">
                                <li ng-repeat="group in data.all_group[local.current_group_i].refs_order">
                                    <input class="item-order form-control" ng-model="group.display_order"
                                           ng-value="{{ $index }} + 1" ng-blur="updateDoubleGroupOrder( group )" />
                                    <span class="item-name" ng-bind="group.id + '-' + group.group_name"></span>
                                    <a href="" ><span class="i i-close" ng-click="deleteGroupFromGroup( group.id )"></span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>