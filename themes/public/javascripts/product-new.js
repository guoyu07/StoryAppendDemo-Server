var orderModel = {    quantities: {}};

var productData = new HitourDataFactory($request_urls.productData, function (data) {
    if(data.status != 3 && location.href.indexOf('test.hitour.cc') == -1 && location.hash.indexOf('wenzi') == -1) {
        location.href = $request_urls.error;
        return {};
    }
    if(/---$/.test(data.description.schedule)){
        data.description.schedule=data.description.schedule.replace(/---$/g,'');
        data.iPatch=true;
    }
    if(data.description.tour_date_title){
        data.rules.redeem_desc = data.rules.redeem_desc.replace('使用日期',data.description.tour_date_title);
    }
    if (data.album_info) {
        data.album_info.album_name = data.album_info.album_name || '通票中包含的精华景点';
        data.album_info.landinfo_md_title = data.album_info.landinfo_md_title || '通票中包含的其他服务';
    }
    else {
        data.album_info = {};
    }
    if(data.type==10){
        data.show_prices.title = "每车";
    }
    if(data.activity_info&&data.activity_info.activity_id){
        orderModel.activity_id=data.activity_info.activity_id;
    }
    if(data.date_rule.operations) {
        var _numberMap = {'一': 1, '二': 2, '三': 3, '四': 4, '五': 5, '六': 6, '日': 7};
        for (var i = 0; i < data.date_rule.operations.length; i++) {
            var freq = data.date_rule.operations[i].frequency;
            var _kick = ['', '周1;', '周2;', '周3;', '周4;', '周5;', '周6;', '周7;'];
            if (freq.indexOf('周') != -1) {
                freq.replace(/周(.)/g, function (m, a, b) {
                    _kick[_numberMap[a]] = '';
                });
                data.date_rule.operations[i].close_dates = _kick.join('') + data.date_rule.operations[i].close_dates;
            }
        }
    }

    //如果只有1张轮播图，则重复这一张
    /*if (data.images.sliders.length < 2) {
        data.images.sliders.push(data.images.sliders[0]);
    }
    var slider_head = data.images.sliders.slice(-2),
        slider_end = data.images.sliders.slice(0, 2);
    data.images.sliders.unshift(slider_head[1]);
    data.images.sliders.unshift(slider_head[0]);
    data.images.sliders.push(slider_end[0]);
    data.images.sliders.push(slider_end[1]);*/

    data.description.schedule = data.description.schedule || '行程安排';

    if (data.comment_stat && data.comment_stat != {}) {
        productCommentsModel.DataInitializer.setScoreData(data.comment_stat);
    }

    if(data.introduction) {
        noticeModel.DataInitializer.setData(data.introduction, data.redeem_usage);
    }

    if(data.multi_day_general) {
        productTourModel.DataInitializer.setData(data.multi_day_general, data.type);
    }

    if(data.type == 9 && data.tour_plan.length > 0) {
        productTourDetailModel.DataInitializer.setData(data.tour_plan, data.description, data.multi_day_general);
    }

    return data;
});
var cmtStatData = {};

var commentStatData = new HitourDataFactory($request_urls.commentStatData, function (data) {
  cmtStatData = data;
  cmtStatData.avg_hitour_service_level = parseFloat(data.avg_hitour_service_level, 10).toFixed(1);
  cmtStatData.avg_supplier_service_level = parseFloat(data.avg_supplier_service_level, 10).toFixed(1);
  cmtStatData.avg_level =( parseFloat(data.avg_supplier_service_level, 10) * 0.3 + parseFloat(data.avg_hitour_service_level, 10) * 0.7 ).toFixed(1);
  cmtStatData.avg_hitour_percents = (parseFloat(data.avg_hitour_service_level, 10) / 5).toFixed(2) * 100;
  cmtStatData.avg_supplier_percents = (parseFloat(data.avg_supplier_service_level, 10) / 5).toFixed(2) * 100;
  cmtStatData.avg_percents = (parseFloat(data.avg_hitour_service_level, 10) / 5).toFixed(2) *70 + (parseFloat(data.avg_supplier_service_level, 10) / 5).toFixed(2) * 30;
  cmtStatData.page_num = Math.ceil(data.total / 3);
  cmtStatData.has_comments = data.total > 0;
  return cmtStatData;
});

