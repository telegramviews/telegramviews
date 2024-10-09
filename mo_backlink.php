<?php
/**
Plugin Name: abtinstar - backlink
Plugin URI: https://abtinstar.com/
Description: این پلاگین به سفارش آقای مهدی باقری جهت داشتن سیستم مدیریت بک لینک ایجاد شده است شماره پشتیبانی پلاگین 09178522925
Version: 1.0.0
Author: امیررضا مولودی || amirreza molodi
Author URI: https://ponisha.ir/profile/amirmotahari
License: GPLv2 or later
 */

if (!headers_sent()) {
    session_start();
}


//--------start define const--------------------------------
defined ('ABSPATH') || exit ;

//path const use for include and require the files like classes and functions
define('mo_backlink_dirpath' , plugin_dir_path(__FILE__)) ;
define('mo_acf_dirpath' , plugins_url("advanced-custom-fields/acf.php",__DIR__)) ;

define('mo_backlink_classes' , plugin_dir_path(__FILE__).'classes') ;
define('mo_backlink_includes' , plugin_dir_path(__FILE__).'includes') ;
define('mo_backlink_views' , plugin_dir_path(__FILE__).'views') ;
define('mo_backlink_views_dashboard' , plugin_dir_path(__FILE__).'views/dashboard') ;
define('mo_backliknk_backlink_files_dir' , plugin_dir_path(__FILE__).'backlink_files') ;



//url consts use for image and assests like css and js
define('mo_disc_css' , plugins_url('template/assets/css',__FILE__) ) ;
define('mo_backliknk_includes_url' , plugins_url('includes',__FILE__) ) ;
define('mo_backliknk_backlink_files_url' , plugins_url('backlink_files',__FILE__) ) ;


define('mo_backlink_views_assets' , plugins_url('views/assets',__FILE__) ) ;
define('mo_backlink_views_dashboard_assets' , plugins_url('views/dashboard/assests',__FILE__) ) ;


//-------end define consts============================================================

//start include important files------------------------------------------------------
require_once mo_backlink_includes."/init.php" ; //it this file we write general functions
require_once mo_backlink_includes."/column.php" ;

require_once mo_backlink_dirpath."/vendor/autoload.php" ;
require_once mo_backlink_includes."/ajax/ajax.php" ; // in this file ajax function exists
//end include ==========================================================================


//start init ---------------------
add_action('init','mo_backlink_init'); // function is in includes/init
add_action('init','mo_modifine_posttypes');

//add_action('init', 'remove_admin_bar'); // function is in includes/init
add_action( 'login_enqueue_scripts', 'bl_login_logo' ); //function is in includes/init
add_action( 'plugins_loaded', array( 'mo_App\PageTemplater', 'get_instance' ) ); // function is in ./classes
add_filter( 'login_url', 'bl_login_url', PHP_INT_MAX ); // callback function is includes/init
add_filter( 'register_url', 'bl_register_url', PHP_INT_MAX ); // callback function is includes/init
add_filter('get_avatar_data', 'mo_change_avatar', 100, 2);// this is change avatar with customize avatar plugin created
//end init ============================
//TODO sort this part 

function mo_change_user_read_status ($userobj){

    update_user_meta($userobj->ID,"new_user","read") ;
}

add_action("personal_options","mo_change_user_read_status",10,1);
 // end of function

//start_short codes --------------------------------------------------------------------
//add_shortcode( 'mo_backlink_user_register', 'mo_backlink_user_register');


//end shortcodes ========================================================================

//start afther save withdrawal
function afther_deposit_withdrawl( $post_id, $post, $update ) {
    $accounting = new \mo_App\mo_bl_accounting() ;

    if($update){
        if ( 'mo_bl_withdraw' == $post->post_type ) {
            $deposit = get_post_meta($post_id,"deposited",true) ;
            $bede = get_post_meta($post_id,"bede",true) ;
            $accounting_update = get_post_meta($post_id,"accounting_update",true) ;

            if($deposit == 1 && $accounting_update != 1 ){
                update_post_meta($post_id,"accounting_update","1",true) ;

                $accounting->add_accounting_record($post->post_author,"0","withdrawal_cash_from_user",$bede
                ,0,0,0,0,"free");
            }else{

            }
        }
    }

    return ;
}
add_action( 'save_post', 'afther_deposit_withdrawl', 10,3 );
//start menues -------------------------------------------------------------------------
function mo_bl_setting_options(){
    require_once mo_backlink_views."/wordpress_admin_panel/options.php" ;
}

function mo_bl_set_menue() {
    add_options_page('بک لینک اتوریتی', 'بک لینک اتوریتی', 'manage_options', 'mo_bl_setting', 'mo_bl_setting_options');
}
add_action('admin_menu', 'mo_bl_set_menue');

//end menue==============================================================================