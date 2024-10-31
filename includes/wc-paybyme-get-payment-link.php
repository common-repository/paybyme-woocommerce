<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PaybyMe_Get_Payment_Link{

	public static function get_paybyme_paymentlink($longSyncId, $assetPrice, $redirectPage, $errorPage)
	{
		global $woocommerce;
		$pbmGW=new WC_PaybyMe();
		$request_url	= str_replace(' ','',trim($pbmGW->pbmVPOSRequestPage));
		$pbmApiUsername = str_replace(' ','',trim($pbmGW->pbmApiUsername));
		$pbmApiToken = str_replace(' ','',trim($pbmGW->pbmApiToken));
		$pbmApiSecretKey = str_replace(' ','',trim($pbmGW->pbmApiSecretKey));
		$keywordId = $pbmGW->pbmDefaultKeywordId;
		$pbmEnableSecondKeywordId = $pbmGW->pbmEnableSecondKeywordId;
		$assetName = $pbmGW->pbmAssetName;
		$subCompany = $pbmGW->pbmSubCompany;        
		$countryCode = 'TR';
		$currencyCode = 'TRY';
		$languageCode = 'tr';
		
		if(get_woocommerce_currency() !== false){
			$currencyCode = get_woocommerce_currency();
			if($pbmEnableSecondKeywordId=="yes"){
				if ($pbmGW->pbmTRYCurrency=="yes" && $currencyCode=="TRY" && !empty($pbmGW->pbmSecondKeywordId)) {
					$keywordId=$pbmGW->pbmSecondKeywordId;
				}elseif ($pbmGW->pbmUSDCurrency=="yes" && $currencyCode=="USD" && !empty($pbmGW->pbmSecondKeywordId)) {
					$keywordId=$pbmGW->pbmSecondKeywordId;
				}elseif ($pbmGW->pbmEURCurrency=="yes" && $currencyCode=="EUR" && !empty($pbmGW->pbmSecondKeywordId)) {
					$keywordId=$pbmGW->pbmSecondKeywordId;
				}elseif ($pbmGW->pbmGBPCurrency=="yes" && $currencyCode=="GBP" && !empty($pbmGW->pbmSecondKeywordId)) {
					$keywordId=$pbmGW->pbmSecondKeywordId;
				}elseif ($pbmGW->pbmRUBCurrency=="yes" && $currencyCode=="RUB" && !empty($pbmGW->pbmSecondKeywordId)) {
					$keywordId=$pbmGW->pbmSecondKeywordId;
				}
			}
		}
		if(get_locale() !== false)
		{
			$languageCode = substr(get_locale(), 0, 2);
			$countryCode =  substr(get_locale(), 3, 2);
		}

		$notifyPage = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https":"http")."://".$_SERVER['HTTP_HOST']."/?wc-api=paybyme";
		
		
		$data = array('username' => $pbmApiUsername, 
						'token' => $pbmApiToken,
						'keywordId' => $keywordId,
						'assetName' => $assetName,
						'subCompany' => $subCompany,
						'longSyncId' => $longSyncId,
						'assetPrice' => $assetPrice,
						'clientIp' => self::getRealIpAddr(),
						'countryCode' => $countryCode,
						'currencyCode' => $currencyCode,
						'languageCode' => $languageCode,
						'notifyPage' => $notifyPage,
						'redirectPage' => $redirectPage,
						'errorPage' => $errorPage
						);            
		
		$pbmGW->DebugLog(print_r($data, true));
		$options = array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'body' => http_build_query($data)
				);

		$result = wp_remote_post($request_url, $options);
		if (isset($result['body'])&&!is_wp_error($result)) {
			parse_str($result['body'], $output);
			$paymenturl = $pbmGW->pbmVPOSPaymentPage.'?hash='.$output['ErrorDesc']; 
			return $paymenturl;
		}else {
			$pbmGW->DebugLog($result->get_error_message());
			return null;
		}	
	}

	public static function getRealIpAddr()
	{
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
			if (array_key_exists($key, $_SERVER) === true){
				foreach (explode(',', $_SERVER[$key]) as $ip){
					$ip = trim($ip); // just to be safe
	
					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
						return $ip;
					}
				}
			}
		}
		return $ip;
	}
}