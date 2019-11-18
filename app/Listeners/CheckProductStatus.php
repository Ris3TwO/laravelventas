<?php

namespace App\Listeners;

use App\Product;
use App\Events\ProductWasSelled;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckProductStatus
{
    /**
     * Handle the event.
     *
     * @param  ProductWasSelled  $event
     * @return void
     */
    public function handle(ProductWasSelled $event)
    {
        if ($event->product->quantity == 0 && $event->product->status == 'available') {
            $event->product->status = Product::PRODUCT_NOT_AVAILABLE;

            $event->product->save();
        }
    }
}
