
/*!
 * jQuery.scrollTo
 * Copyright (c) 2007-2014 Ariel Flesler - aflesler<a>gmail<d>com | http://flesler.blogspot.com
 * Licensed under MIT
 * http://flesler.blogspot.com/2007/10/jqueryscrollto.html
 * @projectDescription Easy element scrolling using jQuery.
 * @author Ariel Flesler
 * @version 1.4.14
 */

;(function(define){'use strict';define(['jquery'],function($){var $scrollTo=$.scrollTo=function(target,duration,settings){return $(window).scrollTo(target,duration,settings)};$scrollTo.defaults={axis:'xy',duration:0,limit:true};$scrollTo.window=function(){return $(window)._scrollable()};$.fn._scrollable=function(){return this.map(function(){var elem=this,isWin=!elem.nodeName||$.inArray(elem.nodeName.toLowerCase(),['iframe','#document','html','body'])!==-1;if(!isWin){return elem}var doc=(elem.contentWindow||elem).document||elem.ownerDocument||elem;return/webkit/i.test(navigator.userAgent)||doc.compatMode==='BackCompat'?doc.body:doc.documentElement})};$.fn.scrollTo=function(target,duration,settings){if(typeof duration==='object'){settings=duration;duration=0}if(typeof settings==='function'){settings={onAfter:settings}}if(target==='max'){target=9e9}settings=$.extend({},$scrollTo.defaults,settings);duration=duration||settings.duration;settings.queue=settings.queue&&settings.axis.length>1;if(settings.queue){duration/=2}settings.offset=both(settings.offset);settings.over=both(settings.over);return this._scrollable().each(function(){if(target===null)return;var elem=this,$elem=$(elem),targ=target,toff,attr={},win=$elem.is('html,body');switch(typeof targ){case'number':case'string':if(/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(targ)){targ=both(targ);break}targ=win?$(targ):$(targ,this);if(!targ.length)return;case'object':if(targ.is||targ.style){toff=(targ=$(targ)).offset()}}var offset=$.isFunction(settings.offset)&&settings.offset(elem,targ)||settings.offset;$.each(settings.axis.split(''),function(i,axis){var Pos=axis==='x'?'Left':'Top',pos=Pos.toLowerCase(),key='scroll'+Pos,old=elem[key],max=$scrollTo.max(elem,axis);if(toff){attr[key]=toff[pos]+(win?0:old-$elem.offset()[pos]);if(settings.margin){attr[key]-=parseInt(targ.css('margin'+Pos),10)||0;attr[key]-=parseInt(targ.css('border'+Pos+'Width'),10)||0}attr[key]+=offset[pos]||0;if(settings.over[pos]){attr[key]+=targ[axis==='x'?'width':'height']()*settings.over[pos]}}else{var val=targ[pos];attr[key]=val.slice&&val.slice(-1)==='%'?parseFloat(val)/100*max:val}if(settings.limit&&/^\d+$/.test(attr[key])){attr[key]=attr[key]<=0?0:Math.min(attr[key],max)}if(!i&&settings.queue){if(old!==attr[key]){animate(settings.onAfterFirst)}delete attr[key]}});animate(settings.onAfter);function animate(callback){$elem.animate(attr,duration,settings.easing,callback&&function(){callback.call(this,targ,settings)})}}).end()};$scrollTo.max=function(elem,axis){var Dim=axis==='x'?'Width':'Height',scroll='scroll'+Dim;if(!$(elem).is('html,body'))return elem[scroll]-$(elem)[Dim.toLowerCase()]();var size='client'+Dim,html=elem.ownerDocument.documentElement,body=elem.ownerDocument.body;return Math.max(html[scroll],body[scroll])-Math.min(html[size],body[size])};function both(val){return $.isFunction(val)||$.isPlainObject(val)?val:{top:val,left:val}}return $scrollTo})}(typeof define==='function'&&define.amd?define:function(deps,factory){'use strict';if(typeof module!=='undefined'&&module.exports){module.exports=factory(require('jquery'))}else{factory(jQuery)}}));
 
