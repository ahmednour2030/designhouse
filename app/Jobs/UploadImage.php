<?php

namespace App\Jobs;

use File;
use Image;
use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $design;

    public function __construct(Design $design)
    {
        $this->design = $design;
    }

    public function handle()
    {
        $disk = $this->design->disk;
        $filename =  $this->design->image;
        $original_file = storage_path() . '/uploads/original/'. $filename;

        try {
            // create the Large and save to tmp disk

            Image::make($original_file)->fit(800, 600, function($constraint){
                            $constraint->aspectRatio();
                        })->save($large = storage_path('uploads/large/'. $filename));

            // create the thumbnail image
            Image::make($original_file)
                        ->fit(250, 200, function($constraint){
                            $constraint->aspectRatio();
                        })
                        ->save($thumbnail = storage_path('uploads/thumbnail/'. $filename));

                // store image to permanent disk
                // Original image
                if (Storage::disk($disk)
                          ->put('uploads/designs/original/'.$filename, fopen($original_file, 'r+'))){
                                File::delete($original_file);
                    }

                // Large image
                if (Storage::disk($disk)
                          ->put('uploads/designs/large/'.$filename, fopen($large, 'r+'))){
                            File::delete($large);
                    }

                // thumbnail image
                if (Storage::disk($disk)
                          ->put('uploads/designs/thumbnail/'.$filename, fopen($thumbnail, 'r+'))){
                            File::delete($thumbnail);
                    }

                // Update the database record with success flag
                $this->design->update([
                    'upload_successful' => true
                ]);

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }




    //     // create the Large and save to tmp disk
    //     Image::make($original_file)->resize(800, 600)
    //                     ->save($large = storage_path('uploads/large/'. $filename));


    }
}