avalon.filters.seq = function () {
  var idx = 1;
  return function (str, l) {
    var ret = (str + 1) + "";
    if (ret.length == 1) {
      ret = '0' + ret;
    }
    return ret;
  }
}();

var productInfo = new ViewModel('productInfo', 'multi_day_general|{},product_id,show_prices|{},gmap_url,switchMap,switchTab,country_name,country_url,city_name,city_url,description|{},sliders|[],qa,openBuy,slideRight,slideLeft,activity_info|{},ad_info|{},type,is_favorite,buy_label');
var productScenes = new ViewModel('productScenes', 'description|{},qa,landinfo_groups|[],all_landinfo|[],communications|[],gmap_url,land_names|[],tour_plan|[],patch|true,dayTour|[],switchDay,mousein,mouseleave,mouseout,tour_plan_type,album_info|{},type');
var buyNotice = new ViewModel('buyNotice', 'rules|{},service_include|[],gmap_url,how_it_works|{},pick_landinfo_groups|[]');
var recommend = new ViewModel('recommend', 'products|[],turnLeft,turnRight,showIndex|0');
var commentStat = new ViewModel('commentStat', 'hitour_score,supplier_score,hitour_percent,supplier_percent,page_num,current_page,paginate,has_comments,total_percent,total_score,pages|[]');

productInfo.viewModel.openBuy=function(){
    $('body,html').animate({ scrollTop: $('.order-panel').offset().top }, 300);
}
var HINavObject = [];
commentStat.bindData(commentStatData.getData({
  hitour_score    : 'avg_hitour_service_level',
  supplier_score  : 'avg_supplier_service_level',
  total_score     : 'avg_level',
  hitour_percent  : 'avg_hitour_percents',
  supplier_percent: 'avg_supplier_percents',
  total_percent   : 'avg_percents',
  current_page    : 1,
  pages           : function () {
    var page_arr = [];
    for (var i = 0; i < commentStat.viewModel.page_num; i++) {
      page_arr[i] = i + 1;
    }
    return page_arr;
  },
  paginate        : function () {
    return function (page) {
      commentsLoadingMask('show');
      commentsData.reload({page: page - 1}).then(function () {
        commentsLoadingMask('hide');
      });
      commentStat.viewModel.current_page = page;
      var pages = [], i, page_num = commentStat.viewModel.page_num;
      if (page_num > 5) {
        if (page_num > page + 2 && page > 3) {
          for (i = page - 2; i <= page + 2; i++) {
            pages.push(i);
          }
        } else if (page <= 3) {
          for (i = 1; i <= 5; i++) {
            pages.push(i);
          }
        } else if (page + 3 > page_num) {
          for (i = page_num; i > page_num - 5; i--) {
            pages.unshift(i);
          }
        }
      } else {
        for (i = 1; i <= page_num; i++) {
          pages.push(i)
        }
      }
      commentStat.viewModel.pages = pages;
    }
  }
})).then(function () {
  var $tabCtn = $('.tab-ctn'), $fix = $tabCtn.find('.fix-wrap'),$pm=$('.product-main');
  var $bn = $('#buy_notice');
  var $exchange = $('#exchange-places');
  var $comments = $('#comments');
  $(window).scroll(function () {
    var st = $(document).scrollTop();
    if (st > $pm.offset().top + $pm.height() - 1) {
      $tabCtn.addClass('fixed');
    } else {
      $tabCtn.removeClass('fixed');
    }
  });
} );
recommend.bindData(productData.getData({
  products : 'related',
  turnLeft : function () {
    return function () {
      if (recommend.viewModel.showIndex > 0) {
        recommend.viewModel.showIndex--;
      }
    }
  },
  turnRight: function (val, data) {
    return function () {
      if (recommend.viewModel.showIndex < Math.ceil(data.related.length / 4) - 1) {
        recommend.viewModel.showIndex++;
      }
    }
  }
}));



