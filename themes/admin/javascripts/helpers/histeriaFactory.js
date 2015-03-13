var commonFactory = function($http, $q) {
    var factory = {};

    factory.getAjaxSearchCityList = function(hasDefault) {

        var defer = $q.defer();
        var address = request_urls.getCities;

        $http.get(address, {cache : true}).success(function(data) {

            var cities = [];
            if(data.code == 200) {

                cities = data.data;

                angular.forEach(cities, function(value, key) {
                    value.city_pinyin = value.city_pinyin || '';
                    cities[key]['group'] =
                    value.hasOwnProperty('city_pinyin') && value.city_pinyin ?
                    value.city_pinyin.slice(0, 1).toUpperCase() :
                    'A';
                });

                if(hasDefault) {
                    cities.unshift({
                        city_pinyin : '',
                        group       : '',
                        city_code   : '0',
                        city_name   : '所有城市'
                    });
                }

            }
            defer.resolve(cities);

        }).error(function() {
            defer.reject();
        });

        return defer.promise;

    };

    factory.getAjaxSearchVendorList = function(hasDefault) {

        var defer = $q.defer();
        var address = request_urls.getSuppliers;

        $http.get(address, {cache : true}).success(function(data) {

            var vendors = [];

            if(data.code == 200) {
                vendors = data.data;

                angular.forEach(vendors, function(value, key) {
                    vendors[key]['group'] =
                    value.hasOwnProperty('name') && value.name ? value.name.slice(0, 1).toUpperCase() : 'A';
                });

                if(hasDefault) {
                    vendors.unshift({
                        group       : '',
                        supplier_id : '0',
                        name        : '所有供应商'
                    });

                }
            }

            defer.resolve(vendors);

        }).error(function() {
            defer.reject();
        });

        return defer.promise;

    };

    factory.formatDate = function(dateObj) {
        if(!(dateObj instanceof Date)) return dateObj;
        var dateStr = "";
        dateStr += dateObj.getFullYear() + "-";
        dateStr += dateObj.getMonth() < 9 ? "0" + (dateObj.getMonth() + 1) + "-" : (dateObj.getMonth() + 1) + "-";
        dateStr += dateObj.getDate() < 10 ? "0" + dateObj.getDate() : dateObj.getDate();

        return dateStr;
    }

    factory.validateDurations = function(durations, limit) {
        result = {
            code : 0,
            msg  : ''
        }
        var len = durations.length;

        angular.forEach(durations, function(value, key) {
            value.from_date = factory.formatDate(value.from_date);
            value.to_date = factory.formatDate(value.to_date);
        });

        durations.sort(function(a, b) {
            if(a.from_date < b.from_date) return -1;
            if(a.from_date > b.from_date) return 1;
            return 0;
        });

        var prev_to_date = '0000-00-00';
        for(var i = 0; i < len; i++) {
            var duration = durations[i];

            if(duration.from_date > duration.to_date) {
                result = {
                    code : 101,
                    msg  : '开始时间大于结束时间。'
                }
                break;
            }

            if(duration.from_date <= prev_to_date) {
                result = {
                    code : 102,
                    msg  : '时间重叠。'
                }
                break;
            }
            if(limit) {
                if(i == 0) {
                    if(duration.from_date < limit.from_date) {
                        result = {
                            code : 111,
                            msg  : '开始时间不能早于售卖开始时间：' + limit.from_date
                        }
                        break;
                    } else if(duration.from_date > limit.from_date) {
                        result = {
                            code : 201,
                            msg  : '开始时间应该等于售卖开始时间：' + limit.from_date
                        }
                    }
                }
                if(i == len - 1) {
                    if(duration.to_date > limit.to_date) {
                        result = {
                            code : 112,
                            msg  : '结束时间不能大于售卖截止时间：' + limit.to_date
                        }
                        break;
                    } else if(duration.to_date < limit.to_date) {
                        result = {
                            code : 202,
                            msg  : '结束时间应该等于售卖截止时间：' + limit.to_date
                        }
                    }
                }
            }

            prev_to_date = duration.to_date;
        }
        return result;
    }

    return factory;
};

var app = angular.module('histeria.factory', ['ngResource']);

app.factory('commonFactory', commonFactory);