/* ========================================================================
 * ScrollPos-Styler v0.6
 * https://github.com/acch/scrollpos-styler
 * ========================================================================
 * Copyright 2015 Achim Christ
 * Licensed under MIT (https://github.com/acch/scrollpos-styler/blob/master/LICENSE)
 * ======================================================================== */

/*!
 * Theia Sticky Sidebar v1.7.0
 * https://github.com/WeCodePixels/theia-sticky-sidebar
 *
 * Glues your website's sidebars, making them permanently visible while scrolling.
 *
 * Copyright 2013-2016 WeCodePixels and other contributors
 * Released under the MIT license
 */
(function($){$.fn.theiaStickySidebar=function(options){var defaults={"containerSelector":"","additionalMarginTop":0,"additionalMarginBottom":0,"updateSidebarHeight":true,"minWidth":0,"disableOnResponsiveLayouts":true,"sidebarBehavior":"modern","defaultPosition":"relative","namespace":"TSS"};options=$.extend(defaults,options);options.additionalMarginTop=parseInt(options.additionalMarginTop)||0;options.additionalMarginBottom=parseInt(options.additionalMarginBottom)||0;tryInitOrHookIntoEvents(options,this);function tryInitOrHookIntoEvents(options,$that){var success=tryInit(options,$that);if(!success){console.log("TSS: Body width smaller than options.minWidth. Init is delayed.");$(document).on("scroll."+options.namespace,function(options,$that){return function(evt){var success=tryInit(options,$that);if(success){$(this).unbind(evt)}}}(options,$that));$(window).on("resize."+options.namespace,function(options,$that){return function(evt){var success=tryInit(options,$that);if(success){$(this).unbind(evt)}}}(options,$that))}}function tryInit(options,$that){if(options.initialized===true){return true}if($("body").width()<options.minWidth){return false}init(options,$that);return true}function init(options,$that){options.initialized=true;var existingStylesheet=$("#theia-sticky-sidebar-stylesheet-"+options.namespace);if(existingStylesheet.length===0){$("head").append($('<style id="theia-sticky-sidebar-stylesheet-'+options.namespace+'">.theiaStickySidebar:after {content: ""; display: table; clear: both;}</style>'))}$that.each(function(){var o={};o.sidebar=$(this);o.options=options||{};o.container=$(o.options.containerSelector);if(o.container.length==0){o.container=o.sidebar.parent()}o.sidebar.parents().css("-webkit-transform","none");o.sidebar.css({"position":o.options.defaultPosition,"overflow":"visible","-webkit-box-sizing":"border-box","-moz-box-sizing":"border-box","box-sizing":"border-box"});o.stickySidebar=o.sidebar.find(".theiaStickySidebar");if(o.stickySidebar.length==0){var javaScriptMIMETypes=/(?:text|application)\/(?:x-)?(?:javascript|ecmascript)/i;o.sidebar.find("script").filter(function(index,script){return script.type.length===0||script.type.match(javaScriptMIMETypes)}).remove();o.stickySidebar=$("<div>").addClass("theiaStickySidebar").append(o.sidebar.children());o.sidebar.append(o.stickySidebar)}o.marginBottom=parseInt(o.sidebar.css("margin-bottom"));o.paddingTop=parseInt(o.sidebar.css("padding-top"));o.paddingBottom=parseInt(o.sidebar.css("padding-bottom"));var collapsedTopHeight=o.stickySidebar.offset().top;var collapsedBottomHeight=o.stickySidebar.outerHeight();o.stickySidebar.css("padding-top",1);o.stickySidebar.css("padding-bottom",1);collapsedTopHeight-=o.stickySidebar.offset().top;collapsedBottomHeight=o.stickySidebar.outerHeight()-collapsedBottomHeight-collapsedTopHeight;if(collapsedTopHeight==0){o.stickySidebar.css("padding-top",0);o.stickySidebarPaddingTop=0}else{o.stickySidebarPaddingTop=1}if(collapsedBottomHeight==0){o.stickySidebar.css("padding-bottom",0);o.stickySidebarPaddingBottom=0}else{o.stickySidebarPaddingBottom=1}o.previousScrollTop=null;o.fixedScrollTop=0;resetSidebar();o.onScroll=function(o){if(!o.stickySidebar.is(":visible")){return}if($("body").width()<o.options.minWidth){resetSidebar();return}if(o.options.disableOnResponsiveLayouts){var sidebarWidth=o.sidebar.outerWidth(o.sidebar.css("float")=="none");if(sidebarWidth+50>o.container.width()){resetSidebar();return}}var scrollTop=$(document).scrollTop();var position="static";if(scrollTop>=o.sidebar.offset().top+(o.paddingTop-o.options.additionalMarginTop)){var offsetTop=o.paddingTop+options.additionalMarginTop;var offsetBottom=o.paddingBottom+o.marginBottom+options.additionalMarginBottom;var containerTop=o.sidebar.offset().top;var containerBottom=o.sidebar.offset().top+getClearedHeight(o.container);var windowOffsetTop=0+options.additionalMarginTop;var windowOffsetBottom;var sidebarSmallerThanWindow=(o.stickySidebar.outerHeight()+offsetTop+offsetBottom)<$(window).height();if(sidebarSmallerThanWindow){windowOffsetBottom=windowOffsetTop+o.stickySidebar.outerHeight()}else{windowOffsetBottom=$(window).height()-o.marginBottom-o.paddingBottom-options.additionalMarginBottom}var staticLimitTop=containerTop-scrollTop+o.paddingTop;var staticLimitBottom=containerBottom-scrollTop-o.paddingBottom-o.marginBottom;var top=o.stickySidebar.offset().top-scrollTop;var scrollTopDiff=o.previousScrollTop-scrollTop;if(o.stickySidebar.css("position")=="fixed"){if(o.options.sidebarBehavior=="modern"){top+=scrollTopDiff}}if(o.options.sidebarBehavior=="stick-to-top"){top=options.additionalMarginTop}if(o.options.sidebarBehavior=="stick-to-bottom"){top=windowOffsetBottom-o.stickySidebar.outerHeight()
}if(scrollTopDiff>0){top=Math.min(top,windowOffsetTop)}else{top=Math.max(top,windowOffsetBottom-o.stickySidebar.outerHeight())}top=Math.max(top,staticLimitTop);top=Math.min(top,staticLimitBottom-o.stickySidebar.outerHeight());var sidebarSameHeightAsContainer=o.container.height()==o.stickySidebar.outerHeight();if(!sidebarSameHeightAsContainer&&top==windowOffsetTop){position="fixed"}else{if(!sidebarSameHeightAsContainer&&top==windowOffsetBottom-o.stickySidebar.outerHeight()){position="fixed"}else{if(scrollTop+top-o.sidebar.offset().top-o.paddingTop<=options.additionalMarginTop){position="static"}else{position="absolute"}}}}if(position=="fixed"){var scrollLeft=$(document).scrollLeft();o.stickySidebar.css({"position":"fixed","width":getWidthForObject(o.stickySidebar)+"px","transform":"translateY("+top+"px)","left":(o.sidebar.offset().left+parseInt(o.sidebar.css("padding-left"))-scrollLeft)+"px","top":"0px"})}else{if(position=="absolute"){var css={};if(o.stickySidebar.css("position")!="absolute"){css.position="absolute";css.transform="translateY("+(scrollTop+top-o.sidebar.offset().top-o.stickySidebarPaddingTop-o.stickySidebarPaddingBottom)+"px)";css.top="0px"}css.width=getWidthForObject(o.stickySidebar)+"px";css.left="";o.stickySidebar.css(css)}else{if(position=="static"){resetSidebar()}}}if(position!="static"){if(o.options.updateSidebarHeight==true){o.sidebar.css({"min-height":o.stickySidebar.outerHeight()+o.stickySidebar.offset().top-o.sidebar.offset().top+o.paddingBottom})}}o.previousScrollTop=scrollTop};o.onScroll(o);$(document).on("scroll."+o.options.namespace,function(o){return function(){o.onScroll(o)}}(o));$(window).on("resize."+o.options.namespace,function(o){return function(){o.stickySidebar.css({"position":"static"});o.onScroll(o)}}(o));if(typeof ResizeSensor!=="undefined"){new ResizeSensor(o.stickySidebar[0],function(o){return function(){o.onScroll(o)}}(o))}function resetSidebar(){o.fixedScrollTop=0;o.sidebar.css({"min-height":"1px"});o.stickySidebar.css({"position":"static","width":"","transform":"none"})}function getClearedHeight(e){var height=e.height();e.children().each(function(){height=Math.max(height,$(this).height())});return height}})}function getWidthForObject(object){var width;try{width=object[0].getBoundingClientRect().width}catch(err){}if(typeof width==="undefined"){width=object.width()}return width}return this}})(jQuery);

