controllers.ArticleEditCtrl = function($scope, $rootScope, $http, $timeout, commonFactory) {
    $scope.data = {};
    $scope.local = {
        tabs              : {
            content : '内容',
            seo     : '配置'
        },
        current_tab_index : 0,
        current           : {},
        article_status    : [
            {
                value : '0',
                label : '编辑中'
            },
            {
                value : '1',
                label : '已生效'
            }
        ],
        default_values    : {
            section : {
                items         : [],
                section_id    : false,
                article_id    : false,
                section_title : '',
                display_order : false
            },
            content : {
                type                : false,
                item_id             : false,
                section_id          : false,
                display_order       : false,
                image_url           : '',
                image_title         : '',
                image_description   : '',
                text_content        : '',
                product_id          : false,
                product_title       : '',
                product_description : ''
            }
        },
        uploader_options  : {
            article_head : {
                target    : $request_urls.updateArticleHeadImage,
                image_url : '',
                beforeCb  : function(event, item) {
                    item.formData = [
                        {
                            article_id : $scope.data.article_id
                        }
                    ];
                },
                successCb : function(event, xhr, item, response, uploader) {
                    uploader.queue = [];
                    if(response.code == 200) {
                        $scope.$apply(function() {
                            $scope.local.uploader_options.article_head.image_url = response.data;
                        });
                    } else {
                        $rootScope.$emit('notify', {msg : response.msg});
                    }
                }
            },
            article_img  : {
                target      : $request_urls.updateArticleSectionImage,
                input_id    : 'article_img',
                image_url   : '',
                accept_type : 'application/jpg',
                beforeCb    : function(event, item) {
                    item.formData = [
                        {
                            article_id : $scope.data.article_id
                        }
                    ];
                    $scope.$apply(function() {
                        $scope.local.current.dialog = '4';
                    });
                },
                successCb   : function(event, xhr, item, response, uploader) {
                    uploader.queue = [];
                    if(response.code == 200) {
                        $scope.local.uploader_options.article_img.image_url = response.data;
                        if($scope.local.new_image) {
                            var si = $scope.lookupSection($scope.local.post_data.section_id);

                            $scope.local.new_image = false;
                            $scope.local.post_data.image_url = response.data;
                            $scope.editContent(si, -1, $scope.local.post_data);
                        } else {
                            $scope.$apply(function() {
                                $scope.local.current.dialog = '2';
                                $scope.content.image_url = response.data;
                            });
                        }
                    } else {
                        $rootScope.$emit('notify', {msg : response.msg});
                    }
                }
            }
        },
        view_product_link : $request_urls.viewProductUrl
    };
    $scope.content = {};

    //初始化
    $scope.init = function() {
        commonFactory.getAjaxSearchCityList().then(function(data) {
            $scope.local.cities = data;
        });
        $http.get($request_urls.article).success(function(data) {
            if(data.code == 200) {
                $rootScope.$emit('loadStatus', false);
                $rootScope.$emit('setBreadcrumb', {
                    back : {
                        part_content : '<span class="i i-eye"></span> ',
                        partClickCb  : function() {
                            window.open($request_urls.viewArticleLink, '_blank');
                        }
                    },
                    body : {
                        content : '编辑文章'
                    }
                });

                $scope.data = data.data;
                $scope.local.uploader_options.article_head.image_url = data.data.head_image_url;

                if($scope.data.sections.length == 0) {
                    $('.chosen-container:nth-of-type(1)').trigger('click');
                }
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };

    $scope.switchTab = function(index) {
        $scope.local.current_tab_index = index;
    };
    $scope.toggleFocus = function(e) {
        $(e.currentTarget).parents('.item-wrapper').toggleClass('focus');
    };
    $scope.toggleHover = function(e) {
        $(e.currentTarget).toggleClass('hover');
    };
    $scope.notAllowEdit = function() {
        return $scope.data.status == '1';
    };
    $scope.lookupSection = function(si) {
        return getIndexByProp($scope.data.sections, 'section_id', si);
    };

    //段落添加、删除
    $scope.addSection = function(previous_si) {
        if($scope.notAllowEdit()) return;

        var current_si = +previous_si + 1;
        $http.post($request_urls.articleSection, {
            display_order : current_si
        }).success(function(data) {
            if(data.code == 200) {
                var result = angular.copy($scope.local.default_values.section);
                result.article_id = data.data.article_id;
                result.section_id = data.data.section_id;
                result.display_order = current_si;
                $scope.data.sections.splice(current_si, 0, result);
                reOrder($scope.data.sections);
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };
    $scope.deleteSection = function(si) {
        if($scope.notAllowEdit()) return;
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;

        var section = $scope.data.sections[si];
        $http.delete($request_urls.articleSection + section.section_id).success(function(data) {
            if(data.code == 200) {
                $scope.data.sections.splice(si, 1);
                reOrder($scope.data.sections);
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };

    //内容添加、删除
    $scope.addContent = function(type, si, previous_ci) {
        if($scope.notAllowEdit()) return;

        var section = $scope.data.sections[si];
        var post_data = angular.copy($scope.local.default_values.content);
        var current_ci = +previous_ci + 1;
        var request_url = $request_urls.articleSectionItem.replace('000', section.section_id);

        post_data.type = type;
        post_data.section_id = section.section_id;
        post_data.display_order = current_ci;

        if(type == '2') {
            $scope.local.new_image = true;
            $scope.local.post_data = post_data;

            $('#' + $scope.local.uploader_options.article_img.input_id).trigger('click');
        } else {
            if(type == '3') {
                post_data.product_id = window.prompt('输入商品ID');
            }

            $scope.actuallyAddContent(request_url, post_data);
        }
    };
    $scope.actuallyAddContent = function(request_url, post_data) {
        function done(item_id) {
            var si = $scope.lookupSection(post_data.section_id);
            post_data.item_id = item_id;
            $scope.data.sections[si].items.splice(post_data.display_order, 0, post_data);
            reOrder($scope.data.sections[si].items);
        }

        $http.post(request_url, post_data).success(function(data) {
            if(data.code == 200) {
                if(post_data.type == '3') { //商品
                    $scope.getProductContent(post_data.product_id, function(return_data) {
                        done(data.data.item_id);
                        var si = $scope.lookupSection(post_data.section_id);
                        $scope.data.sections[si].items[post_data.display_order].product_detail = angular.copy(return_data);
                    });
                } else if(post_data.type == '2') {
                    done(data.data.item_id);
                } else if(post_data.type == '1' || post_data.type == '4') {
                    done(data.data.item_id);
                }
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };
    $scope.getProductContent = function(product_id, cb) {
        $http.get($request_urls.getArticleProductInfo + product_id).success(function(feedback) {
            if(feedback.code == 200) {
                cb && cb(feedback.data);
            } else {
                alert(data.msg);
            }
        });
    };
    $scope.deleteContent = function(si, ci) {
        if($scope.notAllowEdit()) return;

        var section = $scope.data.sections[si];
        var content = section.items[ci];
        var url = $request_urls.articleSectionItem.replace('000', section.section_id) + content.item_id;
        $http.delete(url).success(function(data) {
            if(data.code == 200) {
                $scope.data.sections[si].items.splice(ci, 1);
                reOrder($scope.data.sections[si].items);
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };

    //内容弹窗编辑
    $scope.editContent = function(si, ci, content) {
        $scope.content = angular.copy(content || $scope.data.sections[si].items[ci]);
        $scope.local.current.si = si;
        $scope.local.current.ci = ci;

        if(content) {
            $scope.$apply(function() {
                $scope.local.current.dialog = $scope.content.type;
            });
        } else {
            $scope.local.current.dialog = $scope.content.type;
        }

        if($scope.content.type == 2) {
            $scope.local.uploader_options.article_img.image_url = $scope.content.image_url;
        }
    };
    $scope.confirmContent = function(confirmed) {
        if(confirmed) {
            if($scope.local.current.ci == -1) {
                var request_url = $request_urls.articleSectionItem.replace('000', $scope.local.post_data.section_id);
                $scope.actuallyAddContent(request_url, $scope.content);
            } else {
                $scope.data.sections[$scope.local.current.si].items[$scope.local.current.ci] = angular.copy($scope.content);
                $scope.updateSectionContent(false, $scope.local.current.si, $scope.local.current.ci);
            }
        }

        $scope.local.current.dialog = '';
    };
    $scope.updateProduct = function() {
        $scope.getProductContent($scope.content.product_id, function(return_data) {
            $scope.content.product_detail = angular.copy(return_data);
            $scope.content.product_title = '';
            $scope.content.product_description = '';
        });
    };

    //更新文章
    $scope.updateArticleHead = function() {
        var head_image_url = $scope.local.uploader_options.article_head.image_url;
        var seo = {
            description : $scope.data.seo ? $scope.data.seo.description : '',
            keywords    : $scope.data.seo ? $scope.data.seo.keywords : '',
            title       : $scope.data.seo ? $scope.data.seo.title : ''
        };
        $http.post($request_urls.article, {
            title          : $scope.data.title,
            brief          : $scope.data.brief,
            category       : 1,
            city_code      : $scope.data.city_code,
            head_image_url : head_image_url,
            link_to        : $scope.data.link_to,
            seo            : seo
        }).success(function(data) {
            if(data.code == 200) {
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };
    $scope.updateArticleStatus = function() {
        $scope.local.current.dialog = '';
        $http.post($request_urls.updateArticleStatus, {
            status : $scope.data.status
        }).success(function(data) {
            if(data.code == 200) {
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };

    //更新段落、内容
    $scope.updateSectionTitle = function(e, si) {
        var section = $scope.data.sections[si];

        $http.post($request_urls.articleSection + section.section_id, {
            section_title : section.section_title
        }).success(function(data) {
            if(data.code == 200) {
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };
    $scope.updateSectionContent = function(e, si, ci) {
        var section = $scope.data.sections[si];
        var content = section.items[ci];
        var url = $request_urls.articleSectionItem.replace('000', section.section_id) + content.item_id;

        if(e && [1, 4].indexOf(+content.type) == -1) {
            e && $scope.toggleFocus(e);
        }

        $http.post(url, content).success(function(data) {
            if(data.code == 200) {
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };

    $scope.init();
};

app.controller('ArticleEditCtrl', [
    '$scope', '$rootScope', '$http', '$timeout', 'commonFactory', controllers.ArticleEditCtrl
]);