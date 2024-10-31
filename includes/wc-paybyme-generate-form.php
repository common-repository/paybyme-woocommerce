<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PaybyMe_Generate_Form{

    public static function generate_paybyme_form($order_id) {
	 
        global $woocommerce;
        $order = new WC_Order($order_id);			
        $order_items = $order->get_items();
        $longSyncId =self::pbmOrderInsert($order_id);
        $pbmGW=new WC_PaybyMe();
        $pbmApiUsername = $pbmGW->pbmApiUsername;
        $pbmApiToken= $pbmGW->pbmApiToken;
        $ordertotal =  $order->get_total();
        $assetPrice = intval($ordertotal * 100);

        $redirectPage = $order->get_checkout_order_received_url();
        $errorPage = $order->get_cancel_order_url();       
        
        $items = array();

        foreach ($order_items as $key => $item) {
            array_push($items, array(
                'item' => $item['name'],
                'description' => $item['name'],
                'unit_cost' => $item['line_subtotal']/$item['qty'],
                'quantity' => $item['qty']
                ));
        }

        if($order->get_total_shipping() > 0) {
            array_push($items, array(
                'item' => 'Shipping',
                'description' => 'Total Shipping Cost',
                'unit_cost' => $order->get_total_shipping(),
                'quantity' => 1
                ));
        } 
        
        $payment_url = WC_PaybyMe_Get_Payment_Link::get_paybyme_paymentlink($longSyncId, $assetPrice, $redirectPage, $errorPage);

        header('Location: '.$payment_url.'');
        exit;
    }

    public static function pbmOrderInsert($orderId) {
        global $wpdb;	
        $tableName = $wpdb->prefix . 'pbm_preorder';	
        $uuid4 = wp_generate_uuid4();
        $wpdb->insert($tableName, array( 'woocommerceOrderId' => $orderId, 'pbmLongSyncId' => $uuid4 ));

        $lastId = $wpdb->insert_id;

        $pbmLongSyncId = $wpdb->get_var("SELECT pbmLongSyncId FROM $tableName WHERE orderId = $lastId");
                    
        return $pbmLongSyncId;
    }

    public static function pbmGetOrderIdByLongSyncId($longSyncId)
    {
        global $wpdb;	
        $tableName = $wpdb->prefix . 'pbm_preorder';		

        $orderId = $wpdb->get_var("SELECT woocommerceOrderId FROM $tableName WHERE pbmLongSyncId = '$longSyncId' ORDER BY orderId DESC LIMIT 1");
        $this->DebugLog("get_var orderId". $orderId);
        return $orderId;
    }
    
}