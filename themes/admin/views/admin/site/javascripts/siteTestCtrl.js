controllers.SiteTestCtrl = function($scope, $rootScope, $sce, $timeout, $http) {
    $scope.data_set = {
        cities : [
            {
                "country_code" : "US",
                "city_code"    : "ANA",
                "cn_name"      : "\u963f\u7eb3\u6d77\u59c6",
                "en_name"      : "ANAHEIM",
                "pinyin"       : "a na hai mu",
                "link_url"     : "\/city\/index\/city_code\/ANA"
            },
            {
                "country_code" : "US",
                "city_code"    : "ORL",
                "cn_name"      : "\u5965\u5170\u591a",
                "en_name"      : "Orlando",
                "pinyin"       : "ao lan duo",
                "link_url"     : "\/city\/index\/city_code\/ORL"
            },
            {
                "country_code" : "US",
                "city_code"    : "BOS",
                "cn_name"      : "\u6ce2\u58eb\u987f",
                "en_name"      : "Boston",
                "pinyin"       : "bo shi dun",
                "link_url"     : "\/city\/index\/city_code\/BOS"
            },
            {
                "country_code" : "US",
                "city_code"    : "PHL",
                "cn_name"      : "\u8d39\u57ce",
                "en_name"      : "Philadelphia",
                "pinyin"       : "fei cheng",
                "link_url"     : "\/city\/index\/city_code\/PHL"
            },
            {
                "country_code" : "US",
                "city_code"    : "WAS",
                "cn_name"      : "\u534e\u76db\u987f",
                "en_name"      : "Washington",
                "pinyin"       : "hua sheng dun",
                "link_url"     : "\/city\/index\/city_code\/WAS"
            }
        ],
        fruits : ['apple', 'banana', 'citrus', 'dragon fruit', 'eggplant']
    };

    //$scope.data used for initial data to be shown
    $scope.data = {
        input        : {
            date        : new Date(),
            test_input  : '世界你好',
            test_input2 : 'hello world'
        },
        blocks       : [
            {}
        ],
        chosen       : {
            current_city             : '',
            current_city_with_search : '',
            current_cities           : []
        },
        input_tag    : {
            all_tags : [
                {
                    title : 'hello'
                }
            ]
        },
        input_set    : {
            one_fruit       : '',
            tagged_fruits   : [],
            selected_fruits : []
        },
        tags_select  : {
            select_tags  : ['fruit', 'grape', 'honeydew', 'kiwi', 'watermelon', 'zucchini', 'pickle', 'pepper'],
            all_tag_sets : [
                {
                    title : 'starting with A',
                    tags  : ['avocado', 'apple', 'apologize']
                },
                {
                    title : 'B打头',
                    tags  : ['believe', 'belfast', 'bieber']
                }
            ]
        },
        section_head : {
            title : '使用日期'
        },
        section_body : {
            view_rows : [
                {
                    title : 'E开头',
                    body  : 'egregious ecclesiastical',
                    items : [
                        {
                            title : 'ea',
                            body  : 'Easter'
                        },
                        {
                            title : 'ed',
                            body  : 'edifice'
                        }
                    ]
                },
                {
                    title : 'G开头',
                    body  : 'gangster gamification'
                }
            ]
        }
    };

    //$scope.local used as a config for the directive or other things.
    $scope.local = {
        overlay        : {
            options_one : {
                type    : 'alert',
                target  : {
                    selector : '#test_alert',
                    action   : 'click'
                },
                message : '保存成功',
                buttons : '确定'
            },
            options_two : {
                type    : 'confirm',
                target  : {
                    selector : '#test_confirm'
                },
                title   : '确定这个动作吗？',
                message : '<p class="small-desc">我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字</p>',
                buttons : [
                    {
                        label : '继续修改',
                        cb    : function(next) {
                            alert('你点了继续修改');
                            next();
                        }
                    },
                    {
                        label : '取消'
                    }
                ]
            }
        },
        uploader       : {
            options : {
                target    : $request_urls.upload,
                image_url : ''
            }
        },
        dropdown       : {
            values  : {
                input  : '',
                option : ''
            },
            options : {
                items : {
                    'day'   : '日',
                    'month' : '月',
                    'year'  : '年'
                }
            }
        },
        markdown       : {
            input   : '',
            output  : '',
            options : {
                required : false
            }
        },
        radio_switch   : {
            value   : {
                'value' : ''
            },
            options : {
                name  : 'value',
                items : {
                    'hello' : '你好',
                    'world' : '世界'
                }
            }
        },
        radio_switch_1 : {
            value   : {
                'value' : ''
            },
            options : {
                name     : 'value',
                items    : {
                    'hello' : '你好',
                    'world' : '世界'
                },
                notice   : true,
                comments : {
                    'hello' : '我是你好的注释',
                    'world' : '我是世界的注释'
                }
            }
        },
        add_tags       : {
            in_progress : false,
            is_error    : true,
            message     : ''
        },
        input_tag      : {
            btn_text    : '关联商品',
            title_str   : 'title',
            placeholder : '商品ID',
            cb          : function(tag, next) {
                $timeout(function() {
                    next(false, {
                        title : tag
                    });
                }, 1000);
            }
        },
        section_head   : {
            is_edit  : false,
            updateCb : function() {
                $scope.data.section_head.title = '<span>在这个callback做保存</span>';
                $scope.local.section_head.is_edit = false;
            },
            editCb   : function() {
                $scope.data.section_head.title = '<span>在这个callback做模式切换</span>';
                $scope.local.section_head.is_edit = true;
            }
        }
    };

    /*
     * each js file contains a init func and have some rootscope emit event to broadcast to the whole app.
     */
    $scope.init = function() {
        $timeout(function() {
            $rootScope.$emit('loadStatus', false);
        }, 3000);

        $rootScope.$emit('setBreadcrumb', {
            back : {
                content      : false,
                part_content : false
            },
            body : {
                content : '测试页面',
                clickCb : function() {
                    alert('已点击');
                }
            }
        });

        $http.post(pathinfo.base_dir + 'product/fetchProducts', {
            sort         : {
                product_id : 0
            },
            paging       : {
                start : 0,
                limit : 20
            },
            query_filter : {
                supplier_id : 11
            }
        }).success(function(data) {
            console.log(data);
        });

        $http.post($request_urls.verifyPhone, {
            phone_no : '15510287463'
        }).then(function(values) {

        });
    };

    $scope.addBlock = function() {
        $scope.data.blocks.push({});
    };
    $scope.delBlock = function(index) {
        $scope.data.blocks.splice(index, 1);
    };

    $scope.addTag = function() {
        $scope.local.add_tags.in_progress = true;
        $timeout(function() {
            $scope.local.add_tags.in_progress = false;
            //      $scope.local.add_tags.is_error = true;
            //      $scope.local.add_tags.message = '报错了哟';
            $scope.data.all_tags.push($scope.data.input_tag);
        }, 1500);
    };
    $scope.delTag = function(index) {
        $scope.data.all_tags.splice(index, 1);
    };

    $scope.toggleSelection = function(item, collection) {
        var index = collection.indexOf(item);
        if(index > -1) {
            collection.splice(index, 1);
        } else {
            collection.push(item);
        }
    };

    $scope.alertDefaultMsg = function() {
        $rootScope.$emit('notify', {
            msg : '上传成功什么的都是骗你的'
        });
    };

    $scope.init();
};

app.controller('SiteTestCtrl', ['$scope', '$rootScope', '$sce', '$timeout', '$http', controllers.SiteTestCtrl]);