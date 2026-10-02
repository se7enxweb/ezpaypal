<?php
/**
 * The code of extension/ezpaypal/modules/paypal/notify_url.php, moved into a class (#207 stage 1). The file extension/ezpaypal/modules/paypal/notify_url.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\View\Extension\Ezpaypal\Paypal
{

class NotifyUrl extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $logger  = \eZPaymentLogger::CreateForAdd('var/log/eZPaypal_notify_url.log');
        $checker = new \eZPaypalChecker( 'paypal.ini' );
        if( $checker->createDataFromPOST() )
        {
            unset ($_POST);

            if( $checker->requestValidation() && $checker->checkPaymentStatus() )
            {
        	$orderID = $checker->getFieldValue( 'custom' );

        	if( $checker->setupOrderAndPaymentObject( $orderID ) )
        	{
        	    $amount   = $checker->getFieldValue( 'mc_gross' );
        	    $currency = $checker->getFieldValue( 'mc_currency' );

        	    if( $checker->checkAmount( $amount ) && $checker->checkCurrency( $currency ) )
        	    {
        		$checker->approvePayment();
        		$checker->updateStatus();
        		
        		$order = \eZOrder::fetch( $orderID );

        		if ( isset( $order ) )
        		{
        		    // Any special processing for the order goes here
        	        }
        	    }
        	}
            }
        }

        $logger->writeTimedString( 'notify_url.php was propertly ended' );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
