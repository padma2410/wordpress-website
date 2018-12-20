/*! Auxin WordPress Framework - v2.0.10 (2018-03-13)
 *  Scripts for initializing plugins 
 *  http://averta.net
 *  (c) 2014-2018 averta;
 */



/* ================== js/src/functions.js =================== */


/*--------------------------------------------
 *  Functions
 *--------------------------------------------*/

function auxin_is_rtl(){
    return ((typeof auxin !== 'undefined') && (auxin.is_rtl == "1" || auxin.wpml_lang == "fa") )?true:false;
}
;


/* ================== js/src/generals.js =================== */


/* ------------------------------------------------------------------------------ */
// General javascripts
/* ------------------------------------------------------------------------------ */

;(function ( $, window, document, undefined ) {
    "use strict";

    var $window = $(window),
        $siteHeader = $('#site-header'),
        headerStickyHeight = $('#site-header').data('sticky-height') || 0;

    if ( ( $siteHeader ).find( '.secondary-bar' ).length ) {
        headerStickyHeight += 35; // TODO: it should changed to a dynamic way in future
    }

    /**
     * opens or closes the overlay container in page
     * @param  {jQuery Element} $overlay
     * @param  {Boolean}        close              Is it closed right now?
     * @param  {Number}         animDuration
     */
    window.auxToggleOverlayContainer = function( $overlay, close, animDuration ) {
        var anim = $overlay.data( 'anim' ),
            overlay = $overlay[0],
            animDuration = animDuration || 800;

        if ( anim ) {
            anim.stop( true );
        }

        if ( close ) {
            $overlay.css( {
                opacity: 0,
                display: 'block'
            } );

            overlay.style[ window._jcsspfx + 'Transform' ] = 'perspective(200px) translateZ(30px)';
            anim = CTween.animate($overlay, animDuration, {
                transform: 'none', opacity: 1
            }, {
                ease: 'easeOutQuart'
            });

        } else {
            anim = CTween.animate($overlay, animDuration / 2, {
                transform: 'perspective(200px) translateZ(30px)',
                opacity: 0
            }, {
                ease: 'easeInQuad',
                complete: function() {
                    $overlay.css( 'display', 'none' );
                }
            });
        }

        $overlay.data( 'anim', anim );

    };

/* ------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------ */
    // fullscreen/overlay search
    var overlaySearchIsClosed = true,
        overlaySearchContainer = $('#fs-search'),
        searchField = overlaySearchContainer.find( 'input[type="text"]' );

    $('.aux-overlay-search').click( toggleOverlaySearch );
    overlaySearchContainer.find( '.aux-panel-close' ).click( toggleOverlaySearch );

    $(document).keydown( function(e) {
        if ( e.keyCode == 27 && !overlaySearchIsClosed ) {
            toggleOverlaySearch();
        }
    });

    function toggleOverlaySearch() {
        auxToggleOverlayContainer( overlaySearchContainer, overlaySearchIsClosed );
        overlaySearchIsClosed = !overlaySearchIsClosed;
        if ( !overlaySearchIsClosed ) {
            searchField.focus();
        }
    };

/* ------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------ */
    // burger mobile menu and search intraction
    // @if TODO
    // Selectors should be more accurate in future
    // @endif
    var $burger         = $('#nav-burger'),
        $burgerIcon     = $burger.find('>.aux-burger'),
        isClosed        = true,
        animDuration    = 600,
        $menu           = $('header .aux-master-menu'),
        anim, $menuContainer;

    /* ------------------------------------------------------------------------------ */
    function toggleExpnadableMenu() {
        $burgerIcon.toggleClass( 'aux-close' );

        if ( anim ) {
            anim.stop( true );
        }

        if ( isClosed ) {
            anim = CTween.animate($menuContainer, animDuration, { height: $menu.outerHeight() + 'px' }, {
                ease: 'easeInOutQuart',
                complete: function() {
                    $menuContainer.css( 'height', 'auto' );
                }
            } );
        } else {
            $menuContainer.css( 'height', $menu.outerHeight() + 'px' );
            anim = CTween.animate($menuContainer, animDuration, { height: 0 }, { ease: 'easeInOutQuart' } );
        }

        isClosed = !isClosed;
    }

    /* ------------------------------------------------------------------------------ */
    function toggleOffcanvasMenu() {
        $burgerIcon.toggleClass( 'aux-close' );
        $menuContainer.toggleClass( 'aux-open' );
        isClosed = !isClosed;
    }

    /* ------------------------------------------------------------------------------ */
    function toggleOverlayMenu() {
        $burgerIcon.toggleClass( 'aux-close' );
        if ( isClosed ) {
            $menuContainer.show();
        }
        auxToggleOverlayContainer( $menuContainer, isClosed );
        isClosed = !isClosed;
    }
    /* ------------------------------------------------------------------------------ */
    function closeOnEsc( toggleFunction ) {
        $(document).keydown( function(e) {
            if ( e.keyCode == 27 && !isClosed ) {
                toggleFunction();
            }
        });
    }

    /* ------------------------------------------------------------------------------ */

    switch ( $burger.data( 'target-panel' ) ) {
        case 'toggle-bar':
            $menuContainer  = $('header .aux-toggle-menu-bar');
            $burger.click( toggleExpnadableMenu );
            break;
        case 'offcanvas':
            $menuContainer  = $('#offmenu')
            $burger.click( toggleOffcanvasMenu );
            $menuContainer.find('.aux-close').click( toggleOffcanvasMenu );

            // setup swipe
            //var touchSwipe = new averta.TouchSwipe( $(document) );
            var activeWidth = $menu.data( 'switch-width' ),
                dir = ( $menuContainer.hasClass( 'aux-pin-right' ) ? 'right' : 'left' );

            if ( activeWidth !== undefined ) {
                $window.on( 'resize', function() {
                    if ( window.innerWidth > activeWidth ) {

                        $menuContainer.hide();
                    } else {
                        if ( !isClosed ) {

                        }
                        $menuContainer.show();
                    }
                });
            }

            closeOnEsc( toggleOffcanvasMenu );
            break;

        case 'overlay':
            var activeWidth = $menu.data( 'switch-width' ),
                oldSkinClassName = $menu.attr( 'class' ).match( /aux-skin-\w+/ )[0];
            $menuContainer = $('#fs-menu-search');
            $burger.click( toggleOverlayMenu );
            $menuContainer.find( '.aux-panel-close' ).click( toggleOverlayMenu );

            var checkForHide = function() {
                if ( window.innerWidth > activeWidth ) {
                    $menuContainer.hide();
                    $menu.addClass( oldSkinClassName );
                } else {
                    if ( !isClosed ) {
                        $menuContainer.show();
                    }
                    $menu.removeClass( oldSkinClassName );
                }
            }

            if ( activeWidth !== undefined ) {
                checkForHide();
                $window.on( 'resize', checkForHide );
            }

            closeOnEsc( toggleOverlayMenu );
    }

    /* ------------------------------------------------------------------------------ */
    // scroll to bottom in title bar
    if ( jQuery.fn.scrollTo ) {
        var $scrollToTarget = $('#site-title');
        $('.aux-title-scroll-down .aux-arrow-nav').click( function(){
            var target = $scrollToTarget.offset().top + $scrollToTarget.height() - headerStickyHeight;
            $window.scrollTo( target , {duration: 1500, easing:'easeInOutQuart'}  );
        } );
    }

    /* ------------------------------------------------------------------------------ */
    // goto top
    var gotoTopBtn = $('.aux-goto-top-btn'), distToFooter, footerHeight;

    $( function() {
        if ( gotoTopBtn.length && jQuery.fn.scrollTo ) {
            footerHeight = $('#sitefooter').outerHeight();

            gotoTopBtn.on( 'click touchstart', function() {
                $window.scrollTo( 0, {duration: gotoTopBtn.data('animate-scroll') ? 1500 : 0,  easing:'easeInOutQuart'});
            } );

            gotoTopBtn.css('display', 'block');
            scrollToTopOnScrollCheck();
            $window.on('scroll', scrollToTopOnScrollCheck);
        }


        function scrollToTopOnScrollCheck() {
            if ( $window.scrollTop() > 200 ) {
                gotoTopBtn[0].style[window._jcsspfx + 'Transform'] = 'translateY(0)';
                distToFooter = document.body.scrollHeight - $window.scrollTop() - window.innerHeight - footerHeight;

                if ( distToFooter < 0 ) {
                    gotoTopBtn[0].style[window._jcsspfx + 'Transform'] = 'translateY('+distToFooter+'px)';
                }
            } else {
                gotoTopBtn[0].style[window._jcsspfx + 'Transform'] = 'translateY(150px)';
            }
        }

        /* ------------------------------------------------------------------------------ */
        // add dom ready helper class
        $('body').addClass( 'aux-dom-ready' )
                 .removeClass( 'aux-dom-unready' );

        /* ------------------------------------------------------------------------------ */
        // animated goto
        if ( $.fn.scrollTo ) {
            $('a[href^="\#"]:not([href="\#"])').click( function(e) {
                e.preventDefault();
                var $this = $(this);
                if ( $this.closest('.woocommerce-tabs').length ) { return; }
                $window.scrollTo( $( $this.attr( 'href' ) ).offset().top - headerStickyHeight, $this.hasClass( 'aux-jump' )  ? 0 : 1500,  {easing:'easeInOutQuart'});
            });
        }

        /* ------------------------------------------------------------------------------ */
        // add space above sticky header if we have the wp admin bar in the page

        var $adminBar            = $('#wpadminbar'),
            marginFrameThickness = $('.aux-side-frames').data('thickness') || 0,
            siteHeaderTopPosition;

        $('#site-header').on( 'sticky', function(){
            if ( $adminBar.hasClass('mobile') || window.innerWidth <= 600 ) {
                return;
            }
            // calculate the top position
            siteHeaderTopPosition = 0;
            if( $adminBar.length ){
                siteHeaderTopPosition += $adminBar.height();
            }
            if( marginFrameThickness && window.innerWidth >= 700 ){
                siteHeaderTopPosition += marginFrameThickness;
            }
            $(this).css( 'top', siteHeaderTopPosition + 'px' );

        }).on( 'unsticky', function(){
            $(this).css( 'top', '' );
        });

        /* ------------------------------------------------------------------------------ */
        // disable search submit if the field is empty

        $('.aux-search-field, #searchform #s').each(function(){
            var $this = $(this);
            $this.parent('form').on( 'submit', function( e ){
                if ( $this.val() === '' ) {
                    e.preventDefault();
                }
            });
        });

        /* ------------------------------------------------------------------------------ */
        // fix megamenu width for middle aligned menu in header
        // var $headerContainer = $siteHeader.find('.aux-header-elements'),
        //     $headerMenu = $('#master-menu-main-header');
        // var calculateMegamenuWidth = function(){
        //     var $mm = $siteHeader.find( '.aux-middle .aux-megamenu' );
        //     if ( $mm.length ) {
        //         $mm.width( $headerContainer.innerWidth() );
        //         $mm.css( 'left', -( $headerMenu.offset().left - $headerContainer.offset().left ) + 'px' );
        //     } else {
        //         $headerMenu.find( '.aux-megamenu' ).css('width', '').css( 'left', '' );
        //     }
        // };

        // $(window).load(function() {
        //     calculateMegamenuWidth();
        // });        
        
        // $window.on( 'resize', calculateMegamenuWidth );

        /* ------------------------------------------------------------------------------ */
        // Get The height of Top bar When Overlay Header Option is enable
        if ( $siteHeader.hasClass('aux-overlay-with-tb') || $siteHeader.hasClass('aux-overlay-header') ){

            if( $siteHeader.hasClass('aux-overlay-with-tb') ){
                var $topBarHeight = $('#top-header').outerHeight();
                $('.aux-overlay-with-tb').css( 'top' , $topBarHeight+'px') ;
            }

        }

        /* ------------------------------------------------------------------------------ */

        $('.aux-check-appear, .aux-appear-watch:not(.aux-appear-repeat)').appearl({
            offset: '50px'
        }).one( 'appear', function(event, data) {
            this.classList.add('aux-appeared');
            this.classList.add('aux-appeared-once');
        });

        $('.aux-check-appear, .aux-appear-watch.aux-appear-repeat').appearl({
            offset: '50px'
        }).on( 'appear disappear', function(event, data) {
            if( event.type === 'disappear' ){
                this.classList.remove('aux-appeared');
                this.classList.add('aux-disappeared');
            } else {
                this.classList.remove('aux-disappeared');
                this.classList.add('aux-appeared');
            }
        });

    });

    /* ------------------------------------------------------------------------------ */
    /* ------------------------------------------------------------------------------ */
    // Customize media element
    $(function(){
        if ( typeof MediaElementPlayer === 'function' ) {
            var settings        = window._wpmejsSettings || {};
            settings.features   = settings.features || mejs.MepDefaults.features;
            settings.features.push( 'AuxinPlayList' );
            /* ------------------------------------------------------------------------------ */
            MediaElementPlayer.prototype.buildAuxinPlayList = function( player, controls, layers, media ) {
                if ( player.container.closest('.wp-video-playlist').length ) {
                    // Add special elements for once.
                    if ( !player.container.closest('.aux-mejs-container').length ){
                        // Add new custom wrap
                        player.container.wrap( "<div class='aux-mejs-container aux-4-6 aux-tb-1 aux-mb-1'></div>" );
                        // Add auxin classes
                        player.container.closest( '.wp-playlist' ).addClass('aux-row').find('.wp-playlist-tracks').addClass('aux-2-6 aux-tb-1 aux-mb-1');
                        // Run perfect scrollbar
                        new PerfectScrollbar('.wp-playlist-tracks');
                    }
                    player.container.addClass( 'aux-player-light' );
                    player.options.stretching = 'none';
                    player.width              = '100%';
                    var $playlistContainer    = player.container.closest( '.wp-playlist' ).find( '.wp-playlist-tracks' );
                    if( !$playlistContainer.find('.aux-playlist-background').length ) {
                        $playlistContainer.prepend( "<div class='aux-playlist-background'></div>" );
                    }
                    var $postFormatHeight     = $('.aux-primary .content').width();
                    // Set playlist Height
                    if( $postFormatHeight >= 1600 ) {
                        player.height = 720;
                    } else if( $postFormatHeight >= 768 && $postFormatHeight < 1600 ) {
                        player.height = 480;
                    } else if( $postFormatHeight >= 480 && $postFormatHeight < 768 ) {
                        player.height = 360;
                    } else {
                        player.height = 240;
                    }
                    // Set playlist height by player's height
                    $playlistContainer.css('height', player.height);
                }
            };
        }
    });

    /* ------------------------------------------------------------ */
    // Dropdown Click/Hover Functionality
    /* ------------------------------------------------------------ */
    $.fn.AuxshpDropdownEffect = function(){

        var $auxCartWrapper = $(this).find( '.aux-cart-wrapper' ),
        $auxCartHover       = $auxCartWrapper.find( '.aux-action-on-hover' ),
        auxCartAction       = 'aux-cart-display-dropdown';

        if ( $auxCartHover.length ) {

            $auxCartHover.mouseover(
                function () {
                    $auxCartWrapper.addClass( auxCartAction );
                }
            );
            $auxCartWrapper.mouseleave(
                function(){
                    $auxCartWrapper.removeClass( auxCartAction );
                }
            );

        } else {

            var $auxCartClick    = $auxCartWrapper.find( '.aux-action-on-click' );

            $auxCartClick.unbind('mouseover');
            $auxCartWrapper.unbind('mouseleave');

            $auxCartClick.click( function(e){
                e.preventDefault();
                $auxCartWrapper.addClass( auxCartAction );
            });
            $(document).click( function(e){
                if ( ! $( e.target ).closest( $auxCartWrapper ).length ) {
                    $auxCartWrapper.removeClass( auxCartAction );
                }
            });

        }

    }
    $('.aux-top-header, .site-header-section').each(function() {
        $(this).AuxshpDropdownEffect();
    });

    /* ------------------------------------------------------------ */
    // Switch the color of header buttons on sticky
    /* ------------------------------------------------------------ */

    window.auxinSetupLogoSwitcher = function(){

        if( ! $('body').hasClass('aux-top-sticky') ){
            return;
        }

        var $btns = $('#site-header .aux-btns-box .aux-button'), $btn,
            $default_logo   = $('.aux-logo-header .aux-logo-anchor:not(.aux-logo-sticky)'),
            $sticky_logo    = $('.aux-logo-header .aux-logo-anchor.aux-logo-sticky'),
            has_sticky_logo = $sticky_logo.length;

        $('#site-header').on( 'sticky', function(){
            for ( var i = 0, l = $btns.length; i < l; i++ ) {
                $btn = $btns.eq(i);
                $btn.removeClass( "aux-" + $btn.data("colorname-default") ).addClass( "aux-" + $btn.data("colorname-sticky") );
            }
            if( has_sticky_logo ){
                $default_logo.addClass('aux-logo-hidden');
                $sticky_logo.removeClass('aux-logo-hidden');
            }
        }).on( 'unsticky', function(){
            for ( var i = 0, l = $btns.length; i < l; i++ ) {
                $btn = $btns.eq(i);
                $btn.removeClass( "aux-" + $btn.data("colorname-sticky") ).addClass( "aux-" + $btn.data("colorname-default") );
            }
            if( has_sticky_logo ){
                $default_logo.removeClass('aux-logo-hidden');
                $sticky_logo.addClass('aux-logo-hidden');
            }
        });

    };
    window.auxinSetupLogoSwitcher();

    /* ------------------------------------------------------------ */
    // Select Type of DropDown For Filters
    /* ------------------------------------------------------------ */

    $.fn.AuxDropDownSelect = function(){

        var $DropDown   = $(this).find('ul'),
            $FilterBy   = $(this).find('.aux-filter-by'),
            $AllOptions = Array.from($DropDown.children('li'));

        function ClassCheck(){

            if ( ! $DropDown.hasClass('aux-active') ) {
                $DropDown.addClass('aux-active');
            } else{
                $DropDown.removeClass('aux-active');
            }

        }

        $FilterBy.click( function() {
            ClassCheck();
        });

        function InsertText(){
            var $ItemLabel = $(this).text();

            $FilterBy.find('.aux-filter-name').html($ItemLabel);
            ClassCheck();

        }

        for ( var i = 0 ; $AllOptions.length > i ; i++){
            $AllOptions[i].addEventListener('click', InsertText );
        }

        window.addEventListener('click', function(e){

            if ( e.target.className != $FilterBy.attr('class') && e.target.className != $FilterBy.find('.aux-filter-name').attr('class') ) {
                if ( $DropDown.hasClass('aux-active') ){
                    $DropDown.removeClass('aux-active');
                }
            }

        });

    }

    $('.aux-filters.aux-dropdown-filter').each(function() {
        $(this).AuxDropDownSelect();
    });

    /* Modern contact form */
    $('.aux-modern-form .form-control').on( 'focus', function() {
        $(this).parents('.form-gorup').addClass('focused');
    }).on( 'blur', function() {
        var $this = $(this);
        if ( $this.val() === '' ) {
            $this.parents('.form-gorup').removeClass('focused');
        }
    });

    /* ------------------------------------------------------------ */
    // Colorful Image Fade
    /* ------------------------------------------------------------ */

    window.auxinDynamicImgDropshadow = function( scope ) {
        var $this, scope, allImages, img, imgFrame, clonedImg;
        scope     = scope || 'body' ;
        $this     = $(scope);
        allImages = $this.find('.aux-img-dynamic-dropshadow');

        for ( var i = 0 ; allImages.length > i; i++){
            img = allImages[i];

            if ( ! img.classList.contains('aux-img-has-shadow')){
                imgFrame  = document.createElement('div');
                clonedImg = img.cloneNode();

                clonedImg.classList.add('aux-img-dynamic-dropshadow-cloned');
                clonedImg.classList.remove('aux-img-dynamic-dropshadow');
                img.classList.add('aux-img-has-shadow');
                imgFrame.classList.add('aux-img-dynamic-dropshadow-frame');

                img.parentNode.appendChild(imgFrame);
                imgFrame.appendChild(img);
                imgFrame.appendChild(clonedImg);
            }

        }

    }

    window.auxinDynamicImgDropshadow();

})(jQuery, window, document);

