<div class="full-overlay text-center" ms-controller="overlay" ms-visible="show_overlay == true">

  <div class="overlay-content-container clearfix" ms-class="{{content_type}}">
    <span class="x-close-bg close-overlay" ms-click="closeOverlay"></span>
    <div class="overlay-wrap">


    <img class="left-content" src="themes/public/images/common/login-pic.png" alt=""
         ms-visible="content_type == 'login' || content_type == 'register' || content_type == 'forget'" />

    <div class="right-content" ms-visible="content_type == 'login'">
      <div class="title-container clearfix">
        <h3 class="title-login">
          登录 ｜ <a ms-click="switchContent( 'register' )" class="link-green">注册</a>
        </h3>
      </div>

      <div class="input-container" ms-class="error:login_form.errors.username">
        <label for="lg_email" >{{ login_form.errors.username?login_form.errors.username:"注册邮箱／手机号"}}</label>
        <input type="text" id="lg_email" data-label="邮箱" class="input-style"  required
               ms-duplex-change="login_form.username.value" ms-focus="active($event,'login_form','username')" ms-blur="validate( 'login_form', 'username', '不是合法邮箱或手机号' )" />
      </div>

      <div class="input-container" ms-class="error:login_form.errors.password">
        <label for="lg_pwd" >{{ login_form.errors.password?login_form.errors.password:"密码"}}</label>
        <input type="password" id="lg_pwd" data-label="密码" class="input-style"  required ms-focus="active($event,'login_form','password')"
               ms-duplex-change="login_form.password.value"
               ms-blur="validate( 'login_form', 'password', '密码必须为6-20位' )" ms-keyup="doEnter($event,'actionLogin')"/>
      </div>

      <div class="error-msg standalone">{{ login_form.backend_error }}</div>

      <div class="button-container clearfix">
        <div class="login-info">
          <label class="remember">
            <input id="remember" type="checkbox" value="1" ms-duplex="login_form.remember.value" />
            <span ms-class-1="icon-checkbox-checked: login_form.remember.value.indexOf('1') > -1"
                  ms-class-2="icon-checkbox-unchecked: login_form.remember.value.indexOf('1') == -1"></span>
            不要忘记我
          </label>
          &nbsp;| &nbsp;<a ms-click="switchContent( 'forget' )" class="link-green">找回密码</a>
        </div>
        <button class="button-confirm" id="login_btn" ms-class="disabled: login_form.invalid"
                ms-click="actionLogin">登录
        </button>
      </div>

      <div class="other-login-container">
        <a class="login-icon icon-qq-circle" id="qq_login"></a>
        <a class="login-icon icon-weibo-circle"  id="sina_login"></a>
        <a class="login-icon icon-wechat-circle" id="wx_login"></a>
        <label>社交账号直接登录</label>
      </div>

    </div>

    <div class="right-content" ms-visible="content_type == 'register'">

      <div class="title-container clearfix">
        <h3 class="title-login">
          注册账号
        </h3>
          <span class="title-login-addition">
            已有账号，立即
            <a class="link-green" ms-click="switchContent( 'login' )">登录</a>
          </span>
      </div>

      <div class="register-sub-content" ms-if="sub_content_type == 'normal'">
      <div class="phone-pass" ms-click="switchSubContent('phonePass')"><i class="i icon-phone"></i>手机动态密码注册</div>
      <div class="input-container" ms-class="error:register_form.errors.username">
        <label for="rg_email" >{{ register_form.errors.username?register_form.errors.username:"注册邮箱"}}</label>
        <input type="text" id="rg_email" data-label="注册邮箱" class="input-style"
               ms-duplex-change="register_form.username.value"
               ms-blur="validate( 'register_form', 'username', '不是合法邮箱' )"
               ms-focus="active($event,'register_form','username')" />
      </div>

      <div class="input-container" ms-class="error:register_form.errors.password">
        <label for="rg_pwd" >{{ register_form.errors.password?register_form.errors.password:"密码"}}</label>
        <input type="password" id="rg_pwd" data-label="密码" class="input-style"
               ms-duplex-change="register_form.password.value"
               ms-blur="validate( 'register_form', 'password', '密码必须为6-20位' )"
               ms-focus="active($event,'register_form','password')" ms-keyup="doEnter($event,'actionRegister')" />
      </div>

      <div class="input-container" ms-class="error:register_form.errors.confirm">
        <label for="rg_pwd2" >{{ register_form.errors.confirm?register_form.errors.confirm:"确认密码"}}</label>
        <input type="password" id="rg_pwd2" data-label="确认密码" class="input-style"
               ms-duplex-change="register_form.confirm.value" ms-blur="isConfirm"
               ms-focus="active($event,'register_form','confirm')" />
      </div>

      <div class="button-container clearfix">
        <div class="error-msg standalone">{{ register_form.backend_error }}</div>
        <button class="button-confirm"  id="reg_btn" ms-class="disabled: register_form.invalid"
                ms-click="actionRegister">注册
        </button>
      </div>
      </div>

      <div class="register-sub-content" ms-if="sub_content_type == 'phonePass'">
          <div class="phone-pass" ms-click="switchSubContent('normal')"><i class="i icon-person"></i>邮箱方式注册</div>
          <div class="input-container" ms-class="error:phone_register_form.errors.phoneNumber">
              <label for="rg_phone" >{{ phone_register_form.errors.phoneNumber?phone_register_form.errors.phoneNumber:"注册手机号码"}}</label>
              <input type="text" id="rg_phone" data-label="手机号码" class="input-style" style="display:inline-block;width: 65%;"
                     ms-duplex-change="phone_register_form.phoneNumber.value"
                     ms-blur="validate( 'phone_register_form', 'phoneNumber', '不是合法手机号码' )"
                     ms-focus="active($event,'phone_register_form','phoneNumber')" />
              <button id="auth_code" class="authcode" ms-click="actionGetAuthCode">获取验证码</button>
          </div>
          <div class="input-container" ms-class="error:phone_register_form.errors.auth_code">
              <label for="rg_authcode" >{{ phone_register_form.errors.auth_code?phone_register_form.errors.auth_code:"验证码"}}</label>
              <input type="text" id="rg_authcode" data-label="验证码" class="input-style"
                     ms-duplex-change="phone_register_form.auth_code.value"
                     ms-focus="active($event,'phone_register_form','auth_code')" />
          </div>
          <div class="button-container clearfix">
              <div class="error-msg standalone">{{ phone_register_form.backend_error }}</div>
              <button class="button-confirm"  id="reg_phone_btn" ms-class="disabled: phone_register_form.invalid"
                      ms-click="actionPhoneRegister">注册
              </button>
          </div>
      </div>
    </div>

    <div class="right-content" ms-visible="content_type == 'forget'">

      <div class="title-container clearfix">
        <h3 class="title-login">
          找回密码
        </h3>
          <span class="title-login-addition">
            返回
            <a class="link-green" ms-click="switchContent( 'login' )">登录</a>
          </span>
      </div>

      <div class="input-container" ms-class="error:forget_form.errors.username">
        <label for="fg_email" >{{ forget_form.errors.username?forget_form.errors.username:"账户邮箱或手机号"}}</label>

        <input type="text" id="fg_email" data-label="账户邮箱或手机号" class="input-style"
               ms-duplex-change="forget_form.username.value"
               ms-blur="validate( 'forget_form', 'username', '不是合法邮箱或手机号' )"
               ms-focus="active($event,'forget_form','username')" ms-keyup="doEnter($event,'actionForget')"/>
      </div>

      <div class="button-container clearfix">
        <div class="error-msg standalone" ms-class="link-green: forget_form.isgood">{{ forget_form.backend_error }}
        </div>
        <button class="button-confirm" id="forget_btn" ms-class="disabled: forget_form.invalid"
                ms-click="actionForget">提交
        </button>
      </div>
    </div>

    <?php
    if (isset($this->overlay)) {
      $this->beginContent('//partial/' . $this->overlay);
      $this->endContent();
    }
    ?>
  <div class="x-loading"></div>
  </div>
  </div>
</div>
<div class="tips-box" onclick="$(this).fadeOut(1000);"></div>