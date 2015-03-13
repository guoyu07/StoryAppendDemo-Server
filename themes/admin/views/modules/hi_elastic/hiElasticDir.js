directives.hiElasticDir = function($timeout) {
    return {
        link     : function(scope, element) {
            var resize = function() {
                element[0].style.height = '1px';
                return element[0].style.height = element[0].scrollHeight + "px";
            };
            element.on('blur keyup change', resize);
            //TODO: incorrect initial height
            /*$timeout(function() {
             scope.$apply(resize);
             }, 50);*/
        },
        restrict : 'A',
        priority : -1000 //Loads only after everything else
    };
};

app.directive('hiElastic', ['$timeout', directives.hiElasticDir]);