/* ------------------------------------------------------------ */
// WP Ulike HearBeat Animation
/* ------------------------------------------------------------ */
var UlikeHeart  = document.querySelectorAll('.wp_ulike_btn');

function auxinUlikeHeartBeat(e){
    e.target.classList.add('aux-icon-heart');
}
function removeAuxinUlikeHeartBeat(e){
    e.target.classList.remove('aux-icon-heart');
}

for ( var i = 0 ; UlikeHeart.length > i; i++){
    UlikeHeart[i].addEventListener('click', auxinUlikeHeartBeat );
    UlikeHeart[i].addEventListener('animationend', removeAuxinUlikeHeartBeat );
}
;


/* ================== js/src/init.carousel-lightbox.js =================== */



(function($, window, document, undefined){
    "use strict";

    $.fn.AuxinCarouselInit = function(){
        var $this = $(this);

        $this.find('.aux-lightbox-frame').photoSwipe({
                target: '.aux-lightbox-btn',
                bgOpacity: 0.8,
                shareEl: true
            }
        );

        $this.find('.aux-lightbox-gallery').photoSwipe({
                target: '.aux-lightbox-btn',
                bgOpacity: 0.97,
                shareEl: true
            }
        );

        $this.find('.master-carousel-slider').AuxinCarousel({
            autoplay: false,
            columns: 1,
            speed: 15,
            inView: 15,
            autohight: false,
            rtl: $('body').hasClass('rtl')
        }).on( 'auxinCarouselInit', function(){
            // init lightbox on slider after carousel init
            $this.find('.aux-lightbox-in-slider').photoSwipe({
                    target: '.aux-lightbox-btn',
                    bgOpacity: 0.8,
                    shareEl: true
                }
            );
        } );

        // all other master carousel instances
        $this.find('.master-carousel').AuxinCarousel({
            speed: 30,
            rtl: $('body').hasClass('rtl')
        });
    };

    $(document).AuxinCarouselInit();

})(jQuery, window, document);


