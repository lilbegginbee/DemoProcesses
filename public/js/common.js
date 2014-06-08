
// browser detection lib
// https://github.com/ded/bowser
!function(e,t){typeof define=="function"?define(t):typeof module!="undefined"&&module.exports?module.exports.browser=t():this[e]=t()}("bowser",function(){function g(){return n?{name:"Internet Explorer",msie:t,version:e.match(/(msie |rv:)(\d+(\.\d+)?)/i)[2]}:l?{name:"Opera",opera:t,version:e.match(d)?e.match(d)[1]:e.match(/opr\/(\d+(\.\d+)?)/i)[1]}:r?{name:"Chrome",webkit:t,chrome:t,version:e.match(/(?:chrome|crios)\/(\d+(\.\d+)?)/i)[1]}:i?{name:"PhantomJS",webkit:t,phantom:t,version:e.match(/phantomjs\/(\d+(\.\d+)+)/i)[1]}:a?{name:"TouchPad",webkit:t,touchpad:t,version:e.match(/touchpad\/(\d+(\.\d+)?)/i)[1]}:o||u?(m={name:o?"iPhone":"iPad",webkit:t,mobile:t,ios:t,iphone:o,ipad:u},d.test(e)&&(m.version=e.match(d)[1]),m):f?{name:"Android",webkit:t,android:t,mobile:t,version:(e.match(d)||e.match(v))[1]}:s?{name:"Safari",webkit:t,safari:t,version:e.match(d)[1]}:h?(m={name:"Gecko",gecko:t,mozilla:t,version:e.match(v)[1]},c&&(m.name="Firefox",m.firefox=t),m):p?{name:"SeaMonkey",seamonkey:t,version:e.match(/seamonkey\/(\d+(\.\d+)?)/i)[1]}:{}}var e=navigator.userAgent,t=!0,n=/(msie|trident)/i.test(e),r=/chrome|crios/i.test(e),i=/phantom/i.test(e),s=/safari/i.test(e)&&!r&&!i,o=/iphone/i.test(e),u=/ipad/i.test(e),a=/touchpad/i.test(e),f=/android/i.test(e),l=/opera/i.test(e)||/opr/i.test(e),c=/firefox/i.test(e),h=/gecko\//i.test(e),p=/seamonkey\//i.test(e),d=/version\/(\d+(\.\d+)?)/i,v=/firefox\/(\d+(\.\d+)?)/i,m,y=g();return y.msie&&y.version>=8||y.chrome&&y.version>=10||y.firefox&&y.version>=4||y.safari&&y.version>=5||y.opera&&y.version>=10?y.a=t:y.msie&&y.version<8||y.chrome&&y.version<10||y.firefox&&y.version<4||y.safari&&y.version<5||y.opera&&y.version<10?y.c=t:y.x=t,y})

// console.log fix
if (typeof console == "undefined") { window.console = { log: function () {} }; }

// Layout fix
$(function(){
    handlerContentResize();
    $(window).resize(function() {
		handlerContentResize();
    });
});

(function($) {
	$.fn.removeClassWild = function(mask) {
		return this.removeClass(function(index, cls) {
			var re = mask.replace(/\*/g, '\\S+');
			return (cls.match(new RegExp('\\b' + re + '', 'g')) || []).join(' ');
		});
	};
})(jQuery);

function handlerContentResize(){

	var sHeight = ($(window).height() < $('#workspace').height()) ? $('#workspace').height() : $(window).height();
	//console.log('sHeight',sHeight);

    $('#sidebar').removeClass('filler');
    $('#sidebar').css('height', sHeight + 'px');
}

// End of layoutfix

// getStyleObject Plugin for jQuery JavaScript Library
// From: http://upshots.org/?p=112
$(function($){
	$.fn.getStyleObject = function(){
		var dom = this.get(0);
		var style;
		var returns = {};
		if(window.getComputedStyle){
			var camelize = function(a,b){
				return b.toUpperCase();
			};
			style = window.getComputedStyle(dom, null);
			for(var i = 0, l = style.length; i < l; i++){
				var prop = style[i];
				var camel = prop.replace(/\-([a-z])/g, camelize);
				var val = style.getPropertyValue(prop);
				returns[camel] = val;
			};
			return returns;
		};
		if(style = dom.currentStyle){
			for(var prop in style){
				returns[prop] = style[prop];
			};
			return returns;
		};
		return this.css();
	}
});

$.fn.copyCSS = function(source){
	var styles = $(source).getStyleObject();
	this.css(styles);
}

jQuery.cookie = function(name, value, options) {
	if (typeof value != 'undefined') { // name and value given, set cookie
		options = options || {};
		if (value === null) {
			value = '';
			options = $.extend({}, options); // clone object since it's unexpected behavior if the expired property were changed
			options.expires = -1;
		}
		var expires = '';
		if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
			var date;
			if (typeof options.expires == 'number') {
				date = new Date();
				date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
			} else {
				date = options.expires;
			}
			expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
		}
		// NOTE Needed to parenthesize options.path and options.domain
		// in the following expressions, otherwise they evaluate to undefined
		// in the packed version for some reason...
		var path = options.path ? '; path=' + (options.path) : '';
		var domain = options.domain ? '; domain=' + (options.domain) : '';
		var secure = options.secure ? '; secure' : '';
		document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
	} else { // only name given, get cookie
		var cookieValue = null;
		if (document.cookie && document.cookie != '') {
			var cookies = document.cookie.split(';');
			for (var i = 0; i < cookies.length; i++) {
				var cookie = jQuery.trim(cookies[i]);
				// Does this cookie string begin with the name we want?
				if (cookie.substring(0, name.length + 1) == (name + '=')) {
					cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
					break;
				}
			}
		}
		return cookieValue;
	}
};

function showUserBox (username) {
    $.getJSON('/services/auth/index',{},function(data){
        $('#ucpUser .ucpUsername').text( data.username );
        $('#ucpUser').show().animate({marginTop: -42});
    })
}

function showMessage(message, important) {
    var options = {};
    if (important) {
        options.header = 'Важно';
    }
    $.jGrowl(message, options);
}

function progressStart()	{$('#mask').show(); }
function progressEnd()		{$('#mask').hide(); }

function isModern(){
	//@todo: this should be fixed! Detection lib integrated above.
    /*
	if ((jQuery.browser.msie && parseInt(jQuery.browser.version) < 9) ||
		(jQuery.browser.chrome && parseInt(jQuery.browser.version) < 13) ||
		(jQuery.browser.opera && parseInt(jQuery.browser.version) < 11) ||
		(jQuery.browser.safari && parseInt(jQuery.browser.version) < 4) ||
		(jQuery.browser.mozilla && parseInt(jQuery.browser.version) < 10)) {
		return false;
	}
	*/
	return true;
}

var cBAVar = 'oldbrowseralert';
var cBAPAth = '/';
var UNDEFINED;

function noChecksToday() {
	$.cookie(cBAVar, true, { expires: 1, path: cBAPAth, domain: location.hostname });
	$('#oldBrowser').modal('hide');
}

function noMoreChecks() {
	$.cookie(cBAVar, true, { expires: 1000, path: cBAPAth, domain: location.hostname });
	$('#oldBrowser').modal('hide');
}


function alertOldBrowser() {
	var isAgreed = jQuery.cookie(cBAVar) || false;
	if (!isModern() && !isAgreed) {
		$(document.body)
			.append('<div id="oldBrowser" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
			'<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="myModalLabel">Ваш браузер устарел</h3></div>' +
			'<div class="modal-body"><p>Информационная система разрабатывается с учетом использования современных технологий, используемых в браузерах. Настоятельно рекомендуем для гарантии сохранения функциональности обновить ваш браузер</p>' +
			'<ul class="newBrowsersList"><li><a href="http://www.getfirefox.com" class="firefox">Mozilla Firefox</a></li><li><a href="http://google.com/chrome" class="chrome">Google Chrome</a></li><li><a href="http://getie.com" class="ie">Internet Explorer</a></li><li><a href="http://www.opera.com/" class="opera">Opera</a></li><li><a href="http://support.apple.com/ru_RU/downloads#safari" class="safari">Apple Safari</a></li></ul></div>' +
			'<div class="modal-footer"><button class="btn" onclick="noChecksToday();">Напомнить позже</button><button class="btn" onclick="noMoreChecks();">Больше не напоминать</button></div></div>'

		);
		$('#oldBrowser').modal('show');
	}
}

function plural_str(i, str1, str2, str3){
	function plural (a){
		if ( a % 10 == 1 && a % 100 != 11 ) return 0
		else if ( a % 10 >= 2 && a % 10 <= 4 && ( a % 100 < 10 || a % 100 >= 20)) return 1
		else return 2;
	}

	switch (plural(i)) {
		case 0: return str1;
		case 1: return str2;
		default: return str3;
	}
}


/**
 * Returns true if the object is not null or undefined. Like MooTools' $.defined.
 * @param {Object} obj
 */
function defined(obj) {
	return obj !== UNDEFINED && obj !== null;
}

function shuffle(array) {
    var tmp, current, top = array.length;

    if(top) while(--top) {
        current = Math.floor(Math.random() * (top + 1));
        tmp = array[current];
        array[current] = array[top];
        array[top] = tmp;
    }

    return array;
}

String.prototype.trunc = function(n,useWordBoundary){
	var toLong = this.length > n,
	s_ = toLong ? this.substr(0,n-1) : this;
	s_ = useWordBoundary && toLong ? s_.substr(0,s_.lastIndexOf(' ')) : s_;
	return  toLong ? s_ + '&hellip;' : s_;
};

/**
 * Based on http://www.php.net/manual/en/function.strftime.php
 * @param {String} format
 * @param {Number} timestamp
 * @param {Boolean} capitalize
 */

dateFormat = function (format, timestamp, capitalize) {
	if (!defined(timestamp) || isNaN(timestamp)) {
		return 'Invalid date';
	}
	format = pick(format, '%Y-%m-%d %H:%M:%S');

	var date = new Date(timestamp),
		key, // used in for constuct below
	// get the basic time values
		hours = date[getHours](),
		day = date[getDay](),
		dayOfMonth = date[getDate](),
		month = date[getMonth](),
		fullYear = date[getFullYear](),

		lang = defaultOptions.lang,
		langWeekdays = lang.weekdays,
	/* // uncomment this and the 'W' format key below to enable week numbers
	 weekNumber = function () {
	 var clone = new Date(date.valueOf()),
	 day = clone[getDay]() == 0 ? 7 : clone[getDay](),
	 dayNumber;
	 clone.setDate(clone[getDate]() + 4 - day);
	 dayNumber = mathFloor((clone.getTime() - new Date(clone[getFullYear](), 0, 1, -6)) / 86400000);
	 return 1 + mathFloor(dayNumber / 7);
	 },
	 */

	// list all format keys
		replacements = {

			// Day
			'a': langWeekdays[day].substr(0, 3), // Short weekday, like 'Mon'
			'A': langWeekdays[day], // Long weekday, like 'Monday'
			'd': pad(dayOfMonth), // Two digit day of the month, 01 to 31
			'e': dayOfMonth, // Day of the month, 1 through 31

			// Week (none implemented)
			//'W': weekNumber(),

			// Month
			'b': lang.shortMonths[month], // Short month, like 'Jan'
			'B': lang.months[month], // Long month, like 'January'
			'm': pad(month + 1), // Two digit month number, 01 through 12

			// Year
			'y': fullYear.toString().substr(2, 2), // Two digits year, like 09 for 2009
			'Y': fullYear, // Four digits year, like 2009

			// Time
			'H': pad(hours), // Two digits hours in 24h format, 00 through 23
			'I': pad((hours % 12) || 12), // Two digits hours in 12h format, 00 through 11
			'l': (hours % 12) || 12, // Hours in 12h format, 1 through 12
			'M': pad(date[getMinutes]()), // Two digits minutes, 00 through 59
			'p': hours < 12 ? 'AM' : 'PM', // Upper case AM or PM
			'P': hours < 12 ? 'am' : 'pm', // Lower case AM or PM
			'S': pad(date.getSeconds()), // Two digits seconds, 00 through  59
			'L': pad(mathRound(timestamp % 1000), 3) // Milliseconds (naming from Ruby)
		};


	// do the replaces
	for (key in replacements) {
		format = format.replace('%' + key, replacements[key]);
	}

	// Optionally capitalize the string and return
	return capitalize ? format.substr(0, 1).toUpperCase() + format.substr(1) : format;
};

function getDocHeight() {
	return window.innerHeight;
}

function getDocWidth() {
	return window.innerWidth;
}


(function($) {
	$.fn.textfill = function(maxFontSize) {
		maxFontSize = parseInt(maxFontSize, 10);
		return this.each(function(){
			var ourText = $("span", this),
				parent = ourText.parent(),
				maxHeight = parent.height(),
				maxWidth = parent.width(),
				fontSize = parseInt(ourText.css("fontSize"), 10),
				multiplier = maxWidth/ourText.width(),
				newSize = (fontSize*(multiplier-0.1));
			ourText.css(
				"fontSize",
				(maxFontSize > 0 && newSize > maxFontSize) ?
					maxFontSize :
					newSize
			);
		});
	};
})(jQuery);

$(function() {
	// alertOldBrowser();
	// autobind click->submit form submission on elements with .submit class
	$('.submit').click(function(){ 	$(this).parents('form:first').submit();	})

	if($('form.needvalidation').length) {
		$('form.needvalidation').validationEngine();
	}

	var Loading = document.createElement('div');
	Loading.setAttribute('id', 'loading');
	Loading.set = function(text) { $(this).text(text || ''); }
	Loading.set('Загрузка')
	$(Loading)
		.hide()
		.ajaxStart(function() { $(this).show(); })
		.ajaxStop(function() { $(this).hide(); });

	$(document.body).append(Loading);
});