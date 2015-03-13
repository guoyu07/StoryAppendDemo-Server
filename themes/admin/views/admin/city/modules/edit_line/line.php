<div class="states-section" ng-show="local.current_menu == '5'">
    <div class="section-head">
        <h2 class="section-title">线路商品</h2>
    </div>
    <div class="section-body groups-edit-container other">
        <div class="col-md-18 hi-grid">
            <div class="row grid-bottom">
                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-md-6 col-md-offset-5">
                        <input type="text" class="form-control" ng-model="data.groups.line[0].name"
                               placeholder="输入分组名称" />
                    </div>
                    <div class="col-md-2">
                        <button class="block-action add btn btn-inverse"
                                ng-click="updateGroupInfo( data.groups.line[0] )">
                            保存
                        </button>
                    </div>
                </div>
                <div class="row grid-bottom">
                    <div class="col-md-6 col-md-offset-5">
                        <input type="text" class="form-control" ng-model="local.search_text" placeholder="输入商品ID" />
                    </div>
                    <div class="col-md-1">
                        <button class="block-action add btn btn-inverse"
                                ng-click="addProductToGroupBySearch( 'line', local.search_text )">
                            添加
                        </button>
                    </div>
                    <p class="picture-desc">商品图片尺寸：web(商品图片(770*232),线路图(230*200))</p>
                </div>
            </div>
            <table class="table table-striped" id="hotel_grid">
                <thead>
                    <tr>
                        <th style="width: 5%;">排序</th>
                        <th style="width: 5%;">商品ID</th>
                        <th style="width: 20%;">商品名称</th>
                        <th style="width: 20%;">商品图片</th>
                        <th style="width: 15%;">途经城市</th>
                        <th style="width: 10%;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="product in data.groups.line[0].products">
                        <td>
                            <input class="form-control"
                                   ng-model="product.display_order"
                                   ng-value="{{ $index + 1 }}"
                                   ng-blur="updateProductOrder( 'line', product.product_id )" />
                        </td>
                        <td>
                            <span ng-bind="product.product_id"></span>
                        </td>
                        <td>
                            <span ng-show="product.online == 1">★</span>
                            <a target="_blank" ng-href="{{local.product_url + product.product_id}}">
                                <span ng-bind="product.name"></span>
                            </a>
                            <textarea class="form-control" ng-model="product.product_name" placeholder="商品展示名称(14字以内)"
                                      ng-blur="updateLineProduct( product.product_id )"></textarea>
                        </td>
                        <td>
                            <hi-uploader options="product.uploader"></hi-uploader>
                        </td>
                        <td>
                            <span class="tour-city" ng-repeat="city in product.tour_cities">{{city.city_name}}</span>
                        </td>
                        <td>
                            <button class="block-action add btn btn-inverse"
                                    ng-click="deleteProductFromGroup( 'line', product.product_id )">删除
                            </button>
                            <button class="block-action add btn btn-inverse copy-btn"
                                    ng-click="copyLineProduct( product.product_id )">同步
                            </button>
                            <hi-radio-switch options="product.radio_options.status" model="product.radio_options.value"></hi-radio-switch>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>