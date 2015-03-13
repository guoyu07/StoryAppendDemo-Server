controllers.SupplierGridCtrl = function($scope, $rootScope, $http, commonFactory) {
    $scope.data = {};
    $scope.local = {
        supplier        : '',
        first_call      : true,
        vendor_en_name  : '',
        show_add_vendor : false,
        add_in_progress : false
    };

    $scope.init = function() {
        $rootScope.$emit('setBreadcrumb', {
            back : {
                content : false
            },
            body : {
                content : '添加供应商'
            }
        });

        commonFactory.getAjaxSearchSupplierList().then(function(data) {
            $scope.data.vendors = data;
            $rootScope.$emit('loadStatus', false);
        });
    };
    $scope.addVendor = function() {
        $scope.local.add_in_progress = true;
        $http.post($request_urls.addVendor, {
            en_name : $scope.local.vendor_en_name
        }).then(function(data) {
            if(data.data.code == 200) {
                $scope.local.add_in_progress = false;
                window.location = $request_urls.editVendorUrl + data.data.data.supplier_id;
            }
        });
    };
    $scope.goEditVendor = function() {
        window.location = $request_urls.editVendorUrl + $scope.local.supplier;
    };

    $scope.init();
};

app.controller('SupplierGridCtrl', ['$scope', '$rootScope', '$http', 'commonFactory', controllers.SupplierGridCtrl]);