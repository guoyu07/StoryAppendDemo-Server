controllers.InvoiceSearchCtrl = function($scope, $rootScope, $http, $filter, FileUploader, commonFactory) {
    $scope.data = {};
    $scope.local = {
        //storage supplier id, value is set from chosen's model, used for fetchInvoiceList()
        supplier     : '',
        name         : '',
        date         : $filter('date')(new Date(), 'yyyy-MM-dd'),

        //grid directive config
        grid_options : {
            in_progress : false,
            data        : [],
            table       : {
                table_id : 'invoice_grid'
            },
            label       : {
                getHead : function(col, i) {
                    return col.label;
                },
                getBody : function(col, i, record, j) {
                    if(col.name == 'invoice_sn' || col.name == 'invoice_date' || col.name == 'success' ||
                       col.name == 'fail') {
                        return '<a href="' + $request_urls.edit + record.invoice_id + '">' + record[col.name] + '</a>';
                    }
                }
            },
            request     : {
                api_url : $request_urls.getSupplierInvoiceList
            },
            columns     : [
                {
                    name     : 'invoice_sn',
                    width    : '30%',
                    label    : '对账单ID',
                    use_sort : false
                },
                {
                    name     : 'invoice_date',
                    width    : '30%',
                    label    : '对账日期',
                    use_sort : false
                },
                {
                    name     : 'success',
                    width    : '20%',
                    label    : '正确单数',
                    use_sort : false
                },
                {
                    name     : 'fail',
                    width    : '20%',
                    label    : '问题单数',
                    use_sort : false
                }
            ]
        },

        overlay     : {
            has_overlay : false
        },

        //here's file_uploaded origin value
        file_upload : {
            is_uploaded : false,
            name        : '',
            path        : ''
        }
    };

    //uploader
    $scope.uploader = new FileUploader({
        url     : $request_urls.uploadFile
    });
    $scope.uploader.onBeforeUploadItem = function(item) {
        item.formData = [
            {
                supplier_id : angular.copy($scope.local.supplier)
            }
        ]
    };
    $scope.uploader.onSuccessItem = function(fileItem, response, status, headers) {
        $scope.uploader.queue = [];
        if(response.code == 200) {
            $scope.local.file_upload.is_uploaded = true;
            $scope.local.file_upload.name = response.data.invoice_name;
            $scope.local.file_upload.path = response.data.invoice_path;
        } else {
            $rootScope.$emit('notify', {
                msg : response.msg
            })
        }
    };
    $scope.uploader.onAfterAddingFile = function(addedFileItems) {
        addedFileItems.upload();
    };


    $scope.uploadInvoice = function() {
        //trigger uploader
        $('#invoice-upload').trigger('click');
    };

    $scope.comfirmAddInvoice = function() {
        // if didn't upload, alert notify
        if($scope.local.file_upload.name == '') {
            $rootScope.$emit('notify', {
                msg : '请先上传对账单'
            });
            return;
        }

        var post_data = {
            supplier_id : angular.copy($scope.local.supplier),
            invoice_doc : angular.copy($scope.local.file_upload.path)
        }
        $http.post($request_urls.addSupplierInvoice, post_data).success(function(data) {
            if(data.code == 200) {
                window.location = $request_urls.edit + data.data.invoice_id;
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                })
            }
        })
    }

    $scope.cancelAddInvoice = function() {
        // if didn't upload, just cancel, close overlay
        if($scope.local.file_upload.name == '') {
            $scope.local.overlay.has_overlay = false;
            return;
        }

        var post_data = {
            invoice_doc : angular.copy($scope.local.file_upload.path)
        }
        $http.post($request_urls.deleteFile, post_data).success(function(data) {
            if(data.code == 200) {
                $scope.local.overlay.has_overlay = false;
                // file_upload return back to original value
                $scope.local.file_upload.is_uploaded = false;
                $scope.local.file_upload.name = '';
                $scope.local.file_upload.path = '';
            } else {
                window.location.reload();
            }
        })
    }

    $scope.setSupplierId = function() {
        $scope.local.grid_options.query.query_filter = {
            supplier_id : angular.copy($scope.local.supplier)
        };
        //set supplier name
        for(var i in $scope.data.vendors) {
            if($scope.data.vendors[i].supplier_id == $scope.local.supplier) {
                $scope.local.name = $scope.data.vendors[i].name;
            }
        }
        $scope.local.grid_options.fetchData();
    };

    $scope.addInvoice = function() {
        if(!$scope.local.supplier) {
            $rootScope.$emit('notify', {
                msg : '请先选择供应商'
            });
            return
        }
        $scope.local.overlay.has_overlay = true;
    };

    $scope.init = function() {
        $rootScope.$emit('setBreadcrumb', {
            back : {
                content : false
            },
            body : {
                content : '对账单查询'
            }
        });
        commonFactory.getAjaxSearchSupplierList().then(function(data) {
            $scope.data.vendors = data;
        });
        $rootScope.$emit('loadStatus', false);
    };

    $scope.init();
}

app.controller('InvoiceSearchCtrl', [
    '$scope', '$rootScope', '$http', '$filter', 'FileUploader', 'commonFactory', controllers.InvoiceSearchCtrl
]);