/* ================== js/src/init.general.js =================== */


;(function($){

    $.fn.AuxinGeneralInit = function(){
        var $this = $(this);

        var isResp = $('body').hasClass( 'aux-resp' );

        // init Master Menu
        if ( !isResp && $this.find('.aux-master-menu').data( 'switch-width' ) < 7000 ) {
            // disable switch if layout is not responsive
            $this.find('.aux-master-menu').data( 'switch-width', 0 );
        }

        if ( $this.find('.aux-fs-popup').hasClass('aux-no-indicator') ){

            $this.find('.aux-master-menu').mastermenu( { useSubIndicator: false , addSubIndicator: false} );

        } else if ( $('body').hasClass( 'aux-vertical-menu' ) ) {  // Disable CheckSubmenuPosition in Vertical Menu

            $this.find('.aux-master-menu').mastermenu( { keepSubmenuInView: false } );

        } else{

            $this.find('.aux-master-menu').mastermenu( /*{openOn:'press'}*/ );

        }
        // init masonry Animation
        $this.find('.aux-widget-recent-products-parallax').AuxinMasonryAnimate();
        
        // init Tilt
        var tiltElem = $this.find('.aux-tilt-box');
        tiltElem.tilt({
            maxTilt : $(this).data('max-tilt'),
            easing: 'cubic-bezier(0.23, 1, 0.32, 1)',
            speed: $(this).data('time'),
            perspective: 2000
        });

        // init matchHeight
        $this.find('.aux-match-height > .aux-col').matchHeight();

        // float layout init
        $this.find('.aux-float-layout').AuxinFloatLayout({ autoLocate: isResp });

        // header sticky position
        if ( $('body').hasClass( 'aux-top-sticky' ) ) {
            $this.find('#site-header').AuxinStickyPosition();
        }

        // fullscreen header
        $this.find('.page-header.aux-full-height').AuxinFullscreenHero();

        $this.find('input, textarea').placeholder();

        // init fitvids
        $this.find('main').fitVids();
        $this.find('main').fitVids({ customSelector: 'iframe[src^="http://w.soundcloud.com"], iframe[src^="https://w.soundcloud.com"]'});

        // init image box
        $this.find('.aux-image-box').AuxinImagebox();

        // init before after slider
        $this.find('.aux-before-after').each( function() {
            var $slider = $(this);
            $slider.twentytwenty({
                default_offset_pct: $this.data( 'offset' ) || 0.5,
                orientation: 'horizontal'
            });
        });
    };

    $.fn.AuxinGeneralOnReadyInit = function(){
        var $this = $(this);

        $this.find('.widget-tabs .widget-inner').avertaLiveTabs({
            tabs:            'ul.tabs > li',            // Tabs selector
            tabsActiveClass: 'active',                  // A Class that indicates active tab
            contents:        'ul.tabs-content > li',    // Tabs content selector
            contentsActiveClass: 'active',              // A Class that indicates active tab-content
            transition:      'fade',                    // Animation type white switching tabs
            duration :       '500'                      // Animation duration in milliseconds
        });

        $this.find('.widget-toggle .widget-inner').each( function( index ) {
            $(this).avertaAccordion({
                itemHeader : '.toggle-header',
                itemContent: '.toggle-content',
                oneVisible : $(this).data("toggle") ,
            });
        });

        // parallax
        $this.find('.aux-parallax-box').AvertaParallaxBox();

        $this.find('.aux-frame-cube').AuxinCubeHover();
        $this.find('.aux-hover-twoway').AuxTwoWayHover();

        // general sticky init
        $this.find('.aux-sticky-side > .entry-side').AuxinStickyPosition();

        $this.find('.aux-video-box').AuxinVideobox();

        // init timeline
        $this.find('.aux-timeline').each( function(){
            if ( $(this).hasClass('aux-right') ){
                $(this).AuxinTimeline( { responsive : { 760: 'right' } } );
            }else{
                $(this).AuxinTimeline();
            }
        });

        // init highlight js
        if(typeof hljs !== 'undefined') {
            $this.find('pre code').each(function(i, block) {
                hljs.highlightBlock(block);
            });
        }

        // init auxin load more functionality
        $this.find('.widget-container[class*="aux-ajax-type"]').AuxLoadMore();

        // togglable lists
        $this.find('.aux-togglable').AuxinToggleSelected();
    };

    // Add js callback for customizer partials trigger
    if( typeof wp !== 'undefined' && typeof wp.customize !== 'undefined' ) {
        wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function() {
            // Main selector
            $customizerElement = $('body');
            // Init AuxinGeneral
            $customizerElement.AuxinGeneralInit();
            // Init AuxinGeneral
            $customizerElement.AuxinGeneralOnReadyInit();
            // Init AuxinIsotope with new scope
            $customizerElement.AuxinIsotopeInit();
            // Init AuxinIsotope with new scope
            $customizerElement.find(".site-header-section, .aux-top-header").each(function() {
                $(this).AuxshpDropdownEffect();
            });
        });
    }

    // on document ready
    $(function(){
        $(document).AuxinGeneralOnReadyInit();
    });

    $(document).AuxinGeneralInit();

})(jQuery);