/** Load page */
setTimeout(function () {$('#loading').fadeOut();},500);function add_tip(){if($(".__eHCN5").length >0) { $('#loading').fadeOut();$(".__eHCN5").before('<span style="position: absolute;padding-top: 10px;">ÎÂÜ°ÌáÊ¾£ºÆÀÂÛ×ÖÊý²»ÄÜÉÙÓÚ10¸ö×ÖÅ¶£¡</span>');$(".__3EZVr").prepend('<a class="__1y3LE" href="/thread">ÂÛÌ³Ê×Ò³</a>');}else{setTimeout(add_tip,100);}};if($(".single-thread").length >0) {add_tip();};

/** comment qq */
function kuacg_qq(){var qq_num=document.getElementById("qqinfo").value;if(qq_num){if(!isNaN(qq_num)){$.ajax({url:"/wp-content/themes/Tint/core/api/get-qq-info.php",type:"get",data:{qq:qq_num},dataType:"json",success:function(data){document.getElementById("email").value=(qq_num+'@qq.com');$('#comment').focus();if(data==null){document.getElementById("author").value=('QQÓÎ¿Í')}else{document.getElementById("author").value=(data[qq_num][6]==""?'QQÓÎ¿Í':data[qq_num][6])}},error:function(err){document.getElementById("author").value=('QQÓÎ¿Í');document.getElementById("email").value=(qq_num+'@qq.com');$('#comment').focus()}})}else{document.getElementById("author").value=('ÇëÊäÈëÕýÈ·µÄQQºÅÂë');document.getElementById("email").value=('ÇëÊäÈëÕýÈ·µÄQQºÅÂë')}}else{document.getElementById("author").value=('ÇëÊäÈëÄúµÄQQºÅ');document.getElementById("email").value=('ÇëÊäÈëÄúµÄQQºÅ')}}
// JSHint directives
/* exported ScrollPosStyler */ 

