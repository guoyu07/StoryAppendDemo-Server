var aerrors = { password_form: {}, contact_form: {} };
var status_map = {
    'unpaid_orders': ['1', '22'],
    'shipped_orders': ['3'],
    'processing_orders': ['2', '4', '5', '6', '8', '9', '10', '12', '17', '18', '19', '20', '21', '25', '26'],
    'refunded_orders': ['11'],
    'all_orders': [
        '1', '2', '3', '4', '5', '6', '8', '9', '10', '11', '12', '17', '18', '19', '20', '21', '22', '24', '25', '26'
    ]
};

var accountModel = avalon.define("account", function (vm) {
    vm.password_form = {
        old_password: {
            value: '',
            pattern: /^.{6,20}$/
        },
        password: {
            value: '',
            pattern: /^.{6,20}$/
        },
        confirm: {
            value: '',
            pattern: /^.{6,20}$/
        },
        errors: {},
        invalid: false,
        is_saving: true,
        error_msg: '',
        backend_error: ''
    };
    vm.contact_form = {
        firstname: {
            value: '',
            pattern: /^.{1,20}$/
        },
        telephone: {
            value: '',
            pattern: /^[0-9]{8,20}$/
        },
        email: {
            value: '',
            pattern: /^.+@[a-zA-Z0-9_]+?\.[a-zA-Z]{2,}$/
        },
        errors: {},
        invalid: false,
        error_msg: '',
        is_saving: true,
        address_id: 'false',
        backend_error: ''
    };
    vm.status_action = {
        download: ['3', '24'],
        payment: ['1', '22'],
        cancel: ['1', '22', '25'],
        refund: ['2', '3', '4', '5', '6', '17', '21']
    };

    vm.data = {};
    vm.insurance_codes = [];
    vm.insurance_url = "";
    vm.insurance_expires = "";
    vm.orders = [];
    vm.coupons = [];
    vm.couponList = [];
    vm.couponRuleList=[];
    vm.addresses = [];
    vm.order_tab = 'all_orders';
    vm.header_tab = 'account';
    vm.coupon_tab = 'all_coupon';
    vm.show_password = false;
    vm.current_type = '';
    vm.is_combo = '';
    vm.passenger_index_revise = '';
    vm.current_order = {};
    vm.current_order_index = '';
    vm.current_order_product = {};
    // current_type为8时，数据以group形式组织
    vm.current_order_product_group_0 = [];
    vm.current_order_product_group_name = '';
    vm.current_order_product_group_total = '';
    vm.current_order_product_group_passenger = {};
    vm.current_order_product_group_lead = [];
    vm.current_order_product_group_everyone = [];
    vm.current_order_product_group_has_lead = '';
    vm.current_order_product_group_1 = [];
    vm.current_order_product_group_1_name = '';
    vm.current_order_product_group_2 = [];
    vm.current_order_product_group_3 = [];
    vm.current_order_passenger = {};
    vm.current_order_passenger_lead = {};
    vm.current_order_passenger_everyone = [];
    vm.current_order_product_info = {};
    vm.current_order_product_date = {};
    vm.current_order_has_info=0;
    vm.showCouponRuleList=false;
    vm.toggleCouponRuleList=function(coupon){
        if(coupon.rule.indexOf('<i>查看</i>')!=-1){
            if(vm.showCouponRuleList){
                vm.showCouponRuleList=false;
            }
            else{
                $('.coupon-rule-list').css('top',this.offsetTop+75+'px');
                vm.couponRuleList=coupon.limit_ids;
                vm.showCouponRuleList=true;
            }
        }
    };
    vm.focusField = function () {
        $(this).siblings('label').addClass('upper');
        $(this).parent().addClass('active');
    };
    function buildError(errors, form) {
        var result = '';
        for (var key in errors) {
            if (errors[key].length > 0) result += errors[key] + "<br />";
        }
        form.error_msg = result;
        form.invalid = form.error_msg.length > 0;
        form.errors = errors;
    }

    vm.togglePassword = function (show) {
        vm.show_password = !vm.show_password;
        var $this = $(this);
        if ($this.html() == '修改密码') {
            $this.html('取消修改');
        }
        else {
            $this.html('修改密码');
        }
    };
    vm.linkAccount = $request_urls.headerAccount;
    vm.linkOrders = $request_urls.headerOrders;
    vm.linkCoupon = $request_urls.headerCoupon;
    vm.isConfirm = function () {
        aerrors.password_form.confirm =
            ( vm.password_form.password.value !== vm.password_form.confirm.value ) ? '输入的密码不一致<br />' : '';
        buildError(aerrors.password_form, vm.password_form);
    };
    vm.isVisible = function (status_id) {
        return ( status_map[ vm.order_tab ] == 'all_orders' && status_id != '7' ) ? true :
            status_map[ vm.order_tab ].indexOf(status_id) > -1;
    };

    vm.getStatusClass = function (status_id) {
        for (var key in status_map) {
            if (status_map[ key ].indexOf(status_id) > -1) return 'status-' + key;
        }
    };
    vm.validate = function (form_name, field, message) {
        $(this).parent().removeClass('active');
        console.log(vm[form_name][field].pattern.test(vm[form_name][field].value));
        if (vm[form_name][field].value == '') {
            aerrors[form_name][field] = $(this).attr('data-label') + '不能为空';
        }
        else {
            aerrors[form_name][field] = vm[form_name][field].pattern.test(vm[form_name][field].value) ? '' : message;
        }

        buildError(aerrors[form_name], vm[form_name]);
    };
    vm.editContact = function ($index) {
        if ($index != -1) {
            vm.contact_form.address_id = vm.addresses[ $index ].address_id;
            vm.contact_form.firstname.value = vm.addresses[ $index ].firstname;
            vm.contact_form.telephone.value = vm.addresses[ $index ].telephone;
            vm.contact_form.email.value = vm.addresses[ $index ].email;
            $('.edit-contact-container input').focus();
            $('.edit-contact-container input:last').blur();
        } else {
            vm.contact_form.address_id = '';
            vm.contact_form.firstname.value = '';
            vm.contact_form.telephone.value = '';
            vm.contact_form.email.value = '';
            $('.edit-contact-container input')[0].focus();

        }
    };
    vm.saveContact = function () {
        var contact = {
            address_id: vm.contact_form.address_id,
            firstname: vm.contact_form.firstname.value,
            telephone: vm.contact_form.telephone.value,
            email: vm.contact_form.email.value
        };
        if($('.edit-contact-container .input-container.error').length==0){
            $.ajax({
            url: $request_urls.updateAddress,
            data: contact,
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                if (vm.contact_form.address_id == '') {
                    vm.contact_form.address_id = data.data.address_id;
                    vm.addresses.push(contact);
                }
                else{
                    for (var i=0;i< vm.addresses.length;i++) {
                        if (vm.addresses[i].address_id == vm.contact_form.address_id) {
                            vm.addresses[i].firstname=vm.contact_form.firstname.value;
                            vm.addresses[i].email=vm.contact_form.email.value;
                            vm.addresses[i].telephone=vm.contact_form.telephone.value;
                            break;
                        }
                    }
                }
                vm.contact_form.is_saving = false;
                setTimeout(function () {
                    vm.contact_form.is_saving = true;
                }, 2000);
            }
        });
        }
    };
    vm.process_result = '';
    vm.delContact = function () {
        overlayModel.show_overlay = true;
        overlayModel.content_type = 'delete_contact';
    };
    vm.deleteContact = function () {
        $.ajax({
            url: $request_urls.deleteAddress.replace('%7Baddress_id%7D',vm.contact_form.address_id),
            type: 'DELETE',
            dataType: 'json',
            success: function (data) {
                if (data.code == 200) {
                    for (var i=0;i< vm.addresses.length;i++) {
                        if (vm.addresses[i].address_id == vm.contact_form.address_id) {
                            vm.addresses.splice(i, 1);
                            break;
                        }
                    }
                    vm.contact_form.address_id = 'false';
                }
            }
        });
    };
    vm.confirmRefund = function (oindex) {
        vm.current_order_index = oindex;
        overlayModel.show_overlay = true;
        overlayModel.content_type = 'refund_order';
    };
    vm.confirmCancel = function (oindex) {
        vm.current_order_index = oindex;
        overlayModel.show_overlay = true;
        overlayModel.content_type = 'cancel_order';
    };

    vm.cancelOrder = function (flag, fromDetail) {
        if (flag) {
            $('.overlay-content-container').addClass('processing');
            $('.cancel-confirm-container p').html('取消中,请稍候...');
            $.ajax({
                url: vm.orders[ vm.current_order_index ].cancel_url,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    $('.overlay-content-container').removeClass('processing');
                    $('.cancel-confirm-container p').html('确定取消此订单？');
                    overlayModel.process_result = data.msg;
                    overlayModel.show_overlay = true;
                    overlayModel.content_type = 'process_result';
                    if(data.code==200){
                        vm.orders.splice(vm.current_order_index ,1);
                    }
                },
                error:function(){
                    $('.overlay-content-container').removeClass('processing');
                    $('.cancel-confirm-container p').html('确定取消此订单？');
                    overlayModel.process_result = "系统异常,请稍后再试";
                    overlayModel.show_overlay = true;
                    overlayModel.content_type = 'process_result';
                }
            });
        }
        else {
            if (fromDetail) {
                overlayModel.content_type = 'order';
            }
            else {
                overlayModel.show_overlay = false;
            }
        }
    };
    vm.refundOrder = function (flag, fromDetail) {
        if (flag) {
            $('.overlay-content-container').addClass('processing');
            $('.refund-confirm-container p').html('退订中,请稍候...');
            $.ajax({
                url: vm.orders[ vm.current_order_index ].return_url,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    $('.overlay-content-container').removeClass('processing');
                    $('.refund-confirm-container p').html('确定退订此订单？');
                    overlayModel.process_result = data.msg;
                    overlayModel.show_overlay = true;
                    overlayModel.content_type = 'process_result';
                    vm.orders[ vm.current_order_index ].status_name = '退订处理中';
                    vm.orders[ vm.current_order_index ].return_url = null;
                    vm.current_order.status_shortname = '退订中';
                    vm.current_order.return_url = null;
                }
            });
        }
        else {
            if (fromDetail) {
                overlayModel.content_type = 'order';
            }
            else {
                overlayModel.show_overlay = false;
            }
        }
    };

    vm.changePassword = function () {
        $.ajax({
            url: $request_urls.changePassword,
            data: {
                old_password: vm.password_form.old_password.value,
                password: vm.password_form.password.value,
                confirm: vm.password_form.confirm.value
            },
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                if (data.code == 200) {
                    vm.password_form.is_saving = false;
                    setTimeout(function () {
                        vm.password_form.is_saving = true;
                        vm.show_password = false;
                    }, 2000);
                }
            }
        });
    };

    vm.buildOverlay = function (index) {
        overlayModel.show_overlay = true;
        overlayModel.content_type = 'order';
        $('.overlay-content-container .x-loading').show();
        $('.order-detail-container').hide();
        $.ajax({
            url: vm.orders[index].detail_url,
            type: 'GET',
            cache: true,
            dataType: 'json',
            success: function (data) {
                $('.overlay-content-container .x-loading').hide();
                $('.order-detail-container').show();
                overlayModel.content_type = 'order';
                vm.insurance_codes = data.data.data.insurance_codes;
                if (data.data.data.insurance_codes[0]) {
                    vm.insurance_url = data.data.data.insurance_codes[0].company?data.data.data.insurance_codes[0].company.policy_url:'';
                    vm.insurance_expires = data.data.data.insurance_codes[0].redeem_expire_date;
                }
                vm.current_type = data.data.data.product_type;
                vm.is_combo = data.data.data.is_combo;
                vm.current_order = data.data.data.order;
                vm.current_order_product = data.data.data.product;
                if(vm.current_type == 8 || vm.is_combo == 1) {
                  vm.current_order_product_group_0 = data.data.data.product.group_0;
                  vm.current_order_product_group_name = data.data.data.product.group_0[0].name;
                  vm.current_order_product_group_total = data.data.data.product.group_0[0].total;
                  vm.current_order_product_group_passenger = data.data.data.product.group_0[0].passenger;
                  vm.current_order_product_group_everyone = data.data.data.product.group_0[0].passenger.everyone;
                  vm.current_order_product_group_1 = data.data.data.product.group_1;
                  if(data.data.data.product.group_1.length>0) {
                    vm.current_order_product_group_1_name = data.data.data.product.group_1[0].name;
                  }
                  vm.current_order_product_group_2 = data.data.data.product.group_2;
                  vm.current_order_product_group_3 = data.data.data.product.group_3;

                  vm.current_order_has_info = Object.keys( data.data.data.product.group_0[0].info ).length +
                                              Object.keys( data.data.data.product.group_0[0].date ).length;
                  vm.passenger_index_revise = vm.current_order_product.group_0[0].passenger.has_lead ? 2 : 1;
                } else {
                  vm.current_order_passenger = data.data.data.product.passenger;
                  vm.current_order_passenger_lead = data.data.data.product.passenger.lead;
                  vm.current_order_passenger_everyone = data.data.data.product.passenger.everyone;


                  vm.current_order_product_info = resolveOrderProductInfo(data.data.data.product);
                  vm.current_order_product_date = data.data.data.product.date;
                  vm.current_order_has_info=Object.keys(data.data.data.product.info).length+Object.keys(data.data.data.product.date).length+Object.keys(data.data.data.product.special).length;
                  vm.passenger_index_revise = vm.current_order_passenger.has_lead ? 2 : 1;
                }
            }
        });
    };
    function resolveOrderProductInfo(product){
        var info={};
        for(var k in product.special){
            if(product.special.hasOwnProperty(k)){
                info[k]=product.special[k];
            }
        }
        if(product.info&&!product.info instanceof Array){
            for(k in product.info){
                if(product.info.hasOwnProperty(k)){
                    info[k]=product.info[k];
                }
            }
        }
        return info;
    }
    vm.switchHeadTab = function (type) {
        vm.header_tab = type;
    };
    vm.switchOrderTab = function (type) {
        vm.order_tab = type;
    };
});


