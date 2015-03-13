factories.pricePlanFactory = function() {
    var factory = {};
    var local = {};
    var current_plan_info = {};

    factory.weekdays = {
        'wd1'   : '周一',
        'wd2'   : '周二',
        'wd3'   : '周三',
        'wd4'   : '周四',
        'wd5'   : '周五',
        'wd6'   : '周六',
        'wd7'   : '周日',
        'wdall' : '全部'
    };

    function addTicketType(one_ticket) {
        local.result.ticket_types.push(one_ticket);
        local.result.ticket_types_obj[one_ticket.ticket_id] = one_ticket;
    }

    //region Init
    factory.initPlanInfo = function(plan_info, is_special_plan, is_new_plan) {
        local.config = {
            is_new_plan          : is_new_plan,
            has_code_combo       : false,
            is_special_plan      : is_special_plan,
            sale_in_package      : false,
            has_special_code     : false,
            total_ticket_count   : 0,
            total_ticket_types   : 0,
            total_special_groups : 0
        };
        local.result = {
            ticket_types     : [],
            ticket_types_obj : {},
            special_id_set   : [],
            special_code_set : []
        };
        current_plan_info = angular.copy(plan_info);


        //Special
        local.config.has_special_code = plan_info.has_special_code;
        if(local.config.has_special_code) {
            local.config.total_special_groups = plan_info.special_info.groups.length;
            if(local.config.total_special_groups > 1) {
                local.config.has_code_combo = true;
            }

            local.result.special_code_set = plan_info.special_info.special_codes.map(function(code) {
                return code.special_code;
            });
            local.result.special_id_set = plan_info.special_info.specials.map(function(code) {
                return code.special_id;
            });

            //Index
            local.index = {};
            plan_info.special_info.specials.forEach(function(one_special) {
                one_special.items.forEach(function(one_item) {
                    if(!local.index.hasOwnProperty(one_item.group_id)) {
                        local.index[one_item.group_id] = {
                            ids   : [],
                            codes : []
                        };
                    }

                    local.index[one_item.group_id].ids.push(one_special.special_id);
                });
            });
            plan_info.special_info.special_codes.forEach(function(one_code) {
                if(!local.index.hasOwnProperty(one_code.group_id)) {
                    console.error('special item missing');
                }
                local.index[one_code.group_id].codes.push(one_code.special_code);
            });
        }

        //In Package; If so, only use one ticket type - tid 99
        local.config.sale_in_package = getIndexByProp(plan_info.ticket_types, 'ticket_id', 99) > -1;
        plan_info.ticket_types.forEach(function(one_ticket) {
            if((local.config.sale_in_package && one_ticket.ticket_id == 99) || !local.config.sale_in_package) {
                addTicketType(one_ticket);
            }
        });

        local.config.total_ticket_types = local.result.ticket_types.length;

        return angular.copy(local);
    };
    //endregion

    //region Format && Helper
    //region Item Update
    function copyItem(target_item, origin_item) {
        for(var i_key in origin_item) {
            if(target_item.hasOwnProperty(i_key)) {
                target_item[i_key] = origin_item[i_key];
            }
        }
    }
    function initSpecialCodeFrequency(current_plan) {
        current_plan.special_code_frequency = {};

        local.result.special_id_set.forEach(function(one_code) {
            current_plan.special_code_frequency[one_code] = [];
        });

        current_plan.items = current_plan.items.map(function(item) {
            if(angular.isString(item.frequency)) {
                item.frequency = item.frequency.split(';');
            }
            current_plan.special_code_frequency[item.special_code] = item.frequency.filter(function(one_part) {
                return one_part.length;
            });

            return item;
        });

        if(local.config.is_new_plan) {
            local.result.special_id_set.forEach(function(one_code) {
                current_plan.special_code_frequency[one_code] = [];
            });
        }

        return current_plan;
    }

    factory.isItemPriceValid = function(item) {
        return (item.price != 0);
    };
    factory.generateItems = function(current_plan) {
        var new_items, tmp_item;
        var base_item = {
            price          : 0,
            item_id        : '',
            frequency      : [],
            ticket_id      : '',
            cost_price     : 0,
            orig_price     : 0,
            is_special     : local.config.is_special_plan ? '1' : '0',
            price_plan_id  : '',
            supplier_price : ''
        };

        var has_tier = current_plan.need_tier_pricing == 1;
        var has_special = local.config.has_special_code;
        var special_codes = angular.copy(local.result.special_id_set);
        var ticket_types = local.result.ticket_types;
        var ticket_quantities = current_plan.config.ticket_quantities;

        if(!has_tier && !has_special) {
            new_items = ticket_types.map(function(one_type) {
                tmp_item = angular.copy(base_item);
                tmp_item.ticket_id = one_type.ticket_id;

                return tmp_item;
            });
        } else if(!has_tier && has_special) {
            new_items = special_codes.reduce(function(current_items, one_code) {
                var special_items = ticket_types.map(function(one_type) {
                    tmp_item = angular.copy(base_item);
                    tmp_item.ticket_id = one_type.ticket_id;
                    tmp_item.special_code = one_code;

                    return tmp_item;
                });

                return current_items.concat(special_items);
            }, []);
        } else if(has_tier && !has_special) {
            new_items = ticket_types.reduce(function(current_items, one_type) {
                var type_items = ticket_quantities.map(function(one_quantity) {
                    tmp_item = angular.copy(base_item);
                    tmp_item.quantity = one_quantity;
                    tmp_item.ticket_id = one_type.ticket_id;

                    return tmp_item;
                });

                return current_items.concat(type_items);
            }, []);
        } else if(has_tier && has_special) {
            new_items = special_codes.reduce(function(current_special_items, one_code) {
                var special_items = ticket_types.reduce(function(current_type_items, one_type) {
                    var type_items = ticket_quantities.map(function(one_quantity) {
                        tmp_item = angular.copy(base_item);
                        tmp_item.quantity = one_quantity;
                        tmp_item.ticket_id = one_type.ticket_id;
                        tmp_item.special_code = one_code;

                        return tmp_item;
                    });

                    return current_type_items.concat(type_items);
                }, []);

                return current_special_items.concat(special_items);
            }, []);
        }

        return new_items;
    };
    factory.sortPlanItems = function(items, has_tier) {
        //Sort -- special code, quantity, ticket_id
        items.sort(function(a, b) {
            if(local.config.has_special_code) {
                if(a.special_code != b.special_code) {
                    return local.result.special_code_set.indexOf(a.special_code) - local.result.special_code_set.indexOf(b.special_code);
                } else if(has_tier) {
                    if(a.quantity != b.quantity) {
                        return a.quantity - b.quantity;
                    } else {
                        return a.ticket_id - b.ticket_id;
                    }
                } else {
                    return a.ticket_id - b.ticket_id;
                }
            } else if(has_tier) {
                if(a.quantity != b.quantity) {
                    return a.quantity - b.quantity;
                } else {
                    return a.ticket_id - b.ticket_id;
                }
            } else {
                return a.ticket_id - b.ticket_id;
            }
        });

        return items;
    };
    factory.fillItems = function(current_plan, new_set_items) {
        if(!new_set_items.length) return;

        var current_items = angular.copy(current_plan.items);
        var ticket_types = local.result.ticket_types;
        var special_codes = angular.copy(local.result.special_id_set);
        var ticket_quantities = current_plan.config.ticket_quantities;

        var i, len, index, q_index, s_index, t_index, item, tmp;
        var has_tier = current_plan.need_tier_pricing == 1;
        var has_special = local.config.has_special_code;

        //使用span_map计算好的数字
        var offset = angular.copy(current_plan.config.row_span_map);
        offset.tier = 1;
        tmp = [];
        for(i in offset.special) {
            tmp.push(offset.special[i]);
        }
        offset.special = Math.min.apply(null, tmp);

        if(current_items.length == 0) {
            for(var code in current_plan.special_code_frequency) {
                current_plan.special_code_frequency[code] = ['wdall']; //默认全部售卖
            }
        }

        for(i = 0, len = current_items.length; i < len, item = current_items[i]; i++) {
            if(!factory.isItemPriceValid(item)) continue;

            index = 0;

            if(has_special) {
                s_index = special_codes.indexOf(item.special_code);
                if(s_index > -1) { //如果有special code而且找到了
                    index += s_index * offset.special;
                    current_plan.special_code_frequency[item.special_code] = item.frequency;
                } else {
                    continue;
                }
            }

            t_index = getIndexByProp(ticket_types, 'ticket_id', item.ticket_id);
            if(t_index > -1) {
                index += t_index * offset.ticket;
            }

            if(has_tier) {
                q_index = ticket_quantities.indexOf(+item.quantity);
                if(q_index > -1) {
                    index += q_index * offset.tier;
                } else {
                    continue;
                }
            }

            copyItem(new_set_items[index], item);
        }

        return new_set_items;
    };
    //endregion

    //region Calculate Span
    factory.getColSpan = function(plan) {
        var col_span = 4;

        if(local.config.has_special_code) { //所有special groups＋frequency
            col_span += local.config.total_special_groups + 1;
        }
        if(plan.need_tier_pricing == 1) col_span += 1;

        return col_span;
    };
    factory.getRowSpan = function(plan) {
        var span_map = {};

        var total_ticket_count = plan.need_tier_pricing == 1 ? current_plan_info.max_num - current_plan_info.min_num + 1 : 1;
        span_map['ticket'] = total_ticket_count;

        //Combo: special group的rowspan是上一个group的单条rowspan＊group的codes数量
        if(local.config.has_special_code) {
            span_map['special'] = {};
            span_map['frequency'] = local.config.total_ticket_types * total_ticket_count;

            for(var current_group, previous_group, previous_group_items_count, previous_group_item_span, i = local.config.total_special_groups; i > 0; i--) {
                if(i == local.config.total_special_groups) {
                    previous_group_item_span = span_map['frequency'];
                    previous_group_items_count = 1;
                } else {
                    previous_group = current_plan_info.special_info.groups[i];
                    previous_group_item_span = span_map['special'][previous_group.group_id];
                    previous_group_items_count = previous_group.codes.length;
                }

                current_group = current_plan_info.special_info.groups[i - 1];
                span_map['special'][current_group.group_id] = previous_group_item_span * previous_group_items_count;
            }
        }

        local.span_map = span_map;

        return span_map;
    };
    factory.getRowSpanByCode = function(special_id) {
        var index = getIndexByProp(current_plan_info.special_info.specials, 'special_id', special_id);

        if(index > -1) {
            var current_special = current_plan_info.special_info.specials[index];

            return current_special.items.map(function(one_item) {
                return {
                    label      : one_item.name,
                    group_id   : one_item.group_id,
                    row_span   : local.span_map.special[one_item.group_id],
                    special_id : special_id
                };
            });
        }

        return false;
    };
    //endregion

    factory.formatPlan = function(plan) {
        var i, len;
        var has_tier = plan.need_tier_pricing == 1;

        plan.config = {
            is_invalid    : formatDate(new Date()) >= formatDate(plan.to_date),
            row_span_map  : factory.getRowSpan(plan),
            total_columns : factory.getColSpan(plan)
        };

        if(has_tier) {
            plan.config.ticket_quantities = [];
            for(i = +current_plan_info.min_num; i <= current_plan_info.max_num; i++) {
                plan.config.ticket_quantities.push(+i); //Numeric
            }
        }

        for(i = 0, len = plan.items.length; i < len; i++) {
            plan.items[i].special_index = local.result.special_id_set.indexOf(plan.items[i].special_code);
        }

        if(local.config.has_special_code) {
            initSpecialCodeFrequency(plan);
        }

        plan.items = factory.fillItems(plan, factory.generateItems(plan));


        return plan;
    };
    //endregion

    //region Labels
    factory.processFrequency = function(frequency) {
        var all_week = 'wdall';
        if(angular.isString(frequency)) {
            return (frequency.indexOf(all_week) > -1) ? all_week : frequency.split(';');
        }

        return frequency;
    };
    factory.getPlanRangeLabel = function(plan) {
        if(plan.valid_region == 1) {
            return formatDate(plan.from_date) + ' -- ' + formatDate(plan.to_date);
        } else {
            return '整个区间';
        }
    };
    factory.getFrequencyLabel = function(frequency) {
        var label;

        label = factory.processFrequency(frequency).filter(function(one_part) {
            return one_part.length;
        }).map(function(one_part) {
            return factory.weekdays[one_part];
        });

        if(!label.length) {
            label = '不可售卖';
        } else if(label.length == 7 || (label.length == 1 && label[0] == factory.weekdays.wdall)) {
            label = '全部售卖';
        } else {
            label = label.join(', ');
        }

        return label;
    };
    factory.getSpecialCodeLabel = function(special_code) {
        var index = getIndexByProp(current_plan_info.special_info.special_codes, 'special_code', special_code);
        return (index > -1) ? current_plan_info.special_info.special_codes[index].cn_name : '';
    };
    //endregion


    return factory;
};

app.factory('pricePlanFactory', ['$rootScope', factories.pricePlanFactory]);