/* ================== js/src/init.isotope.js =================== */


//tile element

;(function($, window, document, undefined){
    "use strict";

    $.fn.AuxinIsotopeInit = function(){
        var $this = $(this);
        // general isotope layout
        $this.find('.aux-isotope-layout').AuxIsotope({
            itemSelector:'.aux-iso-item',
            revealTransitionDuration  : 600,
            revealBetweenDelay        : 50,
            revealTransitionDelay     : 0,
            hideTransitionDuration    : 300,
            hideBetweenDelay          : 0,
            hideTransitionDelay       : 0,
            updateUponResize          : true,
            transitionHelper          : true
        });

        // init gallery
        $this.find(".aux-gallery .aux-gallery-container").AuxIsotope({
            itemSelector:'.gallery-item',
            justifyRows: {maxHeight: 340, gutter:0},
            masonry: { gutter:0 },
            revealTransitionDuration  : 600,
            hideTransitionDuration    : 600,
            revealBetweenDelay        : 70,
            hideBetweenDelay          : 40,
            revealTransitionDelay     : 0,
            hideTransitionDelay       : 0,
            updateUponResize          : true,
            transitionHelper          : true,
            deeplink                  : false
        });

        $this.find(".aux-tiles-layout").AuxIsotope({
            itemSelector        :'.aux-post-tile, .aux-iso-item',
            layoutMode          : 'packery',
            revealTransitionDuration  : 600,
            hideTransitionDuration    : 600,
            revealBetweenDelay        : 70,
            hideBetweenDelay          : 40,
            revealTransitionDelay     : 0,
            hideTransitionDelay       : 0,
            updateUponResize          : true,
            transitionHelper          : true,
            packery: {
                gutter      : 0
            }
        }).on( 'auxinIsotopeReveal', function( e, items ){
            items.forEach( function( item, index ) {
                // update image alignment inside the tiles upon pagination
                if ( item.$element.hasClass( 'aux-image-box' ) ) {
                    item.$element.AuxinImagebox('update');
                }
            } );
        });

        $this.find(".aux-big-grid-layout").AuxIsotope({
            itemSelector        :'.aux-news-big-grid, .aux-iso-item',
            layoutMode          : 'packery',
            revealTransitionDuration  : 600,
            hideTransitionDuration    : 600,
            revealBetweenDelay        : 70,
            hideBetweenDelay          : 40,
            revealTransitionDelay     : 0,
            hideTransitionDelay       : 0,
            updateUponResize          : true,
            transitionHelper          : true,
            packery: {
                gutter      : 0,
            }
        }).on( 'auxinIsotopeReveal', function( e, items ){
            items.forEach( function( item, index ) {
                // update image alignment inside the tiles upon pagination
                if ( item.$element.hasClass( 'aux-image-box' ) ) {
                    item.$element.AuxinImagebox('update');
                }
            } );
        });

        // init masonry
        $this.find(".aux-masonry-layout").AuxIsotope({
            itemSelector        :'.aux-post-masonry',
            layoutMode          : 'masonry',
            updateUponResize    : true,
            transitionHelper    : false,
            transitionDuration  : 0
        });

        // faq element isotope
        $this.find('.aux-isotope-faq').AuxIsotope({
            itemSelector:'.aux-iso-item',
            revealTransitionDuration  : 600,
            hideTransitionDuration    : 600,
            revealBetweenDelay        : 70,
            hideBetweenDelay          : 40,
            revealTransitionDelay     : 0,
            hideTransitionDelay       : 0,
            updateUponResize          : false,
            transitionHelper          : true
        }).on('auxinIsotopeReveal',function(){
            $this.find('.aux-iso-item').css({
                'position' : ''
            });
        });
    };

    $(document).AuxinIsotopeInit();

})(jQuery, window, document);


