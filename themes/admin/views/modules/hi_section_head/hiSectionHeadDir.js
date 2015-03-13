directives.hiSectionHeadDir = function($sce, $rootScope) {
    var linkFunc = function(scope) {
        scope.options.title = $sce.trustAsHtml(scope.options.title);
        scope.options.is_edit = scope.options.is_edit || false;
        scope.options.editCb = scope.options.editCb || function() {
            scope.options.is_edit = true;
        };
        if(!scope.options.updateCb) {
            /*var status = {};

             scope.$watch(function() {
             return scope.options.update.form;
             }, function(form) {
             console.log(form);
             });

             scope.$watch(function() {
             var form = scope.options.update.form;
             console.log(form);
             return form ? {
             valid    : form.$valid,
             pristine : form.$pristine
             } : undefined;
             }, function(form_status) {
             if(form_status === undefined) return;
             status = form_status;
             });

             scope.options.updateCb = function() {
             if(status.pristine) {
             scope.options.is_edit = false;
             } else if(status.valid) {
             scope.options.is_edit = false;
             scope.options.update.callback && scope.options.update.callback();
             } else {
             $rootScope.$emit('notify', {msg : '请正确的填写选项再提交。'});
             }
             };*/
        }
        scope.options.getClass = function() {
            return scope.options.is_edit ? 'edit' : 'view';
        };
    };

    return {
        link        : linkFunc,
        scope       : {
            options : '='
        },
        replace     : true,
        restrict    : 'AE',
        templateUrl : pathinfo.module_dir + 'hi_section_head/hi_section_head.html'
    };
};

app.directive('hiSectionHead', ['$sce', '$rootScope', directives.hiSectionHeadDir]);