/**
 * Created by JasonLee on 14-9-5.
 */
var favoriteModel = avalon.define("favorite", function (vm) {
    vm.data = {};
    vm.singleGroup = {};
    vm.hasCollection = true;
    vm.currentTabIndex = 0;

    vm.switchTab = function(index) {
        $(".tab.active").removeClass("active");
        $(".tab").eq(index).addClass("active");
        vm.singleGroup = vm.data[index];
        vm.currentTabIndex = index;
    };

    vm.deleteOneProduct = function(index) {
        var product_id = vm.singleGroup.products[index].product_id;

        $.ajax({
            url: $request_urls.deleteFavoriteProduct,
            type: 'POST',
            data: {
                product_id : product_id
            },
            cache: true,
            dataType: 'json',
            success: function (data) {
                if(data.code==200) {
                    vm.getFavProducts();
                }
                else {
                    alert(data.msg);
                }
            }
        });
    };

    vm.getFavProducts = function() {
        $.ajax({
            url: $request_urls.getFavoriteProducts,
            type: 'GET',
            cache: true,
            dataType: 'json',
            success: function (data) {
                if(data.code==200) {
                    vm.data = data.data;
                    vm.singleGroup = vm.data[0];
                    if(vm.singleGroup.products.length == 0)
                        vm.hasCollection = false;
                    else
                        vm.hasCollection = true;
                    $('.loading-mask').hide();
                }
            }
        });
    };

    (function ($) {
        vm.getFavProducts();
    })(window.jQuery);
});