/* ================== js/src/init.jscomposer.js =================== */


// Visual Composer Adapt

;(function($, window, document, undefined){
    "use strict";
    var $vcElement,
        $_window = $(window);

    $_window.on('vc_reload', function(){

        // Main selector
        $vcElement = $('#vc_inline-frame', window.parent.document).contents().find('.vc_element');

        // Init AuxinGeneral
        $vcElement.AuxinGeneralInit();

        // Init AuxinGeneral
        $vcElement.AuxinGeneralOnReadyInit();

        // Init AuxinCarousel & photoSwipe with new scope
        $vcElement.AuxinCarouselInit();

        // Init AuxinIsotope with new scope
        $vcElement.AuxinIsotopeInit();

        // Init mejs player
        if(typeof MediaElement !== 'undefined') {
            $vcElement.find('video,audio').mediaelementplayer();
        }

        // Init instagram feed
        if(typeof sbi_js_exists !== 'undefined') {
            sbi_init();
        }

        // Init Flickr Justified Gallery
        if (typeof fjgwpp_galleriesInit_functions !== "undefined") {
            for (var i in fjgwpp_galleriesInit_functions) {
                fjgwpp_galleriesInit_functions[i]();
            }
        }

        $_window.trigger('resize');
    });

})(jQuery, window, document);


