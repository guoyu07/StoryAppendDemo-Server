/**
 * Created by zhifan on 14-9-4.
 */
var toggleFavor = function(vm,index) {
  var post_data = {};
  var favor_tip = $('.favor-tip');
  if( accountPanel.viewModel.isLogged ) {
    post_data = { 'product_id' : vm.product_id};
    if( vm.is_favorite == 1 ) {
      //先显示红心效果，如果失败在error里纠正
      vm.is_favorite = 0;
      $.ajax( {
                url      : $request_urls.deleteFavoriteProduct,
                data     : post_data,
                dataType : 'json',
                type     : 'post',
                success  : function( res ) {
                  console.log( res.msg )
                },
                error    : function( res ) {
                  console.log( res.msg );
                  vm.is_favorite = 1;
                }

              } );
      favor_tip.eq(index ).stop(true).html('取消收藏成功' ).fadeIn().delay(1000 ).fadeOut();

    } else {
      vm.is_favorite = 1;
      $.ajax( {
                url      : $request_urls.addFavoriteProduct,
                data     : post_data,
                type     : 'post',
                dataType : 'json',
                success  : function( res ) {
                  console.log( res );
                },
                error    : function( res ) {
                  console.log( res );
                  vm.is_favorite = 0;
                }
              } );
      favor_tip.eq(index ).stop(true).html('收藏成功' ).fadeIn().delay(1000 ).fadeOut();
    }
  } else {
    //登录
    overlayModel.show_overlay = true;
  }
};

