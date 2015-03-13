<div id="promotion-edit-container" class="container page-container" ng-controller="PromotionEditCtrl">
<div class="states-section">
    <hi-section-head model="local.section_head" options="local.section_head"></hi-section-head>
    <div class="section-body promotion-cover clearfix" ng-class="local.section_head.getClass()"
         ng-show="local.section_head.is_edit">
        <!--编辑页面-->
        <div class="row grid-bottom">
            <div class="col-md-18">
                <p class="small-desc">头部背景图：PC尺寸为1920x460，手机尺寸为640x580</p>
                <div class="col-md-12">
                    <div hi-uploader options="local.uploader_options.cover"></div>
                </div>
                <div class="col-md-6">
                    <div hi-uploader options="local.uploader_options.mobile"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-18" ng-show="!data.is_hotelplus">
                <form name="promotion_info">
                    <table class="forms-table">
                        <tr>
                            <td class="col-md-4">
                                <label for="promotion_title">活动标题</label>
                            </td>
                            <td class="col-md-14">
                                <input type="text" ng-model="data.base.title" id="promotion_title"
                                       class="form-control" />
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-4">
                                <label for="promotion_description">活动描述</label>
                            </td>
                            <td class="col-md-14">
                                <textarea ng-model="data.base.description" id="promotion_description"
                                          class="form-control" cols="30" rows="4"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-4">
                                <label for="promotion_duration">活动时间</label>
                            </td>
                            <td class="col-md-14">
                                <div class="col-md-9">
                                    <quick-datepicker ng-model='data.rule.start_date' disable-timepicker='true'
                                                      date-format='yyyy-M-d' required="true"></quick-datepicker>
                                </div>
                                <div class="col-md-9">
                                    <quick-datepicker ng-model='data.rule.end_date' disable-timepicker='true'
                                                      date-format='yyyy-M-d' required="true"></quick-datepicker>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-4">
                                <label for="promotion_discount_range">活动范围</label>
                            </td>
                            <td class="col-md-14">
                                <input type="text" ng-model="data.rule.discount_range" id="promotion_discount_range"
                                       class="form-control" required />
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-4">
                                <label for="promotion_discount_rate">活动力度</label>
                            </td>
                            <td class="col-md-14">
                                <input type="text" ng-model="data.rule.discount_rate" id="promotion_discount_rate"
                                       class="form-control" required />
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-4">
                                <label for="promotion_seo_title">活动SEO标题</label>
                            </td>
                            <td class="col-md-14">
                                <input type="text" ng-model="data.seo.title" id="promotion_seo_title"
                                       class="form-control" />
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-4">
                                <label for="promotion_seo_keywords">活动SEO关键词</label>
                            </td>
                            <td class="col-md-14">
                                <input type="text" ng-model="data.seo.keywords" id="promotion_seo_keywords"
                                       class="form-control" />
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-4">
                                <label for="promotion_seo_description">活动SEO描述</label>
                            </td>
                            <td class="col-md-14">
                                <textarea ng-model="data.seo.description" id="promotion_seo_description"
                                          class="form-control" cols="30" rows="4"></textarea>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="col-md-18" ng-show="data.is_hotelplus">
                <form name="hotelplus_info">
                    <table class="forms-table">
                        <tr>
                            <td class="col-md-4">
                                <label for="promotion_seo_title">页面SEO标题</label>
                            </td>
                            <td class="col-md-14">
                                <input type="text" ng-model="data.seo.title" id="promotion_seo_title" class="form-control" />
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-4">
                                <label for="promotion_seo_keywords">页面SEO关键词</label>
                            </td>
                            <td class="col-md-14">
                                <input type="text" ng-model="data.seo.keywords" id="promotion_seo_keywords"  class="form-control" />
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-4">
                                <label for="promotion_seo_description">页面SEO描述</label>
                            </td>
                            <td class="col-md-14">
                                <textarea ng-model="data.seo.description" id="promotion_seo_description" class="form-control" cols="30" rows="4"></textarea>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div class="section-body promotion-cover clearfix" ng-class="local.section_head.getClass()"
         ng-hide="local.section_head.is_edit">
        <div class="col-md-2 col-md-offset-16 text-right">
            <button class="btn btn-primary" ng-click="viewCity()">查看挂接城市</button>
        </div>
        <!--查看页面-->
        <div class="row grid-bottom">
            <div class="col-md-12 cover">
                <img ng-src="{{ local.uploader_options.cover.image_url }}" class="banner-img grid-bottom" />
            </div>
            <div class="col-md-6 mobile">
                <img ng-src="{{ local.uploader_options.mobile.image_url }}" class="banner-img grid-bottom" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-18" ng-show="!data.is_hotelplus">
                <h4>活动基本信息</h4>
                <table class="forms-table">
                    <tr>
                        <td class="view-title col-md-4">PC端链接</td>
                        <td class="view-body col-md-14" ng-bind="data.preview.desktop"></td>
                    </tr>
                    <tr>
                        <td class="view-title col-md-4">手机端链接</td>
                        <td class="view-body col-md-14" ng-bind="data.preview.mobile"></td>
                    </tr>
                    <tr>
                        <td class="view-title col-md-4">活动标题</td>
                        <td class="view-body col-md-14" ng-bind="data.base.title"></td>
                    </tr>
                    <tr>
                        <td class="view-title col-md-4">活动描述</td>
                        <td class="view-body col-md-14" ng-bind="data.base.description"></td>
                    </tr>
                    <tr>
                        <td class="view-title col-md-4">活动时间</td>
                        <td class="view-body col-md-14"
                            ng-bind="data.rule.start_date + ' － ' + data.rule.end_date"></td>
                    </tr>
                    <tr>
                        <td class="view-title col-md-4">活动范围</td>
                        <td class="view-body col-md-14" ng-bind="data.rule.discount_range"></td>
                    </tr>
                    <tr>
                        <td class="view-title col-md-4">活动力度</td>
                        <td class="view-body col-md-14" ng-bind="data.rule.discount_rate"></td>
                    </tr>
                    <tr>
                        <td class="view-title col-md-4">活动SEO标题</td>
                        <td class="view-body col-md-14" ng-bind="data.seo.title"></td>
                    </tr>
                    <tr>
                        <td class="view-title col-md-4">活动SEO关键词</td>
                        <td class="view-body col-md-14" ng-bind="data.seo.keywords"></td>
                    </tr>
                    <tr>
                        <td class="view-title col-md-4">活动SEO描述</td>
                        <td class="view-body col-md-14" ng-bind="data.seo.description"></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-18" ng-show="data.is_hotelplus">
                <h4>基本信息</h4>
                <table class="forms-table">
                    <tr>
                        <td class="view-title col-md-4">PC端链接</td>
                        <td class="view-body col-md-14" ng-bind="data.preview.desktop"></td>
                    </tr>
                    <tr>
                        <td class="view-title col-md-4">手机端链接</td>
                        <td class="view-body col-md-14" ng-bind="data.preview.mobile"></td>
                    </tr>
                    <tr>
                        <td class="view-title col-md-4">页面SEO标题</td>
                        <td class="view-body col-md-14" ng-bind="data.seo.title"></td>
                    </tr>
                    <tr>
                        <td class="view-title col-md-4">页面SEO关键词</td>
                        <td class="view-body col-md-14" ng-bind="data.seo.keywords"></td>
                    </tr>
                    <tr>
                        <td class="view-title col-md-4">页面SEO描述</td>
                        <td class="view-body col-md-14" ng-bind="data.seo.description"></td>
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
                    <div class="group-name" ng-bind="group.name"></div>
                    <p class="small-desc">此分组含<span ng-bind="group.promotion_product.length"></span>个商品</p>
                </div>
            </div>
            <div class="group-edit-container col-md-12" ng-show="local.current_group > -1">
                <div class="group-edit-info one-block grid-bottom row">
                    <form name="promotion_group_form">
                        <table class="forms-table">
                            <tr>
                                <td class="col-md-6">
                                    <label for="group_title">分组标题</label>
                                </td>
                                <td class="col-md-12">
                                    <input type="text" id="group_title" class="form-control disabled-text"
                                           required="required" ng-model="data.groups[local.current_group].name"
                                           ng-disabled="local.group_edit == false" />
                                </td>
                            </tr>
                            <tr>
                                <td class="col-md-6">
                                    <label for="group_link">分组链接</label>
                                </td>
                                <td class="col-md-12">
                                    <input type="text" id="group_link" class="form-control disabled-text"
                                           ng-model="data.groups[local.current_group].attach_url"
                                           ng-disabled="local.group_edit == false" />
                                </td>
                            </tr>
                            <tr>
                                <td class="col-md-6">
                                    <label for="group_description">分组描述</label>
                                </td>
                                <td class="col-md-12">
                                    <input type="text" id="group_description" class="form-control disabled-text"
                                           ng-model="data.groups[local.current_group].description"
                                           ng-disabled="local.group_edit == false" />
                                </td>
                            </tr>
                        </table>
                        <span class="i block-btn" ng-click="toggleGroupEdit()" ng-class="{ 'i-edit': local.group_edit == false, 'i-save': local.group_edit == true }" ng-disabled="promotion_group_form.$invalid && local.group_edit == true"></span>
                    </form>
                </div>
                <div class="row grid-bottom">
                    <label for="product_id" class="col-md-2 text-right">商品ID</label>
                    <div class="col-md-6" ng-show="!data.hotelplus">
                        <input type="text" class="form-control" ng-model="local.product_input" />
                    </div>
                    <div class="col-md-6" ng-show="data.hotelplus">
                        <select style="width: 100%;" chosen ng-model="local.product_input" ng-options="p.product_id as (p.product_id + ' ' + p.name) for p in data.hotelplus.products"></select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-inverse block-action add" ng-click="addGroupProduct()" ng-disabled="!data.hotelplus && !local.product_input">
                            添加
                            <span class="i i-refresh refresh-animate" ng-show="local.add_in_progress"></span>
                        </button>
                    </div>
                </div>
                <div class="product-edit-container image-group-container">
                    <div class="one-image-container carousel-image grid-bottom"
                         ng-repeat="item in data.groups[local.current_group].promotion_product" hi-dnd item="item"
                         options="local.dnd.product.options"
                         callback="local.dnd.product.callback( info, dst_index )" data-index="{{ $index }}">
                        <div class="image-holder">
                            <div class="upload-holder">
                                <div class="result-screen">
                                    <img ng-src="{{ item.cover_image.image_url }}?imageView2/5/w/580/h/250">
                                </div>
                            </div>
                            <span class="image-order" ng-bind="$index + 1" ng-hide="item.options.is_progressing"></span>
                            <div class="overlay">
                                <div class="overlay-button i i-trash" ng-click="deleteGroupProduct( $index )">
                                    <br /><span class="small-desc">删除商品</span>
                                </div>
                            </div>
                        </div>
                        <div class="image-info">
                            <h3 class="grid-top" ng-bind="item.description.name"></h3>
                            <p ng-bind="item.description.summary"></p>
                        </div>
                        <div class="misc-info row">
                            <span class="pull-left" ng-bind="'门：¥' + item.show_prices.orig_price"></span>
                            <span class="pull-right" ng-bind="'售：¥' + item.show_prices.price"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>