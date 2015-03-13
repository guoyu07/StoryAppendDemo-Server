<div ng-show="local.current_menu == '4'">
    <div class="states-section">
        <hi-section-head options="local.section_head.city_promotion"></hi-section-head>
        <div class="section-body" ng-show="local.section_head.city_promotion.is_edit">
            <form name="city_promotion_form">
                <div class="row grid-bottom">
                    <label class="col-md-2">聚合页状态</label>
                    <div class="col-md-6">
                        <hi-radio-switch options="local.radio_options.promotion_status" model="data.promotion"></hi-radio-switch>
                    </div>
                </div>
                <div class="row grid-bottom">
                    <label class="col-md-2">聚合页ID</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" ng-model="data.promotion.promotion_id" required />
                    </div>
                    <div class="col-md-7">
                        <button class="btn btn-primary grid-right" ng-click="viewPromotion('new')" ng-show="!data.promotion.promotion_id">
                            新增聚合页
                        </button>
                        <button class="btn btn-primary grid-right" ng-click="viewPromotion('current')" ng-show="data.promotion.promotion_id">
                            编辑聚合页
                        </button>
                    </div>
                </div>
                <div class="row grid-bottom">
                    <label class="col-md-2">广告条名称</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" ng-model="data.promotion.introduction_title" required />
                    </div>
                </div>
                <div class="row grid-bottom">
                    <label class="col-md-2">广告条短描述</label>
                    <div class="col-md-6">
                        <textarea class="form-control" rows="3" ng-model="data.promotion.introduction_description" hi-elastic></textarea>
                    </div>
                </div>
                <div class="row grid-bottom">
                    <label class="col-md-2">广告图片</label>
                    <div class="col-md-12" style="height: 138px;">
                        <hi-uploader style="height: 100%" options="local.uploader_options.hotel_plus_img"></hi-uploader>
                    </div>
                </div>
            </form>
        </div>
        <div class="section-body view" ng-hide="local.section_head.city_promotion.is_edit">
            <table class="forms-table">
                <tr>
                    <td class="view-title">广告条名称</td>
                    <td class="view-body">
                        <a target="_blank" ng-click="viewPromotion('current')">
                            {{data.promotion.promotion_id}} - {{data.promotion.introduction_title}}
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="view-title">广告条短描述</td>
                    <td class="view-body" ng-bind="data.promotion.introduction_description"></td>
                </tr>
                <tr>
                    <td class="view-title">广告图片</td>
                    <td class="view-body">
                        <img ng-src="{{data.promotion.introduction_image}}?imageView/5/w/700/h/138" />
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="states-section">
        <div class="section-head">
            <h2 class="section-title">酒店／套餐 － 挂接商品</h2>
        </div>
        <div class="section-body groups-edit-container other">
            <div class="one-block text-left col-md-6">
                <div class="item-list-container ungrouped">
                    <ul class="item-list">
                        <h4 class="list-title">酒店套餐商品</h4>
                        <li ng-repeat="product in data.products" ng-show="product.type == 8">
                            <a class="item-name" target="_blank" ng-href="{{local.product_url + product.product_id}}" ng-bind="(product.online == 1 ? ' ★' : '') + product.product_id + ' - ' + product.name"></a>
                            <span class="i i-enter" ng-click="addProductToGroup( 'hotel', product.product_id )"></span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-12 hi-grid">
                <div class="row grid-bottom">
                    <div class="row" style="margin-bottom: 10px;">
                        <div class="col-md-6 col-md-offset-5">
                            <input type="text" class="form-control" ng-model="data.groups.hotel[0].name"
                                   placeholder="输入分组名称" />
                        </div>
                        <div class="col-md-2">
                            <button class="block-action add btn btn-inverse"
                                    ng-click="updateGroupInfo( data.groups.hotel[0] )">
                                保存
                            </button>
                        </div>
                    </div>
                    <div class="row grid-bottom">
                        <div class="col-md-6 col-md-offset-5">
                            <input type="text" class="form-control" ng-model="local.search_text" placeholder="输入商品ID" />
                        </div>
                        <div class="col-md-1">
                            <button class="block-action add btn btn-inverse" ng-click="addProductToGroupBySearch( 'hotel', local.search_text )">
                                添加
                            </button>
                        </div>
                        <p class="picture-desc">商品图片尺寸：web(700x138)，手机(750x160)，PAD(750x160)</p>
                    </div>
                </div>
                <table class="table table-striped" id="hotel_grid">
                    <thead>
                        <tr>
                            <th style="width: 10%;">排序</th>
                            <th style="width: 8%;">商品ID</th>
                            <th style="width: 30%;">商品图片</th>
                            <th style="width: 40%;">商品名称</th>
                            <th style="width: 12%;">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="product in data.groups.hotel[0].products">
                            <td>
                                <input class="form-control"
                                       ng-model="product.display_order"
                                       ng-value="{{ $index + 1 }}"
                                       ng-blur="updateProductOrder( 'hotel', product.product_id )" />
                            </td>
                            <td>
                                <span ng-bind="product.product_id"></span>
                            </td>
                            <td>
                                <hi-uploader options="product.uploader"></hi-uploader>
                            </td>
                            <td>
                                <span ng-show="product.online == 1">★</span>
                                <a target="_blank" ng-href="{{local.product_url + product.product_id}}">
                                    <span ng-bind="product.name"></span>
                                </a>
                            </td>
                            <td>
                                <button class="block-action add btn btn-inverse"
                                        ng-click="deleteProductFromGroup( 'hotel', product.product_id )">删除
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>