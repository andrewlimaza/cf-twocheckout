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


}

/**
 * Filteres the redirect url and substitutes with 2Checkout if needed.
 *
 * @since 1.0.0
 * @param array		$url			current redirect url
 * @param array		$form			array of the complete form config structure
 * @param array		$config			config array of processor instance
 * @param string	$processid		unique ID if the processor instance
 *
 * @return array	array of altered transient data
 */
function cf_2checkout_redirect_to_checkout( $url, $form, $processid ){
	global $transdata;
		if( ! empty( $transdata['twocheckout' ][ $processid ][ 'url' ] ) && empty( $_GET['error'] ) && empty( $_GET['error_description'] ) ) {
			$saved_url = $transdata['twocheckout' ][ $processid ][ 'url' ];
			return $saved_url;
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
	 * Complete submission after coming back from Dwolla.
	 */
	if( !empty( $_GET['cf-twocheckout-payment-confirmation'] ) && '2checkout' == $_GET['cf-twocheckout-payment-confirmation'] ){
		if ( isset( $_GET[ 'processid' ] ) && isset( $transdata[ 'twocheckout' ] ) && isset( $transdata[ 'twocheckout' ][ $_GET[ 'processid' ] ] ) ) {

			/**
			 * @var Caldera_Forms_Processor_Get_Data
			 */
			$processor_data = $transdata[ 'twocheckout' ][ $_GET[ 'processid' ] ][ 'process_object' ];
			if ( ! is_object( $processor_data )  ){
				return array(
					'type' => 'error',
					'note' => __( 'Error completing transaction', 'cf-twocheckout' )
				);
			}




			if ( ! isset( $transdata[ $proccesid ][ 'meta'] ) ) {
				$transdata[ $proccesid ][ 'meta'] = array();
			}

			$payment_data = $processor_data->get_values();
			if ( isset( $_GET['orderId'] ) && isset( $_GET['status'] ) ) {
				$transdata[ $proccesid ][ 'meta' ][ 'orderId' ] = $_GET['orderId'];
				$transdata[ $proccesid ][ 'meta' ][ 'status' ] = $_GET[ 'status' ];

				if ( $_GET['status'] == 'Completed' ) {
					if ( $_GET['signature'] == hash_hmac( 'sha1', $_GET['checkoutId'] . '&' . $_GET['amount'], $payment_data['dwolla_api_secret'] ) ) {
						$transdata[ $proccesid ][ 'meta' ][ 'transaction' ] = strip_tags( $_GET[ 'transaction' ] );
						$transdata[ $proccesid ][ 'meta' ][ 'clearingDate' ] = strip_tags( $_GET[ 'clearingDate' ] );

						return;

					} else {
						$processor_data->add_error( 'Invalid Signature', 'cf-twocheckout' );
					}
				} else {
					$processor_data->add_error( 'Transaction Failed', 'cf-twocheckout' );
				}
			}else{
				$processor_data->add_error( 'Transaction Failed', 'cf-twocheckout' );
			}
		}

		if( ! empty( $_GET['error'] ) && ! empty( $_GET[ 'error_description' ] ) ){
			$processor_data->add_error( urldecode( $_GET['error_description' ] ) );
		}

		//If we have errors, report them and bail
		$errors = $processor_data->get_errors();
		if ( ! empty( $errors  ) ) {
			return $errors;

		}

		return;

	}

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
	//record data for this proccessor for saving
	$transdata[ $proccesid ] = $processor_data->get_values();

	$processor_data = cf_2checkout_process_payment(  $processor_data, $proccesid );


	$errors = $processor_data->get_errors();

	if ( ! empty( $errors  ) ) {
		return $errors;
	}

	if( isset($transdata['twocheckout' ][ $proccesid ][ 'url' ] ) ){
		// set transient expire to longer to allow user a longer login setup etc..
		$transdata['expire'] = 1800; // 30 min should give enough time to register if needed.
		return array(
			'type' => 'redirect',
			'url' => $transdata['twocheckout' ][ $proccesid ][ 'url' ]
		);

	}

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
 * Process payment and prepare the URL to redirect to Dwolla.
 *
 * @since 0.1.0
 *
 * @param Caldera_Forms_Processor_Get_Data $processor_data
 *
 * @return Caldera_Forms_Processor_Get_Data
 */
function cf_2checkout_process_payment( $processor_data, $proccessid ) {
	global $transdata;
	if ( ! isset( $transdata['twocheckout'] ) || ! isset( $transdata['twocheckout'][ 'return_url' ]  ) ) {
		$processor_data->add_error( 'Could not set redirect URL.', 'cf-twocheckout' );
		return $processor_data;
	}

	// Set default 2Checkout URL
	$url = 'https://www.2checkout.com/checkout/purchase';

	$callback = add_query_arg(
		array(
			'cf-twocheckout-payment-confirmation' => '2checkout',
			'cf_tp' => $transdata['transient'], // add in the cf_tp ( Caldera Forms Transient Process - this is a shortcut to reprocess a transient form submission - Document this please! )
			'processid' => $proccessid,
		),
		$transdata['twocheckout'][ 'return_url' ]
	);

	if ( empty( $payment_data['orderId'] ) ) {
		$payment_data['orderId'] = $proccessid;
	}

	$payment_data = $processor_data->get_values();

	var_dump( $payment_data );


	$body = array(
		"sid" 				=> $payment_data['account_number'],
		"mode" 				=> "2CO",
		"li_0_type"			=> "product",
		"li_0_name"			=> $payment_data['item_name'],
		"li_0_quantity"		=> $payment_data['item_quantity'],
		"li_0_price"		=> $payment_data['price'],
		"li_0_product_id"	=> '1',
		"li_0_tangible"		=> 'N',
		"pay_method"		=> 'CC',
		"purchase_step" 	=> 'billing-information',
		"currency_code"		=> $payment_data['currency']
		);

	if( $processor_data->get_value( 'sandbox' ) ){
		$body[ 'test' ] = true;
		$url = 'https://sandbox.2checkout.com/checkout/purchase';
	}else{
		$body[ 'test' ] = false;
	}

	$body = wp_json_encode( $body );

 	$result = wp_remote_request( $url, array(
			'body' => $body,
			'method' => 'POST',
			'headers' => array(
	             'content-type' =>  'application/json',
	             'timeout' => 60
			),
		)
	);

	if ( is_wp_error( $result ) ) {
		$processor_data->add_error( __( sprintf( 'Transaction Failed', 'cf-twocheckout' ) ) );
	}else{

		
		$response_body = wp_remote_retrieve_body( $result );
		$response_body = (array) json_decode( $response_body );
		if (isset( $response_body[ 'Result' ] ) ) {

			if ( 'Failure' == $response_body[ 'Result' ] ) {
				if ( isset( $response_body[ 'Message' ] ) ) {
					$processor_data->add_error( $response_body[ 'Message' ] );
				}else{
					$processor_data->add_error( __( sprintf( 'Transaction Failed', 'cf-twocheckout' ) ) );
				}


			}else {
				if ( isset( $response_body['CheckoutId'] ) ) {
					$url = $url . $response_body['CheckoutId'];

					$transdata['twocheckout' ][ $proccessid ][ 'url' ] = $url;
					$transdata['twocheckout' ][ $proccessid ][ 'CheckoutId' ] = $response_body['CheckoutId'];
					$transdata['twocheckout' ][ $proccessid ][ 'process_object' ] = $processor_data;
				}
			}
		}
	}

	return $processor_data;
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
