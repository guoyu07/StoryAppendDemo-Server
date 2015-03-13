<div id="article-edit-container" ng-controller="ArticleEditCtrl">
    <hi-uploader class="picture-container" options="local.uploader_options.article_head"></hi-uploader>
    <div class="title-introduction-container">
        <div class="article-options clearfix">
            <ul class="nav nav-tabs" style="position: absolute;width: 56%">
                <li ng-class="{ active: $index == local.current_tab_index }"
                    ng-repeat="tab in local.tabs"
                    ng-click="switchTab($index)">
                    <a href="javascript:void(0);" ng-bind="tab"></a>
                </li>
            </ul>
            <select
                chosen
                class="article-select"
                style="width: 150px;"
                ng-model="data.status"
                ng-options="status.value as status.label for status in local.article_status"
                data-placeholder="点击选择状态"
                no-results-text="'没有找到'"
                ng-change="updateArticleStatus()"
                >
            </select>
            <select
                chosen
                class="article-select article-city"
                style="width: 150px;"
                ng-model="data.city_code"
                ng-options="city.city_code as (city.city_name + ' ' + city.city_pinyin) group by city.group for city in local.cities"
                data-placeholder="点击选择城市"
                no-results-text="'没有找到'"
                ng-change="updateArticleHead()"
                >
            </select>
        </div>
        <input type="text" class="title-add" ng-blur="updateArticleHead()" ng-disabled="notAllowEdit()"
               ng-model="data.title" ng-show="local.current_tab_index == 0" placeholder="写标题">
        <textarea class="introduction-add" ng-blur="updateArticleHead()" ng-disabled="notAllowEdit()"
                  ng-model="data.brief" ng-show="local.current_tab_index == 0" placeholder="写引言" hi-no-break hi-elastic></textarea>
    </div>
    <div ng-show="local.current_tab_index == 0">
        <div class="sections-wrapper">
            <div class="ng-hide">
                <hi-uploader options="local.uploader_options.article_img"></hi-uploader>
            </div>
            <div class="add-section">
                <button class="action-add" ng-click="addSection(-1)">＋ 段落</button>
            </div>
            <section class="one-section" ng-repeat="section in data.sections" ng-mouseenter="toggleHover($event)"
                     ng-mouseleave="toggleHover($event)">
                <div class="section-container">
                    <div class="item-wrapper header">
                        <header class="section-header">
                            <input type="text" class="section-title" ng-model="section.section_title" placeholder="写标题"
                                   ng-blur="updateSectionTitle($event, $index)" ng-disabled="notAllowEdit()"
                                   ng-click="toggleFocus($event)" />
                            <div class="delete-section" ng-click="deleteSection($index)">
                                <span class="i i-trash"></span>
                            </div>
                            <div class="add-content-container">
                                <span class="i i-plus-circle"></span>
                                <span class="one-content-type" ng-click="addContent(1, $index, -1)">文字</span>
                                <span class="one-content-type" ng-click="addContent(4, $index, -1)">引言</span>
                                <span class="one-content-type" ng-click="addContent(2, $index, -1)">图片</span>
                                <span class="one-content-type" ng-click="addContent(3, $index, -1)">商品</span>
                            </div>
                        </header>
                    </div>
                    <article class="section-body">
                        <div class="item-wrapper content" ng-repeat="content in section.items"
                             ng-mouseenter="toggleHover($event)" ng-mouseleave="toggleHover($event)">
                            <div class="section-content">
                                <textarea class="text" placeholder="写文字"
                                          ng-model="content.text_content" hi-no-break hi-elastic
                                          ng-blur="updateSectionContent($event, $parent.$index, $index)"
                                          ng-disabled="notAllowEdit()" ng-show="content.type == 1"
                                          ng-click="toggleFocus($event)"></textarea>
                                <textarea class="text" placeholder="写引言"
                                          ng-model="content.text_content" hi-no-break hi-elastic
                                          ng-blur="updateSectionContent($event, $parent.$index, $index)"
                                          ng-disabled="notAllowEdit()" ng-show="content.type == 4"
                                          ng-click="toggleFocus($event)"></textarea>
                                <div class="image" ng-show="content.type == '2'" ng-click="toggleFocus($event)">
                                    <img src="{{content.image_url}}?imageView/5/w/640/h/327"
                                         ng-click="editContent($parent.$index, $index)" />
                                    <div class="image-title" ng-bind="content.image_title"></div>
                                    <div class="image-desc" ng-bind="content.image_description"></div>
                                </div>
                                <div class="product" ng-show="content.type == '3'" ng-click="toggleFocus($event)">
                                    <div class="title" ng-bind="content.product_title"></div>
                                    <div ng-click="editContent($parent.$index, $index)">
                                        <div ng-include="'product-card.html'"></div>
                                    </div>
                                    <p class="desc" ng-bind="content.product_description"></p>
                                </div>
                                <div class="delete-section" ng-click="deleteContent($parent.$index, $index)">
                                    <span class="i i-trash"></span>
                                </div>
                                <div class="add-content-container">
                                    <span class="i i-plus-circle"></span>
                                <span class="one-content-type"
                                      ng-click="addContent(1, $parent.$index, $index)">文字</span>
                                <span class="one-content-type"
                                      ng-click="addContent(4, $parent.$index, $index)">引言</span>
                                <span class="one-content-type"
                                      ng-click="addContent(2, $parent.$index, $index)">图片</span>
                                <span class="one-content-type"
                                      ng-click="addContent(3, $parent.$index, $index)">商品</span>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="add-section">
                    <button class="action-add" ng-click="addSection($index)">＋ 段落</button>
                </div>
            </section>
        </div>

        <div class="overlay" ng-show="local.current.dialog && !notAllowEdit()">
            <div class="loading-container" ng-show="local.current.dialog == '4'">
                <span class="i i-refresh refresh-animate"></span>
                <label>loading...</label>
            </div>
            <div id="product-binding-dialog" class="article-dialog notify-container row"
                 ng-show="local.current.dialog == '3'">
                <div class="dialog-header clearfix">
                    <label class="col-md-4 col-md-offset-7">编辑商品</label>
                </div>
                <div class="dialog-content clearfix">
                    <div class="col-md-3 content-info">
                        <label>商品ID：</label>
                    </div>
                    <div class="col-md-15 content-info">
                        <div class="pull-left">
                            <input class="title-editor" type="text" ng-model="content.product_id" />
                        </div>
                        <div class="pull-left">
                            <button class="search-product-btn" ng-click="updateProduct()">GO</button>
                        </div>
                    </div>
                    <div class="col-md-3 content-info">
                        <label>标题：</label>
                    </div>
                    <div class="col-md-15 content-info">
                        <input class="title-editor" type="text" ng-model="content.product_title" />
                    </div>
                    <div class="col-md-18">
                        <div ng-include="'product-card.html'"></div>
                    </div>
                    <div class="col-md-18 content-info">
                        <textarea placeholder="写描述" class="summary-editor"
                                  ng-model="content.product_description"></textarea>
                    </div>
                </div>
                <div class="dialog-footer clearfix">
                    <div class="col-md-9 button-block cancel-button" ng-click="confirmContent(false)">
                        取消
                    </div>
                    <div class="col-md-9 button-block button-last" ng-click="confirmContent(true)">
                        确定
                    </div>
                </div>
            </div>

            <div id="image-upload-dialog" class="article-dialog notify-container row" ng-show="local.current.dialog == '2'">
                <div class="dialog-header clearfix">
                    <label class="col-md-5 col-md-offset-7">添加图片(650x312)</label>
                </div>
                <div class="dialog-content clearfix">
                    <div class="content-info">
                        <hi-uploader class="image-upload-container"
                                     options="local.uploader_options.article_img"></hi-uploader>
                    </div>
                    <div class="content-info">
                        <input type="text" ng-model="content.image_title" placeholder="标题……" class="title-editor" />
                    </div>
                    <div class="content-info">
                        <textarea placeholder="写描述" ng-model="content.image_description" class="summary-editor"></textarea>
                    </div>
                </div>
                <div class="dialog-footer clearfix">
                    <div class="col-md-9 button-block cancel-button" ng-click="confirmContent(false)">
                        取消
                    </div>
                    <div class="col-md-9 button-block button-last" ng-click="confirmContent(true)">
                        确定
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div ng-show="local.current_tab_index == 1">
        <div class="sections-wrapper seo-container">
            <div class="row seo-pad">
                <span class="col-md-4 seo-left">分组ID</span>
                <input type="text" class="group-link seo-text" ng-blur="updateArticleHead()" ng-disabled="notAllowEdit()"
                       ng-model="data.link_to" placeholder="请输入商品分组ID">
            </div>
            <div class="row seo-pad">
                <span class="col-md-4 seo-left">页面描述</span>
                <textarea class="form-control seo-text" placeholder="请输入页面描述"
                          ng-model="data.seo.description" hi-no-break hi-elastic
                          ng-blur="updateArticleHead()"
                          ng-disabled="notAllowEdit()"></textarea>
            </div>
            <div class="row seo-pad">
                <span class="col-md-4 seo-left">页面关键词</span>
                <textarea class="form-control seo-text" placeholder="请输入页面关键词"
                          ng-model="data.seo.keywords" hi-no-break hi-elastic
                          ng-blur="updateArticleHead()"
                          ng-disabled="notAllowEdit()"></textarea>
            </div>
            <div class="row seo-pad">
                <span class="col-md-4 seo-left">页面标题</span>
                <textarea class="form-control seo-text" placeholder="请输入页面标题"
                          ng-model="data.seo.title" hi-no-break hi-elastic
                          ng-blur="updateArticleHead()"
                          ng-disabled="notAllowEdit()"></textarea>
            </div>
        </div>
    </div>
</div>

<script type="text/ng-template" id="product-card.html">
    <div class="product-card-container clearfix">
        <div class="col-md-3 img-container">
            <img class="product-img" src="{{content.product_detail.cover_image.image_url}}?imageView/5/w/120/h/110" />
        </div>
        <div class="col-md-14 col-md-offset-1">
            <div class="row">
                <label class="col-md-15 product-title" ng-bind="content.product_detail.description.name"></label>
                <span class="product-price" ng-bind="'¥' + content.product_detail.show_prices.price"></span>
            </div>
            <div class="row clearfix">
                <div class="rating-stars-container">
                    <i class="i i-rating-star score-rating-stars"
                       ng-style="{ 'width' : (content.product_detail.comment_stat.total * 20) + '%' }"></i>
                    <i class="i i-rating-star basic-rating-stars"></i>
                </div>
            </div>
        </div>
        <a class="more-info" href="{{local.view_product_link}}{{content.product_detail.product_id}}" target="_blank">
            了解更多 >
        </a>
    </div>
</script>