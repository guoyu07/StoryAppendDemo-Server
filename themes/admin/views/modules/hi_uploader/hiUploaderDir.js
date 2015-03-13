directives.hiUploaderDir = function(FileUploader, $rootScope) {
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
            scope.options.uploader.onAfterAddingFile = function(item) {
                scope.options.in_progress = true;
                item.upload();
            };
            if(!!scope.options.successCb) {
                scope.options.uploader.onSuccessItem = function(item, response) {
                    scope.options.successCb(false, false, item, response, scope.options.uploader);
                };
            } else {
                scope.options.uploader.onSuccessItem = function(item, response) {
                    scope.options.in_progress = false;
                    scope.options.image_url = response.data;
                    scope.options.uploader.queue = [];

                    $rootScope.$emit('notify', {
                        msg : response.code == 200 ? '上传成功' : '上传失败'
                    });
                };
            }
        };
        scope.triggerUpload = !!scope.options.triggerCb ? scope.options.triggerCb : function() {
            $('#' + scope.local.input_id).trigger('click');
        };
        scope.deleteUpload = !!scope.options.deleteCb ? scope.options.deleteCb : function() {
            scope.options.image_url = '';
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
        templateUrl : pathinfo.module_dir + 'hi_uploader/hi_uploader.html'
    };
};

app.directive('hiUploader', ['FileUploader', '$rootScope', directives.hiUploaderDir]);
