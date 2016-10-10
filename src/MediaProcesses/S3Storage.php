<?php
namespace TypeRocket\MediaProcesses;

use TypeRocket\MediaProvider;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class S3Storage
 *
 * Takes all local image sizes and upload them to Amazon S3
 *
 * Run this last in the media process stack
 *
 * @package CoverQuest\MediaProcesses
 */
class S3Storage implements ImageProcess
{

    public function run( UploadedFile $file, MediaProvider $media )
    {
        $folder = '/uploads/media/' . date('Y') . '/' . date('m');

        try {
            $s3 = Storage::disk('s3');
            $local_storage = $media->sizes['local'];
            $sizes['s3'] = [];

            foreach($local_storage as $key => $location) {
                $destination_file = $folder . '/' . basename($location);
                $s3->put($destination_file, fopen($location, 'r+'), 'public');
                $sizes['s3'][$key] =$destination_file;
            }
            $sizes = array_merge($media->sizes, $sizes);

            $media->sizes = $sizes;

        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function down( MediaProvider $media )
    {
        if(!empty($media->sizes['s3'])) {
            $s3 = Storage::disk('s3');
            // TODO: S3 not deleting files
            foreach($media->sizes['s3'] as $location) {
                $s3->delete( $location );
            }
        }
    }
}