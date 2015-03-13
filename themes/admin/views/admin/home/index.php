<div id="home-carousel-container" class="container page-container" ng-controller="HomeCarouselCtrl">
    <div class="states-section row">
        <hi-section-head model="local.section_head" options="local.section_head"></hi-section-head>
        <div class="section-body" ng-class="local.section_head.getClass()" ng-show="local.section_head.is_edit">
            <form name="home_seo">
                <table class="forms-table">
                    <tr>
                        <td>
                            <label for="home_title">首页页面标题</label>
                        </td>
                        <td>
                            <input type="text" ng-model="data.seo.title" id="home_title" class="form-control"
                                   required />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="home_description">首页页面描述</label>
                        </td>
                        <td>
                            <input type="text" ng-model="data.seo.description" id="home_description"
                                   class="form-control" required />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="city_keywords">首页页面关键词</label>
                        </td>
                        <td>
                            <input type="text" ng-model="data.seo.keywords" id="home_keywords" class="form-control"
                                   required />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="section-body" ng-class="local.section_head.getClass()" ng-hide="local.section_head.is_edit">
            <table class="forms-table">
                <tr>
                    <td class="view-title">首页页面标题</td>
                    <td class="view-body" ng-bind="data.seo.title"></td>
                </tr>
                <tr>
                    <td class="view-title">首页页面描述</td>
                    <td class="view-body" ng-bind="data.seo.description"></td>
                </tr>
                <tr>
                    <td class="view-title">首页页面关键词</td>
                    <td class="view-body" ng-bind="data.seo.keywords"></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="states-section row">
        <div class="section-head">
            <h2 class="section-title">首页轮播图</h2>
        </div>
        <div class="section-body index-image image-group-container">
            <p class="small-desc">首页轮播图的尺寸为1920x480</p>
            <div class="section-subtitle grid-top">新增轮播图
                <button class="btn btn-inverse block-action add" ng-click="addImage()">新增</button>
            </div>
            <div class="section-subbody">
                <div class="one-image-container carousel-image col-md-9 grid-bottom"
                     ng-repeat="image in data.images" data-index="{{ $index }}"
                     hi-dnd item="image" callback="local.home_image_dnd.callback(info, dst_index)"
                     options="local.home_image_dnd.options">
                    <div class="image-holder">
                        <div hi-uploader options="image.options"></div>
                        <span class="image-order" data-ng-bind="$index + 1"></span>
                        <span class="triangle"></span>
                        <div class="overlay">
                            <div class="overlay-button i i-share" ng-click="changeImage( $index )">
                                <br />更换图片
                            </div>
                            <div class="overlay-button i i-trash" ng-click="deleteImage( $index )">
                                <br />删除图片
                            </div>
                        </div>
                    </div>
                    <div class="image-info" ng-show="image.editing == false">
                        <p>链接：<span ng-bind="image.link_url"></span></p>
                    </div>
                    <div class="image-info" ng-show="image.editing == true">
                        <input class="image-title form-control" ng-model="image.link_url" />
                    </div>
                    <span class="i toggle-edit"
                          ng-class="{ 'i-edit' : image.editing == false, 'i-save' : image.editing == true }"
                          ng-click="toggleImageState( $index )"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="states-section row">
        <div class="section-head">
            <h2 class="section-title">首页分组</h2>
        </div>
        <div class="section-body">
            <p class="small-desc">分组图片的尺寸为230x288</p>
            <div class="section-subtitle">新增分组
                <button class="btn btn-inverse block-action add" ng-click="addGroup()">新增</button>
            </div>
            <div class="section-subbody">
                <div class="row">
                    <div ng-repeat="group in data.groups" class="one-block home-group grid-bottom"
                         ng-init="groupIndex = $index" data-index="{{ $index }}"
                         hi-dnd item="group" callback="local.group_dnd.callback(info, dst_index)"
                         options="local.group_dnd.options">
                        <div class="delete-block" ng-click="deleteGroup( $index )">
                            <span class="i i-close"></span>
                        </div>
                        <div class="col-md-4 cover">
                            <img ng-src="{{ group.cover_url }}" ng-click="editGroup( $index )" />
                            <span class="ordering" ng-bind="$index + 1"></span>
                        </div>
                        <div class="col-md-9 group-info" ng-click="editGroup( $index )">
                            <p ng-bind="group.name"></p>
                            <p class="small-desc">
                                此分类包含 <span ng-bind="group.items_count"></span> 个点
                            </p>
                        </div>
                        <div class="col-md-5 status">
                            <div class="btn-group">
                                <button class="btn dropdown-toggle" data-toggle="dropdown"
                                        ng-class="{ 'btn-default' : group.status == '1', 'btn-inverse' : group.status == '2' }">
                                    {{ getGroupState( group.status ) }} <span class="caret"></span>
                                </button>
                                <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                                <ul class="dropdown-menu dropdown-inverse">
                                    <li ng-repeat="status in local.group_status">
                                        <a ng-click="changeState( groupIndex, status.id )" ng-bind="status.name">{</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="states-section row">
        <div class="section-head">
            <h2 class="section-title">热门国家</h2>
        </div>
        <div class="section-body">
            <table class="forms-table">
                <tr>
                    <td class="col-md-2">
                        <label>亚洲</label>
                    </td>
                    <td class="col-md-14">
                        <hi-select-tag options="local.select_tag_1" select="local.country_list.asia" model="data.hotCountry[1]"></hi-select-tag>
                    </td>
                </tr>
                <tr>
                    <td class="col-md-2">
                        <label>欧洲</label>
                    </td>
                    <td class="col-md-14">
                        <hi-select-tag options="local.select_tag_2" select="local.country_list.europe" model="data.hotCountry[2]"></hi-select-tag>
                    </td>
                </tr>
                <tr>
                    <td class="col-md-2">
                        <label>非洲</label>
                    </td>
                    <td class="col-md-14">
                        <hi-select-tag options="local.select_tag_3" select="local.country_list.africa" model="data.hotCountry[3]"></hi-select-tag>
                    </td>
                </tr>
                <tr>
                    <td class="col-md-2">
                        <label>北美</label>
                    </td>
                    <td class="col-md-14">
                        <hi-select-tag options="local.select_tag_4" select="local.country_list.na" model="data.hotCountry[4]"></hi-select-tag>
                    </td>
                </tr>
                <tr>
                    <td class="col-md-2">
                        <label>南美</label>
                    </td>
                    <td class="col-md-14">
                        <hi-select-tag options="local.select_tag_5" select="local.country_list.sa" model="data.hotCountry[5]"></hi-select-tag>
                    </td>
                </tr>
                <tr>
                    <td class="col-md-2">
                        <label>大洋洲</label>
                    </td>
                    <td class="col-md-14">
                        <hi-select-tag options="local.select_tag_6" select="local.country_list.oceania" model="data.hotCountry[6]"></hi-select-tag>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>