/* ================== js/src/init.jssocials.js =================== */


;(function($, window, document, undefined){
    "use strict";

    $(function(){

        var $shareButtons       = $(".aux-tooltip-socials"),        // share buttons
            mainWrapperClass    = 'aux-tooltip-socials-container',  // class for main container for button and tooltip
            tooltipWrapperClass = 'aux-tooltip-socials-wrapper';    // class for wrapper of tooltip

        if( $shareButtons.length ){

            for ( var i = 0, l = $shareButtons.length; i < l; i++ ) {

                $shareButtons.eq(i).on( "click", function( e ){
                    var $this = $(this);
                    e.preventDefault();
                    e.stopPropagation();

                    if( ! $this.parent( '.' + mainWrapperClass ).length ){
                        // wrap the button within a container
                        $this.wrap( "<div class='"+mainWrapperClass+"'></div>" );

                        // append a wrapper for tooltip in main container
                        var $container = $this.parent( '.' + mainWrapperClass );
                            $container.append( "<div class='"+tooltipWrapperClass+"'></div>" );

                        // ini the social links after clicking the main share button
                        $container.children( "." + tooltipWrapperClass ).jsSocials({
                            shares: [
                                {
                                    share: "facebook",
                                    label: "Facebook",
                                    logo : "auxicon-facebook"
                                },
                                {
                                    share: "twitter",
                                    label: "Tweet",
                                    logo : "auxicon-twitter"
                                },
                                {
                                    share: "googleplus",
                                    label: "Google Plus",
                                    logo : "auxicon-googleplus"
                                },
                                {
                                    share: "pinterest",
                                    label: "Pinterest",
                                    logo : "auxicon-pinterest"
                                },
                                {
                                    share: "linkedin",
                                    label: "LinkedIn",
                                    logo : "auxicon-linkedin"
                                },
                                {
                                    share: "stumbleupon",
                                    label: "Stumbleupon",
                                    logo : "auxicon-stumbleupon"
                                },
                                {
                                    share: "whatsapp",
                                    label: "WhatsApp",
                                    logo : "auxicon-whatsapp"
                                },
                                {
                                    share: "pocket",
                                    label: "Pocket",
                                    logo : "auxicon-pocket"
                                },
                                {
                                    share: "email",
                                    label: "Email",
                                    logo : "auxicon-email"
                                },
                                {
                                    share: "telegram",
                                    label: "Telegram",
                                    logo : "auxicon-paperplane"
                                },
                            ],
                            shareIn: 'blank',
                            showLabel: false
                        });
                    }

                    // toggle the open class by clicking on share button
                    $this.parent( "." + mainWrapperClass ).addClass('aux-tip-open').removeClass('aux-tip-close');
                });

            }

            // hide tooltip if outside the element was click
            $(window).on( "click", function() {
                $( "." + mainWrapperClass ).removeClass('aux-tip-open').addClass('aux-tip-close');
            });
        }

    });

})(jQuery, window, document);


