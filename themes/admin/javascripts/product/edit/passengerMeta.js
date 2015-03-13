var selectPassengerInfoTypeCtrl = function($scope, ProductEditFactory) {

    $scope.currentType = ProductEditFactory.current_passenger_type;

};

var editPassengerInfoCtrl = function($scope, $rootScope, $http, $routeParams, $location, ProductEditFactory) {
    $scope.data = {};

    $http.get(request_urls.getProduct).success(function(data) {
        if(data.code == 200) {
            $scope.data = data.data;
        }
    });
};

var editNormalPassengerInfoCtrl = function($scope, $rootScope, $http, $routeParams, $location, ProductEditFactory) {
    if(!$routeParams.type) {
        ProductEditFactory.getPassengerRule().then(function(data) {
            var route;
            if(data.code == 200) {
                route = ProductEditFactory.getRoute(data.data.need_passenger_num, data.data.need_lead);
            }

            route = route ? '/editPassengerInfo/' + route : '/selectPassengerInfoType';
            $location.path(route);
        });
        return;
    }

    //Set Type
    ProductEditFactory.current_passenger_type = $routeParams.type;
    $scope.current_types = ProductEditFactory.passenger_info_types[ $routeParams.type ];
    $scope.lead_info = {
        cn_name : '领队',
        en_name : 'lead'
    };

    //Get Info
    ProductEditFactory.getProductPassengerRule().then(function(data) {
        $scope.data = data;
        $scope.is_GTA = ProductEditFactory.is_GTA;
    });

    //Save
    $scope.submitChanges = function() {
        if($scope.is_GTA) {
            alert('GTA商品不允许编辑该信息。');
            return;
        }

        var output = ProductEditFactory.getNeed($routeParams.type);
        var separator = ',';

        $scope.data.need_passenger_num = output.need_passenger_num;
        $scope.data.need_lead = output.need_lead;

        $scope.data.other_rules.need_passenger_num = output.need_passenger_num;
        $scope.data.other_rules.need_lead = output.need_lead;

        if($scope.data.need_lead == 1) {
            $scope.data.other_rules.lead_fields = $scope.data.other_rules.lead_field_items.join(separator);
            if($scope.data.other_rules.lead_fields.indexOf(separator) == 0) {
                $scope.data.other_rules.lead_fields = $scope.data.other_rules.lead_fields.substr(1, $scope.data.other_rules.lead_fields.length -
                                                                                                    1);
            }
            if($scope.data.other_rules.lead_fields.lastIndexOf(separator) ==
               $scope.data.other_rules.lead_fields.length - 1) {
                $scope.data.other_rules.lead_fields = $scope.data.other_rules.lead_fields.substr(0, $scope.data.other_rules.lead_fields.length -
                                                                                                    1);
            }
        } else {
            $scope.data.other_rules.lead_fields = "";
            $scope.data.other_rules.lead_hidden_fields = "";
        }

        var valid = (output.need_lead == 0) || (output.need_lead && $scope.data.other_rules.lead_fields.length > 0);
        for(var i = 0, len = $scope.data.other_rules.rule_item.length; i < len; i++) {
            if($scope.data.need_passenger_num == 0) {
                $scope.data.other_rules.rule_item[ i ].fields = $scope.data.other_rules.rule_item[ i ].field_items.join(separator);
                if($scope.data.other_rules.rule_item[ i ].fields.length == 0) {
                    valid = false;
                    break;
                }

                if($scope.data.other_rules.rule_item[ i ].fields.indexOf(separator) == 0) {
                    $scope.data.other_rules.rule_item[ i ].fields = $scope.data.other_rules.rule_item[ i ].fields.substr(1, $scope.data.other_rules.rule_item[ i ].fields.length -
                                                                                                                            1);
                }
                if($scope.data.other_rules.rule_item[ i ].fields.lastIndexOf(separator) ==
                   $scope.data.other_rules.rule_item[ i ].fields.length - 1) {
                    $scope.data.other_rules.rule_item[ i ].fields = $scope.data.other_rules.rule_item[ i ].fields.substr(0, $scope.data.other_rules.rule_item[ i ].fields.length -
                                                                                                                            1);
                }
            } else {
                $scope.data.other_rules.rule_item[ i ].fields = "";
                $scope.data.other_rules.rule_item[ i ].hidden_fields = "";
            }
        }

        if(!valid) {
            alert('信息不完整。请检查后重试。');
            return;
        }

        $http.post(request_urls.updateProductPassengerRule, $scope.data.other_rules).success(function(data) {

            alert(data.msg);
        });
    };
};

