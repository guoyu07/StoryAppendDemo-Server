function sendUserData(data) {
    console.log('开始发送数据:\n' + JSON.stringify(data));

    $.ajax({
        url: $request_urls.bindThird,
        data: data,
        type: 'post',
        dataType: 'json',
        success: function (res) {
            //var url=$('#redirect').val()||document.referrer||location.href.substr(0,location.href.indexOf('index'));
            if (res.code == 200) {
                LoginCallback.execute(res);
                accountPanel.viewModel.isLogged = true;
                accountPanel.viewModel.username = data.nick_name;
                //overlayModel.show_overlay = false
            } else {
                $('.tips-box').html(res.msg);
            }
            setTimeout(function(){
                $('.tips-box').fadeOut(1000);
            },1000);
            /*if(res.code>=200&&res.code<300){
             location.reload();
             }
             else{
             $.dialog({
             title: '第三方登录失败',
             width: '400px',
             content: '<p>'+res.message+'</p>'
             });
             }*/
        },
        error: function (xhr, type, msg) {
            console.log(arguments);
            $('.tips-box').html("第三方登录失败");
        }
    });
}
var toggleSpeed = 200;
var errors = {
    login_form: {},
    register_form: {},
    forget_form: {},
    phone_register_form: {}
};
var LoginCallback = {
    callback: [],
    register: function (func) {
        this.callback.push(func);
    },
    execute: function () {
        var ret = true;
        for (var i = 0; i < this.callback.length; i++) {
            if (this.callback[i].apply(this, Array.prototype.slice.call(arguments)) === false) {
                ret = false;
            }
        }
        return ret;

    }
};
var headerModel = avalon.define("header", function (vm) {
    vm.url_fund=$request_urls.headerFund;
    vm.show_account = false;
    vm.show_dropdown = false;
    vm.show_cities = false;
    vm.is_default_header = false;
    vm.continents = [];
    vm.current_country = {};
    vm.current_city = {};
    vm.header_display = {
        city: '',
        country: ''
    };

    vm.logout = function () {
        $.ajax({
            url: $request_urls.headerLogout,
            success: function () {
                location.reload();
            }
        });
    };
    vm.buildOverlay = function (type) {
        overlayModel.show_overlay = true;
        overlayModel.content_type = type;
    };
});
var accountPanel = new ViewModel('accountPanel', 'isLogged|false,username');

