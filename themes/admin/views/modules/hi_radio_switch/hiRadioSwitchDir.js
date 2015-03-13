directives.hiRadioSwitchDir = function($timeout) {
    var linkFunc = function(scope) {
        scope.local = {
            show_expand : true
        };

        scope.init = function() {
            scope.options.input_id = scope.options.input_id || randomStr();

            //TODO: 如果值加载后出现则show_expand等于false
            scope.local.show_expand = !scope.model || !( !!scope.model[scope.options.name] );
        };

        scope.toggleExpand = function() {
            scope.local.show_expand = !scope.local.show_expand;

            if(!scope.local.show_expand) { //收起时
                $timeout(function() {
                    scope.options.callback && scope.options.callback(scope.model[scope.options.name]);
                }, 50);
            }
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
        templateUrl : pathinfo.module_dir + 'hi_radio_switch/hi_radio_switch.html'
    };
};

app.directive('hiRadioSwitch', ['$timeout', directives.hiRadioSwitchDir]);