var editPackagePassengerInfoCtrl = function($scope, $rootScope, $http, $routeParams, $location, ProductEditFactory) {
    $scope.data = {};
    $scope.local = {};

    $scope.init = function() {
        ProductEditFactory.getProductPassengerRule().then(function(data) {
            $scope.data = data.other_rules;
            $http.post(request_urls.getPassengerMetaData, {
                'order' : 'id'
            }).success(function(data) {
                $scope.local.all_criteria = data.data;
                $scope.is_GTA = false;
                //      $scope.is_GTA = ProductEditFactory.is_GTA;

                $scope.prepareData();
            });

        });
    }

    $scope.prepareData = function() {
        $scope.local.lead_field_array = [];
        $scope.local.other_field_array = [];
        var lead_field_array = [];
        var other_field_array = [];

        if($scope.data.need_lead == '1') {
            if($scope.data.lead_hidden_fields.indexOf(',') == 0) {
                $scope.data.lead_hidden_fields = $scope.data.lead_hidden_fields.substr(1, $scope.data.lead_hidden_fields.length -
                                                                                          1);
            }
            if($scope.data.lead_hidden_fields.lastIndexOf(',') == $scope.data.lead_hidden_fields.length - 1) {
                $scope.data.lead_hidden_fields = $scope.data.lead_hidden_fields.substr(0, $scope.data.lead_hidden_fields.length -
                                                                                          1);
            }
            if($scope.data.lead_fields.indexOf(',') == 0) {
                $scope.data.lead_fields = $scope.data.lead_fields.substr(1, $scope.data.lead_fields.length - 1);
            }
            if($scope.data.lead_fields.lastIndexOf(',') == $scope.data.lead_fields.length - 1) {
                $scope.data.lead_fields = $scope.data.lead_fields.substr(0, $scope.data.lead_fields.length -
                                                                            1);
            }
            lead_field_array = $scope.data.lead_fields.split(",");
            $scope.local.lead_hidden_array = $scope.data.lead_hidden_fields.split(",");
        }

        if($scope.data.need_passenger_num == "0") {
            if($scope.data.rule_item[0].hidden_fields.indexOf(',') == 0) {
                $scope.data.rule_item[0].hidden_fields = $scope.data.rule_item[0].hidden_fields.substr(1, $scope.data.rule_item[0].hidden_fields.length -
                                                                                                          1);
            }
            if($scope.data.rule_item[0].hidden_fields.lastIndexOf(',') ==
               $scope.data.rule_item[0].hidden_fields.length - 1) {
                $scope.data.rule_item[0].hidden_fields = $scope.data.rule_item[0].hidden_fields.substr(0, $scope.data.rule_item[0].hidden_fields.length -
                                                                                                          1);
            }
            if($scope.data.rule_item[0].fields.indexOf(',') == 0) {
                $scope.data.rule_item[0].fields = $scope.data.rule_item[0].fields.substr(1, $scope.data.rule_item[0].fields.length -
                                                                                            1);
            }
            if($scope.data.rule_item[0].fields.lastIndexOf(',') == $scope.data.rule_item[0].fields.length - 1) {
                $scope.data.rule_item[0].fields = $scope.data.rule_item[0].fields.substr(0, $scope.data.rule_item[0].fields.length -
                                                                                            1);
            }

            other_field_array = $scope.data.rule_item[0].fields.split(",");
            $scope.local.other_hidden_array = $scope.data.rule_item[0].hidden_fields.split(",");
        }

        for(var i in lead_field_array) {
            if(lead_field_array[i] == $scope.local.all_criteria[lead_field_array[i] - 1].id) {
                $scope.local.lead_field_array.push({
                    'item_id' : lead_field_array[i],
                    'label'   : $scope.local.all_criteria[lead_field_array[i] - 1].label
                });
            } else {
                for(var j in $scope.local.all_criteria) {
                    if(lead_field_array[i] == $scope.local.all_criteria[j].id) {
                        $scope.local.lead_field_array.push({
                            'item_id' : lead_field_array[i],
                            'label'   : $scope.local.all_criteria[j].label
                        });
                        break;
                    }
                }
            }
        }

        for(var i in other_field_array) {
            if(other_field_array[i] == $scope.local.all_criteria[other_field_array[i] - 1].id) {
                $scope.local.other_field_array.push({
                    'item_id' : other_field_array[i],
                    'label'   : $scope.local.all_criteria[other_field_array[i] - 1].label
                });
            } else {
                for(var j in $scope.local.all_criteria) {
                    if(other_field_array[i] == $scope.local.all_criteria[j].id) {
                        $scope.local.other_field_array.push({
                            'item_id' : other_field_array[i],
                            'label'   : $scope.local.all_criteria[j].label
                        });
                        break;
                    }
                }
            }
        }

    };

    $scope.toggleItem = function(item_id, group) {
        if(group == 'lead_field') {
            if($scope.local.lead_hidden_array.indexOf(item_id) == -1) {
                if(item_id == '4') {
                    alert("出生日期为必填项，请勿去除出生日期");
                    return;
                }
                $scope.local.lead_hidden_array.push(item_id);
            } else {
                $scope.local.lead_hidden_array.splice($scope.local.lead_hidden_array.indexOf(item_id), 1);
            }
        } else {
            if($scope.local.other_hidden_array.indexOf(item_id) == -1) {
                if(item_id == '4') {
                    alert("出生日期为必填项，请勿去除出生日期");
                    return;
                }
                $scope.local.other_hidden_array.push(item_id);
            } else {
                $scope.local.other_hidden_array.splice($scope.local.other_hidden_array.indexOf(item_id), 1);
            }
        }
    };

    $scope.submitChanges = function() {
        if($scope.data.need_lead == '1') {
            $scope.data.lead_hidden_fields = $scope.local.lead_hidden_array.toString();
            if($scope.data.lead_hidden_fields.indexOf(',') == 0) {
                $scope.data.lead_hidden_fields = $scope.data.lead_hidden_fields.substr(1, $scope.data.lead_hidden_fields.length -
                                                                                          1);
            }
            if($scope.data.lead_hidden_fields.lastIndexOf(',') == $scope.data.lead_hidden_fields.length - 1) {
                $scope.data.lead_hidden_fields = $scope.data.lead_hidden_fields.substr(0, $scope.data.lead_hidden_fields.length -
                                                                                          1);
            }
        }
        if($scope.data.need_passenger_num == "0") {
            $scope.data.rule_item[0].hidden_fields = $scope.local.other_hidden_array.toString();
            if($scope.data.rule_item[0].hidden_fields.indexOf(',') == 0) {
                $scope.data.rule_item[0].hidden_fields = $scope.data.rule_item[0].hidden_fields.substr(1, $scope.data.rule_item[0].hidden_fields.length -
                                                                                                          1);
            }
            if($scope.data.rule_item[0].hidden_fields.lastIndexOf(',') ==
               $scope.data.rule_item[0].hidden_fields.length - 1) {
                $scope.data.rule_item[0].hidden_fields = $scope.data.rule_item[0].hidden_fields.substr(0, $scope.data.rule_item[0].hidden_fields.length -
                                                                                                          1);
            }
        }

        var post_data = angular.copy($scope.data);

        $http.post(request_urls.updateProductPassengerRule, post_data).success(function(data) {

            alert(data.msg);
        });
    };

    $scope.refreshData = function() {
        $http.get(request_urls.updatePackagePassengerRule).success(function(data) {
            if(data.code == 200) {
                $scope.init();
            } else {
                alert(data.msg);
            }
        });
    };

    $scope.init();

};


angular.module('ProductEditApp').controller('selectPassengerInfoTypeCtrl', selectPassengerInfoTypeCtrl);

angular.module('ProductEditApp').controller('editPassengerInfoCtrl', [
    '$scope', '$rootScope', '$http', '$routeParams', '$location', 'ProductEditFactory', editPassengerInfoCtrl
]);

angular.module('ProductEditApp').controller('editNormalPassengerInfoCtrl', [
    '$scope', '$rootScope', '$http', '$routeParams', '$location', 'ProductEditFactory', editNormalPassengerInfoCtrl
]);

angular.module('ProductEditApp').controller('editPackagePassengerInfoCtrl', [
    '$scope', '$rootScope', '$http', '$routeParams', '$location', 'ProductEditFactory', editPackagePassengerInfoCtrl
]);