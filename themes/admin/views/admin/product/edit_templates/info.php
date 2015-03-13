<script type="text/ng-template" id="editProductInfo.html">
<div class="edit-section last clearfix">
    <sidebar name='editProductName'></sidebar>
    <section class="col-xs-13 col-xs-offset-1 section-action product-info">
        <form name="product_info_form" novalidate>
            <div class="row edit-heading with-dot">
                <h2>商品名称</h2>
            </div>
            <div class="edit-body">
                <div class="row grid-bottom">
                    <label class="info-title col-md-5">
                        商品名称CH
                    </label>
                    <div class="col-md-13">
                        <input type="text" class=" form-control"
                               data-ng-model="product_info.cn_name"
                               required />
                    </div>
                </div>

                <div class="row grid-bottom">
                    <label class="info-title col-md-5">
                        商品名称EN
                    </label>
                    <div class="col-md-13">
                        <input type="text" class="form-control"
                               data-ng-model="product_info.en_name" required />
                    </div>
                </div>

                <div class="row grid-bottom">
                    <label class="info-title col-md-5">
                        原供应商商品名称CH
                    </label>
                    <div class="col-md-13">
                        <input type="text" class="form-control"
                               data-ng-model="product_info.cn_origin_name" />
                    </div>
                </div>

                <div class="row grid-bottom">
                    <label class="info-title col-md-5">
                        原供应商商品名称EN
                    </label>
                    <div class="col-md-13">
                        <input type="text" class="form-control"
                               data-ng-model="product_info.en_origin_name" />
                    </div>
                </div>

                <div class="row grid-bottom" data-ng-show="{{ product_info.supplier_id==89 }}">
                    <label class="info-title col-md-5">
                        供应商商品唯一ID
                    </label>
                    <div class="col-md-13">
                        <input type="text" class="form-control"
                               data-ng-model="product_info.supplier_product_id" />
                    </div>
                </div>

                <div class="row grid-bottom">
                    <label class="info-title col-md-5">
                        供应商商品链接
                    </label>
                    <div class="col-md-13">
                        <input type="text" class="form-control"
                               data-ng-model="product_info.source_url" />
                    </div>
                </div>

                <div class="row grid-bottom">
                    <label class="info-title col-md-5">
                        商品负责人邮箱
                    </label>
                    <div class="col-md-13">
                        <input type="email" required name="manager_email" class="form-control"
                               data-ng-model="product_info.manager_name" />
                        <p ng-show="product_info_form.manager_email.$invalid" class="help-block">请输入合法的邮箱地址</p>
                    </div>
                </div>
                <div class="row edit-heading with-dot">
                    <h2>所属供应商</h2>
                </div>
                <div class="row edit-body" data-ng-hide="{{product_info.supplier_id == 11}}">
                    <select
                        chosen
                        required
                        class="col-xs-9"
                        value="{{product_info.supplier_id}}"
                        ng-model="data.vendor"
                        ng-options="vendor as vendor.name group by vendor.group for vendor in vendors track by vendor.supplier_id"
                        data-placeholder="选择供应商"
                        no-results-text="'没有找到'"
                        >
                    </select>
                </div>
                <div class="row edit-body" data-ng-show="{{ product_info.supplier_id==11 }}">
                    <span>GTA</span>
                        <span id="supplier_product_id" style="padding-left: 20px;"
                              ng-bind="product_info.supplier_product_id"></span>
                    <span style="padding-left: 20px;">导入状态：{{import_status[product_info.import.status].label}}</span>
                    <button class="btn btn-inverse" ng-click="updateImport()"
                            ng-show="product_info.import.status == 2 || product_info.import.status == -1">更新导入
                    </button>
                </div>

                <div class="row edit-heading with-dot">
                    <h2>所属城市</h2>
                </div>
                <div class="row edit-body" data-ng-hide="{{product_info.supplier_id == 11}}">
                    <select
                        chosen
                        required
                        class="col-xs-9"
                        data-ng-model="data.city"
                        data-ng-options="city as (city.city_name + ' ' + city.city_pinyin) group by city.group for city in cities track by city.city_code"
                        data-placeholder="选择城市"
                        no-results-text="'没有找到'"
                        >
                    </select>
                </div>
                <div class="row edit-body" data-ng-show="{{product_info.supplier_id == 11}}">
                    {{product_info.city_name}}
                </div>
                <div class="row edit-heading with-dot">
                    <h2>所属其它城市</h2>
                </div>
                <div class="row edit-body">
                    <select
                        chosen
                        required
                        class="col-xs-9"
                        data-ng-model="data.other_city"
                        data-ng-options="city as (city.city_name + ' ' + city.city_pinyin) group by city.group for city in cities track by city.city_code"
                        data-placeholder="选择城市"
                        no-results-text="'没有找到'"
                        >
                    </select>
                    <button data-ng-click="addMoreCity()" style="margin-right: 20px;">添加</button>
                    <br />
                    <button ng-repeat="city in other_cities" class="btn one-criteria"
                            data-ng-click="deleteCity(city.city_code)">
                        {{city.city_name}}
                    </button>
                </div>
                <div class="row edit-heading with-dot">
                    <h2>商品类型</h2>
                </div>
                <div class="row edit-body">
                    <select
                        chosen
                        required
                        class="col-xs-9"
                        ng-model="data.type"
                        ng-options="type as type.label for type in types track by type.type_id"
                        data-placeholder="选择类型"
                        no-results-text="'没有找到'"
                        >
                    </select>
                </div>
                <div class="row edit-heading with-dot" data-ng-hide="{{product_info.supplier_id == 11}}">
                    <h2>是否组合其他商品？</h2>
                </div>
                <div class="row edit-body" data-ng-hide="{{product_info.supplier_id == 11}}">
                    <radio-switch options="combo_radio_options" model="product_info"></radio-switch>

                    <div class="link-album clearfix" data-ng-show="product_info.is_combo == '1'">
                        <div class="form-group col-xs-4">
                            <input type="number" name="combo_pid" data-ng-model="data.combo_pid" min="1"
                                   placeholder="商品ID"
                                   class="form-control">
                        </div>
                        <button class="btn btn-inverse btn-sharp" data-ng-click="addComboProduct()">关联商品 <span
                            class="glyphicon glyphicon-refresh refresh-animate"
                            data-ng-show="data.check_progress == true"></span></button>
                    </div>
                    <div class="one-location-group-selection one-passenger-edit-box input-section"
                         data-ng-show="product_info.is_combo == '1'">
                        <button class="btn one-criteria one-allcriteria criteria-with-x"
                                data-ng-repeat="product in product_info.combo"
                                data-ng-click="delComboProduct(product.product_id)">
                            {{product.name}}
                        </button>
                    </div>
                </div>

                <div class="row edit-heading with-dot">
                    <h2>商品Tag</h2>
                </div>
                <div class="row edit-body">
                    <div class="view-tag-container">
                        <div class="row view-tag-content">
                            <div class="single-tag" ng-repeat="tag in product_tags">
                                <span ng-if="tag.tag.parent_tag_id > 0" ng-bind="tag.tag.parent_tag_name"></span>
                                <span ng-if="tag.tag.parent_tag_id > 0">/</span>
                                <span ng-bind="tag.tag.name"></span>
                            </div>
                        </div>
                        <div class="row">
                            <button class="col-md-4 col-md-offset-7 btn config-btn" ng-click="editTag()">配置Tag选项</button>
                        </div>
                    </div>
                </div>
            </div>
            
        </form>
    </section>
    <button type="submit" class="col-xs-offset-7 col-xs-4 btn btn-hg btn-primary save-form"
            data-ng-click="submitChanges()" data-ng-disabled="product_info_form.combo_pid.$invalid">
        保存
    </button>
