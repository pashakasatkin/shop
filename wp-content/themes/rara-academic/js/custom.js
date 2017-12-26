jQuery(document).ready( function($) {
	
    if( rara_academic_data.rtl == '1' ){
        var rtl = true;
        var mrtl = false;
    }else{
        var rtl = false;
        var mrtl = true;
    }    
    $('.main-navigation').meanmenu({
    	meanScreenWidth: 767,
    	meanRevealPosition: "center"
    });

    $("#lightSlider").lightSlider({
        item       : 1,
        slideMargin: 0,
        gallery    : false,
        pager      : true,
        currentPagerPosition : 'middle',
        enableDrag : false,
        freeMove   : true,
        keyPress   : true,
        rtl        : rtl
    });
    
    /* Masonry for faq */
    if( $('.page-template-template-home').length > 0 ){
        $('.services .row').imagesLoaded(function(){ 
            $('.services .row').masonry({
                itemSelector: '.col-3',
                isOriginLeft: mrtl
            }); 
        });
    }

});