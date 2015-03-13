<div id="supplier-edit-container" class="container" ng-controller="SupplierEditCtrl">
<div class="states-section">
    <hi-section-head model="data.section_head.supplier_info"
                     options="local.section_head.supplier_info"></hi-section-head>
    <div class="section-body" ng-class="local.section_head.supplier_info.getClass()"
         ng-show="local.section_head.supplier_info.is_edit">
        <form novalidate name="supplier_info" class="block-ctn">
            <div class="col-md-4">
                <div hi-uploader options="local.uploader.options"></div>
            </div>
            <table class="forms-table col-md-6 col-md-offset-2">
                <tr>
                    <td>
                        <label for="cn_name">供应商中文名</label>
                    </td>
                    <td>
                        <input type="text" class="form-control" id="cn_name" name="cn_name" required
                               ng-model="data.supplier.cn_name" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="en_name">供应商英文名</label>
                    </td>
                    <td>
                        <input type="text" class="form-control" id="en_name" name="en_name" required
                               ng-model="data.supplier.name" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="zip_code">邮编</label>
                    </td>
                    <td>
                        <input type="text" class="form-control" id="zip_code" name="zip_code"
                               ng-model="data.supplier.zip_code" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="fax">传真</label>
                    </td>
                    <td>
                        <input type="text" class="form-control" id="fax" name="fax" ng-model="data.supplier.fax" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="telephone">电话</label>
                    </td>
                    <td>
                        <input type="text" class="form-control" id="telephone" name="telephone"
                               ng-model="data.supplier.telephone" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="website">公司网站</label>
                    </td>
                    <td>
                        <input type="text" class="form-control" id="website" name="website"
                               ng-model="data.supplier.website" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="address">公司地址</label>
                    </td>
                    <td>
                        <input type="text" class="form-control" id="address" name="address"
                               ng-model="data.supplier.address" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="payable_by">支付声明</label>
                    </td>
                    <td>
                        <input type="text" class="form-control" id="payable_by" name="payable_by"
                               ng-model="data.supplier.payable_by" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div class="section-body clearfix" ng-class="local.section_head.supplier_info.getClass()"
         ng-hide="local.section_head.supplier_info.is_edit">
        <div class="col-md-4">
            <img ng-src="{{local.uploader.options.image_url}}" style="width: 100%" />
        </div>
        <table class="forms-table col-md-6 col-md-offset-2">
            <tr>
                <td class="view-title">供应商中文名</td>
                <td class="view-body" ng-bind="data.supplier.cn_name"></td>
            </tr>
            <tr>
                <td class="view-title">供应商英文名</td>
                <td class="view-body" ng-bind="data.supplier.name"></td>
            </tr>
            <tr>
                <td class="view-title">邮编</td>
                <td class="view-body" ng-bind="data.supplier.zip_code"></td>
            </tr>
            <tr>
                <td class="view-title">传真</td>
                <td class="view-body" ng-bind="data.supplier.fax"></td>
            </tr>
            <tr>
                <td class="view-title">电话</td>
                <td class="view-body" ng-bind="data.supplier.telephone"></td>
            </tr>
            <tr>
                <td class="view-title">公司网站</td>
                <td class="view-body" ng-bind="data.supplier.website"></td>
            </tr>
            <tr>
                <td class="view-title">公司地址</td>
                <td class="view-body" ng-bind="data.supplier.address"></td>
            </tr>
            <tr>
                <td class="view-title">支付声明</td>
                <td class="view-body" ng-bind="data.supplier.payable_by"></td>
            </tr>
        </table>
    </div>
