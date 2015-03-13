directives.hiMarkdownDir = function($sce) {
    var converter = new Showdown.converter();

    var linkFunc = function(scope) {
        if(!!scope.output && !!scope.output.$$unwrapTrustedValue) {
        } else {
            scope.output = $sce.trustAsHtml(scope.output);
        }

        scope.$watch('input', function(new_val) {
            scope.output = $sce.trustAsHtml(converter.makeHtml(new_val || ''));
        });
    };

    return {
        link        : linkFunc,
        scope       : {
            input   : '=',
            output  : '=',
            options : '='
        },
        replace     : true,
        restrict    : 'AE',
        templateUrl : pathinfo.module_dir + 'hi_markdown/hi_markdown.html'
    };
};

app.directive('hiMarkdown', ['$sce', '$window', directives.hiMarkdownDir]);