controllers.ServicePassOtherCtrl = function($scope, $rootScope, $http, $sce, $route) {
    $scope.data = {};
    $scope.local = {
        tab_path     : 'pass_other',
        form_name    : 'service_introduce_pass_other_form',
        path_name    : helpers.getRouteTemplateName($route.current),
        section_head : {
            landinfo : {
                title    : '文字景点列表',
                updateCb : function() {
                    $scope.saveChanges();
                },
                editCb   : function() {
                    $scope.local.section_head.landinfo.is_edit = true;
                }
            }
        }
    };


    $scope.init = function() {
        if(!$scope.$parent.result) return;

        $scope.local.path_name = $scope.$parent.result.path_name;

        $scope.data.landinfo = {
            landinfo_md_title : $scope.$parent.result.album.landinfo_md_title
        };

        try {
            $scope.data.landinfo.landinfo_md = JSON.parse(decodeURIComponent($scope.$parent.result.album.landinfo_md));
        } catch(e) {
            $scope.data.landinfo.landinfo_md = [];
        }

        $scope.data.landinfo.landinfo_lists = $scope.data.landinfo.landinfo_md.map(function(one_list) {
            if(typeOf(one_list.list.md_html) != "string") {
                one_list.list.md_html = "";
            }

            one_list.list.md_html = $sce.trustAsHtml(one_list.list.md_html);

            return one_list;
        });
    };

    $scope.addList = function() {
        var list = {
            list  : {
                md_text : '',
                md_html : ''
            },
            title : ''
        };
        list.list.md_html = $sce.trustAsHtml(list.list.md_html);
        $scope.data.landinfo.landinfo_lists.push(list);
    };

    $scope.deleteList = function(index) {
        $scope.data.landinfo.landinfo_lists.splice(index, 1);
    };

    $scope.saveChanges = function(cb) {
        $scope.$emit('setTabLoading', $scope.local.tab_path);

        var landinfo_groups = $scope.data.landinfo.landinfo_lists.map(function(one_list) {
            if(!((typeof one_list.list.md_html=='string')&&(one_list.list.md_html.constructor==String))){
                one_list.list.md_html = one_list.list.md_html.$$unwrapTrustedValue();
                delete one_list.$$hashKey;
            }
            return one_list;
        });
        var post_data = {
            //这些其实不需要
            need_album        : $scope.$parent.result.album.need_album,
            album_id          : $scope.$parent.result.album.album_id || '0',
            album_name        : $scope.$parent.result.album.album_name,
            //这些才是更新的
            landinfo_md       : encodeURIComponent(JSON.stringify(landinfo_groups)),
            landinfo_md_title : $scope.data.landinfo.landinfo_md_title
        };

        $http.post($request_urls.saveAlbumInfoAll, post_data).success(function(data) {
            if(data.code == 200) {
                $scope.local.section_head.landinfo.is_edit = false;

                $scope[$scope.local.form_name].$pristine = true;
                $scope.$emit('setTabLoading', $scope.local.tab_path);
                cb ? cb() : $rootScope.$emit('resetDirty');
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $rootScope.$on('clearDirty', function(e, dirty_info, callback) {
        if(dirty_info && dirty_info.path_name == $scope.local.path_name && dirty_info.dirty_forms[$scope.local.form_name]) {
            $scope.saveChanges(callback);
        }
    });


    $scope.init();
};

app.controller('ServicePassOtherCtrl', [
    '$scope', '$rootScope', '$http', '$sce', '$route',
    controllers.ServicePassOtherCtrl
]);