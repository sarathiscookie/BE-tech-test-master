<?php

namespace App\Services\Subscription;

use App\Entities\Subscription;
use App\Entities\ScheduledOrder;

use Carbon\Carbon;
class GetScheduledOrders
{
    /**
     * Handle generating the array of scheduled orders for the given number of weeks and subscription.
     *
     * @param \App\Entities\Subscription $subscription
     * @param int                        $forNumberOfWeeks
     *
     * @return array
     */
    public function handle(Subscription $subscription, $forNumberOfWeeks = 6)
    {
        // Array of Scheduled Orders
        $shouldBeInterval = true;

        $scheduledOrders = [];

        for( $i = 0; $i < $forNumberOfWeeks; $i++ )
        {
			if( $subscription->getStatus() === 'Cancelled' ) { 
				continue;
            }
            
			if( $subscription->getPlan() === 'Weekly' ) { 	
                $scheduledOrder = new ScheduledOrder(new Carbon($subscription->getNextDeliveryDate()), true);
                
				$scheduledOrders[] = $scheduledOrder;				
            }
			elseif( $subscription->getPlan() === 'Fortnightly' ) {				
                $scheduledOrder = new ScheduledOrder(new Carbon($subscription->getNextDeliveryDate()), $shouldBeInterval);
                
                $scheduledOrders[] = $scheduledOrder;
                
				$shouldBeInterval = !$shouldBeInterval;
            }		
			
            $deliverydate = $subscription->getNextDeliveryDate();
            
            $date = (new Carbon($deliverydate))->addWeek();
            
			$subscription->setNextDeliveryDate($date);
			
        }
        
		return $scheduledOrders;
    }
}