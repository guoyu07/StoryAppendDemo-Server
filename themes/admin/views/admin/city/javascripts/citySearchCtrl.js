controllers.CitySearchCtrl = function($scope, commonFactory, $http, $rootScope, $q) {
    $scope.data = {
        cities                          : [],
        incomplete_cities               : [],
        new_group_cities                : [],
        all_cities_have_products_online : [],
        recommend_cities                : []
    };
    $scope.local = {
        selected_city           : '',
        selected_recommend_city : [],
        recommend_cities        : []
    };

    $scope.init = function() {
        $rootScope.$emit('setBreadcrumb', {
            back : {},
            body : {
                content : '查找城市'
            }
        });

        commonFactory.getAjaxSearchCityList().then(function(data) {
            $scope.data.cities = data;
            $scope.data.incomplete_cities = [];

            var ajax_incomplete_cities = $http.get($request_urls.fetchIncompleteCities),
                ajax_new_group_cities = $http.get($request_urls.fetchHaveNewGroupCities),
                ajax_missing_cover_cities = $http.get($request_urls.fetchMissingGroupCoverCities),
                ajax_recommend_cities = $http.get($request_urls.fetchCityRecommend),
                ajax_all_cities_have_product_online = $http.get($request_urls.fetchAllCitiesHaveProductsOnline);

            $q.all([
                ajax_incomplete_cities, ajax_new_group_cities, ajax_all_cities_have_product_online,
                ajax_recommend_cities, ajax_missing_cover_cities
            ]).then(function(values) {
                if(values[0].data.code == 200) {
                    $scope.data.incomplete_cities = values[0].data.data;
                }

                if(values[1].data.code == 200) {
                    $scope.data.new_group_cities = values[1].data.data;
                }
                if(values[2].data.code == 200) {
                    values[2].data.data = getGroupBy(values[2].data.data, 'pinyin', 'A');
                    $scope.data.all_cities_have_products_online = values[2].data.data;
                }
                if(values[3].data.code == 200) {
                    for(var i in values[3].data.data) {
                        $scope.local.recommend_cities.push([
                            values[3].data.data[i]['city_code'], values[3].data.data[i]['cn_name']
                        ]);
                        $scope.data.recommend_cities.push(values[3].data.data[i]['city_code']);
                    }
                }
                if(values[4].data.code == 200) {
                    $scope.data.missing_cover_cities = values[4].data.data;
                }

                $rootScope.$emit('loadStatus', false);
            });
        });
    };

    $scope.doEditCity = function(city_code) {
        if(!!city_code) {
            window.location = $request_urls.edit + city_code;
        } else if($scope.local.selected_city) {
            window.location = $request_urls.edit + $scope.local.selected_city;
        }
    };

    $scope.doEditGroup = function(city_code, group_id) {
        window.location = $request_urls.editCityUrl + city_code + '&group_id=' + group_id;
    };

    $scope.updateRecommendCities = function() {
        $http.post($request_urls.updateCityRecommend, $scope.data.recommend_cities).success(function(data) {
            $rootScope.$emit('notify', {
                msg : data.msg
            });
            if(data.code == 401) {
                //重复，把新添加的即最后一个去掉
                $scope.data.recommend_cities.pop();
                $scope.local.recommend_cities.pop();
            }
        });
    };

    $scope.addRecommendCities = function() {
        if(!window.confirm("确认添加此城市？")) {
            return;
        }
        $scope.local.recommend_cities.push($scope.local.selected_recommend_city);
        var last = $scope.local.recommend_cities.length - 1;
        $scope.data.recommend_cities.push($scope.local.recommend_cities[last][0]);
        $scope.updateRecommendCities();
    };

    $scope.delTag = function(idx) {
        if(!window.confirm("确认删除此城市？")) {
            return;
        }
        $scope.local.recommend_cities.splice(idx, 1);
        $scope.data.recommend_cities.splice(idx, 1);
        $scope.updateRecommendCities();
    };

    $scope.init();
};

app.controller('CitySearchCtrl', ['$scope', 'commonFactory', '$http', '$rootScope', '$q', controllers.CitySearchCtrl]);