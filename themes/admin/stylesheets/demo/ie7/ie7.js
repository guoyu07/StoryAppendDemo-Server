/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referencing this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'hitour\'">' + entity + '</span>' + html;
	}
	var icons = {
		'i-triangle-right': '&#xe600;',
		'i-triangle-down': '&#xe601;',
		'i-triangle-left': '&#xe602;',
		'i-triangle-up': '&#xe603;',
		'i-plus': '&#xe604;',
		'i-minus': '&#xe605;',
		'i-heart-filled': '&#xe606;',
		'i-heart-empty': '&#xe607;',
		'i-share': '&#xe608;',
		'i-swift-arrow-right': '&#xe609;',
		'i-eye': '&#xe60a;',
		'i-search': '&#xe60b;',
		'i-calendar': '&#xe60c;',
		'i-check': '&#xe60d;',
		'i-close': '&#xe60e;',
		'i-trash': '&#xe60f;',
		'i-arrow-left': '&#xe610;',
		'i-arrow-right': '&#xe611;',
		'i-refresh': '&#xe612;',
		'i-edit': '&#xe613;',
		'i-save': '&#xe614;',
		'i-enter': '&#xe615;',
		'i-star-filled': '&#xe617;',
		'i-star-empty': '&#xe616;',
		'i-arrow-up': '&#xe618;',
		'i-arrow-down': '&#xe619;',
		'i-thin-arrow-up': '&#xe61a;',
		'i-thin-arrow-down': '&#xe61b;',
		'i-thin-arrow-left': '&#xe61c;',
		'i-thin-arrow-right': '&#xe61d;',
		'i-rating-star': '&#xe61e;',
		'i-star-circle-bg': '&#xe61f;',
		'i-hotel-circle-bg': '&#xe620;',
		'i-text-circle-bg': '&#xe621;',
		'i-location-circle-bg': '&#xe622;',
		'i-star-filled2': '&#xe623;',
		'i-hotel': '&#xe624;',
		'i-text': '&#xe625;',
		'i-location': '&#xe626;',
		'i-plus-circle-bg': '&#xe627;',
		'i-road-circle-bg': '&#xe628;',
		'i-bus-circle-bg': '&#xe629;',
		'i-walk-circle-bg': '&#xe62a;',
		'i-drive-circle-bg': '&#xe62b;',
		'i-plus-circle': '&#xe62c;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/i-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
