controllers.InvoiceEditCtrl = function($scope, $rootScope, $http, FileUploader) {
    $scope.data = {
        invoice_data        : {},
        invoice_order       : {},
        search_content      : {
            confirmation_ref : '',
            query_filter     : {
                search_supplier_id      : '',
                search_confirmation_ref : '',
                search_product_text     : '',
                search_added_from_date  : '',
                search_added_to_date    : '',
                search_tour_from_date   : '',
                search_tour_to_date     : '',
                search_order_id         : '',
                has_combination         : ''
            }
        },
        invoice_status_data : {
            order_id : '',
            status   : '',
            remark   : '',
            reason   : ''
        }
    };

    $scope.local = {
        section_head : {
            base_info    : {
                title : '基本信息'
            },
            invoice_list : {
                title : '账单列表'
            }
        },

        file_uploading : false,

        invoice_data : {
            id_edit : false
        },

        invoice_order_table : {
            GTA   : [
                {
                    title : '订单号',
                    width : '6%'
                },
                {
                    title : 'GTA-VVK',
                    width : '8%'
                },
                {
                    title : '商品名称',
                    width : '24%'
                },
                {
                    title : '对账状态',
                    width : '7%'
                },
                {
                    title : '订单状态',
                    width : '7%'
                },
                {
                    title : '领队',
                    width : '10%'
                },
                {
                    title : 'TourDate',
                    width : '10%'
                },
                {
                    title : '结账金额',
                    width : '8%'
                },
                {
                    title : '操作',
                    width : '18%'
                }
            ],
            Other : [
                {
                    title : '订单',
                    width : '5%'
                },
                {
                    title : '商品名称',
                    width : '12%'
                },
                {
                    title : 'SpecialCode',
                    width : '6%'
                },
                {
                    title : '下单日期',
                    width : '9%'
                },
                {
                    title : '使用日期',
                    width : '9%'
                },
                {
                    title : '确认码',
                    width : '7%'
                },
                //        {
                //          title : '结算币种',
                //          width : '5%'
                //        },
                {
                    title : '人数',
                    width : '6%'
                },
                {
                    title : '对账状态',
                    width : '6%'
                },
                {
                    title : '订单状态',
                    width : '7%'
                },
                {
                    title : '领队',
                    width : '7%'
                },
                {
                    title : '结账金额',
                    width : '7%'
                },
                {
                    title : '操作',
                    width : '18%'
                }
            ]
        },


        invoice_order_search : {
            search_status : false
        },

        overlay : {
            refund_confirm_overlay : {
                has_overlay : false,
                idx         : ''
            },
            right_confirm_overlay  : {
                has_overlay : false,
                is_post     : false,
                idx         : ''
            },
            problem_overlay        : {
                has_overlay : false,
                is_post     : false,
                reason      : [
                    {
                        name : '以前对过账',
                        code : 1
                    },
                    {
                        name : '已退款',
                        code : 2
                    },
                    {
                        name : '订单明细不明确',
                        code : 3
                    },
                    {
                        name : '其他',
                        code : 4
                    }
                ]
            },
            log_overlay            : {
                has_overlay  : false,
                grid_options : {
                    in_progress : false,
                    data        : [],
                    table       : {
                        table_id : 'invoice_log_grid'
                    },
                    label       : {
                        getHead : function(col, i) {
                            return col.label;
                        },
                        getBody : function(col, i, record, j) {
                            if(col.name == 'status') {
                                if(record[col.name] == 1) {
                                    return '正确';
                                } else if(record[col.name] == 2) {
                                    return '<span class="warning-red">' + '有问题' + '</span>';
                                }
                            } else if(col.name == 'reason') {
                                if(record[col.name] == 1) {
                                    return '已对账';
                                } else if(record[col.name] == 2) {
                                    return '已退货';
                                } else if(record[col.name] == 3) {
                                    return '订单明细不匹配';
                                } else if(record[col.name] == 4) {
                                    return '其他';
                                }
                            } else {
                                return record[col.name];
                            }
                        }
                    },
                    request     : {
                        api_url : $request_urls.getInvoiceOperationList
                    },
                    columns     : [
                        {
                            name     : 'invoice_sn',
                            width    : '10%',
                            label    : '对账单ID',
                            use_sort : false
                        },
                        {
                            name     : 'order_id',
                            width    : '6%',
                            label    : '订单ID',
                            use_sort : false
                        },
                        {
                            name     : 'product_name',
                            width    : '18%',
                            label    : '商品名称',
                            use_sort : false
                        },
                        {
                            name     : 'status',
                            width    : '7%',
                            label    : '对账状态',
                            use_sort : false
                        },
                        {
                            name     : 'order_status',
                            width    : '7%',
                            label    : '订单状态',
                            use_sort : false
                        },
                        {
                            name     : 'insert_time',
                            width    : '14%',
                            label    : '操作时间',
                            use_sort : false
                        },
                        {
                            name     : 'reason',
                            width    : '10%',
                            label    : '问题原因',
                            use_sort : false
                        },
                        {
                            name     : 'remark',
                            width    : '18%',
                            label    : '备注',
                            use_sort : false
                        }
                    ]
                }
            }
        }
    };

    $scope.init = function() {
        $rootScope.$emit('setBreadcrumb', {
            back : {
                content : false
            },
            body : {
                content : '对账单编辑'
            }
        });

        $http.get($request_urls.getSupplierInvoiceDetail).success(function(data) {
            if(data.code == 200) {
                //supplier invoice detail storage in data.invoice_data
                $scope.data.invoice_data = data.data;
                //storage a copy, this is used for check if data.invoice_data is pristine or not
                $scope.data.invoice_data_copy = angular.copy(data.data);
                //invoice_doc is the path, and invoice_name is the file name
                $scope.data.invoice_data.invoice_name = data.data.invoice_doc.split('/').pop();
                // except GTA, query_filter.has_combination must be set as 1
                if(data.data.supplier_id !== '11') {
                    $scope.data.search_content.query_filter.has_combination = 1;
                    $scope.data.search_content.query_filter.search_supplier_id = data.data.supplier_id;
                }
            }
        });

        $rootScope.$emit('loadStatus', false)
    };

    $scope.init();

    $scope.reUpload = function() {
        //trigger the real uploader
        $('#invoice-reupload').trigger('click');
    };

    $scope.uploader = new FileUploader({
        url     : $request_urls.uploadFile
    });
    $scope.uploader.onBeforeUploadItem = function(item) {
        item.formData = [
            {
                supplier_id : angular.copy($scope.data.invoice_data.supplier_id),
                invoice_id  : angular.copy($scope.data.invoice_data.invoice_id)
            }
        ];
        $scope.local.file_uploading = true;
    };
    $scope.uploader.onSuccessItem =function(item, response, status, headers) {
        if(response.code == 200) {
            $scope.data.invoice_data.invoice_name = response.data.invoice_name;
            $scope.data.invoice_data.invoice_doc = response.data.invoice_path;
        } else {
            $rootScope.$emit('notify', {
                msg : response.msg
            })
        }
        $scope.local.file_uploading = false;
        $scope.uploader.queue.pop();
    };
    $scope.uploader.onAfterAddingFile = function(addedItems) {
        addedItems.upload();
    };

    // saveRemarkEdit()
    $scope.saveRemarkEdit = function() {
        // if has no change, return
        if($scope.data.invoice_data.remark == $scope.data.invoice_data_copy.remark) {
            $scope.local.invoice_data.is_edit = false;
            return;
        }

        var post_data = angular.copy($scope.data.invoice_data);
        $http.post($request_urls.updateInvoice, post_data).success(function(data) {
            if(data.code == 200) {
                $scope.local.invoice_data.is_edit = false;
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                })
            }
        })
    }

    //trigger search
    $scope.triggerSearch = function($event) {
        if($event.keyCode == 13) {
            $scope.searchInvoiceOrder();
        }
    }

    //searchInvoiceOrder()
    $scope.searchInvoiceOrder = function() {
        // set different post_data refer to has_combination
        if($scope.data.search_content.query_filter.has_combination == 1) {
            if($scope.data.search_content.query_filter.search_confirmation_ref == '' &&
               $scope.data.search_content.query_filter.search_product_text == '' &&
               ($scope.data.search_content.query_filter.search_added_from_date == '' ||
                $scope.data.search_content.query_filter.search_added_from_date == null) &&
               ($scope.data.search_content.query_filter.search_added_to_date == '' ||
                $scope.data.search_content.query_filter.search_added_to_date == null ) &&
               ($scope.data.search_content.query_filter.search_tour_from_date == '' ||
                $scope.data.search_content.query_filter.search_tour_from_date == null ) &&
               ($scope.data.search_content.query_filter.search_tour_to_date == '' ||
                $scope.data.search_content.query_filter.search_tour_to_date == null ) &&
               $scope.data.search_content.query_filter.search_order_id == '') {
                $rootScope.$emit('notify', {
                    msg : '请至少输入一个搜索条件'
                })
                return;
            }

            $scope.local.invoice_order_search.search_status = true;
            var post_data = {
                query_filter : angular.copy($scope.data.search_content.query_filter)
            };
            if(post_data.query_filter.search_added_from_date != '') {
                post_data.query_filter.search_added_from_date = formatDate(post_data.query_filter.search_added_from_date);
            }
            if(post_data.query_filter.search_added_to_date != '') {
                post_data.query_filter.search_added_to_date = formatDate(post_data.query_filter.search_added_to_date);
            }
            if(post_data.query_filter.search_tour_from_date != '') {
                post_data.query_filter.search_tour_from_date = formatDate(post_data.query_filter.search_tour_from_date);
            }
            if(post_data.query_filter.search_tour_to_date != '') {
                post_data.query_filter.search_tour_to_date = formatDate(post_data.query_filter.search_tour_to_date);
            }
        } else {
            var len = $scope.data.search_content.confirmation_ref.length, str_head = $scope.data.search_content.confirmation_ref.slice(0, 3).toUpperCase();
            if(len != 10) {
                $rootScope.$emit('notify', {
                    msg : '输入vvk长度应为10位'
                });
            } else if(str_head !== 'VVK') {
                $rootScope.$emit('notify', {
                    msg : '输入vvk代码必须以vvk开头'
                });
            } else {
                $scope.local.invoice_order_search.search_status = true;
                var post_data = {
                    confirmation_ref : angular.copy($scope.data.search_content.confirmation_ref)
                };
            }
        }

        $http.post($request_urls.searchInvoiceOrder, post_data).success(function(data) {
            if(data.code == 200) {
                if(data.data.length == 0 || data.data == '') {
                    $rootScope.$emit('notify', {
                        msg : '没有符合条件的订单'
                    })
                }
                $scope.data.invoice_order = data.data;
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
            $scope.local.invoice_order_search.search_status = false;
        });
    }

    //showLog()
    $scope.showLog = function($index) {
        var order_id = $scope.data.invoice_order[$index].order_id
        $scope.local.overlay.log_overlay.has_overlay = true;
        $scope.local.overlay.log_overlay.grid_options.request.api_url = $scope.local.overlay.log_overlay.grid_options.request.api_url +
                                                                        order_id;
        $scope.local.overlay.log_overlay.grid_options.fetchData();
        $scope.local.overlay.log_overlay.grid_options.request.api_url = $request_urls.getInvoiceOperationList;
    };

    /*
     * init_invoice_status_data, set its value to origin one
     * cause each operation use this block to store the data then post
     * so that we must clear it before next operation which will use this again
     */
    $scope.init_invoice_status_data = function() {
        $scope.data.invoice_status_data.order_id = '';
        $scope.data.invoice_status_data.reason = '';
        $scope.data.invoice_status_data.remark = '';
        $scope.data.invoice_status_data.status = '';
    };

    //setInvoiceOrderRight()
    $scope.setInvoiceOrderRight = function($index) {
        /*
         * post_data( order_id , status [, reason , remark ])
         * data.invoice_status_data is for post, that need order_id, status, reason, remark
         */
        $scope.data.invoice_status_data.order_id = $scope.data.invoice_order[$index].order_id;
        // 未对账
        if($scope.data.invoice_order[$index].invoice_status == 0) {
            // 未对账 已退款或者系统外退款
            // status_id = 11 已退货  = 999 系统外退款（部分退款有退款记录），订单状态不改变的
            if($scope.data.invoice_order[$index].status_id == 11 ||
               $scope.data.invoice_order[$index].status_id == 999) {
                //if(!window.confirm("该订单已退款或有退款记录，请确认操作。\n点击'正确'将更改对账状态，点击'取消'不改变 ")) return;
                $scope.local.overlay.refund_confirm_overlay.has_overlay = true;
                $scope.local.overlay.refund_confirm_overlay.idx = $index;
                return;
            }
            // 未对账 正常订单 普遍情况 post
            $scope.data.invoice_status_data.status = 1;
            var post_data = angular.copy($scope.data.invoice_status_data);
            $http.post($request_urls.updateInvoiceStatus, post_data).success(function(data) {
                if(data.code == 200) {
                    // change invoice_status
                    $scope.data.invoice_order[$index].invoice_status = 1;
                } else {
                    $rootScope.$emit('notify', {
                        msg : data.msg
                    });
                }
            });
            //invoice_status_data return to origin value
            $scope.init_invoice_status_data();
        }
        // 有问题 改为正确，需弹窗写备注
        else if($scope.data.invoice_order[$index].invoice_status == 2) {
            //storage the current order index into the overlay idx, that used for
            $scope.local.overlay.right_confirm_overlay.idx = $index;
            $scope.local.overlay.right_confirm_overlay.has_overlay = true;
        }
    };

    //有退款记录订单弹窗confirm提示 cancel
    $scope.cancelRefundConfirm = function() {
        $scope.init_invoice_status_data();
        $scope.local.overlay.refund_confirm_overlay.has_overlay = false;
    }

    //有退款记录订单弹窗confirm提示 confirm
    $scope.confirmRefundConfirm = function() {
        $scope.data.invoice_status_data.status = 1;
        var post_data = angular.copy($scope.data.invoice_status_data);
        $http.post($request_urls.updateInvoiceStatus, post_data).success(function(data) {
            if(data.code == 200) {
                // change invoice_status
                var idx = $scope.local.overlay.refund_confirm_overlay.idx;
                $scope.data.invoice_order[idx].invoice_status = 1;
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
        //invoice_status_data return to origin value
        $scope.init_invoice_status_data();
        $scope.local.overlay.refund_confirm_overlay.has_overlay = false;
    }

    $scope.cancelInvoiceOrderRight = function() {
        $scope.init_invoice_status_data();
        $scope.local.overlay.right_confirm_overlay.has_overlay = false;
        $scope.local.overlay.right_confirm_overlay.idx = '';
    };

    $scope.confirmInvoiceOrderRight = function() {
        $scope.local.overlay.right_confirm_overlay.is_post = true;
        //invoice_status_data already have order_id, now it need status
        $scope.data.invoice_status_data.status = 1;
        var post_data = angular.copy($scope.data.invoice_status_data);
        $http.post($request_urls.updateInvoiceStatus, post_data).success(function(data) {
            if(data.code == 200) {
                var idx = $scope.local.overlay.right_confirm_overlay.idx;
                //here set the matched order's invoice_status, which show its new status in front
                $scope.data.invoice_order[idx].invoice_status = 1;
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
            $scope.init_invoice_status_data();
            $scope.local.overlay.right_confirm_overlay.is_post = false;
            $scope.local.overlay.right_confirm_overlay.has_overlay = false;
            $scope.local.overlay.right_confirm_overlay.idx = '';

        });
    };

    $scope.setInvoiceOrderProblem = function($index) {
        $scope.local.overlay.problem_overlay.has_overlay = true;
        $scope.data.invoice_status_data.order_id = $scope.data.invoice_order[$index].order_id;
        $scope.local.overlay.problem_overlay.idx = $index;
    };

    $scope.cancelInvoiceOrderProblem = function() {
        $scope.init_invoice_status_data();
        $scope.local.overlay.problem_overlay.has_overlay = false;
        $scope.local.overlay.problem_overlay.idx = '';
    };

    $scope.confirmInvoiceOrderProblem = function() {
        $scope.local.overlay.problem_overlay.is_post = true;
        $scope.data.invoice_status_data.status = 2;
        var post_data = angular.copy($scope.data.invoice_status_data);
        $http.post($request_urls.updateInvoiceStatus, post_data).success(function(data) {
            if(data.code == 200) {
                var idx = $scope.local.overlay.problem_overlay.idx;
                $scope.data.invoice_order[idx].invoice_status = 2;
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
            $scope.init_invoice_status_data();
            $scope.local.overlay.problem_overlay.is_post = false;
            $scope.local.overlay.problem_overlay.has_overlay = false;
            $scope.local.overlay.problem_overlay.idx = '';
        });
    };

    $scope.closeLogOverlay = function() {
        $scope.local.overlay.log_overlay.has_overlay = false;
    };

    $scope.filterInvoiceList = function(type) {
        var post_data = {
            invoice_status : type == 'right' ? 1 : 2
        };
        $http.post($request_urls.getInvoiceOrderList, post_data).success(function(data) {
            if(data.code == 200) {
                $scope.data.invoice_order = data.data;
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };
};

app.controller('InvoiceEditCtrl', ['$scope', '$rootScope', '$http', 'FileUploader', controllers.InvoiceEditCtrl]);