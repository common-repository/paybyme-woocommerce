<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_PaybyMe extends WC_Payment_Gateway {

    public function __construct() {
        $this->id 					= 'paybyme_gateway';
        $this->icon 				= apply_filters( 'woocommerce_paybyme_icon', plugins_url('../assets/images/visa-mastercard.png' , __FILE__ ));
        $this->method_title     	= __('title', 'woocommerce-paybyme');
        $this->method_description 	= __('methodDesc', 'woocommerce-paybyme');
        $this->has_fields 			= true;
        $this->order_button_text    = __('orderButtonText', 'woocommerce-paybyme');

        $this->init_form_fields();
        $this->init_settings();
        
        $this->title                    = __('title', 'woocommerce-paybyme');
        $this->description              = __('orderDesc', 'woocommerce-paybyme');
        $this->pbmApiUsername  		    = $this->get_option( 'pbmApiUsername' );
        $this->pbmApiToken  		    = $this->get_option( 'pbmApiToken' );
        $this->pbmApiSecretKey  	    = $this->get_option( 'pbmApiSecretKey' );
        $this->pbmVPOSRequestPage       = $this->get_option( 'pbmVPOSRequestPage' );
        $this->pbmVPOSPaymentPage       = $this->get_option( 'pbmVPOSPaymentPage' );	
        $this->pbmDefaultKeywordId  	= $this->get_option( 'pbmDefaultKeywordId' );     
        $this->pbmEnableSecondKeywordId = $this->get_option( 'pbmEnableSecondKeywordId' );
        $this->pbmSecondKeywordId       = $this->get_option( 'pbmSecondKeywordId' );
        $this->pbmTRYCurrency           = $this->get_option( 'pbmTRYCurrency' );
        $this->pbmUSDCurrency           = $this->get_option( 'pbmUSDCurrency' );
        $this->pbmEURCurrency           = $this->get_option( 'pbmEURCurrency' );
        $this->pbmGBPCurrency           = $this->get_option( 'pbmGBPCurrency' );
        $this->pbmRUBCurrency           = $this->get_option( 'pbmRUBCurrency' );
        $this->pbmSubCompany  	        = $this->get_option( 'pbmSubCompany' );
        $this->pbmAssetName  	        = $this->get_option( 'pbmAssetName' );
        $this->pbmEnableDebugLogging    = $this->get_option( 'pbmEnableDebugLogging' ) === 'yes' ? true : false;
        
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );					
        add_action('woocommerce_receipt_paybyme_gateway', array($this, 'receipt_page'));			
        add_action('woocommerce_api_paybyme', array($this, 'callback_handler'));

        if ( ! $this->currency_is_valid() ) {
            $this->enabled = false;
        }

        $this->DebugLog( print_r($_REQUEST, true));		
    }

    public function init_form_fields() {
        
        $this->form_fields = WC_PaybyMe_Form_Fields::adminFormFields();

    }

    public function receipt_page($order) {
        echo '<p>'.__('Thank you for your order, please click the button below to pay with your Credit/Debit Card.', 'PayByMe').'</p>';
        echo $this->generate_form = WC_PaybyMe_Generate_Form::generate_paybyme_form($order);
    }

    public function currency_is_valid() {
        return true;
    }
    
    public function admin_options() {

        echo '<img src="'.plugins_url('../assets/images/paybyme.png' , __FILE__ ).'"style="max-width:150px;"></img>';
        echo '<p>'.__('adminPageText', 'woocommerce-paybyme').'</p>';

        if ( $this->currency_is_valid() ) {

            echo '<table class="form-table">';
            $this->generate_settings_html();
            echo '</table>';

        }
    }

    public function EnableDebugLogging(){
        global $woocommerce;					
        return $this->pbmEnableDebugLogging;
    }

    public function DebugLog($logData)	{
        if($this->EnableDebugLogging()){
            $now = DateTime::createFromFormat('U.u', microtime(true));
		    $now=$now->format("m-d-Y H:i:s.u");
            $fp = fopen('pbmDebugLog.log', 'a');
            fwrite($fp, $now." - ".$logData);
            fwrite($fp, PHP_EOL);
            fclose($fp);
        }
    }
	
    public static function callback_handler() {	
        global $woocommerce;
        $this->DebugLog(print_r($_POST, true));
		if(!$this->checkIpAddr()){
            $this->log->write('PAYBYME :: Invaild Ip Address: '.gethostbyname(gethostname()));
        }else{
            if(!isset($_POST['secretKey']) || $_POST["secretKey"] == $this->pbmApiSecretKey){
                $longSyncId = wp_filter_post_kses($_POST["longSyncId"]);
                $orderId = $this->pbmGetOrderIdByLongSyncId($longSyncId);
                $this->DebugLog("orderId : ".$orderId);
				$order = wc_get_order($orderId);
				$orderTotal = intval($order->get_total() * 100);
				if ($orderTotal == intval($_POST["price"])
					&& $_POST["errorCode"] == 1000 
					&& $_POST["errorDesc"] == "Approved"){
						if ($order->get_status() ==  "pending" ) {
							$order->payment_complete();
							try {
                                ob_start("callback");
                            } catch (Exception $e) {
                                $this->DebugLog('PAYBYME :: '.$e->getMessage());
                            }
						}
						else if ($order->get_status() ==  "completed" ){
							try {
                                ob_start("callback");
                            } catch (Exception $e) {
                                $this->DebugLog('PAYBYME :: '.$e->getMessage());
                            }
					    }
				    }
			}else{
                $this->DebugLog('PAYBYME :: Invalid secret key.');
            }
			wp_die();
		}	
    }
	
	public function checkIpAddr()
	{
		if (strcmp( gethostbyname(gethostname()), "31.145.70.123" )!=0
        ||strcmp( gethostbyname(gethostname()), "31.145.70.124" )!=0
        ||strcmp( gethostbyname(gethostname()), "31.145.70.125" )!=0
        ||strcmp( gethostbyname(gethostname()), "31.145.70.126" )!=0
        ||strcmp( gethostbyname(gethostname()), "31.145.71.123" )!=0
        ||strcmp( gethostbyname(gethostname()), "31.145.71.124" )!=0
        ||strcmp( gethostbyname(gethostname()), "31.145.71.125" )!=0
        ||strcmp( gethostbyname(gethostname()), "31.145.71.126" )!=0
        ||strcmp( gethostbyname(gethostname()), "176.236.74.26" )!=0)
        {
			return true;
		}else{
            return false;
        }
    }

    public function process_payment($order_id) {
        $order = new WC_Order($order_id);

        if (version_compare('2.7', WOOCOMMERCE_VERSION, '>')) {
            return array(
                'result' => 'success',
                'redirect' => add_query_arg(
                    'key',
                    $order->order_key,
                    add_query_arg(
                        'order',
                        $order->id,
                        $order->get_checkout_payment_url(true)
                    )
                )
            );
        } else {
            return array(
                'result' => 'success',
                'redirect' => add_query_arg(
                    'key',
                    $order->get_order_key(),
                    $order->get_checkout_payment_url(true)
                )
            );
        }
    }
    
    public function pbmGetOrderIdByLongSyncId($longSyncId)
    {
        global $wpdb;	
        $tableName = $wpdb->prefix . 'pbm_preorder';		

        $orderId = $wpdb->get_var("SELECT woocommerceOrderId FROM $tableName WHERE pbmLongSyncId = '$longSyncId' ORDER BY orderId DESC LIMIT 1");
        $this->DebugLog("get_var orderId". $orderId);
        return $orderId;
    }
}

function callback($buffer) {
    $buffer = "OK";
    header( 'HTTP/1.1 200 OK' );
    return $buffer;
}