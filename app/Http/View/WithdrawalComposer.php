<?php

namespace App\Http\View;

use Illuminate\View\View;
use App\Wishlist;
use App\Product;
use Carbon\Carbon;
use App\SellerWithdrawal;
use Illuminate\Support\Facades\Auth;

class WithdrawalComposer {

    private function getWithdrawalsWithSpecificStatusesCount()
    {
        if (Auth::guard('web')->check()) {
            return SellerWithdrawal::where('status', 'menunggu')->count();
        } else {
            return 0;
        }
    }

    public function compose(View $view)
    {
        $withdrawalsWithSpecificStatusesCount = $this->getWithdrawalsWithSpecificStatusesCount();

        $view->with('withdraw_count', $withdrawalsWithSpecificStatusesCount);
    }

}