/* ================== js/src/page-animation.js =================== */


;(function ( $, window, document, undefined ) {
    "use strict";
    /* ------------------------------------------------------------------------------ */
    // page animation timing config
    var pageAnimationConfig = {
        fade: {
            eventTarget: '#inner-body',
            propertyWatch: 'opacity',
            hideDelay: 800,
            loadingHideDuration: 810
        },

        circle: {
            eventTarget: '#inner-body',
            propertyWatch: 'transform',
            hideDelay: 1000,
            loadingHideDuration: 810
        },

        cover: {
            eventTarget: '.aux-page-animation-overlay',
            propertyWatch: 'transform',
            hideDelay: 500,
            loadingHideDuration: 810
        },

        slideup: {
            eventTarget: '.aux-page-animation-overlay',
            propertyWatch: 'transform',
            hideDelay: 500,
            loadingHideDuration: 810
        }
    },
    progressbarHideDuration = 700;

    /* ------------------------------------------------------------------------------ */
    // preload and init page animation
    var $innerBody       = $('#inner-body'),
        $body            = $('body'),
        transitionTarget,
        animationConfig;

    if ( $body.hasClass( 'aux-page-preload' ) ) {
        var $pageProgressbar = $('#pagePreloadProgressbar'),
            pageLoading = document.getElementById( 'pagePreloadLoading' );

        $(window).on( 'load.preload', function( instance ) {

            if ( $body.data( 'page-animation' ) && Modernizr && Modernizr.csstransitions ) {
                setupPageAnimate();
            } else {
                if ( pageLoading ) {
                    setTimeout( function() {
                        pageLoading.style.display = 'none';
                    }, 810 );
                }
                $body.addClass( 'aux-page-preload-done' );
            }

            if ( $pageProgressbar.length ) {
                var pageProgressbar = $pageProgressbar[0];
                pageProgressbar.style.width = pageProgressbar.offsetWidth + 'px';
                $pageProgressbar.removeClass('aux-no-js');
                pageProgressbar.style[ _jcsspfx + 'AnimationPlayState' ] = 'paused';

                setTimeout( function(){
                    pageProgressbar.style.width = '100%';
                    $pageProgressbar.addClass( 'aux-hide' );
                    $body.addClass( 'aux-progressbar-done' );
                }, 10 );

                setTimeout( function(){
                    pageProgressbar.style.display = 'none';
                }, progressbarHideDuration );
            }
        });

        window.onerror = function( e ) {
            $pageProgressbar.addClass( 'aux-hide' );
            $body.addClass( 'aux-page-preload-done' );
            $(window).off( 'load.preload' );
        }

    } else {
        setupPageAnimate();
    }

    function setupPageAnimate() {
        // disable page animation in old browsers
        if ( Modernizr && !Modernizr.csstransitions ) {
            return;
        }

        if ( !$body.hasClass( 'aux-page-animation' ) ) {
            return;
        }

        var animType         = $body.data('page-animation-type');

        animationConfig  = pageAnimationConfig[animType];
        transitionTarget = $(pageAnimationConfig[animType].eventTarget)[0];

        transitionTarget.addEventListener( 'transitionend', pageShowAnimationDone );

        $( 'a:not([href^="\#"]):not([href=""])' ).AuxinAnimateAndRedirect( {
            scrollFixTarget      : '#inner-body',
            delay       : animationConfig.hideDelay,
            //  disableOn   : '.aux-lightbox-frame, ul.tabs, .aux-gallery .aux-pagination',
            animateIn   : 'aux-page-show-' + animType,
            animateOut  : 'aux-page-hide-' + animType,
            beforeAnimateOut  : 'aux-page-before-hide-' + animType
        });
    }

    function pageShowAnimationDone( e ) {
        if ( e.target === transitionTarget && e.propertyName.indexOf( animationConfig.propertyWatch ) !== -1 ) {
            $body.addClass( 'aux-page-animation-done' );
            transitionTarget.removeEventListener( 'transitionend', pageShowAnimationDone );
        }
    }

})(jQuery, window, document);


