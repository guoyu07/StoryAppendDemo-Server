var dataFactory = new HitourDataFactory($request_urls.getProducts, function (data) {
  var groups = data.product_groups;
  //for (var i = 0; i < groups.length; i++) {
  //  if (groups[i].products == "") {
  //    groups.splice(i, 1);
  //    i--;
  //  }
  //}
  if (data.city_image == null) {
    data.city_image = {};
  }
  data.product_groups.sort(function (a, b) {
    if(a.type==1) a.type = 9999;
    if(b.type ==1) b.type =9999;

    if (a.type > b.type) {
      return 1;
    } else if (a.type == b.type) {
      return 0;
    } else {
      return -1;
    }
  });
  return data;
});

var cityInfo = new ViewModel('cityInfo', 'cn_name, en_name, country_name, group_name, group_description');
var cityNav = new ViewModel('cityNav', 'nav_tabs|[], active_tab, setActiveTab');
var allNav = new ViewModel('allNav', 'nav_tabs|[], active_tab, setActiveTab');
var otherCityRec = new ViewModel('otherCityRec', 'other_cities|{}, rec_cities|[]');

cityInfo.bindData(dataFactory.getData(new DataAdapter({
  cn_name     : 'cn_name',
  en_name     : 'en_name',
  country_name: 'country.cn_name'
})));

allNav.bindData(dataFactory.getData(new DataAdapter({
  'nav_tabs': function (val, src) {
    var gps = src.product_groups;
      return gps;
    //if (gps.length > 2) { // ['推荐', ['二级导航']]
    //  var the_gps = [];
    //  for (var i = 0; i < gps.length; i++) {
    //    if (gps[i].type != 1) {
    //      the_gps.push(gps[i]);
    //    }
    //  }
    //  the_gps.push(gps[0]);
    //
    //  // console.log(the_tabs[1].class_name);
    //  return the_gps;
    //} else if (gps.length > 1) {
    //  return gps.slice(0, 2);
    //} else {
    //  return gps.slice(0, 1);
    //}
  }
})));

cityNav.bindData(dataFactory.getData(new DataAdapter({
    'nav_tabs': function (val, src) {
      var gps = src.product_groups;
      var tabs = [];
      tabs.push(gps[0])
      return tabs;
//      if (gps.length > 2) { // ['推荐', ['二级导航']]
//        var the_tabs = [],
//          sub_tab = {
//            name      : '分组',
//            groups    : [],
//            class_name: '',
//            type      : '100'
//          };
//        for (var i = 0; i < gps.length; i++) {
//          if (gps[i].type != 2 && gps[i].type != 1) {
//            sub_tab.groups.push(gps[i]);
//          }
//        }
//
//        the_tabs.push(gps[1]);
//        the_tabs.push(sub_tab);
//        // console.log(the_tabs[1].class_name);
//        return the_tabs;
//      } else if (gps.length > 1) {
//        return gps.slice(0, 2);
//      } else {
//        return gps.slice(0, 1);
//      }
    }
  }))).then(function () {
    $(function () {
      $(document.body).scrollTop = 0;
      $('.loading-mask').hide();
    })
  });

otherCityRec.bindData(dataFactory.getData(new DataAdapter({
  'rec_cities'  : 'city_recommend_list',
  'other_cities': function (val, src) {
    var data_country = src.country,
      repeat_item = {};
    if (data_country.city_groups) {
      repeat_item.groups = data_country.city_groups;
      for (var i = 0; i < data_country.city_groups.length; i++) {
        for (var j = 0; j < data_country.city_groups[i].cities.length; j++) {
          if (data_country.city_groups[i].cities[j].city_code == src.city_code) {
            repeat_item.groups[i].cities.splice(j, 1);
            break;
          }
        }
      }
    } else {
      for (var i = 0; i < data_country.cities.length; i++) {
        repeat_item.cities = data_country.cities;
        if (data_country.cities[i].city_code == src.city_code) {
          repeat_item.cities.splice(i, 1);
        }
      }
    }
    console.log(repeat_item);
    return repeat_item;
  }
})));


/*$(function () {
 $(document).on('click', '.tab-head .tab-item', function() {
 var i = $(this).index();
 window.location.hash = '#' + i;
 $('.tab-head .tab-item').removeClass('active');
 $(this).addClass('active');
 $('.main-content .tab-content').addClass('none').eq(i).removeClass('none');
 });

 function autoHash() {
 var hash = window.location.hash.slice(1);
 $('.tab-head .tab-item').eq(hash).click();
 }
 autoHash();
 });*/
