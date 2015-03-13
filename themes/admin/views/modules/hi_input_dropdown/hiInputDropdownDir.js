directives.hiInputDropdownDir = function() {
    var linkFunc = function(scope) {
        scope.local = {
            input_name    : scope.options.name || randomStr(),
            placeholder   : scope.options.placeholder || '下拉',
            current_label : ''
        };

        scope.init = function() {
            scope.local.current_label = scope.model.option ? scope.options.items[scope.model.option] :
                                        scope.local.placeholder;
        };
        scope.options.updateLabel = function() {
            scope.local.current_label = scope.options.items[scope.model.option];
            scope.$apply('local.current_label');
        };

        scope.init();
    };

    return {
        link        : linkFunc,
        scope       : {
            model   : '=',
            options : '='
        },
        replace     : true,
        restrict    : 'AE',
        templateUrl : pathinfo.module_dir + 'hi_input_dropdown/hi_input_dropdown.html'
    };
};

app.directive('hiInputDropdown', directives.hiInputDropdownDir);