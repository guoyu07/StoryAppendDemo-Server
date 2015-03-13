var dataFactory = new HitourDataFactory($request_urls.getProducts, function (data) {
  var groups = data.groups;
  for (var i = 0; i < groups.length; i++) {
    if (groups[i].products == "") {
      groups.splice(i, 1);
      i--;
    }
  }
  if (data.city_image == null) {
    data.city_image = {};
  }
  data.groups.sort(function (a, b) {
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

var cityInfo = new ViewModel('cityInfo', 'cn_name, en_name, country_name, current_name, city, current_products|[], current_group_id, group_name, group_desc, group_type');
var cityNav = new ViewModel('cityNav', 'nav_tabs|[], active_tab, setActiveTab');
var otherCityRec = new ViewModel('otherCityRec', 'other_cities|{}, rec_cities|[]');

cityInfo.bindData(dataFactory.getData(new DataAdapter({
  cn_name         : 'city.cn_name',
  en_name         : 'city.en_name',
  country_name    : 'city.country.cn_name',
  current_name    : 'name',
  city            : 'city',
  current_products: 'products',
  current_group_id: 'group_id',
  group_name      : 'name',
  group_desc      : 'description',
  group_type      : 'type'
})));

cityNav.bindData(dataFactory.getData(new DataAdapter({
    'nav_tabs': function (val, src) {
      var gps = src.groups;
      if (gps.length > 0) { // ['推荐', ['二级导航']]
        var the_tabs = [],
          sub_tab = {
            name      : '分组',
            groups    : [],
            class_name: '',
            type      : '100'
          };
        for (var i = 0; i < gps.length; i++) {
          if (gps.length > 2) {
            if (gps[i].type != 1) {
              sub_tab.groups.push(gps[i]);
            }
          } else if (gps.length > 1) {
            if (gps[i].type != 1) {
              sub_tab.groups.push(gps[i]);
            }
          } else {
            if (gps[i].type != 1 && gps[i].type != 2) {
              sub_tab.groups.push(gps[i]);
            }
          }
        }
        sub_tab.groups.push(gps[0]);
        /* 分组导航核心逻辑 - by 思密达
         * return 4种状态的一种 */

        var all_groups_len = sub_tab.groups.length;
        var main_rec_len = 0; // 模拟数据
        if (all_groups_len > 12) {
          sub_tab.class_name = 'with-s4_0';
        } else {
          if (!main_rec_len > 0) {
            sub_tab.class_name = 'with-s4_0';
          } else {
            if (all_groups_len > 8 && all_groups_len <= 12) {
              sub_tab.class_name = 'with-s3_1';
            } else if (all_groups_len > 4 && all_groups_len <= 8) {
              if (main_rec_len == 1) {
                sub_tab.class_name = 'with-s3_1';
              } else if (main_rec_len >= 2) {
                sub_tab.class_name = 'with-s2_2';
              }
            } else if (all_groups_len > 0 && all_groups_len <= 4) {
              if (main_rec_len == 1) {
                sub_tab.class_name = 'with-s3_1';
              } else if (main_rec_len == 2) {
                sub_tab.class_name = 'with-s2_2';
              } else if (main_rec_len >= 3) {
                sub_tab.class_name = 'with-s1_3';
              }
            }
          }
        }

        the_tabs.push(gps[1]);
        the_tabs.push(sub_tab);
        // console.log(the_tabs[1].class_name);
        return the_tabs;
      }
      /* else if (gps.length > 1) {
       return gps.slice(0, 2);
       } else {
       return gps.slice(0, 1);
       }*/
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

$(function () {
  $(document).on('click', '.tab-head .groups-dropdown', function () {
    var $hi_rec_body = $('.tab-body');
    if ($hi_rec_body.hasClass('down')) {
      $hi_rec_body.slideUp(300).removeClass('down');
    } else {
      $hi_rec_body.slideDown(300).addClass('down');
    }
  });
});




