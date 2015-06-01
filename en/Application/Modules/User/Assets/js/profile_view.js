$(function(){
    $("#fcarou").carouFredSel({
        circular: true,
        infinite: false,
        auto    : true,
        scroll  : {
        	duration: 1500,
            items   : 1,
            easing  : "swing"
        },
        prev    : {
            button  : ".slideshow .prev",
            key     : "left"
        },
        next    : {
            button  : ".slideshow .next",
            key     : "right"
        }
    });
    
    $('.lightbox').lightbox();
});