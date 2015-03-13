<div class="states-section" ng-show="local.current_menu == '1'">
    <hi-section-head model="local.section_head" options="local.section_head"></hi-section-head>
    <div class="section-body country-cover row" ng-class="local.section_head.getClass()"
         ng-show="local.section_head.is_edit">
        <p class="small-desc">国家封面图尺寸为1920x235</p>
        <div class="col-md-18 grid-bottom">
            <p class="small-desc">国家横幅大图</p>
            <div hi-uploader options="local.uploader_options.country_cover"></div>
        </div>
        <div class="col-md-4">
            <p class="small-desc">国家手机小图</p>
            <div hi-uploader options="local.uploader_options.country_mobile"></div>
        </div>
        <div class="col-md-14">
            <form name="country_seo">
                <table class="forms-table">
                    <tr>
                        <td><h4>国家SEO信息</h4></td>
                    </tr>
                    <tr>
                        <td class="col-md-3">
                            <label for="country_title">国家页面标题</label>
                        </td>
                        <td>
                            <input type="text" ng-model="data.seo.title" id="country_title" class="form-control"
                                   required />
                        </td>
                    </tr>
                    <tr>
                        <td class="col-md-3">
                            <label for="country_description">国家页面描述</label>
                        </td>
                        <td>
                            <input type="text" ng-model="data.seo.description" id="country_description"
                                   class="form-control" required />
                        </td>
                    </tr>
                    <tr>
                        <td class="col-md-3">
                            <label for="country_keywords">国家页面关键词</label>
                        </td>
                        <td>
                            <input type="text" ng-model="data.seo.keywords" id="country_keywords" class="form-control"
                                   required />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <div class="section-body country-cover row" ng-class="local.section_head.getClass()"
         ng-hide="local.section_head.is_edit">
        <div class="col-md-18">
            <p class="small-desc">国家横幅大图</p>
            <img ng-src="{{ local.uploader_options.country_cover.image_url }}" class="country-banner grid-bottom" />
        </div>
        <div class="col-md-4">
            <p class="small-desc">国家手机小图</p>
            <img ng-src="{{ local.uploader_options.country_mobile.image_url }}" class="cover-img" />
        </div>
        <div class="col-md-14">
            <h4>国家SEO信息</h4>
            <table class="forms-table">
                <tr>
                    <td class="view-title col-md-3">国家页面标题</td>
                    <td ng-bind="data.seo.title" class="view-body"></td>
                </tr>
                <tr>
                    <td class="view-title col-md-3">国家页面描述</td>
                    <td ng-bind="data.seo.description" class="view-body"></td>
                </tr>
                <tr>
                    <td class="view-title col-md-3">国家页面关键词</td>
                    <td ng-bind="data.seo.keywords" class="view-body"></td>
                </tr>
            </table>
        </div>
    </div>
</div>