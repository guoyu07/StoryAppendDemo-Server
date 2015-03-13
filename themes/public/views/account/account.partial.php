<!--Account-->
<section class="account-section">
  <div class="section-title">
    <span class="mark"></span>
    登录账户
  </div>
  <!--Account Information-->
  <div class="section-content">
    <div class="clearfix">
      <div class="item-content">{{ data.email }}</div>
      <a class="email-modify"ms-if="!data.isThird" ms-click="togglePassword()">修改密码</a>
    </div>
    <div class="password-container input-wrap" ms-visible="show_password">
      <div class="input-container">
        <label for="ps_oldpassword" ms-class="error:password_form.errors.old_password">{{ password_form.errors.old_password ? password_form.errors.old_password : "旧密码"}}</label>
        <input id="ps_oldpassword" type="password" data-label="旧密码" class="input-style" ms-focus="focusField" ms-blur="validate( 'password_form', 'old_password', '旧密码不合法' )" ms-duplex="password_form.old_password.value" />
      </div>
      <div class="input-container  input-wrap">
        <label for="ps_password" ms-class="error : password_form.errors.password">{{ password_form.errors.password ? password_form.errors.password : "新密码"}}</label>
        <input id="ps_password" type="password" data-label="新密码" class="input-style" ms-focus="focusField" ms-blur="validate( 'password_form', 'password', '新密码必须为6-20位' )" ms-duplex="password_form.password.value" />
      </div>
      <div class="input-container  input-wrap">
        <label for="ps_confirm" ms-class="error : password_form.errors.ps_confirm">{{ password_form.errors.ps_confirm ? password_form.errors.ps_confirm : "确认密码"}}</label>
        <input id="ps_confirm" type="password" data-label="确认密码" class="input-style" ms-focus="focusField" ms-blur="isConfirm" ms-duplex="password_form.confirm.value" />
      </div>
    </div>
    <div class="button-container clearfix" ms-visible="show_password">
      <div class="error-msg">{{ password_form.error_msg | html }}</div>
      <button class="button-warned" ms-class="disabled: password_form.invalid" ms-click="changePassword" ms-visible="password_form.is_saving == true">确认修改</button>
      <button class="button-warned disabled" ms-visible="password_form.is_saving == false"><span class="icon-check"></span>保存成功</button>
    </div>
  </div>
</section>

<!--Contacts-->
<div class="contact-section">
  <div class="section-title">
    <span class="mark"></span>
    我的常用出行联系人
  </div>
  <div class="section-content">
    <a class="item-title grid-bottom new-contact" ms-click="editContact('-1')">新建联系人</a>

    <div class="clearfix contact-list" ms-visible="addresses.length > 0">
      <div class="item-title contacts-label">已有联系人:</div>
      <div class="contacts-list">
        <a class="list-link" ms-repeat-contact="addresses" ms-click="editContact($index)" ms-class="active: contact_form.address_id == contact.address_id">
          {{contact.firstname}}<span class="icon-x" ms-click="delContact($index)"></span>
        </a>
      </div>
    </div>

    <div class="edit-contact-container" ms-visible="contact_form.address_id != 'false'">
      <div class="input-container" ms-class="error : contact_form.errors.firstname">
        <label for="ct_firstname"  >{{ contact_form.errors.firstname ? contact_form.errors.firstname : "名字"}}</label>
        <input id="ct_firstname" type="text" data-label="名字" class="input-style"  ms-duplex="contact_form.firstname.value" ms-focus="focusField" ms-blur="validate( 'contact_form', 'firstname', '名字不能为空' )" />
      </div>
      <div class="input-container" ms-class="error : contact_form.errors.telephone">
        <label for="ct_telephone"  >{{ contact_form.errors.telephone ? contact_form.errors.telephone : "联系电话"}}</label>
        <input id="ct_telephone" type="text" data-label="联系电话" class="input-style"  ms-duplex="contact_form.telephone.value" ms-focus="focusField" ms-blur="validate( 'contact_form', 'telephone', '电话格式错误' )" />
      </div>
      <div class="input-container email" ms-class="error : contact_form.errors.email">
        <label for="ct_email" >{{ contact_form.errors.email ? contact_form.errors.email : "电子邮件"}}</label>
        <input id="ct_email" type="text" data-label="电子邮件" class="input-style" ms-duplex="contact_form.email.value" ms-focus="focusField" ms-blur="validate( 'contact_form', 'email', '不是合法邮箱' )" />
      </div>
      <!--ms-disabled="contact_form.invalid"-->
      <button class="button-confirm" ms-click="saveContact()" ms-visible="contact_form.is_saving == true" ms-class="disabled: contact_form.invalid">保存</button>
      <button class="button-confirm disabled" ms-visible="contact_form.is_saving == false"><span class="icon-check"></span>保存成功</button>
    </div>
  </div>
</div>