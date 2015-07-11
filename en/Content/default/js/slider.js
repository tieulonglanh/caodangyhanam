(function ($) {
	var	params		= [],
		order		= [],
		images		= [],
		links		= [],
		linksTarget	= [],
		titles		= [],
		interval	= [],
		imagePos	= [],
		appInterval	= [],
		squarePos	= [],
		reverse		= [];
	$.fn.slider = $.fn.slider = function (options) {
		var setFields = function (el) {
			var	tWidth	= parseInt(params[el.id].width / params[el.id].spw),
				sWidth	= tWidth,
				tHeight	= parseInt(params[el.id].height / params[el.id].sph),
				sHeight	= tHeight,
				counter	= 0,
				sLeft	= 0,
				sTop	= 0,
				i,
				j,
				tgapx	= params[el.id].width - params[el.id].spw * sWidth,
				gapx	= tgapx,
				tgapy	= params[el.id].height - params[el.id].sph * sHeight,
				gapy	= tgapy;
			for (i = 1; i <= params[el.id].sph; i++) {
				gapx = tgapx;
				if (gapy > 0) {
					gapy--;
					sHeight = tHeight + 1;
				} else {
					sHeight = tHeight;
				}
				for (j = 1; j <= params[el.id].spw; j++) {

					if (gapx > 0) {
						gapx--;
						sWidth = tWidth + 1;
					} else {
						sWidth = tWidth;
					}

					order[el.id][counter] = i + "" + j;
					counter++;

					if (params[el.id].links) {
						$('#' + el.id).append("<a href='" + links[el.id][0] + "' class='shg-" + el.id + "' id='shg-" + el.id + i + j + "' style='width:" + sWidth + "px; height:" + sHeight + "px; float: left; position: absolute;'></a>");
					} else {
						$('#' + el.id).append("<div class='shg-" + el.id + "' id='shg-" + el.id + i + j + "' style='width:" + sWidth + "px; height:" + sHeight + "px; float: left; position: absolute;'></div>");
					}

					/* positioning squares*/
					$("#shg-" + el.id + i + j).css({
						'background-position': -sLeft + 'px ' + (-sTop + 'px'),
						'left' : sLeft,
						'top': sTop
					});

					sLeft += sWidth;
				}

				sTop += sHeight;
				sLeft = 0;

			}

			$('.shg-' + el.id).mouseover(function(){
				$('#shg-navigation-' + el.id).show();
			});

			$('#shg-title-' + el.id).mouseover(function(){
				$('#shg-navigation-' + el.id).show();
			});


			if (params[el.id].hoverPause) {
				$('.shg-' + el.id).mouseover(function(){
					params[el.id].pause = true;
				});

				$('.shg-' + el.id).mouseout(function(){
					params[el.id].pause = false;
				});

				$('#shg-title-' + el.id).mouseover(function(){
					params[el.id].pause = true;
				});

				$('#shg-title-' + el.id).mouseout(function(){
					params[el.id].pause = false;
				});
			}

		};

		var transitionCall = function (el) {

			clearInterval(interval[el.id]);
			var delay = params[el.id].delay + params[el.id].spw * params[el.id].sph * params[el.id].sDelay;
			interval[el.id] = setInterval(function() { transition(el);  }, delay);

		};

		/* transitions*/
		var transition = function (el, direction) {

			if(params[el.id].pause === true){
				return;
			}

			effect(el);

			squarePos[el.id] = 0;
			appInterval[el.id] = setInterval(function() { appereance(el,order[el.id][squarePos[el.id]]);  },params[el.id].sDelay);

			$(el).css({ 'background-image': 'url(' + images[el.id][imagePos[el.id]] + ')' });

			if (typeof(direction) == "undefined") {
				imagePos[el.id]++;
			} else {
				if (direction == 'prev') {
					imagePos[el.id]--;
				} else {
					imagePos[el.id] = direction;
				}
			}

			if (imagePos[el.id] == images[el.id].length) {
				imagePos[el.id] = 0;
			}

			if (imagePos[el.id] == -1) {
				imagePos[el.id] = images[el.id].length-1;
			}

			$('.shg-button-' + el.id).removeClass('shg-active');
			$('#shg-button-' + el.id + "-" + (imagePos[el.id] + 1)).addClass('shg-active');

			if (titles[el.id][imagePos[el.id]]) {
				$('#shg-title-' + el.id).css({ 'opacity' : 0 }).animate({ 'opacity' : params[el.id].opacity }, params[el.id].titleSpeed);
				$('#shg-title-' + el.id).html(titles[el.id][imagePos[el.id]]);
			} else {
				$('#shg-title-' + el.id).css('opacity',0);
			}

		};

		var appereance = function (el, sid) {

			$('.shg-' + el.id).attr('href',links[el.id][imagePos[el.id]]).attr('target',linksTarget[el.id][imagePos[el.id]]);

			if (squarePos[el.id] == params[el.id].spw * params[el.id].sph) {
				clearInterval(appInterval[el.id]);
				return;
			}

			$('#shg-' + el.id + sid).css({ opacity: 0, 'background-image': 'url(' + images[el.id][imagePos[el.id]] + ')' });
			$('#shg-' + el.id + sid).animate({ opacity: 1 }, 300);
			squarePos[el.id]++;

		};

		/* navigation*/
		var setNavigation = function (el) {
			var k;

			/* create prev and next*/
			$(el).append("<div id='shg-navigation-" + el.id + "'></div>");
			$('#shg-navigation-' + el.id).show();

			$('#shg-navigation-' + el.id).append("<a href='#' id='shg-prev-" + el.id + "' class='shg-prev'>"+params[el.id].prevText+"</a>");
			$('#shg-navigation-' + el.id).append("<a href='#' id='shg-next-" + el.id + "' class='shg-next'>"+params[el.id].nextText+"</a>");
			$('#shg-prev-' + el.id).css({
				'position'		: 'absolute',
				'top'			: params[el.id].height / 2 - 15,
				'left'			: '-35px',
				'z-index'		: 1001,
				'line-height'	: '30px',
				'opacity'		: params[el.id].opacity
			}).click( function(e){
				e.preventDefault();
				transition(el,'prev');
				transitionCall(el);
			}).mouseover( function(){ $('#shg-navigation-' + el.id).show(); });

			$('#shg-next-' + el.id).css({
				'position'		: 'absolute',
				'top'			: params[el.id].height / 2 - 15,
				'right'			: '-35px',
				'z-index'		: 1001,
				'line-height'	: '30px',
				'opacity'		: params[el.id].opacity
			}).click( function(e){
				e.preventDefault();
				transition(el);
				transitionCall(el);
			}).mouseover( function(){ $('#shg-navigation-' + el.id).show(); });
			$("<div id='shg-buttons-" + el.id + "' class='shg-buttons'></div>").appendTo($('#shgSlider-' + el.id));

			for (k = 1; k < images[el.id].length + 1; k++){
				$('#shg-buttons-' + el.id).append("<a href='#' class='shg-button-" + el.id + "' id='shg-button-" + el.id + "-" + k + "'>" + k + "</a>");
			}

			$.each($('.shg-button-' + el.id), function(i,item){
				$(item).click( function(e){
					$('.shg-button-' + el.id).removeClass('shg-active');
					$(this).addClass('shg-active');
					e.preventDefault();
					transition(el,i);
					transitionCall(el);
				});
			});

			$('#shg-navigation-' + el.id + ' a').mouseout(function(){
				$('#shg-navigation-' + el.id).show();
				params[el.id].pause = false;
			});

			/*$("#shg-buttons-" + el.id).css({
				'right'			: '17px',
				'margin-left'	: -images[el.id].length * 15 / 2 - 5,
				'position'		: 'absolute',
				'bottom'		: '10px'

			});*/

		};

		var effect = function (el) {
			var effA = ['random','swirl','rain','straight'],
				i,
				j,
				counter,
				eff;

			if (params[el.id].effect === '') {
				eff = effA[Math.floor(Math.random() * (effA.length))];
			} else {
				eff = params[el.id].effect;
			}

			order[el.id] = [];

			if (eff == 'random') {
				counter = 0;
					for (i = 1; i <= params[el.id].sph; i++) {
						for (j = 1; j <= params[el.id].spw; j++) {
							order[el.id][counter] = i + "" + j;
							counter++;
						}
					}
				randomEffect(order[el.id]);
			}

			if (eff == 'rain') {
				rain(el);
			}

			if (eff == 'swirl') {
				swirl(el);
			}

			if (eff == 'straight') {
				straight(el);
			}

			reverse[el.id] *= -1;

			if (reverse[el.id] > 0) {
				order[el.id].reverse();
			}

		};

		var randomEffect = function (arr) {

			var i = arr.length,
				j,
				tempi,
				tempj;

			if ( i === 0 ) {
				return false;
			}

			while ( --i ) {
				j = Math.floor( Math.random() * ( i + 1 ) );
				tempi = arr[i];
				tempj = arr[j];
				arr[i] = tempj;
				arr[j] = tempi;
			}
		};

		var swirl = function (el) {

			var n = params[el.id].sph,
				m = params[el.id].spw,
				x = 1,
				y = 1,
				going = 0,
				num = 0,
				c = 0,
				check,
				dowhile = true,
				i;

			while (dowhile) {

				num = (going === 0 || going === 2) ? m : n;

				for( i=1; i<=num; i++){

					order[el.id][c] = x + '' + y;
					c++;

					if( i != num){
						switch(going){
							case 0 : y++; break;
							case 1 : x++; break;
							case 2 : y--; break;
							case 3 : x--; break;

						}
					}
				}

				going = (going + 1) % 4;

				switch (going) {
					case 0 : m--; y++; break;
					case 1 : n--; x++; break;
					case 2 : m--; y--; break;
					case 3 : n--; x--; break;
				}

				check = max(n,m) - min(n,m);
				if (m <= check && n <= check) {
					dowhile = false;
				}

			}
		};

		var rain = function (el) {

			var n = params[el.id].sph,
				m = params[el.id].spw,
				c = 0,
				to = 1,
				to2 = 1,
				from = 1,
				dowhile = true;

			while (dowhile) {

				for (i = from; i <= to; i++) {
					order[el.id][c] = i + '' + parseInt(to2 - i + 1);
					c++;
				}

				to2++;

				if (to < n && to2 < m && n < m) {
					to++;
				}

				if (to < n && n >= m) {
					to++;
				}

				if (to2 > m) {
					from++;
				}

				if (from > to) {
					dowhile= false;
				}

			}

		};

		var straight = function (el) {
			var counter = 0,
				i,
				j;

			for (i = 1; i <= params[el.id].sph; i++) {
				for (j = 1; j <= params[el.id].spw; j++) {
					order[el.id][counter] = i+''+j;
					counter++;
				}

			}
		};

		var min = function (n,m) {
			if(n > m) {
				return m;
			} else {
				return n;
			}
		};

		var max = function (n,m) {
			if(n < m) {
				return m;
			} else {
				return n;
			}
		};

		var init = function (el) {

			order[el.id]		= [];	
			images[el.id]		= [];
			links[el.id]		= [];
			linksTarget[el.id]	= [];
			titles[el.id]		= [];
			imagePos[el.id]		= 0;
			squarePos[el.id]	= 0;
			reverse[el.id]		= 1;

			params[el.id] = $.extend({}, $.fn.slider.defaults, options);

			$.each($('#' + el.id + ' img'), function (i, item) {
				images[el.id][i]		= $(item).attr('src');
				links[el.id][i]			= $(item).parent().is('a') ? $(item).parent().attr('href') : '';
				linksTarget[el.id][i]	= $(item).parent().is('a') ? $(item).parent().attr('target') : '';
				titles[el.id][i]		= $(item).next().is('span') ? $(item).next().html() : '';
				$(item).hide();
				$(item).next().hide();
			});

			$(el).css({
				'background-image':'url(' + images[el.id][0] + ')',
				'width': params[el.id].width,
				'height': params[el.id].height,
				'position': 'relative',
				'background-position': 'top left'
			}).wrap("<div class='shgSlider' id='shgSlider-" + el.id + "' />");

			$('#' + el.id).append("<div class='shg-title' id='shg-title-" + el.id + "' style='position: absolute; bottom:0; left: 0; z-index: 1000;'></div>");

			setFields(el);

			if(params[el.id].navigation){
				setNavigation(el);
			}

			transition(el,0);
			transitionCall(el);

		};

		this.each (
			function () {
				init(this);
			}
		);


	};

	$.fn.slider.defaults = {
		width: 450, /* width of slider panel*/
		height: 200, /* height of slider panel*/
		spw: 7, /* squares per width*/
		sph: 5, /* squares per height*/
		delay: 3000, /* delay between images in ms*/
		sDelay: 30, /* delay beetwen squares in ms*/
		opacity: 0.7, /* opacity of title and navigation*/
		titleSpeed: 500,  /*speed of title appereance in ms*/
		effect: 'random', /* random, swirl, rain, straight*/
		navigation: true, /* prev next and buttons*/
		links : true, /* show images as links*/
		hoverPause: true, /* pause on hover*/
		prevText: 'prev',
		nextText: 'next'
	};

})(jQuery);