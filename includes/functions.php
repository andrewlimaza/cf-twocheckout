<?php
/**
 * Doc Blocks to be updated. All functions for 2Checkout Add On goes here.
 */

/**
 * Load the plugin text domain for translation.
 *
 * @since 1.0
 */
function cf_2checkout_load_plugin_textdomain(){
	load_plugin_textdomain( 'cf-twocheckout', FALSE, CF_2CHECKOUT_PATH . 'languages');
}


/**
 * Setup redirect to Dwolla
 *
 * @since 0.1.0
 *
 * @uses "caldera_forms_submit_start_processors" filter
 *
 * @param $form
 * @param $referrer
 * @param $process_id
 *
 * @return array|void
 */
function cf_2checkout_set_transient($form, $referrer, $process_id ){
	global $transdata;
	if(!empty($transdata['twocheckout']['return_url'])){

		return $transdata;

	}else{

		// setup return urls
		$return_url = $referrer['scheme'] . '://' . $referrer['host'] . $referrer['path'];
		if ( isset( $referrer[ 'query' ] ) && ! empty( $referrer[ 'query' ] ) ) {
			$return_url = add_query_arg( $referrer[ 'query'], $return_url  );
		}
		$transdata['twocheckout'][ 'return_url' ] = $return_url;
	}

	return $transdata;
}

/**
 * Filteres the redirect url and substitutes with 2Checkout if needed.
 *
 * @since 1.0.0
 * @param array		$url			current redirect url
 * @param array		$form			array of the complete form config structure
 * @param string	$processid		unique ID if the processor instance
 *
 * @return array	array of altered transient data
 */
function cf_2checkout_redirect_to_checkout( $url, $form, $processid ){
	global $transdata;
	if ( ! empty( $transdata[ 'twocheckout' ] ) && ! empty( $transdata[ 'twocheckout' ][ 'url' ] ) ) {
		return $transdata[ 'twocheckout' ][ 'url' ];
	}
	return $url;
}


/**
 * Registers the 2Checkout Processor
 *
 * @since 1.0.0
 * @param array		$processors		array of current regestered processors
 *
 * @return array	array of regestered processors
 */
function cf_2checkout_register_processor( $processors ) {

	$processors['twocheckout'] = array(
		"name"				=>	__( '2Checkout', 'cf-twocheckout' ),
		"description"		=>	__( 'Process a payment via 2Checkout', 'cf-twocheckout' ),
		"icon"				=>	CF_2CHECKOUT_URL . "2checkout-icon.png",
		"single"			=>	true,
		"pre_processor"		=>	'cf_2checkout_pre_processor',
		"processor"			=>	'cf_2checkout_process',
		"template"			=>	CF_2CHECKOUT_PATH . "includes/config.php",
	);
	return $processors;
}

/**
 * Pre-Proccess Dwolla for Caldera Forms proccessor
 *
 * @since 0.1.0
 *
 * @param array $config Processor config
 * @param array $form Form config
 * @param string $proccesid Process ID
 *
 * @return array
 */
function cf_2checkout_pre_processor( $config, $form, $proccesid ) {
	global $transdata;
		
	/**
	 * New Submission
	 */
	//get data and errors from this processor
	$processor_data  = new Caldera_Forms_Processor_Get_Data( $config, $form, cf_2checkout_fields() );

	//If we have errors, report them and bail
	$errors = $processor_data->get_errors();
	if ( ! empty( $errors  ) ) {

		return $errors;

	}

	$payment_data = $processor_data->get_values();
	
	// Set URL.
	$url = 'https://2checkout.com/checkout/purchase';

	if ( isset( $payment_data['sandbox'] ) ) {
		$url = 'https://sandbox.2checkout.com/checkout/purchase';
	}

	$args = array(
		"sid" 					=> $payment_data['account_number'],
		"mode" 					=> "2CO",
		"li_0_type"				=> "product",
		"li_0_name"				=> $payment_data['item_name'],
		"li_0_quantity"			=> $payment_data['item_quantity'],
		"li_0_price"			=> $payment_data['price'],
		"li_0_product_id"		=> '1',
		"li_0_tangible"			=> 'N',
		"pay_method"			=> 'CC',
		"currency_code"			=> $payment_data['currency'],
	);
	
	// Add code here for recurring.

	$url = add_query_arg( $args, $url );

	$transdata[ 'twocheckout' ][ 'url' ] = $url;
	return array(
		'type' => 'success'
	);
}

/**
 * Complete processing of Dwolla for Caldera Forms.
 *
 * @since 0.1.0
 *
 * @param array $config Processor config
 * @param array $form Form config
 * @param string $proccessid Process ID
 *
 * @return array The Transdata var for this form.
 */
function cf_2checkout_process( $config, $form, $proccessid ) {

	global $transdata;

	if ( ! isset( $transdata[ $proccessid ][ 'meta' ] )) {
		$transdata[ $proccessid ][ 'meta'  ] = array();
	}

	return $transdata[ $proccessid ][ 'meta' ];

}

/**
 * The fields for this processor.
 *
 * @since 0.1.0
 *
 * @return array Array of fields
 */
function cf_2checkout_fields() {
	$fields = array(
		array(
			'id' => 'sandbox',
			'label' => __( 'Sandbox', 'cf-twocheckout' ),
			'type' => 'checkbox',
			'desc' => __( 'Use when testing. Make sure to disable before going live.', 'cf-twocheckout'),
			'required' => false,
		),
		array(
			'id'   => 'api_username',
			'label' => __( 'API Username', 'cf-twocheckout' ),
			'desc' => __( 'Enter your API Username.', 'cf-twocheckout' ),
			'type' => 'text',
			'required' => false, //change to true.
			'magic' => false,
		),
		array(
			'id'   => 'api_password',
			'label' => __( 'API Password', 'cf-twocheckout' ),
			'desc' => __( 'Enter your API Password', 'cf-twocheckout' ),
			'type' => 'text',
			'required' => false,
			'magic' => false,
		),
		array(
			'id'   => 'account_number',
			'label' => __( 'Account Number', 'cf-twocheckout' ),
			'desc' => __( 'Enter your Account Number.', 'cf-twocheckout' ),
			'type' => 'text',
			'required' => false,
			'magic' => false,
		),
		// array(
		// 	'id' => 'name',
		// 	'label' => __( 'Customer name', 'cf-twocheckout' ),
		// 	'required' => false,
		// ),
		array(
			'id' => 'price',
			'label' => __( 'Product Price', 'cf-twocheckout' ),
			'required' => false,
		),
		array(
			'id' => 'item_quantity',
			'label' => __( 'Quantity', 'cf-twocheckout' ),
			'required' => false,
		),
		array(
			'id' => 'item_name',
			'label' => __( 'Product Name', 'cf-twocheckout' ),
			'required' => false,
		),
		array(
			'id' => 'item_description',
			'label' => __( 'Product Description', 'cf-twocheckout' ),
			'required' => false,
		),
		array(
			'id' => 'currency',
			'label' => __( 'Currency', 'cf-twocheckout' ),
			'required' => false,
		),
		// array(
		// 	'id' => 'orderId',
		// 	'label' => __( 'Order ID', 'cf-twocheckout' ),
		// 	'desc' => __( 'Optional. If left blank, a random number will be used.', 'cf-twocheckout' ),
		// 	'required' => false,
		// 	'magic' => false,
		// )



	);

	return $fields;

}
