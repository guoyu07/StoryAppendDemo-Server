<div class="states-section" ng-show="local.current_menu == '7'">
    <div class="section-head">
        <h2 class="section-title">APP分组</h2>
    </div>
    <div class="section-body">
        <div class="section-subtitle" id="app_group_watershed">
            添加分组
            <button class="btn btn-inverse block-action add" ng-click="addAppGroup()">新增</button>
        </div>
        <div class="section-subbody row">
            <div class="groups-list-container col-md-6 user-groups app-groups">
                <div class="one-block" ng-repeat="group in data.groups.app" ng-init="group_index = $index"
                     ng-click="setCurrentGroup( 'app', $index )" hi-dnd item="group"
                     callback="local.dnd_app.callback( info, dst_index )" options="local.dnd_app.options"
                     data-index="{{ $index }}" ng-class="{ 'selected' : $index === local.edit.app.index }">
                    <div class="delete-block" ng-click="deleteAppGroup( $index )">
                        <span class="i i-close"></span>
                    </div>
                    <div class="group-name" ng-bind="group.name"></div>
                    <p class="small-desc">此分组含<span ng-bind="group.products.length"></span>个商品</p>
                    <span class="ordering" ng-bind="$index + 1"></span>
                    <div class="btn-group all-status">
                        <button class="btn dropdown-toggle" data-toggle="dropdown"
                                ng-class="{ 'btn-default' : group.status == '1', 'btn-inverse' : group.status == '2' }"
                                ng-bind="local.group_status[ group.status ].label">
                            <span class="caret"></span>
                        </button>
                        <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                        <ul class="dropdown-menu dropdown-inverse">
                            <li ng-repeat="(status_id, status) in local.group_status">
                                <a ng-click="updateAppGroupState( group_index, status_id )" ng-bind="status.label"></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="groups-edit-container col-md-12" ng-show="local.edit.app.index > -1">
                <div class="group-edit-info one-block grid-bottom row">
                    <form name="app_group_form">
                        <div class="row">
                            <div class="citygroup-cover col-md-5">
                                <p class="small-desc pad-top">分组封面图尺寸为(待定)</p>
                                <div hi-uploader options="local.uploader_options.app_group_img"></div>
                            </div>
                            <div class="col-md-13">
                                <div class="row">
                                    <table class="forms-table">
                                        <tr>
                                            <td class="col-md-6">
                                                <label for="group_title">分组标题</label>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control disabled-text"
                                                       ng-model="data.groups.app[local.edit.app.index].name"
                                                       ng-disabled="local.edit.app.edit == false" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-6">
                                                <label for="group_description">分组描述</label>
                                            </td>
                                            <td>
                                                <textarea ng-model="data.groups.app[local.edit.app.index].description"
                                                          class="form-control disabled-text" rows="3" maxlength="60"
                                                          ng-disabled="local.edit.app.edit == false"></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-6">
                                                <label for="group_description">分组简言</label>
                                            </td>
                                            <td style="padding-top: 10px">
                                                <textarea ng-model="data.groups.app[local.edit.app.index].brief"
                                                          class="form-control disabled-text" rows="3" maxlength="20"
                                                          ng-disabled="local.edit.app.edit == false"></textarea>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                                                <span class="i block-btn"
                                                      ng-class="{ 'i-edit': local.edit.app.edit == false, 'i-save': local.edit.app.edit == true }"
                                                      ng-click="toggleGroupEdit( 'app' )"
                                                      ng-disabled="app_group_form.$invalid && local.edit.app.edit == true"></span>
                        </div>
                    </form>
                </div>
                <div class="row text-center">
                    <div class="one-block side-block text-left margin">
                        <div class="item-list-container ungrouped">
                            <h4 class="list-title">未分组商品</h4>
                            <ul class="item-list">
                                <li ng-repeat="product in data.products" ng-show="!isProductInAnyGroup( product.product_id )">
                                    <a class="item-name" target="_blank" ng-href="{{local.product_url + product.product_id}}"
                                       ng-bind="product.product_id + ' - ' + product.name + (product.online == 1 ? ' ★' : '')"></a>
                                    <span class="i i-enter" ng-click="addProductToGroup( 'app', product.product_id )"></span>
                                </li>
                            </ul>
                        </div>
                        <div class="item-list-container grouped">
                            <h4 class="list-title">已分组商品</h4>
                            <ul class="item-list">
                                <li ng-repeat="product in data.products"
                                    ng-show="isProductInAnyGroup( product.product_id ) && !isProductInThisGroup( 'app', product.product_id )">
                                    <a class="item-name" target="_blank" ng-href="{{local.product_url + product.product_id}}"
                                       ng-bind="product.product_id + ' - ' + product.name + (product.online == 1 ? ' ★' : '')"></a>
                                    <span class="i i-enter" ng-click="addProductToGroup( 'app', product.product_id )"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="one-block side-block text-left">
                        <div class="item-list-container ingroup">
                            <h4 class="list-title">分组内的商品</h4>
                            <ul class="item-list">
                                <li ng-repeat="product in data.groups.app[local.edit.app.index].products">
                                    <input class="item-order form-control"
                                           ng-model="product.display_order"
                                           ng-value="{{ $index + 1}}"
                                           ng-blur="updateProductOrder( 'app', product.product_id )" />
                                    <a target="_blank" ng-href="{{local.product_url + product.product_id}}">
                                        <span class="item-name"
                                              ng-bind="product.product_id + ' - ' + product.name + (product.online == 1 ? ' ★' : '')"></span>
                                    </a>
                                    <span class="i i-close"
                                          ng-click="deleteProductFromGroup( 'app', product.product_id )"></span>
                                    <div>
                                        <div>商品别名(20字以内):</div>
                                        <input class="form-control" ng-model="product.product_name" ng-blur="updateAppProductAlias(product.product_id)" />
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>