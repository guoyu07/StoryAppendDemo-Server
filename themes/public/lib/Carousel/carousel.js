//Carousel JavaScript Plugin
var Carousel = function( elements ) {
  this.elements = elements;
  this.nextElement = 0;
  this.lastElement = 0;
  this.activePos = 0;
  this.elemLength = elements.length;

  this.indicators = [];

  this.textIntro = [];
};

Carousel.prototype.init = function() {
  this.setNextActiveElem();
  this.elements.eq( this.activePos ).addClass( "active" );
  this.elements.eq( this.lastElement ).addClass( "prev" );
  this.elements.eq( this.nextElement ).addClass( "next" );
  this.slideControl();
};

Carousel.prototype.initText = function() {
  var text_intro = $("#hitour-carousel-text" );
  if( text_intro ) {
    this.textIntro = text_intro;
    this.textIntro.eq( this.activePos );
  }
};

Carousel.prototype.initIndicator = function() {
  var indicators = $( "#hitour-indicator" ).find( "li" );
  if( indicators ) {
    this.indicators = indicators;
    this.indicators.eq( this.activePos ).addClass( "indicator-active" );
  }
};

Carousel.prototype.updateIndicator = function() {
  if( this.indicators ) {
    for( var i = 0; i < this.indicators.length; i++ ) {
      this.indicators.eq( i ).removeClass( "indicator-active" );
    }
    this.indicators.eq( this.activePos ).addClass( "indicator-active" );
  }
};

Carousel.prototype.setNextActivePos = function( direction ) {
  //direction, "true" means left sliding, "false" means right sliding;
  if( this.elemLength == 1 ) {
    this.activePos = 0;
  } else if( direction == true ) {
    if( this.activePos + 1 < this.elemLength )
      this.activePos++; else
      this.activePos = 0;
  } else if( direction == false ) {
    if( this.activePos - 1 >= 0 )
      this.activePos--; else
      this.activePos = this.elemLength - 1;
  }
};

Carousel.prototype.setNextActiveElem = function() {
  if( this.elemLength == 1 ) {
    this.nextElement = 0;
    this.lastElement = 0;
  } else if( this.activePos + 1 >= this.elemLength ) {
    this.nextElement = 0;
    this.lastElement = this.activePos - 1;
  } else if( this.activePos - 1 < 0 ) {
    this.nextElement = this.activePos + 1;
    this.lastElement = this.elemLength - 1;
  } else {
    this.nextElement = this.activePos + 1;
    this.lastElement = this.activePos - 1;
  }
};

Carousel.prototype.slide = function( dest ) {
  this.elements.eq( this.activePos ).removeClass( "active" );
  this.elements.eq( this.lastElement ).removeClass( "prev" );
  this.elements.eq( this.nextElement ).removeClass( "next" );
  if( dest == "next" ) {
    this.setNextActivePos( false );
  } else {
    this.setNextActivePos( true );
  }
  this.setNextActiveElem();
  this.elements.eq( this.activePos ).addClass( "active" );
  this.elements.eq( this.lastElement ).addClass( "prev" );
  this.elements.eq( this.nextElement ).addClass( "next" );
  this.updateIndicator();
};

Carousel.prototype.slideControl = function() {
  var self = this;
  $( ".left.slide-control" ).click( function() {
    self.slide( "next" );
  } );
  $( ".right.slide-control" ).click( function() {
    self.slide( "left" );
  } );
};

/*var moduleCarousel = (function() {
  $( function() {
    var slides = $( "#hitour-carousel" ).children( ".hitour-carousel-item" );
    if( slides ) {
      var hiCarousel = new Carousel( slides );
      hiCarousel.init();
      hiCarousel.initIndicator();
    }
  } );
})();*/
