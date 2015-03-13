/**
 * Created by JasonLee on 15-1-15.
 */

//  main controller
var guide2345Ctrl = avalon.define("guide2345", function (vm) {

  //for navigation
  vm.continents = {};
  vm.one_continent = {};
  vm.carousel_images = {};
  //for best destinations
  vm.best_views = [];

  vm.hot_groups = {};
  vm.drive_lanes = {};
  vm.hotels = {};

  var dropListTimer;

  vm.listeners = {

    onListHover : function (continent_id) {
      dropListTimer = setTimeout(function(){
        vm.setCountry(continent_id);
        $('.city-list' ).fadeIn();
      }, 300);
    },

    onListOut   : function() {
      clearTimeout(dropListTimer);
    },

    onNavOut    : function() {
      $('.city-list' ).fadeOut();
    }
  };

  vm.setCountry = function (continent_id) {
    if(continent_id < 0) return;
    for (var i = 0; i < guide2345Ctrl.continents.length; i++) {
      if (guide2345Ctrl.continents[i].continent_id == continent_id) {
        guide2345Ctrl.one_continent = guide2345Ctrl.continents[i];
        break;
      }
    }
  };

  vm.changeTab = function (event) {
    var tabs = $(".tab-item" );
    var contents = $(".products-content");

    var obj = event.srcElement ? event.srcElement : event.target;
    var srcId = $(".tab-item.active").attr("groupid");
    var id = $(obj).attr("groupid");

    tabs.eq( srcId ).removeClass("active");
    contents.eq( srcId ).removeClass("group-active");
    tabs.eq( id ).addClass("active");
    contents.eq( id ).addClass("group-active");
  };

});

//carousel clock
var setClock = function() {
  changeImg();
  window.setTimeout(setClock, 8000);
};

var changeImg = function () {
  var images = $(".carousel-item");
  var pos = parseInt($(".carousel-item.active" ).attr("index"));
  var nextPos = (pos + 1) % images.length;
  images.eq(pos ).removeClass("active").fadeOut(800);
  images.eq(nextPos ).addClass("active").fadeIn(800);
}

var initCtrl = function() {

  setClock();

  $( ".loading-mask" ).css( "display", "none" );
  $( "header" ).css( "display", "none" );
  $( "footer" ).css( "display", "none" );
  $( "#LXB_CONTAINER" ).css( "display", "none" );
};

//initialize all data
$( function() {
  $.ajax( {
    url      : "home/citiesInGroup",
    dataType : "json",
    success  : function( res ) {
      if( res.code == 200 ) {

        //get navigation cities
        guide2345Ctrl.continents = res.data;

      } else
        alert( res.msg );
    }
  } );

  $.ajax( {
    url      : "homeExt/carousel",
    dataType : "json",
    success  : function( res ) {
      if( res.code == 200 ) {
        guide2345Ctrl.carousel_images = res.data;
      } else
        alert( res.msg );
    }
  } );

  $.ajax({
    url      : "homeExt/getdata",
    dataType : "json",
    success  : function( res ) {
      if( res.code == 200 ) {
        guide2345Ctrl.hot_groups = res.data.groups;
        guide2345Ctrl.drive_lanes = res.data.lines;
        guide2345Ctrl.hotels = res.data.hotels;
      } else
        alert( res.msg );
    }
  });

  guide2345Ctrl.best_views = [
    {
      name:"新加坡",
      cover_image:"themes/public/views/homeExt/images/guide2345/SG.jpg",
      link_url:"http://www.hitour.cc/Singapore/Singapore",
      left:0,
      top:0
    }, {
      name:"巴黎",
      cover_image:"themes/public/views/homeExt/images/guide2345/PR.jpg",
      link_url:"http://www.hitour.cc/France/Paris",
      left:385,
      top:0
    }, {
      name:"普吉岛",
      cover_image:"themes/public/views/homeExt/images/guide2345/PJ.jpg",
      link_url:"http://www.hitour.cc/Thailand/Phuket",
      left:578,
      top:0
    }, {
      name:"首尔",
      cover_image:"themes/public/views/homeExt/images/guide2345/SL.jpg",
      link_url:"http://www.hitour.cc/South_Korea/Seoul",
      left:772,
      top:0
    }, {
      name:"东京",
      cover_image:"themes/public/views/homeExt/images/guide2345/TK.jpg",
      link_url:"http://www.hitour.cc/Japan/Tokyo",
      left:0,
      top:200
    }, {
      name:"伦敦",
      cover_image:"themes/public/views/homeExt/images/guide2345/LD.jpg",
      link_url:"http://www.hitour.cc/United_Kingdom/London",
      left:193,
      top:200
    }, {
      name:"夏威夷",
      cover_image:"themes/public/views/homeExt/images/guide2345/HW.jpg",
      link_url:"http://www.hitour.cc/promotion/8",
      left:772,
      top:201
    }
  ];

  initCtrl();
} );
