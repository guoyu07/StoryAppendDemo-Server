<div class="states-section" ng-show="local.current_menu == '3'">
    <div class="section-head">
        <h2 class="section-title">Top 10商品</h2>
    </div>
    <div class="section-body groups-edit-container other">
        <div class="one-block text-left col-md-6">
            <div class="item-list-container ungrouped">
                <h4 class="list-title">未分组商品</h4>
                <ul class="item-list">
                    <li ng-repeat="product in data.products" ng-show="!isProductInAnyGroup( product.product_id )">
                        <a class="item-name" target="_blank" ng-href="{{local.product_url + product.product_id}}"
                           ng-bind="(product.online == 1 ? ' ★' : '') + product.product_id + ' - ' + product.name"></a>
                        <span class="i i-enter" ng-click="addProductToGroup( 'top', product.product_id )"
                              ng-show="data.groups.top[0].products.length < 10"></span>
                    </li>
                </ul>
            </div>
            <div class="item-list-container grouped">
                <h4 class="list-title">已分组商品</h4>
                <ul class="item-list">
                    <li ng-repeat="product in data.products"
                        ng-show="isProductInAnyGroup( product.product_id )">
                        <a class="item-name" target="_blank" ng-href="{{local.product_url + product.product_id}}"
                           ng-bind="(product.online == 1 ? ' ★' : '') + product.product_id + ' - ' + product.name"></a>
                        <span class="i i-enter" ng-click="addProductToGroup( 'top', product.product_id )"
                              ng-show="data.groups.top[0].products.length < 10"></span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-12 hi-grid">
            <div class="row" style="margin-bottom: 10px;">
                <div class="col-md-6 col-md-offset-5">
                    <input type="text" class="form-control" ng-model="data.groups.top[0].name" placeholder="输入分组名称" />
                </div>
                <div class="col-md-2">
                    <button class="block-action add btn btn-inverse" ng-click="updateGroupInfo( data.groups.top[0] )">
                        保存
                    </button>
                </div>
            </div>
            <div class="row grid-bottom">
                <div class="col-md-6 col-md-offset-5">
                    <input type="text" class="form-control" ng-model="local.search_text" placeholder="输入商品ID" />
                </div>
                <div class="col-md-2">
                    <button class="block-action add btn btn-inverse"
                            ng-click="addProductToGroupBySearch( 'top', local.search_text )"
                            ng-disabled="data.groups.top[0].products.length >= 10">
                        添加
                    </button>
                </div>
            </div>
            <table class="table table-striped" id="top_grid">
                <thead>
                    <tr>
                        <th style="width: 10%;">排序</th>
                        <th style="width: 10%;">商品ID</th>
                        <th style="width: 60%;">商品名称</th>
                        <th style="width: 20%;">商品操作</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="product in data.groups.top[0].products">
                        <td>
                            <input class="form-control"
                                   ng-model="product.display_order"
                                   ng-value="{{ $index + 1 }}"
                                   ng-blur="updateProductOrder( 'top', product.product_id )" />
                        </td>
                        <td>
                            <span ng-bind="product.product_id"></span>
                        </td>
                        <td>
                            <span ng-show="product.online == 1">★</span>
                            <a target="_blank" ng-href="{{local.product_url + product.product_id}}">
                                <span ng-bind="product.name"></span>
                            </a>
                        </td>
                        <td>
                            <button class="block-action add btn btn-inverse"
                                    ng-click="deleteProductFromGroup( 'top', product.product_id )">删除
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>