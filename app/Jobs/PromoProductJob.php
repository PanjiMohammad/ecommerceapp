<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Imports\PromoProductImport; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Product;
use App\Promo;
use File;

class PromoProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $category;
    protected $filename;
    protected $inputStartDate;
    protected $inputEndDate;

  	//KARENA DISPATCH MENGIRIMKAN 2 PARAMETER, MAKA KITA TERIMA KEDUA DATA TERSEBUT
    public function __construct($category, $filename, $inputStartDate, $inputEndDate)
    {
        $this->category = $category;
        $this->filename = $filename;
        $this->inputStartDate = $inputStartDate;
        $this->inputEndDate = $inputEndDate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Import data from the Excel file stored in the uploads directory
        $files = (new PromoProductImport)->toArray(public_path('/uploads/' . $this->filename));

        // Log the imported data for debugging
        Log::info('Imported Data:', $files);

        // Ensure the products directory exists
        $directory = public_path('/products/');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        // Process each sheet in the imported Excel file
        foreach ($files as $sheet) {
            foreach ($sheet as $index => $row) {
                // Log each row for debugging
                Log::info("Processing row $index:", $row);

                // Ensure the row has the expected number of columns
                if (count($row) < 6) {
                    Log::error("Row $index does not have enough columns: ", $row);
                    continue;
                }

                // Extract the filename and extension from the URL
                $explodeURL = explode('/', $row[4]);
                $explodeExtension = explode('.', end($explodeURL));
                $filename = time() . Str::random(6) . '.' . end($explodeExtension);
                $type = 'promo';

                // Validate and download the image
                $imageUrl = $row[4];
                if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    $imageContent = @file_get_contents($imageUrl);
                    if ($imageContent !== false) {
                        file_put_contents($directory . '/' . $filename, $imageContent);

                        // Create the promo product record
                        try {
                            Promo::create([
                                'name' => $row[0],
                                'slug' => Str::slug($row[0]),
                                'category_id' => $this->category,
                                'seller_id' => auth()->guard('seller')->user()->id,
                                'description' => $row[1],
                                'price' => $row[2],
                                'weight' => $row[3],
                                'image' => $filename,
                                'stock' => $row[5],
                                'status' => true,
                                'type' => $type,
                                'start_date' => $this->inputStartDate,
                                'end_date' => $this->inputEndDate,
                            ]);
                        } catch (\Exception $e) {
                            Log::error("Failed to create product record for row $index: " . $e->getMessage());
                        }
                    } else {
                        // Handle the error (e.g., log it or notify the user)
                        Log::error("Failed to download image from URL: $imageUrl");
                    }
                } else {
                    // Handle invalid URL
                    Log::error("Invalid URL: $imageUrl");
                }
            }
        }

        // Delete the uploaded file after processing
        File::delete(public_path('/uploads/' . $this->filename));
    }
}
