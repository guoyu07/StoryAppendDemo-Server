factories.commonFactory = function($http, $q) {
    var factory = {};

    factory.getAjaxSearchCityList = function(hasDefault) {

        var defer = $q.defer();
        var address = $request_urls.fetchCities;

        $http.get(address, {cache : true}).success(function(data) {

            var cities = [];
            if(data.code == 200) {

                cities = getGroupBy(data.data, 'city_pinyin', 'A');

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
        var address = $request_urls.fetchSuppliers;

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

    factory.getAjaxSearchCountryList = function(hasDefault) {

        var defer = $q.defer();
        var address = $request_urls.fetchCountries;

        $http.get(address, {cache : true}).success(function(data) {

            var countries = [];
            if(data.code == 200) {

                countries = getGroupBy(data.data, 'en_name', 'A');

                if(hasDefault) {
                    countries.unshift({
                        group         : '',
                        country_code  : '0',
                        continent_id  : '',
                        cn_name       : '所有国家',
                        en_name       : '',
                        fullname      : '',
                        pinyin        : '',
                        description   : '',
                        currency_code : '',
                        link_url      : ''
                    });
                }

            }
            defer.resolve(countries);

        }).error(function() {
            defer.reject();
        });

        return defer.promise;

    };

    factory.getAjaxSearchSupplierList = function(hasDefault) {

        var defer = $q.defer();
        var address = $request_urls.fetchSuppliers;

        $http.get(address, {cache : true}).success(function(data) {

            var vendors = [];

            if(data.code == 200) {
                vendors = getGroupBy(data.data, 'name', 'A');

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

    factory.isRangesValid = function(ranges) {
        var result = {
            code : 200
        };

        ranges = ranges.map(function(one_range) {
            if(one_range.from_date > one_range.to_date) {
                result.code = 101;
                result.msg = "开始时间不能大于结束时间。"
            }

            return {
                to_date   : formatDate(one_range.to_date),
                from_date : formatDate(one_range.from_date)
            };
        }).sort(function(a, b) {
            if(a.from_date < b.from_date) return -1;
            if(a.from_date > b.from_date) return 1;
            return 0;
        });

        if(result.code == 200) {
            var r_key;
            var upto_date = '0000-00-00';

            for(r_key in ranges) {
                if(ranges[r_key].from_date <= upto_date) {
                    result.code = 102;
                    result.msg = "时间重叠";
                    break;
                }
            }
        }

        return result;
    };

    factory.isInsideDuration = function(ranges, duration) {
        //TODO: check if has missing range
        var range_result = factory.isRangesValid(ranges);

        if(range_result.code == 200) {
            var min = 0, max = ranges.length - 1, duration_result = range_result;

            if(ranges[min].from_date < duration.from_date) {
                duration_result.code = 111;
                duration_result.msg = "开始时间不能早于售卖开始时间：" + duration.from_date;
            } else if(ranges[min].from_date > duration.from_date) {
                duration_result.code = 201;
                duration_result.msg = "开始时间应该等于售卖开始时间：" + duration.from_date;
            }
            if(ranges[max].to_date > duration.to_date) {
                duration_result.code = 112;
                duration_result.msg = "结束时间不能大于售卖截止时间：" + duration.to_date;
            } else if(ranges[max].to_date < duration.to_date) {
                duration_result.code = 202;
                duration_result.msg = "结束时间应该等于售卖截止时间：" + duration.to_date;
            }

            return duration_result;
        } else {
            return range_result;
        }
    };

    factory.decomposeCloseDate = function(str) {
        var result = {
            'range'     : [],
            'weekday'   : [],
            'singleday' : []
        };
        if(!str) {
            return result;
        }
        var parts = str.split(';');
        var len = parts.length;


        for(var i = 0; i < len; i++) {
            if(parts[i].indexOf('周') > -1) {
                result.weekday.push(parts[i]);
            } else if(parts[i].indexOf('/') > -1) {
                result.range.push(parts[i].replace('/', ' - '));
            } else {
                if(parts[i].trim().length > 0) {
                    result.singleday.push(parts[i]);
                }
            }
        }

        return result;
    };

    factory.composeCloseDate = function(operations) {
        return operations.map(function(operation) {
            var close_str = '';
            if(operation.parts.weekday.length > 0) {
                close_str += operation.parts.weekday.join(";");
                close_str += ";";
            }
            if(operation.parts.singleday.length > 0) {
                close_str += operation.parts.singleday.join(";");
                close_str += ";";
            }
            if(operation.parts.range.length > 0) {
                for(var j in operation.parts.range) {
                    var date = operation.parts.range[j].replace(' - ', '/');
                    close_str += date + ";";
                }
            }

            operation.close_dates = close_str;
            return operation;
        });
    };

    factory.product_status = [
        {
            class       : 'all',
            status_id   : '',
            status_name : '所有状态'
        },
        {
            class       : 'edit',
            status_id   : '1',
            status_name : '编辑中'
        },
        {
            class       : 'review',
            status_id   : '2',
            status_name : '待审核'
        },
        {
            class       : 'onsale',
            status_id   : '3',
            status_name : '已上架'
        },
        {
            class       : 'offsale',
            status_id   : '4',
            status_name : '禁用'
        }
    ];

    factory.product_type = [
        {
            value : '1',
            label : '单票'
        },
        {
            value : '2',
            label : '组合票'
        },
        {
            value : '3',
            label : '通票'
        },
        {
            value : '4',
            label : '随上随下票'
        },
        {
            value : '5',
            label : 'Tour'
        },
        {
            value : '6',
            label : 'Coupon'
        },
        {
            value : '7',
            label : '酒店'
        },
        {
            value : '8',
            label : '酒店套餐'
        },
        {
            value : '9',
            label : '多日游'
        },
        {
            value : '10',
            label : '包车'
        }
    ];

    return factory;
};

app.factory('commonFactory', factories.commonFactory);