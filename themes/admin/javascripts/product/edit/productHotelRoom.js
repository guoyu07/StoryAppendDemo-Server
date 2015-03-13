var editHotelRoomCtrl = function($scope, $rootScope, $route, $http, $q, FileUploader) {
    $scope.local = {
        service_items    : [],
        current_edit     : -1,
        selected_service : []
    };
    $scope.onlyNumbers = /^\d+$/;
    $scope.data = {};

    $scope.init = function() {
        var ajax_rooms = $http.get(request_urls.hotelRoomType);
        var ajax_services = $http.get(request_urls.serviceItems);
        $q.all([
            ajax_rooms, ajax_services
        ]).then(function(values) {
            if(values[0].data.code == 200) {
                $scope.data.room_list = values[0].data.data;
            }

            if(values[1].data.code == 200) {
                $scope.local.service_items = values[1].data.data;
            }

            $scope.initData();
        });
    };

    $scope.initData = function() {
        for(var i in $scope.data.room_list) {
            $scope.data.room_list[i].selected_service = [];
            for(var p in $scope.data.room_list[i].policies) {
                if($scope.data.room_list[i].policies[p].age_range.length > 0) {
                    var ages = $scope.data.room_list[i].policies[p].age_range.split("-");
                    $scope.data.room_list[i].policies[p].age_1 = ages[0];
                    $scope.data.room_list[i].policies[p].age_2 = ages[1];
                } else {
                    $scope.data.room_list[i].policy_tips = $scope.data.room_list[i].policies[p].policy;
                }
            }

            for(var n in $scope.data.room_list[i].services) {
                for(var j in $scope.local.service_items) {
                    if($scope.data.room_list[i].services[n].service_id == $scope.local.service_items[j].service_id) {
                        $scope.data.room_list[i].services[n].name = $scope.local.service_items[j].name;
                        $scope.data.room_list[i].selected_service.push($scope.data.room_list[i].services[n].service_id);
                    }
                }
            }

            try {
                $scope.data.room_list[i].bed_policy_md = JSON.parse(decodeURIComponent($scope.data.room_list[i].bed_policy_md));
            } catch(e) {
                $scope.data.room_list[i].bed_policy_md = {
                    md_text : '',
                    md_html : ''
                };
            }

            try {
                $scope.data.room_list[i].breakfast_md = JSON.parse(decodeURIComponent($scope.data.room_list[i].breakfast_md));
            } catch(e) {
                $scope.data.room_list[i].breakfast_md = {
                    md_text : '',
                    md_html : ''
                };
            }
        }

        $scope.local.back_url = window.location.href;
    }

    $scope.editRoom = function(index) {
        $scope.local.current_edit = index;
    };
    function composeMarkdown(md) {
        return encodeURIComponent(JSON.stringify({md_text : md.md_text, md_html :
            !!md.md_html && md.md_text.length > 0 ? md.md_html : ''}));
    }

    $scope.submitChanges = function() {
        var added_policy_tips = false;
        var i = $scope.local.current_edit;
        var post_data = angular.copy($scope.data.room_list[i]);
        post_data.bed_policy_md = composeMarkdown(post_data.bed_policy_md);
        post_data.breakfast_md = composeMarkdown(post_data.breakfast_md);
        for(var p in post_data.policies) {
            if(post_data.policies[p].hasOwnProperty("age_1") &&
               post_data.policies[p].hasOwnProperty("age_2")) {
                post_data.policies[p].age_range = post_data.policies[p].age_1 + "-" +
                                                  post_data.policies[p].age_2;

            } else {
                post_data.policies[p].policy = post_data.policy_tips;
                added_policy_tips = true;
            }
        }

        if(!added_policy_tips && post_data.hasOwnProperty('policy_tips') &&
           post_data.policy_tips.length > 0) {
            var tmp_policy = {
                policy_id    : "",
                room_type_id : post_data.room_type_id,
                age_range    : "",
                policy       : post_data.policy_tips
            }
            post_data.policies.push(tmp_policy);
        }

        $http.post(request_urls.hotelRoomType +
                   post_data.room_type_id, post_data).success(function(data) {
            if(data.code == 200) {
                $scope.local.current_edit = -1;
                alert(data.msg);
            } else {
                alert(data.msg);
            }
        });
    };

    $scope.delImage = function(index) {
        if(!window.confirm("删除后不可恢复。\n点击‘确认’删除。"))
            return;

        $http.post(request_urls.deleteRoomImage, {
            image_id : $scope.data.room_list[$scope.local.current_edit].images[index].image_id
        }).success(function(data) {
            if(data.code == 200) {
                $scope.data.room_list[$scope.local.current_edit].images.splice(index, 1);
            } else {
                alert(data.msg);
            }
        });
    };

    $scope.toggleService = function(service_id, service_name) {
        var service_index = $scope.data.room_list[$scope.local.current_edit].selected_service.indexOf(service_id);
        if(service_index > -1) {
            $scope.data.room_list[$scope.local.current_edit].selected_service.splice(service_index, 1);
            for(var i in $scope.data.room_list[$scope.local.current_edit].services) {
                if($scope.data.room_list[$scope.local.current_edit].services[i].service_id == service_id) {
                    $scope.data.room_list[$scope.local.current_edit].services.splice(i, 1);
                    break;
                }
            }
        } else {
            var new_service = {
                product_id   : $scope.data.room_list[$scope.local.current_edit].product_id,
                room_type_id : $scope.data.room_list[$scope.local.current_edit].room_type_id,
                service_id   : service_id,
                service_info : "",
                name         : service_name
            };

            $scope.data.room_list[$scope.local.current_edit].services.push(new_service);
            $scope.data.room_list[$scope.local.current_edit].selected_service.push(service_id);
        }
    };

    $scope.addPolicy = function() {
        var tmp_policy = {
            policy_id    : "",
            room_type_id : $scope.data.room_list[$scope.local.current_edit].room_type_id,
            age_range    : "",
            policy       : "",
            age_1        : 0,
            age_2        : 0
        }
        $scope.data.room_list[$scope.local.current_edit].policies.push(tmp_policy);
    };

    $scope.delRoom = function(index, room_type_id) {
        if(!window.confirm("删除后不可恢复。\n点击‘确认’删除。"))
            return;

        $http.delete(request_urls.hotelRoomType + room_type_id).success(function(data) {
            if(data.code == 200) {
                alert(data.msg);
                $scope.data.room_list.splice(index, 1);
            }
        });
    };

    $scope.addRoom = function() {
        $http.post(request_urls.hotelRoomType, {}).success(function(data) {
            if(data.code == 200) {
                var new_room = angular.copy(data.data);
                new_room.services = [];
                new_room.images = [];
                new_room.policies = [];
                new_room.selected_service = [];
                new_room.policy_tips = "";
                $scope.data.room_list.push(new_room);
                $scope.local.current_edit = $scope.data.room_list.length - 1;
            } else {
                alert(data.msg);
            }
        });
    };

    $scope.backToList = function() {
        location.reload();
    };

    $scope.delPolicy = function(index) {
        if(!window.confirm("删除后不可恢复。\n点击‘确认’删除。"))
            return;

        $scope.data.room_list[$scope.local.current_edit].policies.splice(index, 1);
    };

    var image_filter = function(item) {
        var type = '|' + item.type.toLowerCase().slice(item.type.lastIndexOf('/') + 1) + '|';
        return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
    };

    $scope.uploader = new FileUploader({
        url   : request_urls.updateRoomImage,
        scope : $scope
    });
    $scope.uploader.filters.push({
        name : 'imagesOnly',
        fn   : image_filter
    });
    $scope.uploader.onSuccessItem = function(item, response) {
        $scope.local.uploader.queue = [];
        if(response.code == 200) {
            var result = angular.copy(response.data);
            $scope.data.room_list[$scope.local.current_edit].images.push(result);
            alert("图片上传成功");
        } else {
            alert(response.msg);
        }
    };
    $scope.uploader.onBeforeUploadItem = function(item) {
        item.formData = [
            {
                room_type_id  : $scope.data.room_list[$scope.local.current_edit].room_type_id,
                image_id      : '',
                display_order : $scope.data.room_list[$scope.local.current_edit].images.length + 1
            }
        ];
    };
    $scope.uploader.onAfterAddingFile = function(item) {
        item.upload();
    };

    $scope.triggerUpload = function() {
        $('#img-upload').trigger('click');
    };

    $scope.dndOptions = {
        selector : '.carousel-image',
        offset   : 0
    };

    $scope.dndCallback = function(info, dstIndex) {
        $scope.data.room_list[$scope.local.current_edit].images.splice(info.srcIndex, 1); //Remove img item
        $scope.data.room_list[$scope.local.current_edit].images.splice(dstIndex, 0, info.srcItem); //Add img item
        $scope.updateImageOrder();
    };

    $scope.updateImageOrder = function() {
        var order_info = $scope.data.room_list[$scope.local.current_edit].images.map(function(elem, index) {
            return {
                display_order : index,
                image_id      : elem.image_id
            };
        });

        return $http.post(request_urls.updateRoomImageOrder, order_info);
    };

    $scope.init();
};

angular.module('ProductEditApp').controller('editHotelRoomCtrl', [
    '$scope', '$rootScope', '$route', '$http', '$q', 'FileUploader', editHotelRoomCtrl
]);