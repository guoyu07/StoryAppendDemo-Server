var p_resolve = {};

(function() {
    var _cache = {};

    //用于避免同路由多次加载同样的接口
    function fetchFromCache($http, $q, $rootScope, request_url, method) {
        var defer = $q.defer();
        method = method || 'get';

        if(request_url in _cache && $rootScope.use_cache) {
            defer.resolve({
                data : _cache[request_url]
            });
        } else {
            $http[method](request_url).success(function(data) {
                _cache[request_url] = data;
                defer.resolve({
                    data : data
                });
            }).error(function(err) {
                    $rootScope.$emit('errorStatus', true);
                    defer.reject(err);
                });
        }

        return defer.promise;
    }

    //商品说明
    p_resolve.ProductBasicInfo = {
        loadData : [
            '$http', '$q', '$route', '$rootScope', 'commonFactory',
            function($http, $q, $route, $rootScope, commonFactory) {
                var defer = $q.defer();
                var result = {};
                var ajax_map = {};
                var info_type = $route.current.params.info_type;
                var ajax_requests = [];
                var request_callback;

                if(info_type == 'name') {
                    ajax_map.name = fetchFromCache($http, $q, $rootScope, $request_urls.getBasicInfo);
                    ajax_requests = [commonFactory.getAjaxSearchSupplierList(), ajax_map.name];

                    request_callback = function(values) {
                        result.supplier_list = values[0];

                        if(values[1].data.code == 200) {
                            result.info = values[1].data.data;
                        } else {
                            $rootScope.$emit('notify', values[1].data.msg);
                        }

                        defer.resolve(result);
                    };
                } else if(info_type == 'city') {
                    ajax_map.name = fetchFromCache($http, $q, $rootScope, $request_urls.getBasicInfo);
                    ajax_requests = [commonFactory.getAjaxSearchCityList(), ajax_map.name];

                    request_callback = function(values) {
                        result.city_list = values[0];

                        if(values[1].data.code == 200) {
                            result.info = values[1].data.data;
                        } else {
                            $rootScope.$emit('notify', values[1].data.msg);
                        }

                        defer.resolve(result);
                    };
                } else if(info_type == 'image') {
                    ajax_map.images = fetchFromCache($http, $q, $rootScope, $request_urls.getProductImages);
                    ajax_requests = [ajax_map.images];

                    request_callback = function(values) {
                        if(values[0].data.code == 200) {
                            result.images = values[0].data.data;
                        } else {
                            $rootScope.$emit('notify', values[0].data.msg);
                        }

                        defer.resolve(result);
                    };
                } else if(info_type == 'tag') {
                    ajax_map.name = fetchFromCache($http, $q, $rootScope, $request_urls.getBasicInfo);
                    ajax_map.tags = fetchFromCache($http, $q, $rootScope, $request_urls.getTags);
                    ajax_requests = [ajax_map.tags, ajax_map.name];

                    request_callback = function(values) {
                        if(values[0].data.code == 200) {
                            result.parent_tag = values[0].data.data.parent_tag;
                            result.sub_tag = values[0].data.data.sub_tag;
                        } else {
                            $rootScope.$emit('notify', values[0].data.msg);
                        }
                        if(values[1].data.code == 200) {
                            result.info = values[1].data.data;
                        } else {
                            $rootScope.$emit('notify', values[1].data.msg);
                        }

                        defer.resolve(result);
                    };
                } else if(info_type == 'location') {
                    ajax_map.locations = fetchFromCache($http, $q, $rootScope, $request_urls.productSightseeing);
                    ajax_requests = [ajax_map.locations];

                    request_callback = function(values) {
                        if(values[0].data.code == 200) {
                            result.locations = values[0].data.data;
                        } else {
                            $rootScope.$emit('notify', values[0].data.msg);
                        }

                        defer.resolve(result);
                    };
                }

                if(ajax_requests.length) {
                    $q.all(ajax_requests).then(request_callback);
                } else {
                    defer.resolve(result);
                }

                return defer.promise;
            }
        ]
    };

    p_resolve.ProductService = {
        loadData : function($http, $q, $route, $rootScope) {
            var defer = $q.defer();
            var result = {};
            var ajax_map = {};
            var service_type = $route.current.params.service_type;
            var ajax_requests = [];
            var request_callback;

            if(service_type == '_tourplan') {
                ajax_map.tour = fetchFromCache($http, $q, $rootScope, $request_urls.getTourPlanDetail);
                ajax_requests = [ajax_map.tour];

                request_callback = function(values) {
                    if(values[0].data.code == 200) {
                        result.tour = values[0].data.data;
                    } else {
                        $rootScope.$emit('notify', values[0].data.msg);
                    }

                    defer.resolve(result);
                };
            } else if(service_type == 'include') {
                ajax_map.desc = fetchFromCache($http, $q, $rootScope, $request_urls.getProductDescription);
                ajax_requests = [ajax_map.desc];

                request_callback = function(values) {
                    if(values[0].data.code == 200) {
                        result.desc = values[0].data.data;
                    } else {
                        $rootScope.$emit('notify', values[0].data.msg);
                    }

                    defer.resolve(result);
                };
            } else if(service_type == 'introduce_multi_day_general') {
                //TODO - Migrate
                defer.resolve(result);
            } else if(service_type == 'pass_classic' || service_type == 'pass_other') {
                ajax_map.album = fetchFromCache($http, $q, $rootScope, $request_urls.getProductAlbum);
                ajax_requests = [ajax_map.album];

                request_callback = function(values) {
                    if(values[0].data.code == 200) {
                        result.album = values[0].data.data;
                    } else {
                        $rootScope.$emit('notify', values[0].data.msg);
                    }

                    defer.resolve(result);
                };
            }

            if(ajax_requests.length) {
                $q.all(ajax_requests).then(request_callback);
            } else {
                defer.resolve(result);
            }

            return defer.promise;
        }
    };

    p_resolve.ProductNotice = {
        loadData : function($http, $q, $route, $rootScope) {
            var defer = $q.defer();
            var result = {};
            var ajax_map = {};
            var notice_type = $route.current.params.notice_type;
            var ajax_requests = [];
            var request_callback;

            if(notice_type == 'note') {
                ajax_map.note = fetchFromCache($http, $q, $rootScope, $request_urls.productIntroduction);
                ajax_requests = [ajax_map.note];

                request_callback = function(values) {
                    if(values[0].data.code == 200) {
                        result.buy_note = values[0].data.data.buy_note;
                    } else {
                        $rootScope.$emit('notify', values[0].data.msg);
                    }

                    defer.resolve(result);
                };
            } else if(notice_type == 'rule') {
                ajax_map.rule = fetchFromCache($http, $q, $rootScope, $request_urls.getProductRules);
                ajax_requests = [ajax_map.rule];

                request_callback = function(values) {
                    if(values[0].data.code == 200) {
                        result.rules = values[0].data.data;
                    } else {
                        $rootScope.$emit('notify', values[0].data.msg);
                    }

                    defer.resolve(result);
                };
            }

            if(ajax_requests.length) {
                $q.all(ajax_requests).then(request_callback);
            } else {
                defer.resolve(result);
            }

            return defer.promise;
        }
    };

    p_resolve.ProductRedeem = {
        loadData : function($http, $q, $route, $rootScope) {
            var defer = $q.defer();
            var result = {};
            var ajax_map = {};
            var redeem_type = $route.current.params.redeem_type;
            var ajax_requests = [];
            var request_callback;

            if(redeem_type == 'place') {
                ajax_map.usage = fetchFromCache($http, $q, $rootScope, $request_urls.productIntroduction);
                ajax_map.place = fetchFromCache($http, $q, $rootScope, $request_urls.getProductPickTicketAlbum);
                ajax_requests = [ajax_map.usage, ajax_map.place];

                request_callback = function(values) {
                    if(values[0].data.code == 200) {
                        result.all_data = values[0].data.data;
                    } else {
                        $rootScope.$emit('notify', values[0].data.msg);
                    }
                    if(values[1].data.code == 200) {
                        result.place = values[1].data.data;
                    } else {
                        $rootScope.$emit('notify', values[1].data.msg);
                    }

                    defer.resolve(result);
                };
            } else if(redeem_type == 'usage') {
                ajax_map.usage = fetchFromCache($http, $q, $rootScope, $request_urls.productIntroduction);
                ajax_requests = [ajax_map.usage];

                request_callback = function(values) {
                    if(values[0].data.code == 200) {
                        result.all_data = values[0].data.data;
                    } else {
                        $rootScope.$emit('notify', values[0].data.msg);
                    }

                    defer.resolve(result);
                };
            }

            if(ajax_requests.length) {
                $q.all(ajax_requests).then(request_callback);
            } else {
                defer.resolve(result);
            }

            return defer.promise;
        }
    };

    p_resolve.ProductQna = {
        loadData : function($http, $q, $rootScope) {
            var defer = $q.defer();
            var result = {};

            var ajax_qna = fetchFromCache($http, $q, $rootScope, $request_urls.getProductQna);
            $q.all([ajax_qna]).then(function(values) {
                if(values[0].data.code == 200) {
                    result.qna = values[0].data.data;
                } else {
                    $rootScope.$emit('notify', values[0].data.msg);
                }

                defer.resolve(result);
            });

            return defer.promise;
        }
    };

    p_resolve.ProductComment = {
        loadData : function($http, $q, $rootScope) {
            var defer = $q.defer();
            var result = {};

            var ajax_comment = fetchFromCache($http, $q, $rootScope, $request_urls.productComments);
            $q.all([ajax_comment]).then(function(values) {
                if(values[0].data.code == 200) {
                    result.comments = values[0].data.data;
                } else {
                    $rootScope.$emit('notify', values[0].data.msg);
                }

                defer.resolve(result);
            });

            return defer.promise;
        }
    };

    p_resolve.ProductBundle = {
        loadData : function($http, $q, $rootScope) {
            var defer = $q.defer();
            var result = {};

            var ajax_bundle = fetchFromCache($http, $q, $rootScope, $request_urls.getBundleList);
            $q.all([ajax_bundle]).then(function(values) {
                if(values[0].data.code == 200) {
                    result.bundles = values[0].data.data;
                } else {
                    $rootScope.$emit('notify', values[0].data.msg);
                }

                defer.resolve(result);
            });

            return defer.promise;
        }
    };

    p_resolve.ProductFeedback = {
        loadData : function($http, $q, $rootScope) {
            var defer = $q.defer();
            var result = {};
            var ajax_comment = fetchFromCache($http, $q, $rootScope, $request_urls.getProductFeedback);

            $q.all([ajax_comment]).then(function(values) {
                if(values[0].data.code == 200) {
                    result.feedback = values[0].data.data;
                } else {
                    $rootScope.$emit('notify', values[0].data.msg);
                }

                defer.resolve(result);
            });

            return defer.promise;
        }
    };

    p_resolve.HotelRoom = {
        loadData : function($http, $q, $rootScope) {
            var defer = $q.defer();
            var result = {};
            var ajax_room = fetchFromCache($http, $q, $rootScope, $request_urls.hotelRoomType);
            var ajax_services = fetchFromCache($http, $q, $rootScope, $request_urls.serviceItems);

            $q.all([ajax_room, ajax_services]).then(function(values) {
                if(values[0].data.code == 200 && values[1].data.code == 200) {
                    var rooms = values[0].data.data;
                    var room_services = values[1].data.data;


                    for(var i in rooms) {
                        rooms[i].selected_service = [];
                        for(var p in rooms[i].policies) {
                            if(rooms[i].policies[p].age_range.length > 0) {
                                var ages = rooms[i].policies[p].age_range.split("-");
                                rooms[i].policies[p].age_1 = ages[0];
                                rooms[i].policies[p].age_2 = ages[1];
                            } else {
                                rooms[i].policy_tips = rooms[i].policies[p].policy;
                            }
                        }

                        for(var n in rooms[i].services) {
                            for(var j in room_services) {
                                if(rooms[i].services[n].service_id == room_services[j].service_id) {
                                    rooms[i].services[n].name = room_services[j].name;
                                    rooms[i].selected_service.push(rooms[i].services[n].service_id);
                                }
                            }
                        }
                        result.rooms = rooms;
                        result.room_services = room_services;
                    }
                } else {
                    $rootScope.$emit('notify', values[0].data.msg);
                    $rootScope.$emit('notify', values[1].data.msg);
                }


                defer.resolve(result);
            });

            return defer.promise;
        }
    };


    //价格
    p_resolve.ProductPrice = {
        loadData : function($http, $q, $rootScope, $route) {
            var defer = $q.defer();
            var result = {};
            var ajax_map = {};
            var price_type = $route.current.params.price_type;
            var ajax_requests = [];
            var request_callback;

            //针对tab去做ajax加载
            if(['price_plan_list', 'special_price_plan_list'].indexOf(price_type) > -1) {
                result.is_special_plan = price_type == 'special_price_plan_list';
                var url = result.is_special_plan ? $request_urls.productPricePlanSpecials :
                          $request_urls.productPricePlans;
                ajax_map.ajax_plan_info = fetchFromCache($http, $q, $rootScope, $request_urls.productPricePlanBasicInfo);
                ajax_map.ajax_price_plans = fetchFromCache($http, $q, $rootScope, url);
                ajax_requests = [ajax_map.ajax_plan_info, ajax_map.ajax_price_plans];

                request_callback = function(values) {
                    if(values[0].data.code == 200) {
                        result.plan_info = values[0].data.data;
                    } else {
                        $rootScope.$emit('notify', values[0].data.msg);
                    }
                    if(values[1].data.code == 200) {
                        result.price_plans = values[1].data.data;
                    } else {
                        $rootScope.$emit('notify', values[1].data.msg);
                    }

                    defer.resolve(result);
                };
            } else if(['edit_price_plan', 'edit_special_price_plan'].indexOf(price_type) > -1) {
                result.is_special_plan = price_type == 'edit_special_price_plan';

                var plans_url = result.is_special_plan ? $request_urls.productPricePlanSpecials :
                                $request_urls.productPricePlans;
                var price_plan_id = $route.current.params.price_plan_id;

                ajax_map.ajax_plan_info = fetchFromCache($http, $q, $rootScope, $request_urls.productPricePlanBasicInfo);
                ajax_map.ajax_all_plans = fetchFromCache($http, $q, $rootScope, plans_url);

                ajax_requests = [ajax_map.ajax_plan_info, ajax_map.ajax_all_plans];

                if(price_plan_id) {
                    var plan_url = result.is_special_plan ? $request_urls.productPricePlanSpecial :
                                   $request_urls.productPricePlan;

                    ajax_map.ajax_one_plan = fetchFromCache($http, $q, $rootScope, plan_url + price_plan_id);
                    ajax_requests.push(ajax_map.ajax_one_plan);
                }

                request_callback = function(values) {
                    if(values[0].data.code == 200) {
                        result.plan_info = values[0].data.data;
                    } else {
                        $rootScope.$emit('notify', values[0].data.msg);
                    }
                    if(values[1].data.code == 200) {
                        result.price_plans = values[1].data.data;
                    } else {
                        $rootScope.$emit('notify', values[1].data.msg);
                    }
                    if(values[2] && values[2].data.code == 200) {
                        result.current_plan = values[2].data.data;
                    } else {
                        result.current_plan = {};
                    }

                    defer.resolve(result);
                };
            } else if(price_type == 'sale_attribute') {
                ajax_map.ticket_type = fetchFromCache($http, $q, $rootScope, $request_urls.ticketTypes);
                ajax_map.ticket_rule = fetchFromCache($http, $q, $rootScope, $request_urls.ticketRules);
                ajax_map.date_rule = fetchFromCache($http, $q, $rootScope, $request_urls.getDateRule);
                ajax_map.sale_rule = fetchFromCache($http, $q, $rootScope, $request_urls.getSaleRule);
                ajax_requests = [ajax_map.ticket_type, ajax_map.ticket_rule, ajax_map.date_rule, ajax_map.sale_rule];

                request_callback = function(values) {
                    if(values[0].data.code == 200) {
                        result.ticket_type = values[0].data.data;
                    } else {
                        $rootScope.$emit('notify', values[0].data.msg);
                    }
                    if(values[1].data.code == 200) {
                        result.ticket_rule = values[1].data.data;
                    } else {
                        $rootScope.$emit('notify', values[1].data.msg);
                    }
                    if(values[2].data.code == 200) {
                        result.date_rule = values[2].data.data;
                    } else {
                        $rootScope.$emit('notify', values[2].data.msg);
                    }
                    if(values[3].data.code == 200) {
                        result.sale_rule = values[3].data.data;
                    } else {
                        $rootScope.$emit('notify', values[3].data.msg);
                    }

                    defer.resolve(result);
                };
            } else if(price_type == 'departure_point') {
                ajax_map.departure_point = fetchFromCache($http, $q, $rootScope, $request_urls.departurePlans);
                ajax_map.sale_date = fetchFromCache($http, $q, $rootScope, $request_urls.getProductSaleDateRule);
                ajax_requests = [ajax_map.departure_point, ajax_map.sale_date];

                request_callback = function(values) {
                    if(values[0].data.code == 200) {
                        result.departure_point = values[0].data.data;
                    } else {
                        $rootScope.$emit('notify', values[0].data.msg);
                    }
                    if(values[1].data.code == 200) {
                        result.sale_date = values[1].data.data;
                    } else {
                        $rootScope.$emit('notify', values[1].data.msg);
                    }

                    defer.resolve(result);
                };
            } else if(price_type == 'special_code') {
                ajax_map.special_groups = fetchFromCache($http, $q, $rootScope, $request_urls.productSpecialGroup);
                ajax_requests = [ajax_map.special_groups];
                request_callback = function(values) {
                    result.special_groups = values[0].data.data;

                    defer.resolve(result);
                };
            }

            if(ajax_requests.length) {
                $q.all(ajax_requests).then(request_callback);
            } else {
                defer.resolve(result);
            }

            return defer.promise;
        }
    };

    //商品运营
    p_resolve.ProductSeo = {
        loadData : function($http, $q, $rootScope) {
            var defer = $q.defer();
            var result = {};

            var ajax_seo = fetchFromCache($http, $q, $rootScope, $request_urls.productSeo);
            $q.all([ajax_seo]).then(function(values) {
                if(values[0].data.code == 200) {
                    result.seo = values[0].data.data;
                } else {
                    $rootScope.$emit('notify', values[0].data.msg);
                }

                defer.resolve(result);
            });

            return defer.promise;
        }
    };

    p_resolve.RelatedProduct = {
        loadData : function($http, $q, $rootScope) {
            var defer = $q.defer();
            var result = {};

            var ajax_related = fetchFromCache($http, $q, $rootScope, $request_urls.getProductRelated);
            $q.all([ajax_related]).then(function(values) {
                if(values[0].data.code == 200) {
                    result.related = values[0].data.data;
                } else {
                    $rootScope.$emit('notify', values[0].data.msg);
                }

                defer.resolve(result);
            });

            return defer.promise;
        }
    };

    p_resolve.CouponTemplate = {
        loadData : function($http, $q, $rootScope) {
            var defer = $q.defer();
            var result = {};

            var ajax_coupon = fetchFromCache($http, $q, $rootScope, $request_urls.getProductCouponTemplateList);
            $q.all([ajax_coupon]).then(function(values) {
                if(values[0].data.code == 200) {
                    result.templates = values[0].data.data;
                } else {
                    $rootScope.$emit('notify', values[0].data.msg);
                }

                defer.resolve(result);
            });

            return defer.promise;
        }
    };
})();