var ScrollPosStyler = (function(document, window) {
  "use strict";

  /* ====================
   * private variables
   * ==================== */
  var scrollPosY = 0,
      busy = false,
      onTop = true,

      // toggle style / class when scrolling below this position (in px)
      scrollOffsetY = 200,

      // choose elements to apply style / class to
      elements = document.getElementsByClassName("sps");


  /* ====================
   * private funcion to check scroll position
   * ==================== */
  function onScroll() {
    // ensure that events don't stack
    if (!busy) {
      // get current scroll position from window
      scrollPosY = window.pageYOffset;

      // if we were above, and are now below scroll position...
      if (onTop && scrollPosY > scrollOffsetY) {
        // suspend accepting scroll events
        busy = true;

        // remember that we are below scroll position
        onTop = false;

        // asynchronuously add style / class to elements
        window.requestAnimationFrame(belowScrollPos);

      // if we were below, and are now above scroll position...
      } else if (!onTop && scrollPosY <= scrollOffsetY) {
        // suspend accepting scroll events
        busy = true;

        // remember that we are above scroll position
        onTop = true;

        // asynchronuously add style / class to elements
        window.requestAnimationFrame(aboveScrollPos);
      }
    }
  }


  /* ====================
   * private function to style elements when above scroll position
   * ==================== */
  function aboveScrollPos() {
    // iterate over elements
    // for (var elem of elements) {
    for (var i = 0; elements[i]; ++i) { // chrome workaround
      // add style / class to element
      elements[i].classList.add("sps--abv");
      elements[i].classList.remove("sps--blw");
    }

    // resume accepting scroll events
    busy = false;
  }

  /* ====================
   * private function to style elements when below scroll position
   * ==================== */
  function belowScrollPos() {
    // iterate over elements
    // for (var elem of elements) {
    for (var i = 0; elements[i]; ++i) { // chrome workaround
      // add style / class to element
      elements[i].classList.add("sps--blw");
      elements[i].classList.remove("sps--abv");
    }

    // resume accepting scroll events
    busy = false;
  }


  /* ====================
   * public function to initially style elements based on scroll position
   * ==================== */
  var pub = {
    init: function() {
      // suspend accepting scroll events
      busy = true;

      // get current scroll position from window
      scrollPosY = window.pageYOffset;

      // if we are below scroll position...
      if (scrollPosY > scrollOffsetY) {
        // remember that we are below scroll position
        onTop = false;

        // asynchronuously add style / class to elements
        window.requestAnimationFrame(belowScrollPos);

      // if we are above scroll position...
      } else { // (scrollPosY <= scrollOffsetY)
        // remember that we are above scroll position
        onTop = true;

        // asynchronuously add style / class to elements
        window.requestAnimationFrame(aboveScrollPos);
      }
    }
  };


  /* ====================
   * main initialization
   * ==================== */
  // add initial style / class to elements when DOM is ready
  document.addEventListener("DOMContentLoaded", function() {
    // defer initialization to allow browser to restore scroll position
    window.setTimeout(pub.init, 1);
  });

  // register for window scroll events
  window.addEventListener("scroll", onScroll);


  return pub;
})(document, window);



 jQuery(document).ready(function($) {
	'use strict';


   /**
     * Logo image toggler
     */
	var headerImage = $('.header__logo img');

    if ($('body').hasClass('home')) {
        headerImage.attr('src', headerImage.data('light'));
    }

    if ( ! $('body').hasClass('home') ) {
	    if ($('.header').hasClass('sps--blw')) {
	    	headerImage.attr('src', headerImage.data('light'));	
	    } else {
	    	headerImage.attr('src', headerImage.data('dark'));	
	    }

		$(window).scroll(function() {	
		    if ($('.header').hasClass('sps--blw')) {
		    	headerImage.attr('src', headerImage.data('light'));	
		    } else {
		    	headerImage.attr('src', headerImage.data('dark'));	
		    }
		});
	}

    
    /**
     * Smooth scroll to top
     */
    $('.footer__go-top').on('click', function(e) {
        e.preventDefault();

        $('html,body').animate({
            scrollTop: 0
        }, 1200);
    });

	/**
	 * Hero unit animation
	 */
	$('.hero').addClass('hero--animate');

	/**
	 * Sidenav trigger
	 */
	$('.sidenav-trigger, .sidenav__close, .page__wrapper__overlay').on('click', function(e) {
		e.preventDefault();
		$('body').toggleClass('sidenav-open');
	});

	/**
	 * Header search
	 */
	$('.header__nav__btn--search').on('click', function(e) {
		e.preventDefault();
		$(this).addClass('open').find('input').focus();		
	});

	$('.header__nav__btn--search input').on('focusout', function(e) {
		$('.header__nav__btn--search').removeClass('open');
	});


  // sidebar post
  $('aside.sidebar').theiaStickySidebar({
  // Settings
  additionalMarginTop: 80
 });
});
