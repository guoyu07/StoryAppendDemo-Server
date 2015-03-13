/**
 * Created by godsong on 15-1-19.
 */
!function() {
    var _combo_special_map = {}, _special_codes_map = {}, _special_groups_map = {}, _special_info = {};

    function resolveSpecialInfo(special_info) {
        _special_info = special_info;
        special_info.specials.forEach(function(e) {
            _combo_special_map[e.group_info] = e.special_id;
        });
        special_info.special_codes.forEach(function(e) {
            _special_codes_map[e.special_code] = e;
        });
        special_info.groups.forEach(function(e) {
            _special_groups_map[e.group_id] = e;
            e.special_codes = [];
            e.codes.forEach(function(code) {
                _special_codes_map[code].combo_special_code='';
                e.special_codes.push(_special_codes_map[code]);
            });
        });

        return special_info.groups;
    }

    function findComboSpecialCode(/*special_code... */) {
        var groupinfo;
        var special_group = Array.prototype.slice.call(arguments);

        if(arguments.length == 1 && !arguments[0]) {
            return arguments[0];
        }
        if(arguments.length == 1 && arguments[0].indexOf(':') > -1) {
            groupinfo = arguments[0];
        } else {
            //假如查不到special code，那就暂时设置为第一个
            if(!_special_codes_map[special_group[0]]) {
                return arguments[0];
            }

            special_group.forEach(function(sg, i) {
                if(sg) {
                    special_group[i] = _special_codes_map[sg].group_id + ':' + sg;
                }
            });
            groupinfo=special_group.join('|');
        }

        return _combo_special_map[groupinfo];
    }

    function findComboByPartialSpecialCode(special_set) {
        var group_info = special_set.map(function(special_code) {
            return special_code ? _special_codes_map[special_code].group_id + ':' + special_code : '';
        }).join('|');

        var result_set = [];

        _special_info.specials.forEach(function(one_special) {
            if(one_special.group_info.indexOf(group_info) > -1) {
                result_set.push(one_special.special_id);
            }
        });

        return result_set;
    }

    function findByGroupInfo(groupinfo) {
        return _combo_special_map[groupinfo];
    }

    function getSpecialCodeInfo(special_code) {
        return _special_codes_map[special_code];
    }

    function getTitleFromSpecialCode(special_code) {
        var code_obj = _special_codes_map[special_code];

        if(code_obj) {
            return {
                code_cn_title  : code_obj.cn_name,
                code_en_title  : code_obj.en_name,
                group_cn_title : _special_groups_map[code_obj.group_id] && _special_groups_map[code_obj.group_id].title,
                group_en_title : ''
            };
        }
    }

    function getGroupIndexFromCode(special_code) {
        for(var one_group, i = 0, len = _special_info.groups.length; i < len, one_group = _special_info.groups[i]; i++) {
            if(one_group.codes.indexOf(special_code) > -1) return i;
        }

        return -1;
    }


    window.resolveSpecialInfo = resolveSpecialInfo;

    window.findByGroupInfo = findByGroupInfo;
    window.findComboSpecialCode = findComboSpecialCode;
    window.findComboByPartialSpecialCode = findComboByPartialSpecialCode;

    window.getSpecialCodeInfo = getSpecialCodeInfo;
    window.getGroupIndexFromCode = getGroupIndexFromCode;
    window.getTitleFromSpecialCode = getTitleFromSpecialCode;
}();