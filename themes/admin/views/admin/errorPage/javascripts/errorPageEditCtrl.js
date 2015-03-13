controllers.ErrorPageEditCtrl = function($scope, $http, $rootScope) {
    $scope.data = {
        page_info : {}
    };
    $scope.local = {
        product_id_copy  : '',
        radio_switch     : {
            options : {
                name  : 'status',
                items : {
                    '0' : '禁用',
                    '1' : '启用'
                }
            }
        },
        detail_section   : {
            title    : '基本信息',
            is_edit  : false,
            updateCb : function() {
                $scope.data.page_info.bg_image_url = $scope.local.uploader_options.pc_bg.image_url;
                $scope.data.page_info.mobile_image_url = $scope.local.uploader_options.mobile_bg.image_url;

                if($scope.error_page.$pristine) {
                    $scope.local.detail_section.is_edit = false;
                } else if($scope.data.page_info.status == 0) {
                    //禁用下可以随意保存，未必需要所有信息都完善
                    $http.post($request_urls.saveErrorPage, $scope.data.page_info).success(function(data) {
                        if(data.code == 200) {
                            $scope.local.detail_section.is_edit = false;
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                } else {
                    //启用状态，检查image与pid是否有
                    if(!!$scope.data.page_info.bg_image_url && !!$scope.data.page_info.mobile_image_url &&
                       !!$scope.data.page_info.product_id && $scope.error_page.$valid) {
                        $http.post($request_urls.saveErrorPage, $scope.data.page_info).success(function(data) {
                            if(data.code == 200) {
                                $scope.local.detail_section.is_edit = false;
                            } else {
                                $rootScope.$emit('notify', {msg : data.msg});
                            }
                        });
                    } else {
                        $rootScope.$emit('notify', {
                            msg : '启用状态下请完善信息'
                        })
                    }
                }
            },
            editCb   : function() {
                $scope.local.detail_section.is_edit = true;
            }
        },
        uploader_options : {
            pc_bg     : {
                target    : $request_urls.uploadBGImage,
                image_url : '',
                beforeCb  : function(event, item) {
                    item.formData = [
                        {
                            type : 1
                        }
                    ];
                }
            },
            mobile_bg : {
                target    : $request_urls.uploadMobileBGImage,
                image_url : '',
                beforeCb  : function(event, item) {
                    item.formData = [
                        {
                            type : 2
                        }
                    ];
                }
            }
        }
    };

    $scope.init = function() {
        $http.get($request_urls.getErrorPageDetail).success(function(data) {
            if(data.code == 200) {
                $scope.data.page_info = data.data;
                $scope.local.uploader_options.pc_bg.image_url = data.data.bg_image_url;
                $scope.local.uploader_options.mobile_bg.image_url = data.data.mobile_image_url;
                $scope.local.product_id_copy = data.data.product_id;
                $rootScope.$emit('loadStatus', false);
                $rootScope.$emit('setBreadcrumb', {
                    back : {
                        part_content : '<span class="i i-eye"></span> ',
                        partClickCb  : function() {
                            window.open($request_urls.linkBaseUrl + '/' + 'site/error?error_page_id=' +
                                        data.data.error_page_id, '_blank')
                        }
                    },
                    body : {
                        content : '编辑错误页面'
                    }
                });
            }
        });
    };

    $scope.bindingErrorProduct = function() {
        $http.get($request_urls.bindingProduct + $scope.data.page_info.product_id).success(function(data) {
            if(data.code = 200) {
                $scope.data.page_info.city_code = data.data.city.city_code;
                $scope.data.page_info.country_code = data.data.city.country_code;
                $scope.data.page_info.product_id = data.data.product_id;
                $scope.data.page_info.product_name = data.data.description.name;
                $scope.data.page_info.product_description = data.data.description.description;
            }
        })
    };

    $scope.init();
};

app.controller('ErrorPageEditCtrl', ['$scope', '$http', '$rootScope', controllers.ErrorPageEditCtrl]);