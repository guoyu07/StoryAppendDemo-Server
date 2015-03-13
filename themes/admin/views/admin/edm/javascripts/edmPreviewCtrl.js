controllers.EDMPreviewCtrl = function($scope, $http, $rootScope, $sce) {
    $scope.data = {};

    $scope.init = function() {
        $rootScope.$emit('setBreadcrumb', {
            back : {},
            body : {
                content : '预览EDM模版'
            }
        });

        $http.get($request_urls.getEdmDetail).success(function(data) {
            if(data.code == 200) {
                $scope.data.base = {
                    title        : data.data.title,
                    title_link   : data.data.title_link,
                    small_title  : data.data.small_title,
                    description  : $sce.trustAsHtml(data.data.description),
                    banner_image : data.data.banner_image
                };
                $scope.data.groups = data.data.groups;
                $scope.data.base.banner_image = $scope.data.base.banner_image;

                $scope.local.style = {
                    'background-image' : 'url("' + $scope.data.base.banner_image + '")'
                };

                $rootScope.$emit('loadStatus', false);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.init();
};

app.controller('EDMPreviewCtrl', ['$scope', '$http', '$rootScope', '$sce', controllers.EDMPreviewCtrl]);