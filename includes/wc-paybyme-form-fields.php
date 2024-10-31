<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PaybyMe_Form_Fields{

    public static function adminFormFields(){
        return $form_fields=array(
            'pbmApiUsername' => array(
                'title' 		=> __('pbmApiUsernameTitle', 'woocommerce-paybyme'),
                'type' 			=> 'text',
                'description' 	=> __('pbmApiUsernameDesc', 'woocommerce-paybyme'),
                'desc_tip'      => true
            ),
            'pbmApiToken' => array(
                'title' 		=> __('pbmApiTokenTitle', 'woocommerce-paybyme'),
                'type' 			=> 'text',
                'description' 	=> __('pbmApiTokenDesc', 'woocommerce-paybyme'),
                'desc_tip'      => true
            ),
            'pbmApiSecretKey' => array(
                'title' 		=> __('pbmApiSecretKeyTitle', 'woocommerce-paybyme'),
                'type' 			=> 'text',
                'description' 	=> __('pbmApiSecretKeyDesc', 'woocommerce-paybyme'),
                'desc_tip'      => true
            ),
            'pbmDefaultKeywordId' => array(
                'title' 		=> __('pbmDefaultKeywordIdTitle', 'woocommerce-paybyme'),
                'type' 			=> 'text',
                'description' 	=> __('pbmApiKeywordIdDesc', 'woocommerce-paybyme'),
                'desc_tip'      => true
            ),

            'pbmEnableSecondKeywordId' => array(
                "label"             => __('pbmEnableSecondKeywordIdDesc', 'woocommerce-paybyme'),
                'description' 		=> __('pbmEnableSecondKeywordIdDesc', 'woocommerce-paybyme'),
                'type'        		=> 'checkbox',
                'default'     		=> 'no',
            ),
            'pbmTRYCurrency'        => array(
                "label"             => __('pbmTRY', 'woocommerce-paybyme'),
                'type'        		=> 'checkbox',
                'default'     		=> 'no',
            ),
            'pbmUSDCurrency' => array(
                "label"             => __('pbmUSD', 'woocommerce-paybyme'),
                'type'        		=> 'checkbox',
                'default'     		=> 'no',
            ),
            'pbmEURCurrency' => array(
                "label"             => __('pbmEUR', 'woocommerce-paybyme'),
                'type'        		=> 'checkbox',
                'default'     		=> 'no',
            ),
            'pbmGBPCurrency' => array(
                "label"             => __('pbmGBP', 'woocommerce-paybyme'),
                'type'        		=> 'checkbox',
                'default'     		=> 'no',
            ),
            'pbmRUBCurrency' => array(
                "label"             => __('pbmRUB', 'woocommerce-paybyme'),
                'type'        		=> 'checkbox',
                'default'     		=> 'no',
            ),
            'pbmSecondKeywordId' => array(
                'title' 		=> __('pbmSecondKeywordIdTitle', 'woocommerce-paybyme'),
                'type' 			=> 'text',
                'description' 	=> __('pbmApiKeywordIdDesc', 'woocommerce-paybyme'),
                'desc_tip'      => true
            ),

            'pbmVPOSRequestPage' => array(
                'title' 		=> __('pbmVPOSRequestPageTitle', 'woocommerce-paybyme'),
                'type' 			=> 'text',
                'description' 	=> __('pbmVPOSRequestPageDesc', 'woocommerce-paybyme'),
                'desc_tip'      => true
            ),
            'pbmVPOSPaymentPage' => array(
                'title' 		=> __('pbmVPOSPaymentPageTitle', 'woocommerce-paybyme'),
                'type' 			=> 'text',
                'description' 	=> __('pbmVPOSPaymentPageDesc', 'woocommerce-paybyme'),
                'desc_tip'      => true
            ),				
            'pbmSubCompany' => array(
                'title' 		=> __('pbmSubCompanyTitle', 'woocommerce-paybyme'),
                'type' 			=> 'text',
                'description' 	=> __('pbmSubCompanyDesc', 'woocommerce-paybyme'),
                'desc_tip'      => true
            ),
            'pbmAssetName' => array(
                'title' 		=> __('pbmAssetNameTitle', 'woocommerce-paybyme'),
                'type' 			=> 'text',
                'description' 	=> __('pbmAssetNameDesc', 'woocommerce-paybyme'),
                'desc_tip'      => true
            ),              								
            'pbmEnableDebugLogging' => array(
                'title'       		=> __('pbmEnableDebugLoggingTitle', 'woocommerce-paybyme'),
                'description' 		=> __('pbmEnableDebugLoggingDesc', 'woocommerce-paybyme'),
                'type'        		=> 'checkbox',
                'default'     		=> 'no',
            )
        );
    }
}

