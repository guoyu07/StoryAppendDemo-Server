directives.hiAfterLoadDir = function() {
    var linkFunc = function(scope) {
        scope.hiAfterLoad();
    };
    return {
        link     : linkFunc,
        scope    : {
            hiAfterLoad : '&'
        },
        restrict : 'EA',
        priority : -1000 //Loads only after everything else
    };
};

app.directive('hiAfterLoad', directives.hiAfterLoadDir);