directives.hiWatchDirtyDir = function($rootScope) {
    var linkFunc = function(scope, element, attributes, form_controller) {
        var options = {
            path_name : scope.hiWatchDirty,
            form_name : form_controller.$name
        };

        scope.$watch(function() {
            return form_controller.$pristine;
        }, function(is_pristine) {
            if(is_pristine === undefined) return;
            $rootScope.$emit('setDirty', is_pristine, options);
        });
    };

    return {
        link     : linkFunc,
        scope    : {
            'hiWatchDirty' : '='
        },
        require  : '^form',
        restrict : 'EA'
    };
};

app.directive('hiWatchDirty', ['$rootScope', directives.hiWatchDirtyDir]);