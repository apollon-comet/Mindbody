<?php 

function oxygen_vsb_update_3_6() {

    if ( !get_option("oxygen_vsb_update_3_6") && oxygen_vsb_is_touched_install() ) {

        // check user license to whether enable Edit Mode option ot not
        oxygen_vsb_check_is_agency_bundle();

        // need to update universal.css to apply new Columns Padding Global Styles
        oxygen_vsb_cache_universal_css();

        // make sure this fires only once
        add_option("oxygen_vsb_update_3_6", true);

    };
}
add_action("admin_init", "oxygen_vsb_update_3_6");


function oxygen_vsb_update_3_7() {

    if ( !get_option("oxygen_vsb_update_3_7") ) {
    
        if ( oxygen_vsb_is_touched_install() ) {
            add_option("oxygen_options_autoload", "yes");
        }
        else {
            add_option("oxygen_options_autoload", "no");
        }

        add_option("oxygen_vsb_update_3_7", true);
    };
}
add_action("admin_init", "oxygen_vsb_update_3_7", 1);
