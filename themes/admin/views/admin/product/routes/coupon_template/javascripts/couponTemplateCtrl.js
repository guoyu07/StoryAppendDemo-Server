controllers.CouponTemplateCtrl = function($scope, $rootScope, $route, $filter) {
    $scope.data = {};
    $scope.local = {
        tab_options   : {
            tabs        : [
                {
                    label  : '已生效模版',
                    status : '1'
                },
                {
                    label  : '未生效模版',
                    status : '0'
                }
            ],
            current_tab : ''
        },
        radio_options : {
            is_coupon_related : {
                name  : 'is_coupon_related',
                items : {
                    '0' : '不需要',
                    '1' : '需要'
                }
            },
            related_type      : {
                name  : 'related_type',
                items : {
                    '0' : '全部挂接',
                    '1' : '选择性挂接'
                }
            }
        },
        edit_template : $request_urls.editCouponTemplateUrl
    };


    function formatDropdown(str) {
        return str.replace(/day/gi, '日').replace(/month/gi, '月').replace(/year/gi, '年');
    }

    function formatTemplate(template) {
        if(template.date_type == '0') {
            template.date_str = '绝对日期（' + $filter('date')(new Date(template.date_start), 'yyyy-MM-dd') + ' － ' +
                                $filter('date')(new Date(template.date_end), 'yyyy-MM-dd') + '）';
        } else if(template.date_type == '1') {
            template.date_str = '自下单日期起' + '（优惠券自下单日期' + formatDropdown(template.start_offset) + ' 后可使用；优惠券有效期为' + formatDropdown(template.end_range) + '）';
        }

        if(template.template_coupon) {
            template.discount_str = ( template.template_coupon.type == 'P' ? '折扣' : '现金减免' ) + ' / ' +
                                    template.template_coupon.discount;

            if(template.template_coupon.limit_ids.length) {
                template.usage_str = (template.template_coupon.limit_type == 0 ? '不' : '') + '可以使用的';
                if(template.template_coupon.valid_type == 1) {
                    template.usage_str += '商品';
                } else if(template.template_coupon.valid_type == 2) {
                    template.usage_str += '城市';
                } else if(template.template_coupon.valid_type == 3) {
                    template.usage_str += '国家';
                }

                var tmp = template.template_coupon.limit_ids.reduce(function(prev, curr) {
                    return prev + curr.name;
                }, '');

                template.usage_str += '（' + tmp + '）';
            } else {
                template.usage_str = '不限制使用商品（全球券）';
            }
        } else {
            template.discount_str = '';
            template.usage_str = '';
        }

        return template;
    }

    $scope.init = function() {
        $scope.data = angular.copy($route.current.locals.loadData);
        $scope.local.path_name = helpers.getRouteTemplateName($route.current);
        $scope.local.tab_options.current_tab = $scope.local.tab_options.tabs[0];

        $scope.data.templates = $scope.data.templates.map(formatTemplate);
    };

    $scope.addTemplate = function() {
        $scope.data.templates.unshift({
            status          : '1',
            date_str        : '',
            usage_str       : '',
            discount_str    : '',
            template_coupon : {
                name        : '新的模版',
                description : ''
            }
        });
        window.open($request_urls.newCouponTemplateUrl.replace(/xxx/gi, $rootScope.product.name), '_blank');
    };


    $scope.init();
};

app.controller('CouponTemplateCtrl', [
    '$scope', '$rootScope', '$route', '$filter', controllers.CouponTemplateCtrl
]);