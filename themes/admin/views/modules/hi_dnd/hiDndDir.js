var dnd;

directives.hiDndDir = function() {
    var linkFunc = function(scope, element) {
        var el = $(element[0]);
        scope.options.offset = parseInt(scope.options.offset, 10) || 0;

        el.attr('draggable', true).on('dragstart', function(e) {
            $(this).addClass('dragging');
            e.originalEvent.dataTransfer.dropEffect = 'move';

            dnd = {
                src_index : $(this).attr('data-index'),
                src_item  : scope.item
            };
            if(scope.options.offset > 0) {
                dnd.src_index = parseInt(dnd.src_index) + scope.options.offset;
            }

        }).on('dragover', function(e) {
            e.preventDefault();

            return false;
        }).on('dragenter', function() {
            if(!$(this).hasClass('dragging')) {
                $(this).addClass('hovering');
            }
        }).on('dragleave', function() {
            $(this).removeClass('hovering');
        }).on('dragend', function() {
            $(this).removeClass('hovering');
            $(this).removeClass('dragging');
        }).on('drop', function(e) {
            e.stopPropagation();

            var dsc_index = $(this).attr('data-index');
            if(scope.options.offset > 0) {
                dsc_index = parseInt(dsc_index) + scope.options.offset;
            }
            if(dnd.src_index != dsc_index) {
                var param = {info : dnd, dst_index : dsc_index};
                scope.callback(param);
            }

            $(this).removeClass('hovering');
            $(this).removeClass('dragging');

            return false;
        });
    };

    return {
        link     : linkFunc,
        scope    : {
            item     : '=',
            options  : '=',
            callback : '&'
        },
        replace  : false,
        restrict : 'AE'
    };
};

app.directive('hiDnd', directives.hiDndDir);