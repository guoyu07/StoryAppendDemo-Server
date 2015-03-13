<script type="text/ng-template" id="editProductBundle.html">
<div id="product-bundle-container">
<div class="section-action col-md-16 col-md-offset-1">
<div class="package-contains-container">
<div class="row edit-heading with-dot">
    <div class="bundle-group-title clearfix">
        <div class="package-contains-title">
            <h4>商品挂接</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="group-list-container">
        <div class="grid-bottom col-md-17 bundle-contains-group">
            <div class="detail-info-container">
                <form name="bundle_product">
                    <div class="row">
                        <div class="signle-line group-alias">
                            <label class="col-md-8 col-md-offset-1 display-info">套餐包含</label>
                        </div>
                    </div>
                    <div class="with-bottom-border">
                        <div class="row subtitle">
                            <label class="col-md-8 col-md-offset-1 display-info"
                                   ng-bind="local.radio_options.bundle_type.items[data.hotel_products.group_type]"></label>
                        </div>
                        <div class="row product-binding-container">
                            <div class="col-md-18 binding-product" ng-show="data.hotel_products.is_editing">
                                <input type="text" name="addProduct" class="col-md-6 col-md-offset-1" placeholder="请输入商品ID"
                                       ng-model="data.hotel_products.binding_product_id" ng-pattern="onlyNumbers" />
                                <button class="btn btn-inverse col-md-3 col-md-offset-1"
                                        ng-click="addBundle('hotel_products')">确认添加
                                </button>
                            </div>

                            <div class="col-md-18 product-bind-list">
                                <div class="bound-product-info col-md-16 col-md-offset-1"
                                     data-ng-repeat="item in data.hotel_products.items"
                                     data-index="{{ 'hotel_products-' + $index }}"
                                     dnd-sortable
                                     item="group"
                                     callback="slideProductDndCallback(info, dstIndex)"
                                     options="slideProductDndOptions">
                                    <div class="row single-product-content">
                                        <label class="col-md-3">
                                            商品ID:{{item.binding_product_id}}
                                        </label>
                                        <a class="col-md-8" ng-href="{{local.product_edit_url + item.binding_product_id}}"
                                           target="_blank">
                                            {{item.product.description.name}}
                                        </a>
                                    <span class="glyphicon glyphicon-remove-sign p-del-icon"
                                          data-ng-click="unbindProduct($index, 'hotel_products')"></span>
                                    </div>
                                </div>

                                <div class="col-md-4 col-md-offset-13 submit-content"
                                     ng-show="data.hotel_products.is_editing">
                                    <button class="btn btn-inverse"
                                            ng-disabled="bundle_product.$invalid"
                                            ng-click="submitGroupChange( 'hotel_products' )">保存
                                    </button>
                                </div>
                                <div class="col-md-4 col-md-offset-13 submit-content"
                                     ng-show="!data.hotel_products.is_editing">
                                    <button class="btn btn-inverse"
                                            ng-click="editGroup( 'hotel_products' )">编辑
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="row subtitle">
                            <label class="col-md-8 col-md-offset-1 display-info"
                                   ng-bind="local.radio_options.bundle_type.items[data.contains_products.group_type]"></label>
                        </div>
                        <div class="row product-binding-container">
                            <div class="col-md-18 binding-product" ng-show="data.contains_products.is_editing">
                                <input type="text" name="addProduct" class="col-md-6 col-md-offset-1" placeholder="请输入商品ID"
                                       ng-model="data.contains_products.binding_product_id" ng-pattern="onlyNumbers" />
                                <button class="btn btn-inverse col-md-3 col-md-offset-1"
                                        ng-click="addBundle('contains_products')">确认添加
                                </button>
                            </div>

                            <div class="col-md-18 product-bind-list">
                                <div class="bound-product-info col-md-16 col-md-offset-1"
                                     data-ng-repeat="item in data.contains_products.items"
                                     data-index="{{ 'contains_products-' + $index }}"
                                     dnd-sortable
                                     item="group"
                                     callback="slideProductDndCallback(info, dstIndex)"
                                     options="slideProductDndOptions">
                                    <div class="row single-product-content">
                                        <label class="col-md-3">
                                            商品ID:{{item.binding_product_id}}
                                        </label>
                                        <a class="col-md-8" ng-href="{{local.product_edit_url + item.binding_product_id}}"
                                           target="_blank">
                                            {{item.product.description.name}}
                                        </a>
                                        <label class="col-md-5 discount-content"
                                               ng-show="!data.contains_products.is_editing">
                                            {{local.radio_options.present_type.items[item.count_type]}}
                                        </label>
                                        <div class="col-md-6" style="margin: 3px 0px;"
                                             ng-show="data.contains_products.is_editing">
                                            <label class="col-md-7" style="font-size: 12px;">配赠方式:</label>
                                            <div class="col-md-11">
                                                <select
                                                    chosen
                                                    style="width: 100%;"
                                                    ng-model="item.count_type"
                                                    ng-options="type.id as type.name for type in local.present_type"
                                                    no-results-text="'没有找到'"
                                                    >
                                                </select>
                                            </div>
                                        </div>
                                    <span class="glyphicon glyphicon-remove-sign p-del-icon"
                                          data-ng-click="unbindProduct($index, 'contains_products')"></span>
                                    </div>
                                </div>

                                <div class="col-md-4 col-md-offset-13 submit-content"
                                     ng-show="data.contains_products.is_editing">
                                    <button class="btn btn-inverse"
                                            ng-disabled="bundle_product.$invalid"
                                            ng-click="submitGroupChange( 'contains_products' )">保存
                                    </button>
                                </div>
                                <div class="col-md-4 col-md-offset-13 submit-content"
                                     ng-show="!data.contains_products.is_editing">
                                    <button class="btn btn-inverse"
                                            ng-click="editGroup( 'contains_products' )">编辑
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div class="grid-bottom col-md-17 bundle-contains-group">
            <div class="detail-info-container">
                <form name="bundle_product">
                    <div class="row">
                        <div class="signle-line group-alias">
                            <label class="col-md-8 col-md-offset-1 display-info">独享特惠</label>
                        </div>
                    </div>
                    <div class="row product-binding-container">
                        <div class="col-md-18 binding-product" ng-show="data.recommend_products.is_editing">
                            <input type="text" name="addProduct" class="col-md-6 col-md-offset-1" placeholder="请输入商品ID"
                                   ng-model="data.recommend_products.binding_product_id" ng-pattern="onlyNumbers" />
                            <button class="btn btn-inverse col-md-3 col-md-offset-1"
                                    ng-click="addBundle('recommend_products')">确认添加
                            </button>
                        </div>

                        <div class="col-md-18 product-bind-list">
                            <div class="bound-product-info col-md-16 col-md-offset-1"
                                 data-ng-repeat="item in data.recommend_products.items"
                                 data-index="{{ 'recommend_products-' + $index }}"
                                 dnd-sortable
                                 item="group"
                                 callback="slideProductDndCallback(info, dstIndex)"
                                 options="slideProductDndOptions">
                                <div class="row single-product-content">
                                    <label class="col-md-3">
                                        商品ID:{{item.binding_product_id}}
                                    </label>
                                    <a class="col-md-8" ng-href="{{local.product_edit_url + item.binding_product_id}}"
                                       target="_blank">
                                        {{item.product.description.name}}
                                    </a>
                                    <label class="col-md-5 discount-content"
                                           ng-show="!data.recommend_products.is_editing">
                                        减{{item.discount_amount}}元
                                    </label>
                                    <div class="col-md-6"
                                         ng-show="data.recommend_products.is_editing">
                                        <label class="col-md-9">优惠金额: 减</label>
                                        <input type="text" class="col-md-6"
                                               ng-model="item.discount_amount"
                                               ng-pattern="onlyNumbers" />
                                        <label class="col-md-2">元</label>
                                    </div>
                                    <span class="glyphicon glyphicon-remove-sign p-del-icon"
                                          data-ng-click="unbindProduct($index, 'recommend_products')"></span>
                                </div>
                            </div>

                            <div class="col-md-4 col-md-offset-13 submit-content"
                                 ng-show="data.recommend_products.is_editing">
                                <button class="btn btn-inverse"
                                        ng-disabled="bundle_product.$invalid"
                                        ng-click="submitGroupChange( 'recommend_products' )">保存
                                </button>
                            </div>
                            <div class="col-md-4 col-md-offset-13 submit-content"
                                 ng-show="!data.recommend_products.is_editing">
                                <button class="btn btn-inverse"
                                        ng-click="editGroup( 'recommend_products' )">编辑
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
</script>