var overlayModel = avalon.define("overlay", function (vm) {
    vm.show_overlay = false;
    vm.content_type = 'login';
    vm.sub_content_type = 'normal';
    vm.login_content_type = 'normal';
    vm.status_action = {
        download: ['3', '24'],
        payment: ['1', '22'],
        cancel: ['1', '22', '25'],
        refund: ['2', '3', '4', '5', '6', '17', '21']
    };
    vm.process_result = '';
    vm.authCodeTimer = 0;

    vm.phone_login_form = {
        phoneNumber : {
            value: '',
            pattern : /^\d{11}$/
        },
        password : {
            value : '',
            pattern : /^.{6,80}$/
        },
        invalid : false,
        errors: {},
        error_msg: '',
        backend_error: ''
    };
    vm.login_form = {
        username: {
            value: '',
            pattern: /^([\w\d]+[-\w\d.]*@[-\w\d.]+\.[a-zA-Z]{2,10}|1[358]\d{9})$/
        },
        password: {
            value: '',
            pattern: /^.{6,80}$/
        },
        remember: {
            value: ['1']
        },
        invalid: false,
        errors: {},
        error_msg: '',
        backend_error: ''
    };
    vm.phone_register_form = {
        phoneNumber: {
            value: '',
            pattern: /^\d{11}$/
        },
        auth_code: {
            value: '',
            pattern: /^\d{6}$/
        },
        invalid : false,
        errors: {},
        error_msg: '',
        backend_error: ''
    }
    vm.register_form = {
        username: {
            value: '',
            pattern: /^[\w\d]+[-\w\d.]*@[-\w\d.]+\.[a-zA-Z]{2,10}$/
        },
        password: {
            value: '',
            pattern: /^.{6,20}$/
        },
        confirm: {
            value: '',
            pattern: /^.{6,20}$/
        },
        invalid: false,
        errors: {},
        error_msg: '',
        backend_error: ''
    };
    vm.forget_form = {
        username: {
            value: '',
            pattern: /^(.+@[a-zA-Z0-9_]+?\.[a-zA-Z.]{2,}|\d{11})$/
        },
        invalid: false,
        is_good: false,
        errors: {},
        error_msg: '',
        backend_error: ''
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

    vm.validate = function (form_name, field, message) {
        var $this = $(this);
        $this.parent().removeClass('active');
        if (vm[form_name][field].value == '') {
            errors[form_name][field] = $this.attr('data-label') + '不能为空';
        } else {
            errors[form_name][field] = vm[form_name][field].pattern.test(vm[form_name][field].value) ? '' : message;
        }

        buildError(errors[form_name], vm[form_name]);
    };
    vm.doEnter = function (evt, mode) {
        if (evt.keyCode == 13) {
            vm[mode]();
        }
    };
    vm.active = function (evt, a, b) {
        if (vm[a]['errors'][b]) {
            vm[a]['errors'][b] = '';
        }
        $(evt.target).siblings('label').addClass('upper').parent().addClass('active');
    };
    vm.isConfirm = function () {
        $(this).siblings('label').removeClass('active');
        errors.register_form.confirm =
            ( vm.register_form.password.value !== vm.register_form.confirm.value ) ? '输入的密码不一致' : '';
        buildError(errors.register_form, vm.register_form);
    };
    vm.closeOverlay = function () {
        vm.show_overlay = false;
    };
    vm.setAuthCodeTimer = function() {
        $("#auth_code").html(vm.authCodeTimer + "秒后重试");
        vm.authCodeTimer--;
        if(vm.authCodeTimer == 0) {
            $("#auth_code").removeAttr("disabled").html("获取验证码");
            return;
        }
        setTimeout(function(){
            vm.setAuthCodeTimer();
        },1000);
    }
    vm.switchContent = function (type) {
        vm.content_type = type;
        if (type == 'register') {
            $('#rg_email').focus().val(vm.login_form.username.value);
        } else if (type == 'forget') {
            $('#fg_email').focus().val(vm.login_form.username.value);
        }
    };

    vm.switchSubContent = function(SubType) {
        vm.sub_content_type = SubType;
    };

    vm.actionLogin = function () {
        $('#lg_email,#lg_pwd').trigger('focus').trigger('blur');

        if (vm.login_form.invalid) {

        } else {
            vm.login_form.backend_error='';
            $('#login_btn').attr('disabled', 'disabled').html('登录中...');
            $.ajax({
                url: $request_urls.headerLogin,
                type: 'POST',
                data: {
                    email: vm.login_form.username.value,
                    password: vm.login_form.password.value,
                    ajax_post: true,
                    auto_login: vm.login_form.remember.value[0]
                },
                dataType: 'json',
                success: function (data) {
                    if (data.code == 200) {
                        LoginCallback.execute(data)
                        //window.location = data.data.redirect;
                        accountPanel.viewModel.isLogged = true;
                        accountPanel.viewModel.username = vm.login_form.username.value.slice(0,vm.login_form.username.value.indexOf('@'));
                        overlayModel.show_overlay = false

                    } else {
                        vm.login_form.backend_error = data.msg;
                        $('#login_btn').removeAttr('disabled').html('登录');
                    }
                },
                error: function () {
                    vm.login_form.backend_error = "服务器异常，请稍后再试";
                    $('#login_btn').removeAttr('disabled').html('登录');
                }
            });
        }
    };
    vm.actionForget = function () {
        $('#fg_email').trigger('focus').trigger('blur');
        vm.forget_form.backend_error = "";
        if (!vm.forget_form.invalid) {
            $('#forget_btn').attr('disabled', 'disabled').html('提交中...');
            $.ajax({
                url: $request_urls.headerForget,
                type: 'POST',
                data: {
                    email: vm.forget_form.username.value,
                    ajax_post: true
                },
                dataType: 'json',
                success: function (data) {
                    vm.forget_form.is_good = data.code == 200;
                    vm.forget_form.backend_error = data.msg;
                    if (data.code == 200) {
                        $('#forget_btn').remove();
                    } else {
                        $('#forget_btn').removeAttr('disabled').html('提交');
                    }
                },
                error: function () {
                    vm.forget_form.backend_error = '服务器异常,请稍后再试';
                    $('#forget_btn').removeAttr('disabled').html('提交');
                }
            });
        }
    };
    vm.actionGetAuthCode = function() {
        $("#rg_phone").trigger("focus").trigger("blur");
        if(!vm.phone_register_form.invalid && vm.authCodeTimer == 0) {
            vm.authCodeTimer = 60;//60s
            $("#auth_code").attr("disabled","disabled");
            vm.setAuthCodeTimer();
            vm.phone_register_form.errors.auth_code = "";
            $.ajax({
                url: $request_urls.verifyPhone,
                type: "POST",
                dataType: "json",
                data: {
                    phone_no: vm.phone_register_form.phoneNumber.value
                },
                success : function(data){
                    vm.phone_register_form.backend_error = data.msg;
                },
                error : function() {
                    vm.phone_register_form.backend_error = "服务器异常，请稍后再试或联系客服";
                }
            });
        }
    };
    vm.actionPhoneRegister = function() {
        $("#rg_phone,#rg_authcode").trigger("focus").trigger("blur");
        if(!vm.phone_register_form.invalid) {
            $('#reg_phone_btn').attr('disabled', 'disabled').html('注册中...');
            vm.phone_register_form.backend_error='';
            $.ajax({
                url: $request_urls.verifyPhone,
                type: 'POST',
                dataType: "json",
                data: {
                    phone_no: vm.phone_register_form.phoneNumber.value,
                    verify_code : vm.phone_register_form.auth_code.value
                },
                success: function(data) {
                    if (data.code == 200) {
                        $.ajax({
                            url : $request_urls.headerRegisterByPhone,
                            type: "POST",
                            dataType: "json",
                            data: {
                                phone_no: vm.phone_register_form.phoneNumber.value,
                                ajax_post:true
                            },
                            success : function(data){
                                if(data.code == 200) {
                                    LoginCallback.execute(data);
                                    vm.phone_register_form.backend_error = '注册成功，自动登录中。。。';
                                    setTimeout(function () {
                                        accountPanel.viewModel.isLogged = true;
                                        accountPanel.viewModel.username = vm.phone_register_form.phoneNumber.value;
                                        overlayModel.show_overlay = false;
                                    }, 3000);
                                }
                                else {
                                    vm.phone_register_form.backend_error = data.msg;
                                    $('#reg_phone_btn').removeAttr('disabled').html('注册');
                                }
                            },
                            error : function() {
                                vm.register_form.backend_error = "服务器异常请稍后再试";
                                $('#reg_phone_btn').removeAttr('disabled').html('注册');
                            }
                        });
                    } else {
                        vm.phone_register_form.backend_error = data.msg;
                        $('#reg_phone_btn').removeAttr('disabled').html('注册');
                    }
                },
                error: function () {
                    vm.phone_register_form.backend_error = "服务器异常,请稍后再试或联系客服";
                    $('#reg_phone_btn').removeAttr('disabled').html('注册');
                }
            });
        }
    }
    vm.actionRegister = function () {
        $('#rg_email,#rg_pwd,#rg_pwd2').trigger('focus').trigger('blur');
        if (!vm.register_form.invalid) {
            $('#reg_btn').attr('disabled', 'disabled').html('注册中...');
            vm.register_form.backend_error='';
            $.ajax({
                url: $request_urls.headerRegister,
                type: 'POST',
                data: {
                    email: vm.register_form.username.value,
                    confirm: vm.register_form.confirm.value,
                    password: vm.register_form.password.value,
                    ajax_post: true,
                    auto_login: true
                },
                dataType: 'json',
                success: function (data) {
                    if (data.code == 200) {
                        LoginCallback.execute(data);
                        vm.register_form.backend_error = '注册成功，自动登录中。。。';
                        setTimeout(function () {
                            accountPanel.viewModel.isLogged = true;
                            accountPanel.viewModel.username = vm.register_form.username.value.slice(0,vm.register_form.username.value.indexOf('@'));

                            overlayModel.show_overlay = false;
                        }, 3000);


                    } else {
                        vm.register_form.backend_error = data.msg + ' ' + data[0].email;
                        $('#reg_btn').removeAttr('disabled').html('注册');
                    }
                },
                error: function () {
                    vm.register_form.backend_error = "服务器异常请稍后再试";
                    $('#reg_btn').removeAttr('disabled').html('注册');
                }
            });
        }
    };

    vm.sendDelete = function (type) {
        if (type == 'delete') {
            accountModel.deleteContact();
        }
        vm.show_overlay = false;
    };
    vm.sendRefund = function (flag) {
        accountModel.refundOrder(flag);
    };
    vm.sendCancel = function (flag) {
        accountModel.cancelOrder(flag);
    }

    vm.closeResult = function () {
        vm.show_overlay = false;
    };
});

$(function () {
    $(document).ready(function () {
        accountPanel.viewModel.isLogged = !!$('#account_panel').attr('data-is-logged');

        window.onresize = function () {
            if (window.innerHeight > document.body.offsetHeight) {
                $('.main-footer').addClass('fix-to-bottom');
                $(document.body).css('padding-bottom', '265px');
            } else {
                $(document.body).css('padding-bottom', '0px');
                $('.main-footer').removeClass('fix-to-bottom')

            }
        };
        WB2.anyWhere(function (W) {
            W.widget.followButton({
                'nick_name': '玩途',	//用户昵称
                'id': "wb_follow_btn",
                'show_head' : true,	//是否显示头像
                'show_name' : true,	//是否显示名称
                'show_cancel': true,	//是否显示取消关注按钮
                'callback' : function(type, result) {
                    console.log(type);
                    console.log(result)
                }
            });
        });
        $('#qq_login').on('click', function () {
            overlayModel.show_overlay = false;
            $('.tips-box').html('QQ登录中...').show();
            QC.Login.signOut();
            QC.Login.getMe(function (openId, accessToken) {
                QC.api("get_user_info", {}).success(function (response) {
                    var userData = {
                        otype: 1,
                        ouid: openId,
                        accessToken: accessToken,
                        nick_name: response.data.nickname,
                        avatar_url: response.data.figureurl_qq_1
                    };
                    sendUserData(userData);
                }).error(function (response) {
                    //失败回调
                    alert("获取用户信息失败！");
                });


            });
            var redirectURI = location.host + (location.href.indexOf('/hitour/') != -1 ? '/hitour/' : '/') +
                'themes/public/static/oauthcallback.html';
            QC.Login.showPopup({
                appId: "100547865",
                redirectURI: redirectURI
            });
        });
        $('#sina_login').on('click', function () {
            overlayModel.show_overlay = false;
            $('.tips-box').html('新浪微博登录中...').show();
            WB2.login(function () {
                WB2.anyWhere(function (W) {
                    W.parseCMD("/users/show.json", function (response, bStatus) {
                        var userData = {
                            otype: 2,
                            ouid: WB2.oauthData.uid,
                            accessToken: WB2.oauthData.access_token,
                            nick_name: response.name,
                            avatar_url: response.profile_image_url
                        };
                        sendUserData(userData);
                    }, {uid:WB2.oauthData.uid}, {
                        method: 'get'
                    });
                });
            });
        });
        $('#wx_login').on('click',function(){
            var pathname=location.pathname||'/';
            if(pathname=='/error'){
                pathname='/';
            }
           url='https://open.weixin.qq.com/connect/qrconnect?appid=wx04efe92fb96d9638&redirect_uri=http%3A%2F%2F'+location.hostname+'%2Faccount%2FweixinLogin&response_type=code&scope=snsapi_login&state=wlogin-1.rt-'+encodeURIComponent(pathname)+'#wechat_redirect';
           location.href=url;
        });
    });

    // back-top
    $(document).on('click', '.back-top-simi', function () {
        $('html, body').animate({scrollTop : 0}, 500);
    });
    $(document).on('scroll', function () {
        if (document.body.scrollTop > 500) {
            $('.back-top-simi').fadeIn(200);
        } else {
            $('.back-top-simi').fadeOut(200);
        }
    });
});


avalon.define('footer', function (vm) {
    vm.mouseenter = function () {
        $(this).find('.animate-content').addClass('show').css('z-index',2);
    };
    vm.mouseleave = function () {
        $(this).find('.animate-content').removeClass('show').css('z-index',1);
    };


});