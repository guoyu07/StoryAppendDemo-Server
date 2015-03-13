directives.hiInputTagDir = function($timeout) {
    var linkFunc = function(scope) {
        scope.local = {
            btn_text    : scope.options.btn_text || '立刻关联',
            input_tag   : '',
            in_progress : false,
            placeholder : scope.options.placeholder || '关联ID'
        };

        scope.addTag = function() {
            scope.local.in_progress = true;

            scope.options.addCb(scope.local.input_tag, function(is_error, msg) {
                scope.local.message = msg || ( is_error ? '添加失败' : '添加成功' );
                scope.local.is_error = is_error;
                scope.local.in_progress = false;

                $timeout(function() {
                    scope.local.message = '';
                }, 3000);
            });
        };

        scope.delTag = function(index) {
            if(scope.options.deleteCb) {
                scope.options.deleteCb(index);
            } else {
                scope.model.splice(index, 1);
            }
        };
    };

    return {
        link        : linkFunc,
        scope       : {
            model   : '=',
            options : '='
        },
        replace     : true,
        restrict    : 'AE',
        templateUrl : pathinfo.module_dir + 'hi_input_tag/hi_input_tag.html'
    };
};

app.directive('hiInputTag', ['$timeout', directives.hiInputTagDir]);