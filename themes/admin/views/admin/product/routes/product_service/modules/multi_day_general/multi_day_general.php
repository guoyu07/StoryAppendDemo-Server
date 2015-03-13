<div class="states-section" ng-if="local.tab_options.current_tab.path == 'introduce_multi_day_general'">
<div ng-controller="ServiceIntroduceMultiDayGeneralCtrl">
<hi-section-head options="local.section_head.recommend"></hi-section-head>

<div class="multi-day-contents">

    <!--编辑态-->
    <div class="section-body clearfix"
         ng-show="local.section_head.recommend.is_edit">
        <table class="forms-table multi-day-table">
            <tr>
                <td class="view-title">状态</td>
                <td class="view-body">
                    <hi-radio-switch options="local.radio_options.trip_introduction_status" model="data.introduce"></hi-radio-switch>
                </td>
            </tr>
            <tr>
                <td class="view-title">推荐人</td>
                <td class="view-body">
                    <input type="text" class="form-control" ng-model="data.introduce.brief_author" />
                </td>
            </tr>
            <tr>
                <td class="view-title img-container">头像</td>
                <td class="view-body img-comments">
                    <div class="avatar_container">
                        <div hi-uploader options="local.uploader_options.avatar_img"></div>
                    </div>
                    请上传124*124的图片
                </td>
            </tr>
            <tr>
                <td class="view-title">推荐标题</td>
                <td class="view-body">
                    <input type="text" class="form-control" ng-model="data.introduce.brief_title" />
                </td>
            </tr>
            <tr>
                <td class="view-title">推荐内容</td>
                <td class="view-body">
                    <textarea class="form-control recommend-content"
                              ng-model="data.introduce.brief_description"></textarea>
                </td>
            </tr>
        </table>
    </div>
    <!--显示态-->
    <div class="section-body clearfix"
         ng-hide="local.section_head.recommend.is_edit">
        <table class="forms-table multi-day-table">
            <tr>
                <td class="view-title">状态</td>
                <td class="view-body">{{ data.introduce.status == 0 ? "编辑中" : "已生效" }}
                </td>
            </tr>
            <tr>
                <td class="view-title">推荐人</td>
                <td class="view-body" ng-bind="data.introduce.brief_author"></td>
            </tr>
            <tr>
                <td class="view-title img-container">头像</td>
                <td class="view-body img-comments">
                    <img class="avatar_img" data-ng-src="{{data.introduce.brief_avatar}}?imageView/5/w/124/h/124" />
                    124*124
                </td>
            </tr>
            <tr>
                <td class="view-title">推荐标题</td>
                <td class="view-body" ng-bind="data.introduce.brief_title"></td>
            </tr>
            <tr>
                <td class="view-title">推荐内容</td>
                <td class="view-body" ng-bind="data.introduce.brief_description"></td>
            </tr>
        </table>
    </div>
</div>

<div class="section-head">
    <h2 class="section-title">行程地图</h2>
</div>
<div class="section-body clearfix">
    <div class="section-subtitle">PC行程地图</div>
    <div class="section-subbody">
        <div class="multi-day-contents clearfix">
            <div class="brief-image">
                <div hi-uploader options="local.uploader_options.brief_img"></div>
            </div>
            <label class="img-comments">1000*400</label>
        </div>
    </div>

    <div class="section-subtitle">Mobile行程地图</div>
    <div class="section-subbody">
        <div class="multi-day-contents clearfix">
            <div class="brief-image-mobile">
                <div hi-uploader options="local.uploader_options.brief_img_mobile"></div>
            </div>
            <label class="img-comments">768*416</label>
        </div>
    </div>

    <div class="section-subtitle">线路图</div>
    <div class="section-subbody">
        <div class="multi-day-contents clearfix">
            <div class="line-image-mobile">
                <div hi-uploader options="local.uploader_options.line_image"></div>
            </div>
            <label class="img-comments">230*200</label>
        </div>
    </div>
</div>

<div class="section-head">
    <h2 class="section-title">行程详情导入</h2>
</div>

<div class="multi-day-contents clearfix">
    <div class="intro-image">
        <div hi-uploader options="local.uploader_options.intro_image"></div>
    </div>
    <label class="img-comments">1000*254</label>
</div>

<hi-section-head options="local.section_head.multi_day_trip_general"></hi-section-head>

<div ng-hide="local.section_head.multi_day_trip_general.is_edit">
    <div class="section-body clearfix">
        <table class="forms-table high-light-summary-table">
            <tr>
                <td class="view-title">天数</td>
                <td class="view-body">
                    {{data.general_highlight.total_days}}天
                </td>
                <td class="view-title">里程数</td>
                <td class="view-body">
                    {{data.general_highlight.distance}}公里
                </td>
            </tr>
            <tr>
                <td class="view-title">起点</td>
                <td class="view-body" ng-bind="data.general_highlight.start_location"></td>
                <td class="view-title">终点</td>
                <td class="view-body" ng-bind="data.general_highlight.finish_location"></td>
            </tr>
            <tr>
                <td class="view-title">亮点</td>
                <td class="view-body">
                    <div ng-repeat="summary in data.general_highlight.highlight_summary track by $index">
                        <div ng-bind="summary"></div>
                    </div>
                </td>
                <td class="view-title">最佳旅游时间</td>
                <td class="view-body" ng-bind="data.general_highlight.suitable_time"></td>
            </tr>
            <tr>
                <td class="view-title">途经城市</td>
                <td class="view-body">
                    <div ng-repeat="city in data.general_highlight.tour_cities track by $index">
                        <div ng-bind="city.city_name"></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="grey-line"></div>
    <div class="section-body clearfix">
        <table class="highlight-list-table">
            <tr>
                <th class="first-column">
                    时间
                </th>
                <th class="second-column">
                    地点
                </th>
                <th class="third-column">
                    行程亮点
                </th>
                <th class="fourth-column">
                    住宿
                </th>
            </tr>
            <tr ng-repeat="highlight in data.general_highlight.highlight_refs">
                <td>
                    D{{highlight.date}}
                </td>
                <td>
                    {{highlight.location}}
                </td>
                <td>
                    <div class="left-text" ng-repeat="item in highlight.display_highlight">
                        {{item}}
                    </div>
                </td>
                <td>
                    {{highlight.lodging}}
                </td>
            </tr>
        </table>
    </div>
