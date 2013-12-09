$(function() {
	var stickyNav = $('.side-nav'),
		stickyNavList = stickyNav.find('.menu'),
		header = $('.header'),
		headerHeight = header[0].offsetHeight + 1,
		content = $('.content.small-12'),
		footer = $('.footer'),
		sideNavAnchor = stickyNav.find('.side-nav-anchor');

	function contentSize(){
		stickyNav[0].style.height = (stickyNav[0].offsetHeight < content[0].offsetHeight ? content[0].offsetHeight : stickyNav[0].offsetHeight) + 'px';
		stickyNavList[0].style.height = stickyNav[0].offsetHeight + 'px';
	}

	function correctHeight(){
		var footerTopPosition = footer.offset().top - $(window).scrollTop();
		if(footerTopPosition < $(window).height()){
			stickyNav[0].style.height = footerTopPosition - headerHeight + 'px';
			stickyNavList[0].style.height = stickyNav[0].style.height;
		}
	}

	if(stickyNav.length){
		header.addClass('fixed');

		function isOfCanvas(){
			if(stickyNav.offset().left < 0 || $('body').width() < 1100){
				stickyNav.addClass('of-canvas');
			}
			else{
				stickyNav.removeClass('of-canvas').css({'marginLeft': - stickyNav[0].offsetWidth + 'px'});
			}
		}
		isOfCanvas();

		stickyNav.on('click', '.pull-nav', function(){
			if( $(this).hasClass('active') ){
				$(this).removeClass('active');
				stickyNav.animate({'marginLeft': - stickyNav[0].offsetWidth + 'px'}, 240);
			}
			else{
				$(this).addClass('active');
				stickyNav.animate({'marginLeft': - stickyNav[0].offsetWidth - stickyNav.offset().left +'px'}, 240);
			}
		});

		sideNavAnchor.on('click', function(){
			if( stickyNav.hasClass('of-canvas') ){
				stickyNav.find('.pull-nav').removeClass('active');
				stickyNav.animate({'marginLeft': - stickyNav[0].offsetWidth + 'px'}, 240);
			}
		});

		contentSize();
		window.onscroll = function() {
			correctHeight();
		}

		$(window).resize(function(){
			isOfCanvas();
		});
	}

	$('.programlisting').each(function(i, e) {hljs.highlightBlock(e)});
});