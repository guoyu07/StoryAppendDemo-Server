function radioSwitchDir() {
    var template = '<div class="radio-switch {{options.class}}">' +
                   '<label ng-repeat="(value, label) in options.items" ng-class="{visible: model[options.name] == value}">' +
                   '<input type="radio" name="{{options.name}}" value="{{value}}" ng-change = "options.onchange()" ng-model="model[options.name]" ng-required="{{options.required}}" />' +
                   '<span class="label">{{label}}</span>' +
                   '<span ng-if="options.notice == true">{{options.comments[value]}}</span>' + '</label>' + '</div>';
    return {
        scope    : {
            model    : '=',
            options  : '=',
            callback : '&'
        },
        replace  : true,
        restrict : 'EA',
        template : template,
        link     : function(scope, element) {
            scope.toggleSwitch = function(e) {
                if(e.target.tagName.toLowerCase() === 'span') {
                    element.toggleClass('expand');
                }
            }
            $(element).on('click', 'label', scope.toggleSwitch);
        }
    };
}

function btnSelectDir() {
    var template = '<div class="input-group button-select {{options.class}}">' +
                   '<input type="number" min="{{model.min_num}}" class="form-control" ng-model="localdata.qty">' +
                   '<div class="input-group-btn">' +
                   '<button type="button" class="btn btn-default dropdown-toggle correct-padding" data-toggle="dropdown">{{selected_label}} <span class="caret"></span></button>' +
                   '<ul class="dropdown-menu pull-right">' + "<li ng-repeat='(value, label) in options.items'>" +
                   '<label>' +
                   '<input type="radio" name="{{options.name}}" ng-model="localdata.unit" value="{{value}}" />' +
                   '{{label}}' + '</label>' + '</li>' + '</ul>' + '</div>' + '</div>';
    return {
        scope    : {
            options : '=',
            model   : '='
        },
        replace  : true,
        restrict : 'E',
        template : template,
        link     : function(scope, element) {
            scope.localdata = scope.model;
            scope.selected_label = scope.options.items[scope.model.unit.toLowerCase()];

            $(element).on('click', 'label', function(e) {
                if(e.target.tagName.toLowerCase() === 'input') {
                    var key = $(this).find('input').val();
                    scope.selected_label = scope.options.items[key];
                    scope.$apply('selected_label');
                }
            });
            scope.$watch('localdata', function(newValue) {
                scope.$parent[scope.options.model][scope.options.name] = newValue.qty.toString() + newValue.unit;
            }, true);
        }
    };
}

function markdownDir($sce) {
    var converter = new Showdown.converter();
    var template = '<div class="markdown-container {{options.class}}">' +
                   '<textarea class="editor area" ng-model="input" ng-required="{{required}}"></textarea>' +
                   '<div class="preview area" ng-bind-html="output"></div>' + '</div>';

    var linkFunc = function(scope) {
        if(!!scope.output && !!scope.output.$$unwrapTrustedValue) {
        } else {
            scope.output = $sce.trustAsHtml(JSON.stringify(scope.output));
        }

        scope.$watch('input', function(new_val) {
            scope.output = $sce.trustAsHtml(converter.makeHtml(new_val || ''));
        });
    };

    return {
        link     : linkFunc,
        scope    : {
            input   : '=',
            output  : '=',
            options : '='
        },
        replace  : true,
        restrict : 'AE',
        template : template
    };
}

