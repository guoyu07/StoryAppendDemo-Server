<script type="text/ng-template" id="ProductQna.html">
    <div class="states-section grid-top">
        <hi-section-head options="local.section_head.qna"></hi-section-head>
        <!--编辑态-->
        <div class="section-body clearfix" ng-class="local.section_head.qna.getClass()" ng-show="local.section_head.qna.is_edit">
            <form name="qna_form" hi-watch-dirty="local.path_name">
                <hi-markdown input="data.qna.qa.md_text" output="data.qna.qa.md_html"></hi-markdown>
            </form>
        </div>
        <!--展示态-->
        <div class="section-body clearfix" ng-class="local.section_head.qna.getClass()" ng-hide="local.section_head.qna.is_edit">
            <div class="markdown-display" ng-bind-html="data.qna.qa.md_html"></div>
        </div>
    </div>
</script>