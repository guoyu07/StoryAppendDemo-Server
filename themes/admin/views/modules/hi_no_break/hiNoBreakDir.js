directives.hiNoBreakDir = function() {
    return {
        restrict : 'A',
        link     : function(scope, element) {
            element.on('keydown', function(e) {
                if(e.keyCode == 13) {
                    e.preventDefault();
                }
            });
        }
    };
};

app.directive('hiNoBreak', directives.hiNoBreakDir);