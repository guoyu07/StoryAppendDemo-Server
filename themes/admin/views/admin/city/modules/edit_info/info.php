<div class="states-section" ng-show="local.current_menu == '1'">
    <hi-section-head options="local.section_head.basic_info"></hi-section-head>
    <div class="section-body city-cover clearfix" ng-class="local.section_head.basic_info.getClass()"
         ng-show="local.section_head.basic_info.is_edit">
        <p class="small-desc grid-bottom">城市横幅大图：尺寸为1920x235；城市方形小图：尺寸为235x212（桌面站），230x288（桌面站）, 570x415（手机站）</p>
        <div class="row grid-bottom">
            <div class="col-md-18">
                <p class="small-desc">城市横幅大图</p>
                <div hi-uploader options="local.uploader_options.city_banner"></div>
            </div>
        </div>
        <div class="row grid-bottom">
            <div class="col-md-4">
                <p class="small-desc">城市方形小图</p>
                <div hi-uploader options="local.uploader_options.city_cover"></div>
            </div>
            <div class="col-md-14">
                <form name="city_seo">
                    <table class="forms-table">
                        <tr>
                            <td class="col-md-4">
                                <label for="city_title">城市页面标题</label>
                            </td>
                            <td>
                                <input type="text" ng-model="data.seo.title" id="city_title" class="form-control"
                                       required />
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-4">
                                <label for="city_description">城市页面描述</label>
                            </td>
                            <td>
                                <input type="text" ng-model="data.seo.description" id="city_description"
                                       class="form-control" required />
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-4">
                                <label for="city_keywords">城市页面关键词</label>
                            </td>
                            <td>
                                <input type="text" ng-model="data.seo.keywords" id="city_keywords" class="form-control"
                                       required />
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <div class="row grid-bottom">
            <div class="col-md-9">
                <p class="small-desc">城市头图(1242x922)</p>
                <div hi-uploader options="local.uploader_options.city_cover_app"></div>
            </div>
            <div class="col-md-9">
                <p class="small-desc">全部商品背景图(1194x232)</p>
                <div hi-uploader options="local.uploader_options.city_cover_strip"></div>
            </div>
        </div>
    </div>
    <div class="section-body city-cover clearfix" ng-class="local.section_head.basic_info.getClass()"
         ng-hide="local.section_head.basic_info.is_edit">
        <div class="col-md-18">
            <p class="small-desc">城市横幅大图</p>
            <img ng-src="{{ local.uploader_options.city_banner.image_url }}" class="banner-img grid-bottom" />
        </div>
        <div class="col-md-4">
            <p class="small-desc">城市方形小图</p>
            <img ng-src="{{ local.uploader_options.city_cover.image_url }}" class="cover-img" />
        </div>
        <div class="col-md-14">
            <h4>城市SEO信息</h4>
            <table class="forms-table">
                <tr>
                    <td class="view-title col-md-4">城市页面标题</td>
                    <td class="view-body" ng-bind="data.seo.title"></td>
                </tr>
                <tr>
                    <td class="view-title col-md-4">城市页面描述</td>
                    <td class="view-body" ng-bind="data.seo.description"></td>
                </tr>
                <tr>
                    <td class="view-title col-md-4">城市页面关键词</td>
                    <td class="view-body" ng-bind="data.seo.keywords"></td>
                </tr>
            </table>
        </div>
        <h4 class="col-md-4">APP相关图片</h4>
        <div class="col-md-18">
            <div class="col-md-9">
                <p class="small-desc">城市头图</p>
                <img ng-src="{{ local.uploader_options.city_cover_app.image_url }}" class="cover-img-app" />
            </div>
            <div class="col-md-9">
                <p class="small-desc">全部商品背景图</p>
                <img ng-src="{{ local.uploader_options.city_cover_strip.image_url }}" class="cover-img-strip" />
            </div>
        </div>

    </div>
</div>