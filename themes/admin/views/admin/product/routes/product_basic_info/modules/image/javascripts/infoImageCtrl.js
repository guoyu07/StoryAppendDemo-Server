controllers.InfoImageCtrl = function($scope, $rootScope, $http) {
    $scope.data = {};
    $scope.local = {
        dnd                   : {
            options  : {
                selector : '.one-image-container.carousel-image',
                offset   : 0
            },
            callback : function(info, dst_index) {
                $scope.data.carousel_images.splice(info.src_index, 1); //Remove item
                $scope.data.carousel_images.splice(dst_index, 0, info.src_item); //Insert item
                $scope.updateImagesOrder();
            }
        },
        uploader_options      : {
            sample   : {
                target    : $request_urls.addOrUpdateProductSampleImage,
                image_url : '',
                successCb : function(event, xhr, item, response, uploader) {
                    uploader.queue = [];

                    $scope.local.uploader_options.sample.in_progress = false;
                    $scope.local.uploader_options.sample.image_url = response.sample_image.image_url;

                    $rootScope.$emit('notify', {
                        msg : response.code == 200 ? '上传成功' : '上传失败'
                    });
                }
            },
            carousel : {
                target      : $request_urls.addProductImage,
                input_id    : 'add_product_image',
                successCb   : function(event, xhr, item, response) {
                    if(response.code == 200) {
                        $scope.local.uploader_options.carousel.in_progress = false;
                        $scope.addCarouselImage(response.loop_image);
                    } else {
                        $rootScope.$emit('notify', {msg : response.msg});
                    }
                }
            }
        },
        selected_album_images : []
    };
    $scope.result = {};


    $scope.init = function() {
        if(!$scope.$parent.result) return;

        var result = angular.copy($scope.$parent.result.images);

        $scope.local.has_album = result.landinfos && result.landinfos.length > 0;
        $scope.local.uploader_options.sample.image_url = result.sample_image && result.sample_image.image_url;

        $scope.data.album_images = result.landinfos.map(function(elem) {
            return {
                'name'        : elem.name,
                'image_url'   : elem.image_url,
                'landinfo_id' : elem.landinfo_id
            };
        });
        $scope.data.carousel_images = result.loop_images.map(function(elem) {
            if(elem.image_usage == 2) { //来自于景点的轮播图
                $scope.local.selected_album_images.push(elem.landinfo_id);
            }

            elem.edit = false;

            return elem;
        });
    };

    $scope.addCarouselImage = function(image) {
        image.edit = false;
        $scope.data.carousel_images.unshift(image);
        $scope.updateImagesOrder();
    };

    $scope.selectAlbumImage = function(index) {
        var album_image = $scope.data.album_images[index];
        $http.post($request_urls.addProductImageOfLandinfo, {
            landinfo_id : album_image.landinfo_id
        }).success(function(data) {
            if(data.code == 200) {
                $scope.addCarouselImage(data.loop_image);
                $scope.local.selected_album_images.push(album_image.landinfo_id);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.selectCoverImage = function(index) {
        var carousel_image = $scope.data.carousel_images[index];
        $http.post($request_urls.productImageSetCover, {
            product_image_id : carousel_image.product_image_id
        }).success(function(data) {
            if(data.code == 200) {
                $scope.data.carousel_images = $scope.data.carousel_images.map(function(image) {
                    if(image.product_image_id == carousel_image.product_image_id) {
                        image.as_cover = 1;
                    } else {
                        image.as_cover = 0;
                    }
                    return image;
                });
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.triggerCarouselUpload = function() {
        $('#' + $scope.local.uploader_options.carousel.input_id).trigger('click');
    };

    $scope.toggleState = function(index) {
        var carousel_image = $scope.data.carousel_images[index];
        if(carousel_image.edit) { //编辑到预览
            $scope.updateImageInfo(index);
        }
        carousel_image.edit = !carousel_image.edit;
    };

    $scope.deleteCarouselImage = function(index) {
        if(!window.confirm('确认删除图片？')) return;

        var carousel_image = $scope.data.carousel_images[index];

        $http.post($request_urls.deleteProductImage, {
            image_usage      : carousel_image.image_usage,
            product_image_id : carousel_image.product_image_id
        }).success(function(data) {
            if(data.code == 200) {
                var image_index;

                image_index = getIndexByProp($scope.data.carousel_images, 'product_image_id', carousel_image.product_image_id);
                if(image_index > -1) {
                    $scope.data.carousel_images.splice(image_index, 1);
                }

                if(carousel_image.image_usage == 2) {
                    image_index = $scope.local.selected_album_images.indexOf(carousel_image.landinfo_id);
                    $scope.local.selected_album_images.splice(image_index, 1);
                }
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.updateImageInfo = function(index) {
        var carousel_image = $scope.data.carousel_images[index];
        $http.post($request_urls.updateProductImage, {
            name             : carousel_image.name,
            short_desc       : carousel_image.short_desc,
            product_image_id : carousel_image.product_image_id
        }).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.updateImagesOrder = function(images) {
        images = images || $scope.data.carousel_images;
        var order_info = images.map(function(elem, index) {
            return {
                sort_order       : index,
                product_image_id : elem.product_image_id
            };
        });

        $http.post($request_urls.updateProductImageOrder, {
            order_info : order_info
        }).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };


    $scope.init();
};

app.controller('InfoImageCtrl', [
    '$scope', '$rootScope', '$http', controllers.InfoImageCtrl
]);