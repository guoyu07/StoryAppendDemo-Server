!function($) {

    'use strict';


    function HiModal(options) {
        this.$HiModal = $('#' + options.dom);
        this.id = options.dom;
        this.$overlay = $('#' + this.id + ' .full-overlay');
        this.$modal = $('#' + this.id + ' .modal-wrap');

        this.type = options.type ? options.type : 'pop';
        this.default_show = options.default_show ? options.default_show : false;
        this.bg_type = options.bg_type ? options.bg_type : 'black';

        this.init();
    }

    HiModal.prototype.init = function() {
        var self = this;
        self.$overlay.addClass(self.bg_type);
        self.$HiModal.addClass(self.type);
        switch(self.type) {
            case 'pop':
                if(self.default_show == true) {
                    self.$overlay.fadeIn(200);
                    self.$modal.fadeIn(200);
                    $('html').css('overflow', 'hidden');
                }
                self.showControl.pop(self);
                self.hideControl.pop(self);
                break;
            case 'side':
                if(self.default_show == true) {
                    self.$overlay.fadeIn(300);
                    self.$modal.css('right', 0);
                    $('html').css('overflow', 'hidden');
                }
                self.showControl.side(this);
                self.hideControl.side(this);
                break;
        }
    };

    HiModal.prototype.showControl = {
        'pop'  : function(self) {
            $(document).on('click', '*[data-target="' + self.id + '"]', function() {
                self.$overlay.fadeIn(200);
                self.$modal.fadeIn(200);
                $('html').css('overflow', 'hidden');
            });
        },
        'side' : function(self) {
            $(document).on('click', '*[data-target="' + self.id + '"]', function() {
                self.$overlay.fadeIn(200);
                self.$modal.css('right', 0);
                $('html').css('overflow', 'hidden');
            });
        }
    };

    HiModal.prototype.hideControl = {
        'pop'  : function(self) {
            $(document).on('click', '*[data-close="' + self.id + '"]', function() {
                self.$overlay.fadeOut(200);
                self.$modal.fadeOut(200);
                $('html').css('overflow', 'auto');
            });
        },
        'side' : function(self) {
            $(document).on('click', '*[data-close="' + self.id + '"]', function() {
                self.$overlay.fadeOut(200);
                self.$modal.css('right', -self.$modal.width());
                $('html').css('overflow', 'auto');
            });
        }
    };


    window.HiModal = HiModal;


}(jQuery);






