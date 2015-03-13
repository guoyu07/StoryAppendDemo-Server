<div hi-overlay options="local.overlay_options"></div>


<!--jQuery & Bootstrap & Components-->
<script src="themes/admin/bower_components/jquery/jquery.min.js"></script>
<script src="themes/admin/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="themes/admin/bower_components/chosen/public/chosen.jquery.min.js"></script>

<!--Angular Libraries-->
<script src="themes/admin/bower_components/angular/angular.js"></script>
<script src="themes/admin/bower_components/angular-route/angular-route.min.js"></script>
<script src="themes/admin/bower_components/angular-resource/angular-resource.min.js"></script>

<!--Non Angular/jQuery Components-->
<script src="themes/admin/bower_components/es5-shim/es5-shim.min.js"></script>
<script src="themes/admin/bower_components/showdown/compressed/showdown.js"></script>
<script src="themes/admin/bower_components/d3/d3.js"></script>
<script src="themes/admin/bower_components/nvd3/nv.d3.js"></script>

<!--Angular Components-->
<script src="themes/admin/bower_components/ngQuickDate/dist/ng-quick-date.min.js"></script>
<script src="themes/admin/bower_components/angular-file-upload/angular-file-upload.js"></script>
<script src="themes/admin/bower_components/angular-chosen-localytics/chosen.js"></script>
<script src="themes/admin/bower_components/angularjs-nvd3-directives/dist/angularjs-nvd3-directives.min.js"></script>

<!--Common Non-Angular Functions-->
<script src="themes/admin/javascripts/common/common.js"></script>
<script type="text/javascript">
    var app = angular.module('HitourAdminApp', [
        'ngRoute', 'ngResource', 'ngQuickDate', 'angularFileUpload', 'localytics.directives', 'nvd3ChartDirectives'
    ]);
    var helpers = {}, factories = {}, directives = {}, controllers = {}, interceptors = {}, angular_routes = {};
</script>

<script type="text/javascript" src="themes/admin/javascripts/common/rootCtrl.js"></script>

<!--Directives-->
<script type="text/javascript" src="themes/admin/views/modules/hi_after_load/hiAfterLoadDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_breadcrumb/hiBreadcrumbDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_dnd/hiDndDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_elastic/hiElasticDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_enter/hiEnterDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_grid/hiGridDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_input_dropdown/hiInputDropdownDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_input_tag/hiInputTagDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_loader/hiLoaderDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_markdown/hiMarkdownDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_multiple_uploader/hiMultipleUploaderDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_no_break/hiNoBreakDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_overlay/hiOverlayDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_pick_ticket_map/hiPickTicketMapDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_radio_switch/hiRadioSwitchDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_section_head/hiSectionHeadDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_select_tag/hiSelectTagDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_tab/hiTabDir.js"></script>
<script type="text/javascript" src="themes/admin/views/modules/hi_uploader/hiUploaderDir.js"></script>

<!--Custom Components-->
<script type="text/javascript" src="themes/admin/javascripts/common/factory.js"></script>