<?php
/**
 * Plugin Name: Woo Email Subscribe
 * Description: This plugin operated in WooCommerce offers immediate resolution if you subscribe to your website’s email. The operations of discount amount can be handled easily at the back-end, and are not a cumbersome task to perform.
A bundle of discount advantages is offered by the plugin for your website. Step forward and let your website get enhanced through this feature!
 * Version:     1.1.0
 * Author:      thehtmlcoder
 * Author URI: thehtmlcoder.net
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: woo-email-subscribe 
 */
 
 
define( 'WPWES_PLUGIN', __FILE__ );
define( 'WPWES_PLUGIN_DIR', untrailingslashit( dirname( WPWES_PLUGIN ) ) );
define( 'WPWES_PLUGIN_WOO_DIR', WPWES_PLUGIN_DIR . '/init' );
 
add_option( "jal_db_version", "1.0" );
 
global $jal_db_version;
$jal_db_version = '1.0';

function wpwes_email_subcribe_install() {
	
	global $wpdb;
	global $jal_db_version;

	$table_name = $wpdb->prefix . 'wc_email_subcribe';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,		
		wc_email text NOT NULL,		
		time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'jal_db_version', $jal_db_version );
}

register_activation_hook( __FILE__, 'wpwes_email_subcribe_install' );
 


 function wpwes_add_scripts() { 
 
   
  wp_enqueue_script('jquery');
  wp_enqueue_script( 'scripts', plugin_dir_url(__FILE__) . 'assets/js/scripts.js', array ( 'jquery' ), 1.1, true);
  wp_localize_script( 'scripts', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));   
   
}
add_action( 'wp_enqueue_scripts', 'wpwes_add_scripts' );
 
add_action( 'woocommerce_before_add_to_cart_quantity', 'wpwes_email_subscribe_html' );


function wpwes_get_email_status( $post_email   ){
	
 	 global $wpdb;
	 
	$table_name = $wpdb->prefix . 'wc_email_subcribe'; 
 	$resultCheck = '';
 	 if( !empty( $post_email )  ){ 
		
	
		$resultCheck = $wpdb->get_results( "SELECT * FROM $table_name WHERE  `wc_email` = '$post_email' ");
	 } 
	
	return $resultCheck;   
	
	
}
 
  


function wpwes_email_subscribe_html() {
	
	$current_user = wp_get_current_user();

	if( empty( wpwes_get_email_status( $current_user->user_email ) ) ){ 
		 ?>
			<div class="form-group">			
			 <p><?php esc_html_e( 'Get 50% discount now! Just enter the email', 'woo-email-subscribe' ); ?></p>

				<input type="text" name="subscribe_email" value="" placeholder="<?php esc_attr_e( 'Your email address', 'woo-email-subscribe' ); ?>" class="form-control sub_wc_email" style="border: 1px solid #eee;border-radius: 5px;padding: 5px 6px;display: block;max-width: 250px;margin-bottom: 10px;">
				<div class="wc_sub_error_message"></div>
				<button class="btn btn-sucess wc_sub_btn" name="submi_email" style="padding:8px 15px;font-size:16px;background: #222;color: #fff;border-radius: 5px;display: inline-block;margin-bottom: 20px;"><?php esc_html_e( 'Get the discount', 'woo-email-subscribe' ); ?></button>
				<div class="wc_subc_success" ></div>
			</div> 
		 <?php
	 }
 
}

add_action("wp_ajax_wpwes_email_subcribe_function", "wpwes_email_subcribe_function");
add_action("wp_ajax_nopriv_wpwes_email_subcribe_function", "wpwes_email_subcribe_function");

function wpwes_email_subcribe_function(){
	
	 $post_email = sanitize_email( $_POST["subc_email"] );

	  global $wpdb;

		$table_name = $wpdb->prefix . 'wc_email_subcribe';   
		 
	  if( empty( wpwes_get_email_status( $post_email ) ) ){
		
		
				 		
		  $result= $wpdb->insert( 
					$table_name, 
					array( 
						'wc_email' => $post_email 							
					) 
				);
				
		if($result){
			  esc_html_e('Thanks For subscribe','woo-email-subscribe');

		}else{

			  esc_html_e('Somthing Went wrong. Please as with','woo-email-subscribe');
		}  

			
		
	}else{
		esc_html_e( 'Please try with diffrent Email, Email is already in use..' );
	}   
		
	die;	
}
 

add_action('init','link_url');
function link_url(){ 
$current_user = wp_get_current_user();

 if( !empty( wpwes_get_email_status( $current_user->user_email  ) ) ){
	require_once WPWES_PLUGIN_WOO_DIR . '/price-discount-class.php';

 }

}
 