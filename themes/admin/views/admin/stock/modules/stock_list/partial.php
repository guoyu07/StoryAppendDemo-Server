<div class="overlay confirm" id="addstock"
     ng-if="local.overlay.has_overlay && local.overlay.current_overlay == 'addstock'">
    <div class="notify-container confirm states-section">
        <div class="notify-head">
            <span ng-bind="data.add_stock.current_record.product_id"></span>
            &nbsp;
            <span ng-bind="data.add_stock.current_record.product_name"></span>
        </div>
        <div class="notify-body section-body view">
            <div class="section-subbody">
                <form name="add_stock_zip">
                    <table class="forms-table">
                        <tbody>
                            <tr>
                                <td class="view-title">
                                    <label>城市</label>
                                </td>
                                <td class="view-body" ng-bind="data.add_stock.current_record.city_name"></td>
                            </tr>
                            <tr>
                                <td class="view-title">
                                    <label>供应商</label>
                                </td>
                                <td class="view-body" ng-bind="data.add_stock.current_record.supplier_name"></td>
                            </tr>
                            <tr>
                                <td class="view-title">
                                    <label>票种</label>
                                </td>
                                <td class="view-body-container">
                                    <hi-radio-switch options="local.radio_switch.add_stock.options"
                                                     model="local.radio_switch.add_stock.value"></hi-radio-switch>
                                </td>
                            </tr>
                            <tr>
                                <td class="view-title">
                                    <label>邮件来源备注</label>
                                </td>
                                <td class="view-body-container">
                                    <textarea class="form-control" cols="40"
                                              ng-model="data.add_stock.current_record.comment" required></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        <div class="notify-foot">
            <button class="block-action btn btn-default" ng-click="toggleOverlay( '' )">取消</button>
            <button class="block-action btn btn-inverse" ng-click="triggerUpload( 'product' )"
                    ng-disabled="add_stock_zip.$invalid" ng-hide="local.uploader_options.product.in_progress">上传
            </button>
            <button class="block-action btn btn-inverse" disabled class="upload-progress"
                    ng-show="local.uploader_options.product.in_progress">
                上传中 <span ng-bind="local.uploader_options.product.progress"></span>%
            </button>
        </div>
    </div>
</div>
<div class="overlay confirm" id="duplicate"
     ng-show="local.overlay.has_overlay && local.overlay.current_overlay == 'duplicate'">
    <div class="notify-container confirm states-section fixed-notify">
        <div class="notify-head">
            <span ng-bind="data.duplicate.current_record.duplicated.product_id"></span>
            &nbsp;
            <span ng-bind="data.duplicate.current_record.duplicated.product_name"></span>
        </div>
        <div class="notify-body section-body view">
            <div class="section-subbody">
                <table class="forms-table">
                    <tbody>
                        <tr>
                            <td class="view-title">
                                <label>城市</label>
                            </td>
                            <td class="view-body" ng-bind="data.duplicate.current_record.duplicated.city_name"></td>
                        </tr>
                        <tr>
                            <td class="view-title">
                                <label>供应商</label>
                            </td>
                            <td class="view-body" ng-bind="data.duplicate.current_record.duplicated.supplier"></td>
                        </tr>
                        <tr>
                            <td class="view-title">
                                <label>票种</label>
                            </td>
                            <td class="view-body"
                                ng-bind="data.duplicate.current_record.duplicated.ticket.cn_name"></td>
                        </tr>
                        <tr>
                            <td class="view-title">
                                <label>邮件来源备注</label>
                            </td>
                            <td class="view-body"
                                ng-bind="data.duplicate.current_record.duplicated.source_comment"></td>
                        </tr>
                    </tbody>
                </table>
                <p class="small-desc error-msg">
                    上传完成，有重复文件，请查看详情！<br />
                    由于存在重复文件，本次上传没有入库，请检查后重新上传
                </p>
                <div class="separator"></div>
                <table class="table table-hover table-striped">
                    <thead>
                        <tr class="table-head-row">
                            <td class="new-file-col">本次文件</td>
                            <td colspan="3">已存在文件</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="new-file-col" style="width: 25%;">文件名</td>
                            <td style="width: 25%;">文件名</td>
                            <td style="width: 35%;">上传时间</td>
                            <td style="width: 20%;">邮件备注</td>
                        </tr>
                        <tr ng-repeat="item in data.duplicate.current_record.detailed_duplication_info">
                            <td class="new-file-col" ng-bind="item.file"></td>
                            <td ng-bind="item.existed_file"></td>
                            <td ng-bind="item.upload_time"></td>
                            <td ng-bind="item.source_comment"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="notify-foot">
            <button class="block-action btn btn-default" ng-click="toggleOverlay( '' )">关闭</button>
        </div>
    </div>
</div>
<div class="overlay confirm" id="inspect"
     ng-show="local.overlay.has_overlay && local.overlay.current_overlay == 'inspect'">
    <div class="notify-container confirm states-section fixed-notify">
        <div class="notify-head">
            <span ng-bind="data.inspect.batch_info.product_id"></span>
            &nbsp;
            <span ng-bind="data.inspect.batch_info.product_name"></span>
        </div>
        <div class="notify-body section-body view">
            <div class="section-subbody">
                <table class="forms-table">
                    <tbody>
                        <tr>
                            <td class="view-title">
                                <label>城市</label>
                            </td>
                            <td class="view-body" ng-bind="data.inspect.batch_info.city_name"></td>
                        </tr>
                        <tr>
                            <td class="view-title">
                                <label>供应商</label>
                            </td>
                            <td class="view-body" ng-bind="data.inspect.batch_info.supplier"></td>
                        </tr>
                        <tr>
                            <td class="view-title">
                                <label>票种</label>
                            </td>
                            <td class="view-body" ng-bind="data.inspect.batch_info.ticket.cn_name"></td>
                        </tr>
                        <tr>
                            <td class="view-title">
                                <label>邮件来源备注</label>
                            </td>
                            <td class="view-body" ng-bind="data.inspect.batch_info.source_comment"></td>
                        </tr>
                    </tbody>
                </table>
                <div class="separator"></div>
                <div class="files-container">
                    <a class="one-file" ng-href="{{ file.pdf_url }}" target="_blank"
                       ng-repeat="file in data.inspect.file_info" ng-bind="file.filename"></a>
                </div>
            </div>
        </div>
        <div class="notify-foot">
            <button class="block-action btn btn-default" ng-click="toggleOverlay( '' )">关闭</button>
            <button class="block-action btn btn-danger" ng-click="setInspect( false )">有问题，删除</button>
            <button class="block-action btn btn-inverse" ng-click="setInspect( true )">没问题，启用</button>
        </div>
    </div>
</div>