var buying = false;

function scrollTo(y) {
  $('body,html').animate({ scrollTop: y }, 500);
}
var infoAdapter = new DataAdapter({
  product_id   : 'product_id',
  country_name : 'city.country.cn_name',
  country_url  : 'city.country.link_url',
  city_name    : 'city.cn_name',
  city_url     : 'city.link_url',
  activity_info: 'activity_info',
  buy_label    : 'buy_label',
  ad_info      : 'ad_info',
  type         : 'type',
  is_favorite  : 'is_favorite',
  sliders      : function (val, data) {
    var sliders = data.images.sliders;
    for (var i = 0; i < data.images.sliders.length; i++) {
      var slider = data.images.sliders[i];
      if (slider.name == '') {
        slider['desc'] = '';
      }
      else {
        slider['desc'] = slider['short_desc'];
      }
    }
    return data.images.sliders;
  },
  'switchTab'  : function () {
    return function (e) {
      //$('.tab-ctn .tab.active').removeClass('active');
      var selector = $(e.target).attr('data-tab');
      if (selector) {
        //$('section.tab-content.active').removeClass('active');
        scrollTo($(selector).addClass('active').offset().top - 49);

      }
    }
  },
  switchMap    : function () {
    return function (evt) {
      var el = evt.target, p = el.parentNode;
      if (p.className == 'switch-map') {
        p.className = 'switch-scene';
        el.innerHTML = '查看景点图';
        $('.scene-map').css('left', '10px');
      }
      else {
        $('.scene-map').css('left', '1020px');
        p.className = 'switch-map';
        el.innerHTML = '查看地图';
      }
    }
  },
  'gmap_url'   : function (src, data) {
    if (data.album_info.album_map && data.album_info.album_map.length > 10) {
      return data.album_info.album_map;
    }
    var markers = '';
    var center, idx = 1;
    if (!data.landinfo_groups || data.landinfo_groups.length == 0) {
      return null;
    }
    for (var key in data.landinfo_groups) {
      if (data.landinfo_groups.hasOwnProperty(key)) {
        for (var i = 0; i < data.landinfo_groups[key].length; i++) {
          var loc = data.landinfo_groups[key][i].location_latlng.split(',');
          loc = $.trim(loc[1]) + ',' + $.trim(loc[0]);
          if (idx > 1) {
            markers += ',';
          }
          else {
            center = loc + ',10';
          }
          markers += 'pin-s-' + new Number(idx).toString(16) + '+f00(' + loc + ')';
          idx++;

        }
      }
    }
    return 'http://api.tiles.mapbox.com/v3/natecui.ig5adgfm/' + markers + '/' + center + '/1000x450.png';
  }
});
var sceneAdapter = new DataAdapter({
    'type': 'type',
    'tour_plan': function (src, data) {

        if(data.iPatch||data.product_id==2531||data.product_id==2595||data.product_id==2599){
            this.patch=false;
        }
        for (var i = 0; i < src.length; i++) {
            src[i].active = false;
        }
        if (src.length > 0) {
            this.dayTour = src[0].groups;
            avalon.define('dayPrompt', function (vm) {
                vm.x = -165;
                vm.prompt = src[0].title;
                vm.prevX = -165;
                vm.prevPrompt = src[0].title;
                vm.y=-95;
                vm.prevY=-95;
                vm.show=true;
            });
            src[0].active = true;

            if (src[0].total_days > 0) {
                this.tour_plan_type = 1;
                return src;
            }
            else {
                this.tour_plan_type = 2;
                return src[0].groups;
            }
        }
        else {
            return src;
        }
    },
    mousein: function () {
    return function (idx, title, evt) {
      if (evt.target.className != 'dline') {
        avalon.vmodels.dayPrompt.prompt = title;
        avalon.vmodels.dayPrompt.show = true;
        avalon.vmodels.dayPrompt.x = -165 + this.offsetLeft;
        avalon.vmodels.dayPrompt.y = -95 + this.offsetTop

      }
    }
  },
  mouseout   : function () {
    return function (idx) {
      avalon.vmodels.dayPrompt.show = false;

    }
  },
  mouseleave : function () {
    return function (idx) {
      avalon.vmodels.dayPrompt.show = true;
      avalon.vmodels.dayPrompt.prompt = avalon.vmodels.dayPrompt.prevPrompt;
      avalon.vmodels.dayPrompt.x = avalon.vmodels.dayPrompt.prevX;
      avalon.vmodels.dayPrompt.y = avalon.vmodels.dayPrompt.prevY;
    }
  },
  switchDay  : function (src, data) {
    return function (tour) {
      for (var i = 0; i < productScenes.viewModel.tour_plan.length; i++) {
        productScenes.viewModel.tour_plan[i].active = false;
      }
      tour.active = true;
      avalon.vmodels.dayPrompt.prevPrompt = avalon.vmodels.dayPrompt.prompt;
      avalon.vmodels.dayPrompt.prevX = avalon.vmodels.dayPrompt.x;
      avalon.vmodels.dayPrompt.prevY = avalon.vmodels.dayPrompt.y;
      productScenes.viewModel.dayTour = tour.groups;
      scrollTo($('.day-nav-ctn').offset().top - 52);
        setTimeout(function() {
            HINavObject[0].refreshToTop();
            console.log(HINavObject[0].toTop)
        }, 1000);
    }
  },

  'all_landinfo': function (all_landinfo) {
    var tmp;
    var len = all_landinfo.length;
    var pattern = /<h2>.*?<\/h2><ol>.*?<\/ol>/g;

    for (var i = 0; i < len; i++) {
      all_landinfo[i].lists = [];

      do {
        tmp = pattern.exec(all_landinfo[i].list);
        tmp && all_landinfo[i].lists.push(tmp[0]);
      } while (tmp);
    }

    return all_landinfo;
  },

  'communications': function (communications) {
    var result = [];
    if (communications) {
      var len = communications.length;
      for (var i = 0; i < len; i++) {
        result.push({
          title      : communications[i].title,
          description: communications[i].description
        });
      }

    }
    return result;
  }
});
var bnAdapter = new DataAdapter({
  /* sample: 'images.sample',
   benefit: function (val, src) {
   return core.isEmpty(src.description.benefit) ? null : src.description.benefit
   },*/
  'service_include'   : function (val, data) {
    var reg = /<h2[^>]*>(.*?)<\/h2>[^<]*?(<ol>.*?<\/ol>)/ig, match;
    var obj = [];
    if (data.description.service_include) {
      while (match = reg.exec(data.description.service_include.replace(/\n/g, ''))) {
        obj.push({key: match[1], val: match[2]});
      }
    }
    return obj;
  },
  how_it_works        : function (val, data) {
    var reg = /<h2[^>]*>(.*?)<\/h2>[^<]*?(<ol>.*?<\/ol>)/gi, match;
    var obj = {};
    if (data.description.how_it_works) {
      while (match = reg.exec(data.description.how_it_works.replace(/\n/g, ''))) {
        obj[match[1]] = match[2];
      }
    }

    return obj;
  },
  pick_landinfo_groups: function (val, src) {
    if (src.album_info.pick_ticket_map && src.album_info.pick_ticket_map.length > 10) {
      this.gmap_url = src.album_info.pick_ticket_map;
    } else {
      var makers = '', center, idx = 0;
      for (var i = 0; i < val.length; i++) {
        var obj = val[i];

        for (var j = 0; j < obj.landinfos.length; j++) {
          var loc = obj.landinfos[j].location_latlng.split(',');
          loc = $.trim(loc[1]) + ',' + $.trim(loc[0]);

          if (!center) {
            center = loc + ',10';
          }
          else {
            makers += ',';
          }
          obj.landinfos[j].seq = String.fromCharCode(65 + idx) + '. ';
          makers += 'pin-s-' + String.fromCharCode(97 + idx++) + '+f00(' + loc + ')';
        }
      }
      this.gmap_url = 'http://api.tiles.mapbox.com/v3/natecui.ig5adgfm/' + makers + '/' + center + '/1280x310.png';
    }

    return val;
  }

});

