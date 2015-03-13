<div id="edm-edit-container" class="container page-container" ng-controller="EDMEditCtrl">
    <div class="states-section">
        <hi-section-head model="local.section_head" options="local.section_head"></hi-section-head>
        <div class="section-body edm-cover clearfix" ng-class="local.section_head.getClass()"
             ng-show="local.section_head.is_edit">
            <!--编辑页面-->
            <div class="row grid-bottom">
                <div class="col-md-12 col-md-offset-3">
                    <p class="small-desc">头部背景图：尺寸为640x160</p>
                    <div hi-uploader options="local.uploader_options.cover"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-md-offset-3">
                    <form name="edm_info">
                        <table class="forms-table">
                            <tr>
                                <td class="col-md-6">
                                    <label for="edm_title">EDM大标题</label>
                                </td>
                                <td class="col-md-12">
                                    <input type="text" ng-model="data.base.title" id="edm_title" class="form-control"
                                           required />
                                </td>
                            </tr>
                            <tr>
                                <td class="col-md-6">
                                    <label for="edm_title_small">EDM小标题</label>
                                </td>
                                <td class="col-md-12">
                                    <input type="text" ng-model="data.base.small_title" id="edm_title_small"
                                           class="form-control" required />
                                </td>
                            </tr>
                            <tr>
                                <td class="col-md-6">
                                    <label for="edm_title_link">EDM标题链接</label>
                                </td>
                                <td class="col-md-12">
                                    <input type="text" ng-model="data.base.title_link" id="edm_title_link"
                                           class="form-control" required />
                                </td>
                            </tr>
                            <tr>
                                <td class="col-md-6">
                                    <label for="edm_title_link">EDM描述</label>
                                </td>
                                <td class="col-md-12">
                                    <textarea ng-model="data.base.description" id="edm_description"
                                              class="form-control" required cols="30" rows="4"></textarea>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <div class="section-body edm-cover clearfix" ng-class="local.section_head.getClass()"
             ng-hide="local.section_head.is_edit">
            <!--查看页面-->
            <div class="row grid-bottom">
                <div class="col-md-12 col-md-offset-3">
                    <button class="block-action btn btn-inverse" ng-click="preview()">预览</button>
                    <br />
                    <br />
                    <img ng-src="{{ local.uploader_options.cover.image_url }}" class="banner-img grid-bottom" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-md-offset-3">
                    <h4>EDM基本信息</h4>
                    <table class="forms-table">
                        <tr>
                            <td class="view-title col-md-6">EDM大标题</td>
                            <td class="view-body col-md-12" ng-bind="data.base.title"></td>
                        </tr>
                        <tr>
                            <td class="view-title col-md-6">EDM小标题</td>
                            <td class="view-body col-md-12" ng-bind="data.base.small_title"></td>
                        </tr>
                        <tr>
                            <td class="view-title col-md-6">EDM标题链接</td>
                            <td class="view-body col-md-12" ng-bind="data.base.title_link"></td>
                        </tr>
                        <tr>
                            <td class="view-title col-md-6">EDM描述</td>
                            <td class="view-body col-md-12" ng-bind="data.base.description"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="states-section">
        <div class="section-head">
            <h2 class="section-title">商品分组</h2>
        </div>
        <div class="section-body">
            <div class="section-subtitle" id="group_watershed">
                添加分组
                <button class="btn btn-inverse block-action add" ng-click="addGroup()">新增</button>
            </div>
            <p class="small-desc">商品图片大小：580x250</p>

            <div class="section-subbody row">
                <div class="group-list-container col-md-6">
                    <div class="one-block" ng-repeat="group in data.groups"
                         ng-click="setCurrentGroup( $index )" hi-dnd item="group"
                         callback="local.dnd.group.callback( info, dst_index )" options="local.dnd.group.options"
                         data-index="{{ $index }}" ng-class="{ 'selected' : $index === local.current_group }"
                        >
                        <div class="delete-block" ng-click="deleteGroup( $index )">
                            <span class="i i-close"></span>
                        </div>
                        <div class="group-name" ng-bind="group.title"></div>
                        <p class="small-desc">此分组含<span ng-bind="group.group_products.length"></span>个商品</p>
                    </div>
                </div>
                <div class="group-edit-container col-md-12" ng-show="local.current_group > -1">
                    <div class="group-edit-info one-block grid-bottom row">
                        <form name="edm_group_form">
                            <table class="forms-table">
                                <tr class="row">
                                    <td class="col-md-3">
                                        <label for="edm_title">分组标题</label>
                                    </td>
                                    <td class="col-md-14">
                                        <input type="text" class="form-control disabled-text" required="required"
                                               ng-model="data.groups[local.current_group].title"
                                               ng-disabled="local.group_edit == false" />
                                    </td>
                                </tr>
                                <tr class="row">
                                    <td class="col-md-3">
                                        <label for="edm_title">标题链接</label>
                                    </td>
                                    <td class="col-md-14">
                                        <input type="text" class="form-control disabled-text"
                                               ng-model="data.groups[local.current_group].title_link"
                                               ng-disabled="local.group_edit == false" />
                                    </td>
                                </tr>
                            </table>
                            <span class="i block-btn" ng-click="toggleGroupEdit()"
                                  ng-class="{ 'i-edit': local.group_edit == false, 'i-save': local.group_edit == true }"
                                  ng-disabled="edm_group_form.$invalid && local.group_edit == true"></span>
                        </form>
                    </div>
                    <div class="row grid-bottom">
                        <label for="product_id" class="col-md-2 text-right">商品ID</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" ng-model="local.product_input" />
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-inverse block-action add" ng-click="addGroupProduct()"
                                    ng-disabled="!local.product_input">
                                添加
                                <span class="i i-refresh refresh-animate" ng-show="local.add_in_progress"></span>
                            </button>
                        </div>
                    </div>
                    <div class="product-edit-container image-group-container">
                        <div class="one-image-container carousel-image grid-bottom"
                             ng-repeat="item in data.groups[local.current_group].group_products" hi-dnd item="item"
                             options="local.dnd.product.options"
                             callback="local.dnd.product.callback( info, dst_index )" data-index="{{ $index }}">
                            <div class="image-holder">
                                <div hi-uploader options="item.options"></div>
                                <span class="image-order" ng-bind="$index + 1"
                                      ng-hide="item.options.is_progressing"></span>
                                <div class="overlay">
                                    <div class="overlay-button i i-share"
                                         ng-click="triggerGroupProductImageChange( item )">
                                        <br /><span class="small-desc">更换图片</span>
                                    </div>
                                    <div class="overlay-button i i-trash" ng-click="deleteGroupProduct( $index )">
                                        <br /><span class="small-desc">删除商品</span>
                                    </div>
                                </div>
                            </div>
                            <div class="image-info" ng-show="item.edit == false">
                                <h3 class="grid-top" ng-bind="item.product_name"></h3>
                                <p ng-bind="item.product_link"></p>
                                <p ng-bind="item.product_description"></p>
                            </div>
                            <div class="image-info" ng-show="item.edit == true">
                                <input class="form-control" ng-model="item.product_name" />
                                <input class="form-control" ng-model="item.product_link" />
                                <textarea class="form-control" ng-model="item.product_description"></textarea>
                            </div>
                            <button class="i toggle-edit" ng-click="toggleGroupProductEdit( $index )"
                                    ng-class="{ 'i-edit' : item.edit == false, 'i-save' : item.edit == true }"
                                    ng-disabled="!item.product_name || !item.product_description"></button>
                            <div class="misc-info row">
                                <span class="pull-left" ng-bind="'门：¥' + item.orig_price"></span>
                                <span class="pull-right" ng-bind="'售：¥' + item.price"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>