</div>

<div ng-show="local.section_head.multi_day_trip_general.is_edit">
    <div class="section-body clearfix">
        <table class="forms-table high-light-summary-table">
            <tr>
                <td class="view-title">天数</td>
                <td class="view-body">
                    <input type="text" class="form-control" ng-model="data.general_highlight.total_days" />
                </td>
                <td class="view-title"></td>
                <td class="view-title">里程数</td>
                <td class="view-body">
                    <input type="text" class="form-control" ng-model="data.general_highlight.distance" />
                </td>
            </tr>
            <tr>
                <td class="view-title">起点</td>
                <td class="view-body">
                    <input type="text" class="form-control" ng-model="data.general_highlight.start_location" />
                </td>
                <td class="view-title"></td>
                <td class="view-title">终点</td>
                <td class="view-body">
                    <input type="text" class="form-control" ng-model="data.general_highlight.finish_location" />
                </td>
            </tr>
            </tr>
        </table>
        <table class="forms-table high-light-summary-table">
            <tr>
                <td class="view-title with-narrow">亮点</td>
                <td class="view-title with-number">1、</td>
                <td class="view-body">
                    <input type="text" class="form-control" ng-model="data.general_highlight.highlight_summary[0]" />
                </td>
            </tr>
            <tr>
                <td class="view-title with-narrow"></td>
                <td class="view-title with-number">2、</td>
                <td class="view-body">
                    <input type="text" class="form-control" ng-model="data.general_highlight.highlight_summary[1]" />
                </td>
            </tr>
            <tr>
                <td class="view-title with-narrow"></td>
                <td class="view-title with-number">3、</td>
                <td class="view-body">
                    <input type="text" class="form-control" ng-model="data.general_highlight.highlight_summary[2]" />
                </td>
            </tr>
        </table>
        <table class="forms-table high-light-summary-table">
            <tr>
                <td class="view-title">最佳旅游时间</td>
                <td class="view-body" colspan="2">
                    <input type="text" class="form-control" ng-model="data.general_highlight.suitable_time" />
                </td>
            </tr>
        </table>
        <table class="forms-table high-light-summary-table">
            <tr>
                <td class="view-title">途经城市</td>
                <td class="view-body" colspan="2">
                    <hi-select-tag options="local.tour_cities"
                                   select="data.cities"
                                   model="data.general_highlight.tour_cities"></hi-select-tag>
                </td>
            </tr>
        </table>
    </div>
    <div class="grey-line"></div>
    <div class="section-body clearfix">
        <table class="highlight-list-table">
            <tr>
                <th class="first-column">
                    时间
                </th>
                <th class="second-column">
                    地点
                </th>
                <th class="third-column">
                    行程亮点
                </th>
                <th class="fourth-column">
                    住宿
                </th>
                <th></th>
            </tr>
            <tr class="one-highlight" ng-repeat="highlight in data.general_highlight.highlight_refs" hi-dnd
                data-index="{{ $index }}" item="highlight" callback="local.highlight_dnd.callback(info, dst_index)"
                options="local.highlight_dnd.options" ng-click="editHighLight($index)">
                <td>
                    D{{highlight.date}}
                </td>
                <td>
                    {{highlight.location}}
                </td>
                <td>
                    <div class="left-text" ng-repeat="item in highlight.display_highlight">
                        {{item}}
                    </div>
                </td>
                <td>
                    {{highlight.lodging}}
                </td>
                <td>
                    <button class="btn btn-inverse" ng-click="deleteHighlight($index)">删除</button>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="add-container">
                    <button class="add-btn btn btn-inverse" ng-click="addHighlight()"></button>
                    加一天
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="overlay confirm" ng-if="local.overlay.has_overlay">
    <div class="notify-container confirm states-section">
        <div class="notify-body section-body view">
            <div class="section-subbody">
                <form name="edit_highlight_form">
                    <table class="forms-table">
                        <tbody>
                            <tr>
                                <td class="view-title">
                                    <label>地点</label>
                                </td>
                                <td class="view-body">
                                    <input class="location-input" type="text"
                                           ng-model="local.editing_highlight.location" />
                                </td>
                            </tr>
                            <tr>
                                <td class="view-title">
                                    <label>行程亮点</label>
                                </td>
                                <td class="view-body">
                                    <textarea ng-model="local.editing_highlight.local_highlight"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td class="view-title">
                                    <label>住宿</label>
                                </td>
                                <td class="view-body">
                                    <input class="lodging-input" type="text"
                                           ng-model="local.editing_highlight.lodging" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        <div class="notify-foot">
            <button class="block-action btn btn-default" ng-click="toggleOverlay()">取消</button>
            <button class="block-action btn btn-inverse" ng-click="saveHighlight()"
                    ng-disabled="edit_highlight_form.$invalid">确定
            </button>
        </div>
    </div>
</div>

<div class="section-body clearfix"
     ng-show="local.section_head.multi_day_trip_general.is_edit">

</div>

</div>
</div>