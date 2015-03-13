controllers.NoticeNoteCtrl = function($scope, $rootScope, $http, $route, $sce) {
    $scope.data = {};
    $scope.local = {
        tab_path     : 'note',
        form_name    : 'notice_note_form',
        path_name    : helpers.getRouteTemplateName($route.current),
        section_head : {
            note : {
                title    : '购买提醒',
                updateCb : function() {
                    if($scope[$scope.local.form_name].$invalid) {
                        $rootScope.$emit('notify', {msg : '有不正确的内容，请修复。'});
                    } else {
                        $scope.saveChanges();
                    }
                },
                editCb   : function() {
                    $scope.local.section_head.note.is_edit = true;
                }
            }
        }
    };


    $scope.init = function() {
        if(!$scope.$parent.result) return;

        $scope.data.buy_note = decomposeMarkdown(angular.copy($scope.$parent.result.buy_note));
        $scope.data.buy_note.md_html = $sce.trustAsHtml($scope.data.buy_note.md_html);
    };

    $scope.saveChanges = function(cb) {
        $scope.$emit('setTabLoading', $scope.local.tab_path);

        var post_data = {};
        post_data.buy_note = composeMarkdown($scope.data.buy_note);

        $http.post($request_urls.productIntroduction, post_data).success(function(data) {
            if(data.code == 200) {
                $scope.$emit('setTabLoading', $scope.local.tab_path);
                $scope.local.section_head.note.is_edit = false;
                cb ? cb() : $rootScope.$emit('resetDirty');
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    function parseParagraph(string) {
        return string.split(/\n *\n *\n*/g).map(function(paragraph) {
            return '<p>' + paragraph + '</p>';
        }).join('');
    }

    $scope.$watch('data.buy_note.md_text', function(new_val) {
        if(!$scope.data.buy_note) return;
        $scope.data.buy_note.md_html = $sce.trustAsHtml(parseParagraph(new_val || ''));
    });

    $rootScope.$on('clearDirty', function(e, dirty_info, callback) {
        if(dirty_info && dirty_info.path_name == $scope.local.path_name && dirty_info.dirty_forms[$scope.local.form_name]) {
            $scope.saveChanges(callback);
        }
    });


    $scope.init();
};

app.controller('NoticeNoteCtrl', [
    '$scope', '$rootScope', '$http', '$route', '$sce', controllers.NoticeNoteCtrl
]);