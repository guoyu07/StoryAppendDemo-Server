var noticeModel = avalon.define("productNoticeCtrl", function(vm) {
    vm.data = {};
    vm.local = {
        type : ''
    };

    vm.DataInitializer = {
        'setData' : function(data, type) {
            var location_count = 0;
            if(data.redeem_usage && data.redeem_usage.pick_landinfo_groups.length > 0) {
                for(var i = 0; i < data.redeem_usage.pick_landinfo_groups.length; i++) {
                    data.redeem_usage.pick_landinfo_groups[i].location_count = location_count;
                    location_count += data.redeem_usage.pick_landinfo_groups[i].landinfos.length;
                }
            }
            noticeModel.data = data;
            noticeModel.local.type = type;
        }
    }

    vm.renderCallback = function() {
        if(noticeModel.data.redeem_usage && noticeModel.data.redeem_usage.pick_landinfo_groups.length > 0) {
            PageInitializer.initRedeemMap();
        }
    };

    var PageInitializer = {
        'initRedeemMap' : function() {
            var map = HImap.init('redeem_map');
            var viewpoints = [];

            for(var i = 0; i < noticeModel.data.redeem_usage.pick_landinfo_groups.length; i++) {
                for(var j = 0; j < noticeModel.data.redeem_usage.pick_landinfo_groups[i].landinfos.length; j++) {
                    var name = noticeModel.data.redeem_usage.pick_landinfo_groups[i].landinfos[j].name;
                    var coord = noticeModel.data.redeem_usage.pick_landinfo_groups[i].landinfos[j].location_latlng.split(',');
                    viewpoints.push([name, coord[0], coord[1], i]);
                }
            }
            console.log(viewpoints);

            HImap.drawMarkers(map, viewpoints);
            HImap.suitBounds(map, viewpoints);
        }
    }

});