directives.hiOverlayDir = function($rootScope, $sce) {
    var linkFunc = function(scope) {
        var closeOverlay = function() {
            scope.local.has_overlay = false;
            $rootScope.$emit('overlay', false);
        };
        var buildOverlay = function() {
            scope.local.has_overlay = true;
            scope.$apply('local.has_overlay', scope.local.has_overlay);
            $rootScope.$emit('overlay', scope.options.type);
        };

        scope.local = {
            has_target  : false,
            has_overlay : false
        };

        scope.init = function() {
            scope.local.has_target = scope.options.target && !!Object.keys(scope.options.target).length;
            scope.options.message = $sce.trustAsHtml(scope.options.message);

            if(scope.options.type == 'alert') {
                scope.options.buttons = scope.options.buttons || '确定';

                scope.local.close_cb = function() {
                    scope.options.close_cb ? scope.options.close_cb(closeOverlay) : closeOverlay();
                };
            } else if(scope.options.type == 'confirm') {
                scope.triggerCb = function(i) {
                    scope.options.buttons[i].cb ? scope.options.buttons[i].cb(closeOverlay) : closeOverlay();
                };
            }

            if(scope.local.has_target) {
                scope.options.target.action = scope.options.target.action || 'click';

                var element = angular.element(document.querySelector(scope.options.target.selector));
                element.bind(scope.options.target.action, buildOverlay);
            } else {
                scope.options.buildOverlay = buildOverlay;
            }
        };

        scope.init();
    };

    return {
        link        : linkFunc,
        scope       : {
            options : '='
        },
        restrict    : 'AE',
        templateUrl : pathinfo.module_dir + 'hi_overlay/hi_overlay.html'
    };
};

app.directive('hiOverlay', ['$rootScope', '$sce', directives.hiOverlayDir]);