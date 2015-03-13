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
		'icon-person': '&#xe600;',
		'icon-head-slogan': '&#xe601;',
		'icon-map': '&#xe602;',
		'icon-chart': '&#xe603;',
		'icon-gift': '&#xe604;',
		'icon-ticket': '&#xe605;',
		'icon-presentation-chart': '&#xe606;',
		'icon-gift-bold': '&#xe607;',
		'icon-bus-circle-bg': '&#xe608;',
		'icon-three-bar': '&#xe609;',
		'icon-menu': '&#xe60a;',
		'icon-quote-begin': '&#xe60b;',
		'icon-quote-end': '&#xe60c;',
		'icon-arrow-left': '&#xe60d;',
		'icon-arrow-right': '&#xe60e;',
		'icon-arrow-up': '&#xe60f;',
		'icon-arrow-down': '&#xe610;',
		'icon-tag': '&#xe611;',
		'icon-fire': '&#xe612;',
		'icon-ctrip': '&#xe613;',
		'icon-qyer': '&#xe614;',
		'icon-mafengwo': '&#xe615;',
		'icon-kuxun': '&#xe616;',
		'icon-rmb': '&#xe617;',
		'icon-circle': '&#xe618;',
		'icon-check-circle': '&#xe619;',
		'icon-x-circle-bg': '&#xe61a;',
		'icon-exclamation-circle': '&#xe61b;',
		'icon-one-circle-bg': '&#xe61c;',
		'icon-two-circle-bg': '&#xe61d;',
		'icon-three-circle-bg': '&#xe61e;',
		'icon-refund-circle-bg': '&#xe61f;',
		'icon-refund-circle': '&#xe620;',
		'icon-calendar-circle-bg': '&#xe621;',
		'icon-calendar-circle': '&#xe622;',
		'icon-calendar': '&#xe623;',
		'icon-clock-circle-bg': '&#xe624;',
		'icon-clock-circle': '&#xe625;',
		'icon-thumbs-up-circle-bg': '&#xe626;',
		'icon-thumbs-up-circle': '&#xe627;',
		'icon-paper-plane-circle-bg': '&#xe628;',
		'icon-paper-plane-circle': '&#xe629;',
		'icon-location-circle': '&#xe62a;',
		'icon-location': '&#xe62b;',
		'icon-hitour': '&#xe62c;',
		'icon-qq': '&#xe62d;',
		'icon-qq-circle': '&#xe62e;',
		'icon-wechat': '&#xe62f;',
		'icon-weibo': '&#xe630;',
		'icon-weibo-circle': '&#xe631;',
		'icon-checkbox-unchecked': '&#xe632;',
		'icon-checkbox-checked': '&#xe633;',
		'icon-x': '&#xe634;',
		'icon-empty-person': '&#xe635;',
		'icon-x-circle': '&#xe636;',
		'icon-check': '&#xe637;',
		'icon-logo-quote': '&#xe638;',
		'icon-slogan': '&#xe639;',
		'icon-view-map': '&#xe63a;',
		'icon-view-photo': '&#xe63b;',
		'icon-circle2': '&#xe63c;',
		'icon-circle-bg': '&#xe63d;',
		'icon-four-circle-bg': '&#xe63e;',
		'icon-five-circle-bg': '&#xe63f;',
		'icon-six-circle-bg': '&#xe640;',
		'icon-seven-circle-bg': '&#xe641;',
		'icon-eight-circle-bg': '&#xe642;',
		'icon-nine-circle-bg': '&#xe643;',
		'icon-minus': '&#xe644;',
		'icon-plus': '&#xe645;',
		'icon-checked': '&#xe646;',
		'icon-bell': '&#xe647;',
		'icon-thin-arrow-left': '&#xe648;',
		'icon-thin-arrow-right': '&#xe649;',
		'icon-cry-circle-bg': '&#xe64a;',
		'icon-smile-circle-bg': '&#xe64b;',
		'icon-gift-circle-bg': '&#xe64c;',
		'icon-rmb-circle-bg': '&#xe64d;',
		'icon-share-circle-bg': '&#xe64e;',
		'icon-ticket2': '&#xe64f;',
		'icon-phone': '&#xe650;',
		'icon-heart-empty': '&#xe651;',
		'icon-heart-filled': '&#xe652;',
		'icon-tag2': '&#xe653;',
		'icon-rating-star': '&#xe654;',
		'icon-wechat-circle': '&#xe655;',
		'icon-wifi': '&#xe656;',
		'icon-tv': '&#xe657;',
		'icon-snowflake': '&#xe658;',
		'icon-diningware': '&#xe659;',
		'icon-attach': '&#xe65a;',
		'icon-bed': '&#xe65b;',
		'icon-check2': '&#xe65c;',
		'icon-arrow-up-circle': '&#xe65d;',
		'icon-arrow-down-circle': '&#xe65e;',
		'icon-arrow-right-circle': '&#xe65f;',
		'icon-file': '&#xe660;',
		'icon-rocket': '&#xe661;',
		'icon-center-dot': '&#xe662;',
		'icon-phone-full': '&#xe663;',
		'icon-phone-empty': '&#xe664;',
		'icon-couple': '&#xe665;',
		'icon-family': '&#xe666;',
		'icon-parking': '&#xe667;',
		'icon-title-before': '&#xe668;',
		'icon-thin-time': '&#xe669;',
		'icon-thin-heart': '&#xe66a;',
		'icon-thin-crown': '&#xe66b;',
		'icon-car': '&#xe66c;',
		'icon-rating-logo': '&#xe66d;',
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
		c = c.match(/icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
