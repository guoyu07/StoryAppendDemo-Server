<div class="states-section" ng-show="local.current_menu == '2'">
    <div class="section-head">
        <h2 class="section-title">分组设置</h2>
    </div>
    <div class="section-body">
        <div class="section-subtitle" id="pre_group_watershed">
            默认分组
        </div>
        <div class="section-subbody row">
            <p class="small-desc">分组封面图尺寸为235x212</p>

            <div class="groups-list-container col-md-6">
                <div ng-repeat="group in data.groups.pre">
                    <div class="one-block" ng-class="{ 'selected' : $index === local.edit.pre.index }"
                         ng-click="setCurrentGroup( 'pre', $index )">
                        <p class="title" ng-bind="group.name"></p>
                        <p class="small-desc">
                            此分类包含 <span class="brand-secondary" ng-bind="group.products.length"></span> 个商品
                        </p>
                        <span class="all-status" ng-bind="local.group_status[ group.status ].label"></span>
                    </div>
                </div>
            </div>
            <div class="groups-edit-container col-md-12" ng-show="local.edit.pre.index > -1">
                <div class="group-edit-info one-block grid-bottom row">
                    <form name="pre_group_form">
                        <div class="row">
                            <div class="citygroup-cover col-md-5">
                                <div hi-uploader options="local.uploader_options.pre_group_img"></div>
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
                                                       ng-model="data.groups.pre[local.edit.pre.index].name"
                                                       ng-disabled="true" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-6">
                                                <label for="group_description">分组描述</label>
                                            </td>
                                            <td>
                                                <textarea ng-model="data.groups.pre[local.edit.pre.index].description"
                                                          class="form-control disabled-text" rows="2" maxlength="60"
                                                          ng-disabled="local.edit.pre.edit == false"></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-6">
                                                <label for="group_seo_title">分组SEO标题</label>
                                            </td>
                                            <td>
                                                <input type="text" ng-model="data.groups.pre[local.edit.pre.index].seo.title"
                                                       id="group_seo_title" class="form-control disabled-text"
                                                       ng-disabled="local.edit.pre.edit == false" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-6">
                                                <label for="group_seo_description">分组SEO描述</label>
                                            </td>
                                            <td>
                                                <input type="text"
                                                       ng-model="data.groups.pre[local.edit.pre.index].seo.description"
                                                       id="group_seo_description" class="form-control disabled-text"
                                                       ng-disabled="local.edit.pre.edit == false" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-6">
                                                <label for="group_keywords">分组SEO关键词</label>
                                            </td>
                                            <td>
                                                <input type="text" ng-model="data.groups.pre[local.edit.pre.index].seo.keywords"
                                                       id="group_keywords" class="form-control disabled-text"
                                                       ng-disabled="local.edit.pre.edit == false" />
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                                                            <span class="i block-btn"
                                                                  ng-class="{ 'i-edit': local.edit.pre.edit == false, 'i-save': local.edit.pre.edit == true }"
                                                                  ng-click="toggleGroupEdit( 'pre' )"
                                                                  ng-disabled="pre_group_form.$invalid && local.edit.pre.edit == true"></span>
                        </div>
                    </form>
                </div>
                <div class="row text-center">
                    <div class="one-block side-block text-left margin"
                         ng-show="data.groups.pre[local.edit.pre.index].type == 2">
                        <div class="item-list-container ungrouped">
                            <h4 class="list-title">未分组商品</h4>
                            <ul class="item-list">
                                <li ng-repeat="product in data.products" ng-show="!isProductInAnyGroup( product.product_id )">
                                    <a class="item-name" target="_blank" ng-href="{{ local.product_url + product.product_id }}"
                                       ng-bind="product.product_id + ' - ' + product.name + (product.online == 1 ? ' ★' : '')"></a>
                                    <span class="i i-enter" ng-click="addProductToGroup( 'pre', product.product_id )"></span>
                                </li>
                            </ul>
                        </div>
                        <div class="item-list-container grouped">
                            <h4 class="list-title">已分组商品</h4>
                            <ul class="item-list">
                                <li ng-repeat="product in data.products"
                                    ng-show="isProductInAnyGroup( product.product_id ) && !isProductInThisGroup( 'pre', product.product_id )">
                                    <a class="item-name" target="_blank" ng-href="{{ local.product_url + product.product_id }}"
                                       ng-bind="product.product_id + ' - ' + product.name + (product.online == 1 ? ' ★' : '')"></a>
                                    <span class="i i-enter" ng-click="addProductToGroup( 'pre', product.product_id )"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="one-block side-block text-left">
                        <div class="item-list-container ingroup">
                            <h4 class="list-title">分组内的商品</h4>
                            <ul class="item-list">
                                <li ng-repeat="product in data.groups.pre[local.edit.pre.index].products">
                                    <input class="item-order form-control" ng-value="{{ $index + 1 }}"
                                           ng-blur="updateProductOrder( 'pre', product.product_id )" />
                                    <a target="_blank" ng-href="{{local.product_url + product.product_id}}">
                                                            <span class="item-name"
                                                                  ng-bind="product.product_id + ' - ' + product.name + (product.online == 1 ? ' ★' : '')"></span></a>
                                    <!--只针对热门推荐，全部分组不能删除商品-->
                                            <span class="i i-close"
                                                  ng-click="deleteProductFromGroup( 'pre', product.product_id )"
                                                  ng-show="data.groups.pre[local.edit.pre.index].type == 2"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="midline-separator"></div>

        <div class="section-subtitle" id="user_group_watershed">
            添加分组
            <button class="btn btn-inverse block-action add" ng-click="addUserGroup()">新增</button>
        </div>
        <div class="section-subbody row">
            <div class="groups-list-container col-md-6 user-groups">
                <div class="one-block" ng-repeat="group in data.groups.user" ng-init="group_index = $index"
                     ng-click="setCurrentGroup( 'user', $index )" hi-dnd item="group"
                     callback="local.dnd.callback( info, dst_index )" options="local.dnd.options"
                     data-index="{{ $index }}" ng-class="{ 'selected' : $index === local.edit.user.index }">
                    <div class="delete-block" ng-click="deleteUserGroup( $index )">
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
                                <a ng-click="updateGroupState( group_index, status_id )" ng-bind="status.label"></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="groups-edit-container col-md-12" ng-show="local.edit.user.index > -1">
                <div class="group-edit-info one-block grid-bottom row">
                    <form name="user_group_form">
                        <div class="row">
                            <div class="citygroup-cover col-md-5">
                                <p class="small-desc pad-top">分组封面图尺寸为235x212</p>
                                <div hi-uploader options="local.uploader_options.user_group_img"></div>
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
                                                       ng-model="data.groups.user[local.edit.user.index].name"
                                                       ng-disabled="local.edit.user.edit == false" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-6">
                                                <label for="group_description">分组描述</label>
                                            </td>
                                            <td>
                                                <textarea ng-model="data.groups.user[local.edit.user.index].description"
                                                          class="form-control disabled-text" rows="2" maxlength="60"
                                                          ng-disabled="local.edit.user.edit == false"></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-6">
                                                <label for="group_seo_title">分组SEO标题</label>
                                            </td>
                                            <td>
                                                <input type="text" ng-model="data.groups.user[local.edit.user.index].seo.title"
                                                       id="group_seo_title" class="form-control disabled-text"
                                                       ng-disabled="local.edit.user.edit == false" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-6">
                                                <label for="group_seo_description">分组SEO描述</label>
                                            </td>
                                            <td>
                                                <input type="text"
                                                       ng-model="data.groups.user[local.edit.user.index].seo.description"
                                                       id="group_seo_description" class="form-control disabled-text"
                                                       ng-disabled="local.edit.user.edit == false" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-6">
                                                <label for="group_keywords">分组SEO关键词</label>
                                            </td>
                                            <td>
                                                <input type="text"
                                                       ng-model="data.groups.user[local.edit.user.index].seo.keywords"
                                                       id="group_keywords" class="form-control disabled-text"
                                                       ng-disabled="local.edit.user.edit == false" />
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                                                <span class="i block-btn"
                                                      ng-class="{ 'i-edit': local.edit.user.edit == false, 'i-save': local.edit.user.edit == true }"
                                                      ng-click="toggleGroupEdit( 'user' )"
                                                      ng-disabled="user_group_form.$invalid && local.edit.user.edit == true"></span>
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
                                    <span class="i i-enter" ng-click="addProductToGroup( 'user', product.product_id )"></span>
                                </li>
                            </ul>
                        </div>
                        <div class="item-list-container grouped">
                            <h4 class="list-title">已分组商品</h4>
                            <ul class="item-list">
                                <li ng-repeat="product in data.products"
                                    ng-show="isProductInAnyGroup( product.product_id ) && !isProductInThisGroup( 'user', product.product_id )">
                                    <a class="item-name" target="_blank" ng-href="{{local.product_url + product.product_id}}"
                                       ng-bind="product.product_id + ' - ' + product.name + (product.online == 1 ? ' ★' : '')"></a>
                                    <span class="i i-enter" ng-click="addProductToGroup( 'user', product.product_id )"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="one-block side-block text-left">
                        <div class="item-list-container ingroup">
                            <h4 class="list-title">分组内的商品</h4>
                            <ul class="item-list">
                                <li ng-repeat="product in data.groups.user[local.edit.user.index].products">
                                    <input class="item-order form-control"
                                           ng-model="product.display_order"
                                           ng-value="{{ $index + 1}}"
                                           ng-blur="updateProductOrder( 'user', product.product_id )" />
                                    <a target="_blank" ng-href="{{local.product_url + product.product_id}}">
                                        <span class="item-name"
                                              ng-bind="product.product_id + ' - ' + product.name + (product.online == 1 ? ' ★' : '')"></span>
                                    </a>
                                    <span class="i i-close"
                                          ng-click="deleteProductFromGroup( 'user', product.product_id )"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>