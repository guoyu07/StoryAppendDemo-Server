<section class="grid-top states-section" ng-if="local.tab_options.current_tab.path == 'include'">
    <div ng-controller="ServiceIncludeCtrl">
        <hi-section-head options="local.section_head.service_include"></hi-section-head>
        <div class="section-body" ng-hide="local.section_head.service_include.is_edit">
            <div class="section-subtitle">包含服务</div>
            <div class="section-subbody">
                <div class="markdown-display" ng-bind-html="data.desc.cn_service_include.md_html"></div>
            </div>
            <div class="section-subtitle">商品卖点</div>
            <div class="section-subbody">
                <p ng-bind="data.desc.cn_benefit"></p>
            </div>
            <div class="section-subtitle">简要描述</div>
            <div class="section-subbody">
                <p ng-bind="data.desc.cn_summary"></p>
            </div>
            <div class="section-subtitle">推荐理由</div>
            <div class="section-subbody">
                <p ng-bind="data.desc.cn_description"></p>
            </div>
        </div>

        <div class="section-body" ng-show="local.section_head.service_include.is_edit">
            <!-- hi-watch-dirty="local.path_name" -->
            <form name="service_include_form">
                <div class="section-subtitle">包含服务</div>
                <div class="section-subbody">
                    <div hi-markdown input="data.desc.cn_service_include.md_text" output="data.desc.cn_service_include.md_html"></div>
                </div>
                <div class="section-subtitle">商品卖点</div>
                <div class="section-subbody">
                    <input type="text" class="form-control" maxlength="10" placeholder="最多10个字" ng-model="data.desc.cn_benefit">
                </div>
                <div class="section-subtitle">简要描述</div>
                <div class="section-subbody markdown-container">
                    <textarea class="editor" required ng-model="data.desc.cn_summary"></textarea>
                </div>
                <div class="section-subtitle">推荐理由</div>
                <div class="section-subbody markdown-container">
                    <textarea class="editor" required ng-model="data.desc.cn_description"></textarea>
                </div>
            </form>
        </div>
    </div>
</section>
