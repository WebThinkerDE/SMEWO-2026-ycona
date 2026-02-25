dom_rdy(function() {
// console.log('loaded');
    // event click for accordions
    jQuery(".acc-box .click-area").on("click", function (e) {

        console.log('clicker');
        // get/set vars
        let all_boxes   = jQuery(".acc-block .acc-wrapper .acc-box");
        let is_box      = jQuery(e.target).hasClass("acc-box");
        let jbox = "";
        // check if target is the the accordion tab
        if(is_box)
        {
            jbox = jQuery(e.target);
        }
        else
        {
            jbox = jQuery(e.target).closest(".acc-box");
        }

        // check if element has active class
        let check = jbox.hasClass("active");

        // toggle active class
        if(!check)
        {
            all_boxes.removeClass("active");
            jbox.addClass("active");
        }
        else
        {
            jbox.removeClass("active");
        }
    });
});