controllers.SupplierEditCtrl = function($scope, $rootScope, $http) {
    $scope.local = {
        section_head : {
            supplier_info : {
                title    : '供应商信息',
                is_edit  : false,
                updateCb : function() {
                    // if no change return to view status
                    var upload_img = false;
                    if($scope.local.uploader.options.image_url &&
                       $scope.local.uploader.options.image_url.length > 0 &&
                       $scope.data.supplier.image_url != $scope.local.uploader.options.image_url) {
                        upload_img = true;
                    }

                    if($scope.supplier_info.$pristine && !upload_img) {
                        $scope.local.section_head.supplier_info.is_edit = false;
                        return;
                    }
                    //if required item is empty, notify and return
                    if($scope.supplier_info.$invalid) {
                        $rootScope.$emit('notify', {
                            msg : '必填项不能为空'
                        });
                        return;
                    }

                    var post_data = angular.copy($scope.data.supplier);
                    post_data.image_url = $scope.local.uploader.options.image_url;
                    $http.post($request_urls.supplierInfo, post_data).success(function(data) {
                        $rootScope.$emit('notify', {msg : data.code == 200 ? '保存成功' : '保存失败'});
                    });
                    $scope.local.section_head.supplier_info.is_edit = false;
                },
                editCb   : function() {
                    $scope.local.section_head.supplier_info.is_edit = true;
                }
            }
        },
        uploader     : {
            options : {
                target    : $request_urls.addVendorImage,
                image_url : ''
            }
        }
    };
    $scope.data = {
        section_head      : {
            supplier_info           : {
                title : '供应商信息'
            },
            supplier_contact        : {
                title : '供应商联系人'
            },
            supplier_local_supports : {
                title : '供应商客服联系方式'
            }
        },
        supplier_contacts : []
    };

    /* Supplier Contact */
    $scope.addContact = function() {
        $scope.data.supplier_contacts.push({
            contact_id      : '',
            supplier_id     : $scope.data.supplier.supplier_id,
            is_edit         : true,
            en_name         : '',
            cn_name         : '',
            email           : '',
            position        : '',
            telephone       : '',
            mobilephone     : '',
            qq              : '',
            wechat          : '',
            skype           : '',
            work_time       : '',
            work_time_start : '',
            work_time_end   : '',
            comments        : ''
        });
        if($('.supplier_contacts').length > 0) {
            var last = $('.supplier_contacts:nth-last-of-type(1)');
            $('html,body').animate({scrollTop : last.height() + last.offset().top}, 1000);
        }
    };
    $scope.delContact = function(index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;
        var post_data = $scope.data.supplier_contacts[index].contact_id;

        //support_id is empty means this record is just create on local, delete method just splice it from array is enough
        if(!post_data) {
            $scope.data.supplier_contacts.splice(index, 1);
            return;
        }
        $http.delete($request_urls.supplierContact + post_data).success(function(data) {
            $rootScope.$emit('notify', {
                msg : data.msg
            });
            if(data.code == 200) {
                $scope.data.supplier_contacts.splice(index, 1);
            } else if(data.code == 401) {
                window.location.reload();
            }
        });
    };
    $scope.editContact = function(index) {
        $scope.data.supplier_contacts[index].is_edit = true;
    };
    $scope.saveContact = function(index) {
        var post_data = angular.copy($scope.data.supplier_contacts[index]);
        //check supplier name
        if(post_data.cn_name == '' && post_data.en_name == '') {
            $rootScope.$emit('notify', {
                msg : '中文名和英文名至少有一项不能为空'
            });
            return;
        }
        //set work_time
        if(post_data.work_time_start && post_data.work_time_end) {
            post_data.work_time = post_data.work_time_start + '-' + post_data.work_time_end;
        } else {
            post_data.work_time = '';
        }

        //delete extra property
        delete post_data.work_time_start;
        delete post_data.work_time_end;
        delete post_data.is_edit;

        $http.post($request_urls.supplierContact, post_data).success(function(data) {
            $rootScope.$emit('notify', {
                msg : data.msg
            });
            if(data.code == 200) {
                $scope.data.supplier_contacts[index].contact_id = data.data.contact_id;
                $scope.data.supplier_contacts[index].is_edit = false;
            }
        })
    };


    /* Supplier Local Support */
    $scope.addLocalSupport = function() {
        $scope.data.supplier_local_supports.push({
            support_id         : '',
            supplier_id        : $scope.data.supplier.supplier_id,
            is_edit            : true,
            phone              : '',
            office_hours       : '',
            office_hours_start : '',
            office_hours_end   : '',
            language_name      : ''
        });
        if($('.supplier_local_supports').length > 0) {
            var last = $('.supplier_local_supports:nth-last-of-type(1)');
            $('html,body').animate({scrollTop : last.height() + last.offset().top}, 1000);
        }
    };
    $scope.delLocalSupport = function(index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;
        var post_data = $scope.data.supplier_local_supports[index].support_id;

        //support_id is empty means this record is just create on local, delete method just splice it from array is enough
        if(!post_data) {
            $scope.data.supplier_local_supports.splice(index, 1);
            return;
        }
        $http.delete($request_urls.supplierLocalSupport + post_data).success(function(data) {
            $rootScope.$emit('notify', {
                msg : data.msg
            });
            if(data.code = 200) {
                $scope.data.supplier_local_supports.splice(index, 1)
            } else if(data.code == 401) {
                window.location.reload();
            }
        });
    };
    $scope.editLocalSupport = function(index) {
        $scope.data.supplier_local_supports[index].is_edit = true;
    };
    $scope.saveLocalSupport = function(index) {
        var post_data = angular.copy($scope.data.supplier_local_supports[index]);
        //set work_time
        if(post_data.office_hours_start && post_data.office_hours_end) {
            post_data.office_hours = post_data.office_hours_start + '-' + post_data.office_hours_end;
        } else {
            post_data.office_hours = '';
        }

        //delete extra property
        delete post_data.office_hours_start;
        delete post_data.office_hours_end;
        delete post_data.is_edit;

        //post
        $http.post($request_urls.supplierLocalSupport, post_data).success(function(data) {
            $rootScope.$emit('notify', {
                msg : data.msg
            });
            if(data.code == 200) {
                $scope.data.supplier_local_supports[index].support_id = data.data.support_id;
                $scope.data.supplier_local_supports[index].is_edit = false;
            }
        })
    };


    /*
     * init():
     * set breadcrumb, get data, prepare data, show page
     */
    $scope.init = function() {
        $rootScope.$emit('setBreadcrumb', {
            back : {
                content : false
            },
            body : {
                content : '编辑供应商'
            }
        });

        $http.get($request_urls.supplierInfo).success(function(data) {
            if(data.code == 200) {
                var i;

                //merge data.data & $scope.data into $scope.data
                for(i in data.data) {
                    $scope.data[i] = data.data[i];
                }
                //put uploader image url and contacts working time & local supports office hours into $scope.data
                $scope.local.uploader.options.image_url = data.data.supplier.image_url;
                for(i in data.data.supplier_contacts) {
                    var time = data.data.supplier_contacts[i].work_time.split('-');
                    $scope.data.supplier_contacts[i].work_time_start = time[0] || '';
                    $scope.data.supplier_contacts[i].work_time_end = time[1] || '';
                    $scope.data.supplier_contacts[i].is_edit = false;
                }
                for(i in data.data.supplier_local_supports) {
                    var hours = data.data.supplier_local_supports[i].office_hours.split('-');
                    $scope.data.supplier_local_supports[i].office_hours_start = hours[0] || '';
                    $scope.data.supplier_local_supports[i].office_hours_end = hours[1] || '';
                    $scope.data.supplier_local_supports[i].is_edit = false;
                }

                //when data prepared ready show the page
                $rootScope.$emit('loadStatus', false);
            }
        });
    };

    $scope.init();

};

//var validContactDir = function() {
//  return {
//    scope : {
//      contact : '='
//    },
//    link  : function( scope, element ) {
//      var firstRun = true;
//      scope.$watch( 'contact', function( newContact ) {
//        if( firstRun ) {
//          firstRun = !firstRun;
//        } else {
//          if( !!newContact.cn_name || !!newContact.en_name ) {
//            element.removeAttr( 'disabled' );
//          } else {
//            element.prop( 'disabled', true );
//          }
//        }
//      }, true );
//    }
//  };
//};

directives.validSupport = function() {
    return {
        scope : {
            support : '='
        },
        link  : function(scope, element) {
            var firstRun = true;
            scope.$watch('support', function(newSupport) {
                if(firstRun) {
                    firstRun = !firstRun;
                } else {
                    if(!!newSupport.phone) {
                        element.removeAttr('disabled');
                    } else {
                        element.prop('disabled', true);
                    }
                }
            }, true);
        }
    };
};

app.controller('SupplierEditCtrl', ['$scope', '$rootScope', '$http', controllers.SupplierEditCtrl]);
//app.directive( 'validContact', validContactDir );
app.directive('validSupport', directives.validSupport);
