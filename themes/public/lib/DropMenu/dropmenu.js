var Dropmenu = function(element) {
	this.element = element;
}

Dropmenu.prototype.init = function() {
  var that = this;
//  that.element.children(".menu" ).hide();
  that.element.children( ".dropmenu-init" ).bind( "click", function() {
    that.element.children( ".menu" ).slideToggle();
  } );
}

