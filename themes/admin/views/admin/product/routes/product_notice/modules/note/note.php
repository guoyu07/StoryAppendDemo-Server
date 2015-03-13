<div class="states-section" ng-if="local.tab_options.current_tab.path == 'note'">
    <div ng-controller="NoticeNoteCtrl">
        <hi-section-head options="local.section_head.note"></hi-section-head>
        <!--编辑态-->
        <div class="section-body clearfix" ng-class="local.section_head.note.getClass()" ng-show="local.section_head.note.is_edit">
            <p class="small-desc">此处不应该使用Markdown；全部都应该是文本，两行直接应有空行。</p>
            <form name="notice_note_form" hi-watch-dirty="local.path_name">
                <div class="markdown-container clearfix">
                    <div class="col-md-9">
                        <textarea class="editor form-control" ng-model="data.buy_note.md_text" rows="10" hi-elastic></textarea>
                    </div>
                    <div class="preview col-md-9" ng-bind-html="data.buy_note.md_html"></div>
                </div>
            </form>
        </div>
        <!--展示态-->
        <div class="section-body clearfix" ng-class="local.section_head.note.getClass()" ng-hide="local.section_head.note.is_edit">
            <div class="markdown-display" ng-bind-html="data.buy_note.md_html"></div>
        </div>
    </div>
</div>