//(function ($) {
//    accountModel.header_tab = ( location.hash.indexOf('orders') > -1 ) ? 'orders' : 'account';
//    $.ajax({
//        url: $request_urls.getAccount,
//        type: 'GET',
//        cache: true,
//        dataType: 'json',
//        success: function (data) {
//
//            if (data.data.isThird) {
//                data.data.email = data.data.firstname;
//            }
//            accountModel.data = data.data;
//            accountModel.coupons = data.data.coupons;
//            accountModel.addresses = data.data.addresses;
//        }
//    });
//    $.ajax({
//        url: $request_urls.getOrders,
//        type: 'GET',
//        cache: true,
//        dataType: 'json',
//        success: function (data) {
//            accountModel.orders = data.data;
//            $(function () {
//                $(document.body).scrollTop = 0;
//                $('.loading-mask').hide();
//            });
//        }
//    });
//})(window.jQuery);
//window.onhashchange = function () {
//    accountModel.header_tab = ( location.hash.indexOf('orders') > -1 ) ? 'orders' : 'account';
//    headerModel.show_account = false;
//};

(function ($) {
  if(location.hash == '#orders')
    accountModel.header_tab = "orders";
  else if(location.hash == '#account')
    accountModel.header_tab = "account";
  else
    accountModel.header_tab = "coupon";
  $.ajax({
           url: $request_urls.getAccount,
           type: 'GET',
           cache: true,
           dataType: 'json',
           success: function (data) {

             if (data.data.isThird) {
               data.data.email = data.data.firstname;
             }
             accountModel.data = data.data;
             accountModel.coupons = data.data.coupons;
             accountModel.addresses = data.data.addresses;
           }
         });
  $.ajax({
           url: $request_urls.getOrders,
           type: 'GET',
           cache: true,
           dataType: 'json',
           success: function (data) {
             accountModel.orders = data.data;
             $(function () {
               $(document.body).scrollTop = 0;
               $('.loading-mask').hide();
             });
           }
         });
    var _metaInfo=['全部商品','商品','城市','国家'];
  $.ajax({
           url: $request_urls.getCoupon,
           type: 'GET',
           dataType: 'json',
           success : function (data) {
               if(data.code == 200) {
                   for(var i = 0;i < data.data.length;i++) {
                       var coupon=data.data[i];
                       coupon.used_status = "已使用";
                       if(coupon.used_times == 0) {
                           coupon.used_status = "未使用";
                       }
                       if(coupon.discount.indexOf('%')!=-1){
                           if(parseInt(coupon.discount)==100){
                               coupon.discount='全额抵扣';
                           }
                           else {
                               coupon.discount='抵扣'+coupon.discount;
                           }
                       }
                       else{
                           coupon.discount='抵扣'+coupon.discount+'元';
                       }
                       if(coupon.description){
                           coupon.rule=coupon.description;
                       }
                       else if(coupon.valid_type==0){
                           coupon.rule='全部商品';
                       }
                       else{
                           coupon.rule=coupon.could_use==1?'<em>仅限以下'+_metaInfo[coupon.valid_type]+'可用</em><i>查看</i>':'<em>以下'+_metaInfo[coupon.valid_type]+'不可使用</em><i>查看</i>';
                       }
                   }
                   accountModel.couponList = data.data;
             }
             else {
               alert("获取优惠券信息失败，请稍后重试！");
             }
           }
         });
})(window.jQuery);
window.onhashchange = function () {
  if(location.hash == '#orders')
    accountModel.header_tab = "orders";
  else if(location.hash == '#account')
    accountModel.header_tab = "account";
  else
    accountModel.header_tab = "coupon";
  headerModel.show_account = false;
};