var productCommentsModel = avalon.define("productCommentsCtrl", function(vm) {

    vm.data = {
        'comments' : [],
        'score'    : {}
    };
    vm.local = {
        'comment_page_number' : [],
        'active_comment_page' : 0
    }

    vm.DataInitializer = {
        'getCommentsData' : function() {
            $.ajax({
                url      : $request_urls.commentsData,
                dataType : "json",
                success  : function(data) {
                    if(data.code == 200) {
                        productCommentsModel.data.comments = data.data;
                    } else {
                        alert(res.msg);
                    }
                }
            });
        },
        'setScoreData'    : function(score) {
            productCommentsModel.data.score = score;
            PageInitializer.setCommentPage(score);
        }
    };

    var PageInitializer = {
        'setCommentPage' : function(score) {
            var tmp_array = [];
            tmp_array.length = Math.ceil(score.total / 3);
            productCommentsModel.local.comment_page_number = tmp_array;
        }
    }

    var changeCommentPage = function(page_num) {
        $.ajax({
            url      : $request_urls.commentsData + '&page=' + page_num,
            dataType : "json",
            success  : function(data) {
                if(data.code == 200) {
                    productCommentsModel.data.comments = data.data;
                    productCommentsModel.local.active_comment_page = page_num;
                } else {
                    alert(res.msg);
                }
            }
        });
    }
    vm.goCommentPage = function(index) {
        var active = productCommentsModel.local.active_comment_page;
        var last = productCommentsModel.local.comment_page_number.length;
        if (index == 'prev' && active > 0) {
            changeCommentPage(active - 1);
        } else if (index == 'next' && active < last - 1) {
            changeCommentPage(active + 1);
        } else if (index > -1) {
            changeCommentPage(index);
        }
    }

});

$(function() {
    productCommentsModel.DataInitializer.getCommentsData();
});