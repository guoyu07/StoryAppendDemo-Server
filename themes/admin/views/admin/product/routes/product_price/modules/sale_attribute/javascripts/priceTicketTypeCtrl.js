controllers.priceTicketTypeCtrl = function($scope, $rootScope, $http) {
    $scope.data = {
        'ticket_type' : {}
    };
    $scope.local = {
        ticket_type         : {
            map : {}
        },
        section_head        : {
            ticket_type : {
                title    : '票种设置',
                editCb   : function() {
                    if($scope.$parent.isEditable()) {
                        $scope.local.section_head.ticket_type.is_edit = true;
                    }
                },
                updateCb : function() {
                    $scope.saveChanges();
                }
            }
        },
        radio_options       : {
            'ticket_type' : {
                name     : 'ticket_type',
                class    : 'inline',
                items    : {
                    '1' : '一种票',
                    '2' : '区分成人／儿童',
                    '3' : '多种票'
                },
                callback : function() {
                    $scope.changeTicketType();
                }
            }
        },
        default_ticket_rule : {
            age         : {
                begin : '',
                end   : ''
            },
            age_range   : '',
            ticket_id   : '',
            description : ''
        }
    };


    function getEmptyTicketRule(ticket_id) {
        var ticket_rule = angular.copy($scope.local.default_ticket_rule);
        ticket_rule.ticket_id = ticket_id;

        return ticket_rule;
    }

    $scope.init = function() {
        if(!$scope.$parent.result || Object.keys($scope.$parent.result).length == 0) return;

        $scope.local.ticket_type.all = angular.copy($scope.$parent.result.ticket_type);
        $scope.local.old_ticket_type = $scope.data.ticket_type.ticket_type;
        $scope.data.ticket_type = angular.copy($scope.$parent.result.ticket_rule);

        $scope.data.ticket_type.ticket_rules = $scope.data.ticket_type.ticket_rules.map(function(one_rule) {
            one_rule.age = {begin : '', end : ''};
            var range_parts = one_rule.age_range.split('-');

            if(range_parts.length == 2) {
                one_rule.age.begin = parseInt(range_parts[0], 10);
                one_rule.age.end = parseInt(range_parts[1], 10);
            }

            return one_rule;
        });

        $scope.local.ticket_type.all.shift(); //Why?
        $scope.local.ticket_type.all.forEach(function(one_type) {
            $scope.local.ticket_type.map[one_type.ticket_id] = one_type.cn_name;
        });
    };

    $scope.addTicket = function() {
        var new_ticket = {
            age         : {
                begin : '',
                end   : ''
            },
            age_range   : '',
            ticket_id   : '4',
            description : ''
        };

        $scope.data.ticket_type.ticket_rules.push(new_ticket);
    };
    $scope.deleteTicket = function(index) {
        if($scope.data.ticket_type.ticket_rules.length > 3) {
            $scope.data.ticket_type.ticket_rules.splice(index, 1);
        } else {
            $rootScope.$emit('notify', {msg : '票种不能小于3。'});
        }
    };

    $scope.changeTicketId = function(ticket, rule) {
        rule.ticket_id = ticket;
    };
    $scope.changeTicketType = function() {
        alert('更改票种后会导致之前设定的售卖方式和价格计划失效，请重新设定售卖方式和价格计划。');
        //flag for backend to trigger data cleaning
        if($scope.local.old_ticket_type != $scope.data.ticket_type.ticket_type) {
            $scope.data.ticket_type.reset_ticket_type = 1;
        }

        if($scope.data.ticket_type.ticket_type == 1) {
            $scope.data.ticket_type.ticket_rules = [
                getEmptyTicketRule(1)
            ];
        } else if($scope.data.ticket_type.ticket_type == 2 &&
                  (!$scope.data.ticket_type.ticket_rules || $scope.data.ticket_type.ticket_rules.length != 2)) {
            $scope.data.ticket_type.ticket_rules = [
                getEmptyTicketRule(2), getEmptyTicketRule(3)
            ];
        } else if($scope.data.ticket_type.ticket_type == 3 &&
                  (!$scope.data.ticket_type.ticket_rules || $scope.data.ticket_type.ticket_rules.length < 3)) {
            $scope.data.ticket_type.ticket_rules = [
                getEmptyTicketRule(2), getEmptyTicketRule(3), getEmptyTicketRule(5)
            ];
        }
    };

    $scope.saveChanges = function() {
        var i, len, one_rule, prev_rule;
        var types = [];

        $scope.data.ticket_type.ticket_rules.sort(function(rule_a, rule_b) {
            return +rule_a.age.begin - +rule_b.age.begin;
        });

        for(i = 0, len = $scope.data.ticket_type.ticket_rules.length; i < len; i++) {
            one_rule = $scope.data.ticket_type.ticket_rules[i];
            prev_rule = $scope.data.ticket_type.ticket_rules[i - 1];

            //Age Check
            if(one_rule.age.begin > one_rule.age.end) {
                $rootScope.$emit('notify', {msg : '年龄范围，开始年龄不能大于结束年龄。'});
                return;
            }
            if(!!prev_rule) {
                if(one_rule.age.begin <= prev_rule.age.end) {
                    $rootScope.$emit('notify', {msg : '年龄范围不能重叠。'});
                    return;
                }
                if(one_rule.age.begin != prev_rule.age.end + 1) {
                    if(!window.confirm('年龄范围有空隙，确认继续')) return;
                }
            }

            //Prevent Duplicate Ticket Types
            if($scope.data.ticket_type.ticket_type == '3') {
                if(types.indexOf(one_rule.ticket_id) > -1) {
                    $rootScope.$emit('notify', {msg : '不能有重复的票种'});
                    return;
                } else {
                    types.push(one_rule.ticket_id);
                }
            } else if($scope.data.ticket_type.ticket_type == '1') {
                one_rule.is_independent = 1;
            }

            //Format Age Range
            if(one_rule.age.begin == '' && one_rule.age.end == '') {
                one_rule.age_range = '';
            } else {
                one_rule.age_range = one_rule.age.begin + '-' + one_rule.age.end;
            }
        }

        $http.post($request_urls.ticketRules, $scope.data.ticket_type).success(function(data) {
            if(data.code == 200) {
                $scope.local.section_head.ticket_type.is_edit = false;
                $scope.local.old_ticket_type = $scope.data.ticket_type.ticket_type;
                window.location.reload();
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };


    $scope.init();
};

app.controller('priceTicketTypeCtrl', [
    '$scope', '$rootScope', '$http', controllers.priceTicketTypeCtrl
]);