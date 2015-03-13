directives.hiLoaderDir = function($compile) {
    return {
        link : function(scope, element, attr) {
            var html = scope.$eval(attr.hiLoader);
            var elem;
            if(html) {
                if(html[0] == '<') {
                    elem = angular.element(html);
                    $compile(elem)(scope);
                } else {
                    elem = html;
                }
            } else {
                elem = '';
            }
            element.append(elem);
        }
    };
};

app.directive('hiLoader', ['$compile', directives.hiLoaderDir]);