function dndSortableDir() {
    return {
        scope   : {
            item     : '=',
            options  : '=',
            callback : '&'
        },
        replace : false,
        link    : function(scope, element) {
            var el = $(element[0]);
            scope.options.offset = parseInt(scope.options.offset, 10) || 0;

            el.attr('draggable', true).on('dragstart', function(e) {
                e.stopPropagation();
                $(this).addClass('dragging');
                e.originalEvent.dataTransfer.dropEffect = 'move';

                dnd = {
                    srcIndex : $(this).attr('data-index'),
                    srcItem  : angular.copy(scope.item)
                };
                if(scope.options.offset > 0) {
                    dnd.srcIndex = parseInt(dnd.srcIndex) + scope.options.offset;
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

                var dscIndex = $(this).attr('data-index');
                if(scope.options.offset > 0) {
                    dscIndex = parseInt(dscIndex) + scope.options.offset;
                }
                if(dnd.srcIndex != dscIndex) {
                    var param = {info : dnd, dstIndex : dscIndex};
                    scope.callback(param);
                }

                $(this).removeClass('hovering');
                $(this).removeClass('dragging');

                return false;
            });
        }
    };
}

function htPagination() {
    var templateData = '<div class="pagination"  ng-show = "totalpages > 1">' + '<ul>' +
                       '<li data-ng-hide="currentpage == 1" class="previous"><a href="#" class="fui-arrow-left" data-ng-click="mySelectPage(1)"></a></li>' +

                       '<li data-ng-repeat="pageIndex in page_indexes" data-ng-class="{active: currentpage == pageIndex  }"><a href="#" data-ng-click="mySelectPage(pageIndex)">{{ pageIndex }}</a></li>' +

                       '<li data-ng-hide="currentpage == totalpages" class="next"><a href="#" class="fui-arrow-right" data-ng-click="mySelectPage(totalpages)"></a></li>' +
                       '</ul>' + '</div>';

    return {
        replace    : true,
        restrict   : 'E',
        template   : templateData,
        transclude : true,
        scope      : {
            totalpages  : '=',
            currentpage : '=',
            selectpage  : '='
        },
        link       : function(scope, element, attrs) {
            scope.mySelectPage = function(page_index) {
                scope.selectpage(page_index);
                scope.currentpage = page_index;
                scope.initPageIndexes();
            };

            scope.$watch('totalpages', function(newValue, oldValue) {
                console.log('totalpages: ' + newValue);
                if(newValue > 0) {
                    scope.initPageIndexes();
                }
            });

            scope.initPageIndexes = function() {
                // update page indexes for pagination
                if(scope.totalpages <= 10) {
                    scope.startPageIndex = 1;
                    scope.endPageIndex = scope.totalpages;
                } else {
                    if(scope.currentpage > 5) {
                        scope.endPageIndex = scope.currentpage + 5;
                        if(scope.endPageIndex > scope.totalpages) {
                            scope.endPageIndex = scope.totalpages;
                        }
                        scope.startPageIndex = scope.endPageIndex - 9;
                    } else {
                        scope.startPageIndex = 1;
                        scope.endPageIndex = 10;
                    }
                }

                scope.page_indexes = [];
                for(var i = 0; i < scope.endPageIndex - scope.startPageIndex + 1; i++) {
                    scope.page_indexes[i] = i + scope.startPageIndex;
                }
            }
        }
    };
}

function sortBy() {
    var templateData = '<a ng-click="sort(sortvalue)"><span ng-transclude=""></span><span class="sorting-icon" ng-show="sortedby == sortvalue"><i class="glyphicon" ng-class="{true: ' +
                       "'glyphicon-arrow-up'" + ', false: ' + "'glyphicon-arrow-down'" + '}[sortdir == ' + "'asc'" +
                       ']"></i></span></a>';
    return {
        replace    : true,
        restrict   : 'E',
        template   : templateData,
        transclude : true,
        scope      : {
            sortdir   : '=',
            sortedby  : '=',
            sortvalue : '@',
            onsort    : '='
        },
        link       : function(scope, element, attrs) {
            scope.sort = function() {
                if(scope.sortedby == scope.sortvalue) {
                    scope.sortdir = scope.sortdir == 'asc' ? 'desc' : 'asc';
                } else {
                    scope.sortedby = scope.sortvalue;
                    scope.sortdir = 'asc';
                }
                scope.onsort(scope.sortedby, scope.sortdir);
            }
        }
    };
}

function shwoTab() {
    return {
        link : function(scope, element, attrs) {
            element.click(function(e) {
                e.preventDefault();
                $(element).tab('show');
            });
        }
    }
}

var module = angular.module('histeria.directive', ['ngSanitize']);

module.directive('radioSwitch', radioSwitchDir);

module.directive('btnSelect', btnSelectDir);

module.directive('markdown', ['$sce', markdownDir]);

module.directive('dndSortable', dndSortableDir);

module.directive('htPagination', htPagination);

module.directive('sortBy', sortBy);

module.directive('showTab', shwoTab);
