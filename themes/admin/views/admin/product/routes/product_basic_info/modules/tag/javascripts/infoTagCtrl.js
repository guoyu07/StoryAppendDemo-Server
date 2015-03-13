controllers.InfoTagCtrl = function($scope, $rootScope, $http) {
    var tab_path = 'tag';

    $scope.local = {
        edit_tag             : false,
        origin_sub_tags      : [],
        selected_sub_tags    : [],
        current_parent_tag   : -1,
        origin_parent_tags   : [],
        selected_parent_tags : []
    };


    $scope.init = function() {
        if(!$scope.$parent.result) return;

        $scope.sub_tag = angular.copy($scope.$parent.result.sub_tag);
        $scope.parent_tag = angular.copy($scope.$parent.result.parent_tag);
        $scope.product_tags = $scope.$parent.result.info.tags;
        $scope.product_info = {
            product_id : $rootScope.product.product_id
        };

        $scope.initProductTag();
    };

    $scope.initProductTag = function() {
        for(var n in $scope.product_tags) {
            if($scope.product_tags[n].tag.parent_tag_id > 0) {
                $scope.local.selected_sub_tags.push($scope.product_tags[n].tag_id);
                $scope.local.selected_parent_tags.push($scope.product_tags[n].tag.parent_tag_id);
            } else {
                $scope.local.selected_parent_tags.push($scope.product_tags[n].tag_id);
            }
        }
        for(var i in $scope.parent_tag) {
            $scope.parent_tag[i].has_child = 0;

            for(var j in $scope.sub_tag) {
                if($scope.sub_tag[j].parent_tag_id == $scope.parent_tag[i].tag_id) {
                    $scope.parent_tag[i].has_child = 1;
                    break;
                }
            }
        }
    };

    $scope.selectTag = function(which_level, index) {
        if(which_level == 'parent') {
            $scope.local.current_parent_tag = $scope.parent_tag[index].tag_id;
            if(!$scope.parent_tag[index].has_child) {
                var tag_index = $scope.local.selected_parent_tags.indexOf($scope.local.current_parent_tag);
                if(tag_index == -1) {
                    $scope.local.selected_parent_tags.push($scope.local.current_parent_tag);
                } else {
                    $scope.local.selected_parent_tags.splice(tag_index, 1);
                }
            }
        } else if(which_level = 'sub') {
            var current_sub = $scope.sub_tag[index].tag_id;
            var sub_index = $scope.local.selected_sub_tags.indexOf(current_sub);
            if(sub_index > -1) {
                $scope.local.selected_sub_tags.splice(sub_index, 1);
                var parent_id = $scope.sub_tag[index].parent_tag_id;
                var need_remove_parent = true;
                for(var i in $scope.sub_tag) {
                    if($scope.sub_tag[i].parent_tag_id == parent_id &&
                       $scope.local.selected_sub_tags.indexOf($scope.sub_tag[i].tag_id) > -1) {
                        need_remove_parent = false;
                        break;
                    }
                }

                if(need_remove_parent) {
                    $scope.local.selected_parent_tags.splice($scope.local.selected_parent_tags.indexOf(parent_id), 1);
                }
            } else {
                $scope.local.selected_sub_tags.push(current_sub);
                var parent_id = $scope.sub_tag[index].parent_tag_id;
                if($scope.local.selected_parent_tags.indexOf(parent_id) == -1) {
                    $scope.local.selected_parent_tags.push(parent_id);
                }
            }
        }
    };

    $scope.editTag = function() {
        $scope.local.edit_tag = true;
        $scope.local.origin_parent_tags = angular.copy($scope.local.selected_parent_tags);
        $scope.local.origin_sub_tags = angular.copy($scope.local.selected_sub_tags);
    };

    $scope.closeTagDialog = function() {
        $scope.local.edit_tag = false;
        $scope.local.selected_parent_tags = angular.copy($scope.local.origin_parent_tags);
        $scope.local.selected_sub_tags = angular.copy($scope.local.origin_sub_tags);
    };

    $scope.applyTagChange = function() {
        $scope.local.edit_tag = false;
        $scope.$emit('setTabLoading', tab_path);

        var tmp_tag;
        var tag_template = {
            'product_id' : $scope.product_info.product_id,
            'tag_id'     : '',
            'tag'        : {}
        };
        var result_tag_list = [];
        var candidate_parent_tag = [];

        for(var i in $scope.parent_tag) {
            if($scope.local.selected_parent_tags.indexOf($scope.parent_tag[i].tag_id) > -1) {
                if($scope.parent_tag[i].has_child == '1') {
                    candidate_parent_tag.push(angular.copy($scope.parent_tag[i]));
                } else {
                    tmp_tag = angular.copy(tag_template);
                    tmp_tag.tag_id = $scope.parent_tag[i].tag_id;
                    tmp_tag.tag = angular.copy($scope.parent_tag[i]);
                    result_tag_list.push(tmp_tag);
                }
            }
        }

        for(var j in $scope.sub_tag) {
            if($scope.local.selected_sub_tags.indexOf($scope.sub_tag[j].tag_id) > -1) {
                for(var n in candidate_parent_tag) {
                    if(candidate_parent_tag[n].tag_id == $scope.sub_tag[j].parent_tag_id) {
                        tmp_tag = angular.copy(tag_template);
                        tmp_tag.tag_id = $scope.sub_tag[j].tag_id;
                        tmp_tag.tag = angular.copy($scope.sub_tag[j]);
                        tmp_tag.tag.parent_tag_name = candidate_parent_tag[n].name
                        result_tag_list.push(tmp_tag);
                        break;
                    }
                }
            }
        }

        $http.post($request_urls.updateBasicInfo, {
            tags : result_tag_list
        }).success(function(data) {
            if(data.code == 200) {
                $scope.$emit('setTabLoading', tab_path);

                $scope.product_tags = result_tag_list;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };


    $scope.init();
};

app.controller('InfoTagCtrl', [
    '$scope', '$rootScope', '$http', controllers.InfoTagCtrl
]);