</div>
<div class="states-section">
    <div class="section-head">
        <div class="section-title" ng-bind="data.section_head.supplier_contact.title"></div>
    </div>
    <div class="section-body">
        <div class="section-subtitle">新增供应商联系人
            <button class="btn btn-inverse block-action add" ng-click="addContact()">新增</button>
        </div>
        <div class="section-subbody">
            <div class="one-block supplier_contacts" ng-repeat="contacts in data.supplier_contacts">
                <div class="delete-block" ng-click="delContact( $index )">
                    <span class="i i-close"></span>
                </div>
                <div class="block-ctn" ng-show="contacts.is_edit">
                    <div class="block-main text-center">
                        <div class="block-main-wrap">
                            <div class="row mr10">
                                <div class="col-md-6">中文名</div>
                                <div class="col-md-12">
                                    <input class="form-control" type="text" ng-model="contacts.cn_name" />
                                </div>
                            </div>
                            <div class="row mr10">
                                <div class="col-md-6">英文名</div>
                                <div class="col-md-12">
                                    <input class="form-control" type="text" ng-model="contacts.en_name" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block-aside">
                        <div class="row mr10">
                            <div class="col-md-3 indent20">联系电话</div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" ng-model="contacts.telephone" /></div>
                            <div class="col-md-3 indent20">QQ</div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" ng-model="contacts.qq" />
                            </div>
                        </div>
                        <div class="row mr10">
                            <div class="col-md-3 indent20">移动电话</div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" ng-model="contacts.mobilephone" />
                            </div>
                            <div class="col-md-3 indent20">微信</div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" ng-model="contacts.wechat" />
                            </div>
                        </div>
                        <div class="row mr10">
                            <div class="col-md-3 indent20">邮箱</div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" ng-model="contacts.email" />
                            </div>
                            <div class="col-md-3 indent20">Skype</div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" ng-model="contacts.skype" />
                            </div>
                        </div>
                        <div class="row mr10">
                            <div class="col-md-3 indent20">职位</div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" ng-model="contacts.position" /></div>
                        </div>
                        <div class="row mr10">
                            <div class="col-md-3 indent20">工作时间</div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" ng-model="contacts.work_time_start" />
                            </div>
                            <div style="float: left">-</div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" ng-model="contacts.work_time_end" />
                            </div>
                        </div>
                        <div class="row mr10">
                            <div class="col-md-3 indent20">备注</div>
                            <div class="col-md-15">
                                <textarea class="form-control" ng-model="contacts.comments"></textarea>
                            </div>
                        </div>
                        <button class="i block-btn i-save pull-right" style="margin-right: 20px;"
                                ng-click="saveContact($index)"></button>
                    </div>
                </div>
                <div class="block-ctn" ng-hide="contacts.is_edit">
                    <div class="block-main">
                        <div class="block-main-wrap">
                            <div class="row mr10">
                                <div class="col-md-18 text-center lg-text" ng-bind="contacts.cn_name"></div>
                            </div>
                            <div class="row mr10">
                                <div class="col-md-18 text-center sm-text" ng-bind="contacts.en_name"></div>
                            </div>
                        </div>
                    </div>
                    <div class="block-aside">
                        <div class="row mr10">
                            <div class="col-md-3 indent20">联系电话</div>
                            <div class="col-md-4" ng-bind="contacts.telephone"></div>
                            <div class="col-md-3 indent20">QQ</div>
                            <div class="col-md-4" ng-bind="contacts.qq"></div>
                        </div>
                        <div class="row mr10">
                            <div class="col-md-3 indent20">移动电话</div>
                            <div class="col-md-4" ng-bind="contacts.mobilephone"></div>
                            <div class="col-md-3 indent20">微信</div>
                            <div class="col-md-4" ng-bind="contacts.wechat"></div>
                        </div>
                        <div class="row mr10">
                            <div class="col-md-3 indent20">邮箱</div>
                            <div class="col-md-4" ng-bind="contacts.email"></div>
                            <div class="col-md-3 indent20">Skype</div>
                            <div class="col-md-4" ng-bind="contacts.skype"></div>
                        </div>
                        <div class="row mr10">
                            <div class="col-md-3 indent20">职位</div>
                            <div class="col-md-4" ng-bind="contacts.position"></div>
                        </div>
                        <div class="row mr10">
                            <div class="col-md-3 indent20">工作时间</div>
                            <div class="col-md-6">{{contacts.work_time_start}}-{{contacts.work_time_end}}</div>
                        </div>
                        <div class="row mr10">
                            <div class="col-md-3 indent20">备注</div>
                            <div class="col-md-15" ng-bind="contacts.comments"></div>
                        </div>
                        <button class="i block-btn i-edit pull-right" style="margin-right: 20px;"
                                ng-click="editContact($index)"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="states-section">
    <div class="section-head">
        <div class="section-title" ng-bind="data.section_head.supplier_local_supports.title"></div>
    </div>
    <div class="section-body">
        <div class="section-subtitle">新增供应商客服联系方式
            <button class="btn btn-inverse block-action add" ng-click="addLocalSupport()">新增</button>
        </div>
        <div class="section-subbody">
            <div class="one-block supplier_local_supports" ng-repeat="local_supports in data.supplier_local_supports">
                <div class="delete-block" ng-click="delLocalSupport( $index )">
                    <span class="i i-close"></span>
                </div>
                <div class="block-ctn" ng-show="local_supports.is_edit">
                    <div class="row mr10">
                        <div class="col-md-2 col-md-offset-3">客服电话</div>
                        <div class="col-md-4">
                            <input class="form-control" type="text" required ng-model="local_supports.phone" />
                        </div>
                        <div class="col-md-2">支持语言</div>
                        <div class="col-md-4">
                            <input class="form-control" type="text" ng-model="local_supports.language_name"
                                   ng-maxlength="10" />
                        </div>
                    </div>
                    <div class="row mr10">
                        <div class="col-md-2 col-md-offset-3">工作时间</div>
                        <div class="col-md-4">
                            <input class="form-control" type="text" ng-model="local_supports.office_hours_start" />
                        </div>
                        <div style="float: left">-</div>
                        <div class="col-md-4">
                            <input class="form-control" type="text" ng-model="local_supports.office_hours_end" />
                        </div>
                    </div>
                    <button class="i block-btn i-save pull-right" style="margin-right: 20px;" valid-support
                            support="local_supports" ng-click="saveLocalSupport($index)"></button>
                </div>
                <div class="block-ctn" ng-hide="local_supports.is_edit">
                    <div class="row mr10">
                        <div class="col-md-2 col-md-offset-3">客服电话</div>
                        <div class="col-md-4" ng-bind="local_supports.phone"></div>
                        <div class="col-md-2">支持语言</div>
                        <div class="col-md-4" ng-bind="local_supports.language_name"></div>
                    </div>
                    <div class="row mr10">
                        <div class="col-md-2 col-md-offset-3">工作时间</div>
                        <div class="col-md-10">
                            {{local_supports.office_hours_start}}-{{local_supports.office_hours_end}}
                        </div>
                    </div>
                    <button class="i block-btn i-edit pull-right" style="margin-right: 20px;"
                            ng-click="editLocalSupport($index)"></button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>