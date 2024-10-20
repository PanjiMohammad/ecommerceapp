<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Promo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DeleteExpiredPromos extends Command
{
    protected $signature = 'promos:delete-expired';
    protected $description = 'Hapus produk pada masa promo yang sudah kadaluarsa';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Get the current date and time
        $now = Carbon::now();

        // Find promos that have expired
        $expiredPromos = Promo::where('end_date', '<', $now)->get();

        foreach ($expiredPromos as $promo) {
            // Delete the promo image from storage
            $imagePath = public_path('products/' . $promo->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Delete the promo from the database
            $promo->delete();

            $this->info('Hapus Promo Kadaluarsa: ' . $promo->name);
        }

        $this->info('Promo Kadaluarsa berhasil dihapus.');
    }
}