/* ================== js/src/resize.js =================== */



/*--------------------------------------------
 *  on resize
 *--------------------------------------------*/

;(function($){

    var $_window                = $(window),
        $body                   = $('body'),
        screenWidth             = $_window.width(),
        $main_content           = $('#main'),
        breakpoint_tablet       = 768,
        breakpoint_desktop      = 1024,
        breakpoint_desktop_plus = 1140,
        original_page_layout    = '',
        layout_class_names      = {
            'right-left-sidebar' : 'right-sidebar',
            'left-right-sidebar' : 'left-sidebar',
            'left2-sidebar'      : 'left-sidebar',
            'right2-sidebar'     : 'right-sidebar'
        };


    function updateSidebarsHeight() {

        screenWidth = window.innerWidth;

        var $content   = $('.aux-primary');
        var $sidebars  = $('.aux-sidebar');

        var max_height = $('.aux-sidebar .sidebar-inner').map(function(){
            return $(this).outerHeight();
        }).get();

        max_height = Math.max.apply(null, max_height);
        max_height = Math.max( $content.outerHeight(), max_height );
        $sidebars.height( screenWidth >= breakpoint_tablet ? max_height : 'auto' );

        // Switching 2 sidebar layouts on mobile and tablet size
        // ------------------------------------------------------------

        // if it was not on desktop size
        if( screenWidth <= breakpoint_desktop_plus ){

            for ( original in layout_class_names) {
                if( $main_content.hasClass( original ) ){
                    original_page_layout =  original;
                    $main_content.removeClass( original ).addClass( layout_class_names[ original ] );
                    return;
                }
            }

        // if it was on desktop size
        } else if( '' !== original_page_layout ) {
            $main_content.removeClass('left-sidebar')
                         .removeClass('right-sidebar')
                         .addClass( original_page_layout );

            original_page_layout = '';
        }
    };


    // overrides instagram feed class and updates sidebar height on instagram images load
    if ( window.instagramfeed ) {
        var _run = instagramfeed.prototype.run;
        instagramfeed.prototype.run = function() {
            var $target = $(this.options.target);
            if ( $target.parents( '.aux-sidebar' ).length > 0 ) {
                var _after = this.options.after;
                this.options.after = function() {
                    _after.apply( this, arguments );
                    $target.find('img').one( 'load', updateSidebarsHeight );
                };
            }
            _run.apply( this, arguments );
        };
    }


    // if site frame is enabled
    if( $body.data('framed') ){

        // disable frame on small screens
        $_window.on( "debouncedresize", function(){
            $body.toggleClass('aux-framed', $_window.width() > 700 );
        });

    }

    if( $body.hasClass("aux-sticky-footer") ){

        // update the
        $_window.on( "debouncedresize", function(){

            var marginFrameThickness = $body.hasClass('aux-framed') ? $('.aux-side-frames').data('thickness') : 0,
                $footer      = $(".aux-site-footer"),
                $subfooter   = $(".aux-subfooter"),
                footerHeight = $footer.is(":visible") ? $footer.outerHeight() : 0;


            $("#main").css( "margin-bottom", footerHeight + $subfooter.outerHeight() );
            $footer.css( "bottom", marginFrameThickness );
            $subfooter.css( "bottom", footerHeight + marginFrameThickness );
        });

    }

    $_window.on( "debouncedresize", updateSidebarsHeight ).trigger('debouncedresize');


    $(document).on( 'lazyloaded', function(){
        $_window.trigger('resize');
    });

})(jQuery);

/*--------------------------------------------*/


;