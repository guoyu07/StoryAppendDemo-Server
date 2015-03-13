controllers.ExpertListCtrl = function($scope, $http, $rootScope, commonFactory) {

    $scope.data = {};
    $scope.local = {
        total:0
    };

    $scope.init = function() {
        $http.get($request_urls.expert).success(function(data) {
            if(data.code == 200) {
                $rootScope.$emit('loadStatus', false);
                $rootScope.$emit('setBreadcrumb', {
                    back : {},
                    body : {
                        content : '专家管理'
                    }
                });

                $scope.data.experts = data.data;

                for(var expert_index in $scope.data.experts) {
                    $scope.local.total++;
                    $scope.data.experts[expert_index].edit = false;
                    $scope.data.experts[expert_index].uploader = {
                        target    : $request_urls.updateExpertImage,
                        image_url : $scope.data.experts[expert_index].avatar,
                        beforeCb  : function(event, item) {
                            item.formData = [
                                {
                                    id : $scope.data.experts[expert_index].id
                                }
                            ];
                        },
                        successCb : function(event, xhr, item, response, uploader) {
                            uploader.queue = [];
                            if(response.code == 200) {
                                $scope.data.experts[expert_index].uploader.image_url = response.data;
                                $scope.data.experts[expert_index].avatar = response.data;
                            } else {
                                $rootScope.$emit('notify', {msg : response.msg});
                            }
                        }
                    }
                }

            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.addExpert = function() {
        $http.post($request_urls.expert).success(function(data) {
            if(data.code == 200) {
                var new_expert = data.data;
                new_expert.edit = true;
                new_expert.uploader = {
                    target    : $request_urls.updateExpertImage,
                    image_url : new_expert.avatar,
                    beforeCb  : function(event, item) {
                        item.formData = [
                            {
                                id : new_expert.id
                            }
                        ];
                    },
                    successCb : function(event, xhr, item, response, uploader) {
                        uploader.queue = [];
                        if(response.code == 200) {
                            new_expert.uploader.image_url = response.data;
                            new_expert.avatar = response.data;
                        } else {
                            $rootScope.$emit('notify', {msg : response.msg});
                        }
                    }
                };
                $scope.data.experts.splice(0, 0, new_expert);
                $scope.local.total++;
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };

    $scope.deleteExpert = function(expert_index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;
        var current_expert = $scope.data.experts[expert_index];
        $http.delete($request_urls.expert + current_expert.id).success(function(data) {
            if(data.code == 200) {
                $scope.data.experts.splice(expert_index, 1);
                $scope.local.total--;
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };

    $scope.toggleEdit = function(expert_index) {
        var current_expert = $scope.data.experts[expert_index];
        if(current_expert.edit){
            $http.post($request_urls.expert, {
                id : current_expert.id?current_expert.id:'',
                name      : current_expert.name,
                brief     : current_expert.brief
            }).success(function(data) {
                if(data.code == 200) {
                    current_expert.edit = false;
                } else {
                    $rootScope.$emit('notify', {
                        msg : data.msg
                    });
                }
            });

        } else {
            current_expert.edit = true;
        }
    };

    $scope.init();
};

app.controller('ExpertListCtrl', ['$scope', '$http', '$rootScope', 'commonFactory', controllers.ExpertListCtrl]);