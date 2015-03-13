controllers.CountrySearchCtrl = function($scope, $http, $rootScope) {
    $scope.data = {};
    $scope.local = {
        selected_country : ''
    };

    $scope.init = function() {
        $rootScope.$emit('setBreadcrumb', {
            back : {},
            body : {
                content : '查找国家'
            }
        });

        $http.get($request_urls.countryInfo, {cache : true}).success(function(data) {
            if(data.code == 200) {
                $scope.data = angular.copy(data.data);
                $scope.data.countries = getGroupBy($scope.data.countries, 'en_name');

                $rootScope.$emit('loadStatus', false);
            }
        });
    };
    $scope.goEditCountry = function(country_code) {
        country_code = !!country_code ? country_code : $scope.local.selected_country;
        window.location = $request_urls.editCountry + country_code;
    };

    $scope.init();
};

app.controller('CountrySearchCtrl', ['$scope', '$http', '$rootScope', controllers.CountrySearchCtrl]);