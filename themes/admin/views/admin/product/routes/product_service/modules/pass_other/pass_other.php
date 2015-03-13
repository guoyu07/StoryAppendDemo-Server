<div class="grid-top states-section" ng-if="local.tab_options.current_tab.path == 'pass_other'">
    <div ng-controller="ServicePassOtherCtrl">
        <hi-section-head options="local.section_head.landinfo"></hi-section-head>
        <!--预览态-->
        <div class="section-body" ng-hide="local.section_head.landinfo.is_edit">
            <h3 class="text-center pad-bottom" ng-bind="data.landinfo.landinfo_md_title"></h3>
            <div class="section-body" ng-repeat="list in data.landinfo.landinfo_lists">
                <div class="section-subtitle" ng-bind="list.title"></div>
                <div class="section-subbody">
                    <div class="markdown-display" ng-bind-html="list.list.md_html"></div>
                </div>
            </div>
        </div>
        <!--编辑态-->
        <div class="section-body" ng-show="local.section_head.landinfo.is_edit">
            <div class="row text-center pad-bottom">
                <h3>
                    添加文字景点列表
                    <button class="block-action add btn btn-inverse" ng-click="addList()">新增列表</button>
                </h3>
            </div>
            <form name="service_introduce_pass_other_form" hi-watch-dirty="local.path_name">
                <div class="row pad-bottom">
                    <label class="col-md-3" for="landinfo_md_title">
                        景点列表名称：
                    </label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" ng-model="data.landinfo.landinfo_md_title">
                    </div>
                </div>
                <div class="pad-top pad-bottom grid-top" ng-repeat="list in data.landinfo.landinfo_lists">
                    <div class="row">
                        <label class="col-md-3">
                            列表名称：
                        </label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" ng-model="list.title">
                        </div>
                        <div class="col-md-2">
                            <button class="block-action add btn btn-inverse" ng-click="deleteList($index)">删除</button>
                        </div>
                    </div>
                    <hi-markdown input="list.list.md_text" output="list.list.md_html"></hi-markdown>
                </div>
            </form>
        </div>
    </div>
</div>