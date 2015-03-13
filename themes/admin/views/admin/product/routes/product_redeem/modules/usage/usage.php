<div ng-if="local.tab_options.current_tab.path == 'usage'">
    <div ng-controller="RedeemUsageCtrl">
        <hi-section-head options="local.section_head.usage"></hi-section-head>
        <div class="redeem-section section-body">
            <form name="redeem_usage_form" hi-watch-dirty="local.path_name">
                <div class="markdown-display" ng-bind-html="data.usage.md_html" ng-hide="local.section_head.usage.is_edit"></div>
                <div hi-markdown input="data.usage.md_text" output="data.usage.md_html" ng-show="local.section_head.usage.is_edit"></div>
            </form>
        </div>
    </div>
</div>