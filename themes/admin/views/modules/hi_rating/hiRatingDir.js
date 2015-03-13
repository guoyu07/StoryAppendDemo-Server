directives.hiRatingDir = function() {
    var linkFunc = function(scope) {
        scope.local = {
            stars : []
        };
        scope.options.star_count = scope.options.star_count || 5;

        for(var i = 1; i <= scope.options.star_count; i++) {
            scope.local.stars.push(i);
        }
    };

    return {
        link     : linkFunc,
        scope    : {
            model   : '=',
            options : '='
        },
        restrict : 'AE'
    };
};

app.directive('hiRating', [directives.hiRatingDir]);