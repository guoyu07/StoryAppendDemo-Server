<!--历史记录-->
<div class="states-section order-history clearfix">
    <div class="section-head">
        <h2 class="section-title">订单更变历史</h2>
    </div>
    <div class="section-body col-md-10 col-md-offset-4">
        <table class="table table-striped table-hover">
            <tbody>
                <tr data-ng-repeat="history in data.shipping.history">
                    <td ng-bind="history.status_name"></td>
                    <td ng-bind="history.date_added"></td>
                    <td ng-bind="history.comment"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>