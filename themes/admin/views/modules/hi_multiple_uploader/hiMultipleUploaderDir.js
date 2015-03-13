directives.hiMultipleUploaderDir = function(FileUploader) {
    var linkFunc = function(scope) {
        scope.local = {};

        scope.init = function() {
            scope.local.input_id = scope.options.hasOwnProperty('input_id') ? scope.options.input_id : randomStr();
            scope.options.in_progress = false;
            scope.options.accept_type = scope.options.accept_type || 'image/*';
            if(scope.options.show_overlay !== false) scope.options.show_overlay = true;

            if(scope.options.filterCb) {
                scope.options.uploader.filters.push({
                    name : 'custom_filter',
                    fn   : scope.options.filterCb
                });
            } else {
                scope.options.uploader.filters.push({
                    name : 'image_filter',
                    fn   : imageFilter
                });
            }

            scope.options.progress = scope.options.uploader.progress;

            if(scope.options.beforeCb) {
                scope.options.uploader.onBeforeUploadItem = function(item) {
                    scope.options.beforeCb(false, item);
                };
            }
            scope.options.uploader.onAfterAddingFile = function() {
                scope.options.in_progress = true;
                for(var i = 0; i < scope.options.uploader.queue.length; i++) {
                    scope.options.uploader.queue[i].upload()
                }
            };
            scope.options.uploader.onCompleteAll = function() {
                scope.options.uploader.queue = [];
            };
            scope.options.uploader.onCompleteItem = function(item, response) {
                scope.options.successCb(false, false, item, response, scope.options.uploader);
            };
        };
        scope.triggerUpload = !!scope.options.triggerCb ? scope.options.triggerCb : function() {
            $('#' + scope.local.input_id).trigger('click');
        };

        scope.init();
    };

    return {
        scope       : {
            options : '='
        },
        compile     : function() {
            return {
                pre : function(scope) {
                    scope.options.uploader = new FileUploader({
                        url : scope.options.target
                    });
                },
                post : linkFunc
            }
        },
        replace     : true,
        restrict    : 'AE',
        templateUrl : pathinfo.module_dir + 'hi_multiple_uploader/hi_multiple_uploader.html'
    };
};

app.directive('hiMultipleUploader', ['FileUploader', '$rootScope', directives.hiMultipleUploaderDir]);