
var dataFactory = new HitourDataFactory($request_urls.getPromotionDetail, function (data) {
  return data;
});


var promoInfo = new ViewModel('promoInfo', 'promo_banner_img, promo_title, promo_desc');
var promoGroups = new ViewModel('promoGroups', 'groups|[]');

promoInfo.bindData(dataFactory.getData(new DataAdapter({
  promo_banner_img: 'image',
  promo_title     : 'title',
  promo_desc      : 'description'
})));

promoGroups.bindData(dataFactory.getData(new DataAdapter({
    'groups': 'promotion_group'
  }))).then(function () {
    $(function () {
      $(document.body).scrollTop = 0;
      $('.loading-mask').hide();

      /*$('.product-list .cover-img').lazyload({
        event : "click",
        effect: 'fadeIn'
      });*/
    })
  });

