directives.hiEnterDir = function($parse) {
    return function(scope, elem, attr) {
        var fn = $parse(attr['hiEnter']);
        //Do not use keydown to avoid event trigger twice
        elem.bind('keypress', function(event) {
            if(event.which === 13) {
                scope.$apply(function() {
                    fn(scope, {$event : event});
                });
            }
        });
    }
};

app.directive('hiEnter', ['$parse', directives.hiEnterDir]);