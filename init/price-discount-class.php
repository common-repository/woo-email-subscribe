<?php
/*
* This Class is used for the Woocommerce Simple
* Variable Product Discount
*
*/
class Wpwes_Email_Subscribe{ 

	  public function __construct(){
		
		// Generating dynamically the product "regular price"
		add_filter( 'woocommerce_product_get_regular_price', array( $this, 'wpwes_dynamic_regular_price' ) , 10, 2 );
		add_filter( 'woocommerce_product_variation_get_regular_price', 	array( $this, 'wpwes_dynamic_regular_price' ), 10, 2 );
		
		// Generating dynamically the product "sale price"
		add_filter( 'woocommerce_product_get_sale_price', 	array( $this, 'wpwes_dynamic_sale_price' ), 10, 2 );
		add_filter( 'woocommerce_product_variation_get_sale_price', array( $this, 'wpwes_dynamic_sale_price' ), 10, 2 );
		
		// Displayed formatted regular price + sale price
		add_filter( 'woocommerce_get_price_html', array( $this, 'wpwes_dynamic_sale_price_html' ), 20, 2 );
		
		// Calculating Total on the cart page
		add_action( 'woocommerce_before_calculate_totals', 	array( $this, 'wpwes_set_cart_item_sale_price' ), 10, 1 );
		add_filter( 'woocommerce_cart_item_price', array( $this, 'wpwes_change_cart_table_price_display' ), 30, 3 );
		
	}   
	
	public function wpwes_dynamic_regular_price( $regular_price, $product ) {
		if( empty($regular_price) || $regular_price == 0 )
			return $product->get_price();
		else
		   return $regular_price;
	}


	
	public function wpwes_dynamic_sale_price( $sale_price, $product ) {
		
		$rate = 10;
		
		if( empty($sale_price) || $sale_price == 0 )
			
			return $product->get_regular_price() ;
			
		else
				$total_discount = ($rate / 100) * $sale_price;
			return  ($sale_price - $total_discount);
	}  

	
	
	 
	public function wpwes_dynamic_sale_price_html( $price_html, $product ) {
		
		if( $product->is_type('variable') ) 
			return $price_html;

		$price_html = wc_format_sale_price( wc_get_price_to_display( $product, 
		array( 'price' => $product->get_regular_price() ) ), wc_get_price_to_display(  $product, array( 'price' => $product->get_sale_price() ) ) ) . $product->get_price_suffix();

		return $price_html;
	}   

	 
	
	public function wpwes_set_cart_item_sale_price( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) )
			return;

		// Iterate through each cart item
		foreach( $cart->get_cart() as $cart_item ) {
			$price = $cart_item['data']->get_sale_price(); // get sale price
			$cart_item['data']->set_price( $price ); // Set the sale price
		}
	}
 

	public function wpwes_change_cart_table_price_display( $price, $values, $cart_item_key ) {
		$slashed_price = $values['data']->get_price_html();
		$is_on_sale = $values['data']->is_on_sale();
		if ( $is_on_sale ) {
			$price = $slashed_price;
		}
		return $price;
	}  
	 
}

new Wpwes_Email_Subscribe();