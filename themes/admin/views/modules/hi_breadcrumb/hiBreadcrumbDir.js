directives.hiBreadcrumbDir = function($sce) {
    var linkFunc = function(scope) {
        var emptyFunc = function() {
        };
        scope.local = {
            back : {
                content      : false,
                clickCb      : function() {
                    window.history.back();
                },
                part_content : false,
                partClickCb  : emptyFunc
            },
            body : {
                content       : false,
                clickCb       : emptyFunc,
                right_content : false,
                rightClickCb  : emptyFunc
            }
        };

        scope.init = function() {
            angular.extend(scope.local, scope.options);

            if(angular.isString(scope.local.back.content)) {
                scope.local.back.content = $sce.trustAsHtml(scope.local.back.content);
            } else {
                scope.local.back.content = $sce.trustAsHtml('<span class="i i-arrow-left"></span>');
            }
            if(angular.isString(scope.local.back.part_content)) {
                scope.local.back.has_part = true;
                scope.local.back.part_content = $sce.trustAsHtml(scope.local.back.part_content);
            }

            if(angular.isString(scope.local.body.content)) {
                scope.local.body.content = $sce.trustAsHtml(scope.local.body.content);
            }
            if(angular.isString(scope.local.body.right_content)) {
                scope.local.body.has_right = true;
                scope.local.body.right_content = $sce.trustAsHtml(scope.local.body.right_content);
            }
        };

        scope.init();
    };

    return {
        link        : linkFunc,
        scope       : {
            options : '='
        },
        replace     : true,
        restrict    : 'AE',
        templateUrl : pathinfo.module_dir + 'hi_breadcrumb/hi_breadcrumb.html'
    };
};

app.directive('hiBreadcrumb', ['$sce', directives.hiBreadcrumbDir]);