</div>

<div ng-include="'editTag.html'"></div>
</script>

<script type="text/ng-template" id="editTag.html">
    <div class="edit-tag-container" ng-show="local.edit_tag">
        <div class="overlay"></div>
        <div class="edit-tag-dialog">
            <div class="row">
                <label class="dot-head">为此商品选择Tag</label>
            </div>
            <div class="row tag-selector">
                <div class="tag-level-selector col-md-8">
                    <div class="row single-tag" ng-repeat="tag in parent_tag">
                        <div class="col-md-1 tag-check-box" ng-click="selectTag('parent', $index)">
                            <div class="check-box" ng-class="{'disabled' : tag.has_child == '1'}">
                                <i class="i" ng-class="{'i-check' : local.selected_parent_tags.indexOf(tag.tag_id) > -1}"></i>
                            </div>
                        </div>
                        <div class="col-md-13 tag-content" ng-class="{'selected' : local.current_parent_tag == tag.tag_id}"
                             ng-click="selectTag('parent', $index)">
                            <div class="col-md-16 tag-name" ng-bind="tag.name">
                            </div>
                            <div class="col-md-2 tag-arrow" ng-show="tag.has_child == '1'">
                                <div class="arrow" ng-class="{'selected' : local.current_parent_tag == tag.tag_id}"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="vertical-separate-lines col-md-1"></div>
                <div class="tag-level-selector col-md-8">
                    <div class="row single-tag" ng-repeat="tag in sub_tag" ng-show = "local.current_parent_tag == tag.parent_tag_id">
                        <div class="col-md-1 tag-check-box" ng-click="selectTag('sub', $index)">
                            <div class="check-box">
                                <i class="i" ng-class = "{'i-check' : local.selected_sub_tags.indexOf(tag.tag_id) > -1}"></i>
                            </div>
                        </div>
                        <div class="col-md-13 tag-content" ng-click="selectTag('sub', $index)">
                            <div class="col-md-16 tag-name" ng-bind="tag.name"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <button class="col-md-4 col-md-offset-7 btn config-btn" ng-click="applyTagChange()">确定</button>
            </div>
            <div class="close-btn" ng-click = "closeTagDialog()">
                <i class="i i-close"></i>
            </div>
        </div>
    </div>
</script>