productInfo.bindData(productData.getData(infoAdapter)).then(function (data) {
  var status = data.available;
  if( status != 1 ) {
    $( "#buy_btn" ).attr( "disabled", true ).addClass( "buy-btn-disabled" );
    $( ".fixed-buy-btn" ).attr( "disabled", true ).addClass( "buy-btn-disabled" );
  } else {
    $( "#buy_btn" ).attr( "disabled", false ).removeClass( "buy-btn-disabled" );
    $( ".fixed-buy-btn" ).attr( "disabled", false ).removeClass( "buy-btn-disabled" );
  }

  if (data.ad_info && data.ad_info.length != 0) {
    if (data.ad_info.link_url == "")
      $("#summer_link").removeAttr("href");
  }

  //for friday sale
  if (data.activity_info && data.activity_info.length != 0 && data.activity_info.name == 'FlashSale') {
    productInfo.viewModel.show_prices.orig_price = productInfo.viewModel.show_prices.price;
    productInfo.viewModel.show_prices.price = Math.floor(productInfo.viewModel.show_prices.price / 2);
    productInfo.viewModel.show_prices.title = "五折价"
  }

    var tips_code = document.cookie.replace(/(?:(?:^|.*;\s*)once_tips\s*\=\s*([^;]*).*$)|^.*$/, "$1");
    var pd_id = document.cookie.replace(/(?:(?:^|.*;\s*)pd_id\s*\=\s*([^;]*).*$)|^.*$/, "$1");
    tips_code = tips_code == '' ? 0 : parseInt(tips_code, 10);
    setTimeout(function() {
        new HiCarousel({
            dom: '#base_carousel',
            type: 'fade',
            time: 400
        });
        // init TAB 组件
        HINavObject = [];
        var $hi_nav = $('.hi-nav');
        console.log($hi_nav);
        if($hi_nav) {
            $.each($hi_nav, function(i, val) {
                HINavObject.push(new HINav($hi_nav[i]));
            });
        }
        console.warn(HINavObject);
        $('img').on('load', function() {
            if (HINavObject.length > 0) {
                for (var i = 0; i < HINavObject.length; i++) {
                    HINavObject[i].refreshToTop();
                };
            }
        });
        if(data.activity_info && data.activity_info.length != 0 && data.activity_info.name == 'FlashSale'
               && tips_code < 3 && pd_id != data.product_id) {
            new HiModal({
                dom          : 'activity_tips_modal',
                default_show : true
            });
            document.cookie = "once_tips=" + (tips_code + 1);
            document.cookie = "pd_id=" + data.product_id;
        }
        getSlideImgsWidths();

        noticeModel.renderCallback();
        productTourDetailModel.renderCallback();
    }, 500);
  $(function () {
    $(document.body).scrollTop = 0;
    $('.loading-mask').hide();
      if(data.supplier_id=='89'){
          $('#guarantee').find('.col-md-4:eq(1)').hide();
      }
  });


  var ctn_width = 0,
    imgs_widths = [],
    slide_index = 2;
  var slider_init_left = 0;

  function getSlideImgsWidths() {
    var $ht_slider = $('.ht-slider');
    slider_init_left = 1006 - (($(window).width() - 1006) / 2);
//        $ht_slider.prepend($ht_slider.find('.slider-img:last-child').clone().css('margin-right','-4px'));
//        $ht_slider.prepend($ht_slider.find('.slider-img:nth-last-child(2)').clone());
//        $ht_slider.append($ht_slider.find('.slider-img:nth-child(3)').clone().css('margin-left','-5px'));
//        $ht_slider.append($ht_slider.find('.slider-img:nth-child(4)').clone());
    ctn_width = $('.ht-slider').width();
    $ht_slider.find('.slider-img').each(function () {
      imgs_widths.push($(this).width());
    });
    $ht_slider.children().eq(slide_index).find('.slider-ps').fadeIn(200);

    $ht_slider.css({
      'transition': 'all 0s',
      'left'      : 0 - slider_init_left - 1006
    });
    setTimeout(function () {
      $ht_slider.css('visibility', 'visible');
    }, 100);
  }

  productInfo.viewModel.slideRight = function () {
    var $ht_slider = $('.ht-slider');
    var ctn_left = $ht_slider.offset().left;
    $ht_slider.css({
      'transition': 'all 0.35s',
      'left'      : ctn_left - 1006
    });
    $ht_slider.children().eq(slide_index + 1).find('.slider-ps').fadeIn(200);
    $ht_slider.children().eq(slide_index).find('.slider-ps').fadeOut(200);
    slide_index++;
    if (slide_index > imgs_widths.length - 3) {
      $ht_slider.children().eq(1).find('.slider-ps').fadeOut(200);
      setTimeout(function () {
        $ht_slider.css({
          'transition': 'all 0s',
          'left'      : 0 - slider_init_left - 1006
        });
        $ht_slider.children().eq(slide_index).find('.slider-ps').fadeOut(0);
        slide_index = 2;
        $ht_slider.children().eq(slide_index).find('.slider-ps').fadeIn(0);
      }, 500)
    }
  }
  productInfo.viewModel.slideLeft = function () {
    var $ht_slider = $('.ht-slider');
    var ctn_left = $('.ht-slider').offset().left;
    $('.ht-slider').css({
      'transition': 'all 0.35s',
      'left'      : ctn_left + 1006
    });
    slide_index--;
    $ht_slider.children().eq(slide_index).find('.slider-ps').fadeIn(200);
    $ht_slider.children().eq(slide_index + 1).find('.slider-ps').fadeOut(200);
    if (slide_index < 2) {
      setTimeout(function () {
        $('.ht-slider').css({
          'transition': 'all 0s',
          'left'      : 0 - slider_init_left - 1006 * imgs_widths.length + 4024
        });
        $ht_slider.children().eq(slide_index).find('.slider-ps').fadeOut(0);
        slide_index = imgs_widths.length - 3;
        $ht_slider.children().eq(slide_index).find('.slider-ps').fadeIn(0);
      }, 500)
    }
  }


})
productScenes.bindData(productData.getData(sceneAdapter)).then(function () {
  $('.all-land-ctn ol').each(function (i, e) {
    $(e).find('li').each(function (i, e) {
      if (i % 2 == 1) {
        e.style.background = '#f7f7f7';
      }
    })
  });
})
buyNotice.bindData(productData.getData(bnAdapter)).then(function () {
  setTimeout(function () {
      $(".content-list").find("a").attr("target", "_blank");
  }, 500)
})



function getPricePlan(pricePlan, dateStr) {
  for (var i = 0; i < pricePlan.length; i++) {
    priceSection = pricePlan[i];

    if (priceSection.valid_region == '0' || dateStr >= priceSection.from_date && dateStr <= priceSection.to_date) {
      return priceSection.price_map;
    }
  }
  return null;
}


