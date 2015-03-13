directives.hiTabDir = function() {
    var linkFunc = function(scope) {
        scope.options.setCurrentTab = function(tab) {
            if(scope.options.setCallback) {
                scope.options.setCallback(tab, function() {
                    scope.options.current_tab = tab;
                });
            } else {
                scope.options.current_tab = tab;
            }
        };
    };

    return {
        link        : linkFunc,
        scope       : {
            options : '='
        },
        restrict    : 'AE',
        templateUrl : pathinfo.module_dir + 'hi_tab/hi_tab.html'
    };
};

app.directive('hiTab', directives.hiTabDir);