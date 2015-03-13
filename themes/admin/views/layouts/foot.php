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
<script src="themes/admin/bower_components/angular-file-upload/angular-file-upload.min.js"></script>
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

<!--Custom Components-->
<script type="text/javascript" src="themes/admin/javascripts/common/directives.js"></script>
<script type="text/javascript" src="themes/admin/javascripts/common/factory.js"></script>