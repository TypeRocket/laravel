<?php

namespace TypeRocket\MediaProcesses;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use TypeRocket\MediaProvider;

class LocalStorage implements ImageProcess
{

    public function run( UploadedFile $file, MediaProvider $media )
    {
        $folder = storage_path() . '/uploads/media/' . date('Y') . '/' . date('m');
        $name = preg_replace("/[^a-z0-9\._-]+/i", '-', $file->getClientOriginalName() );
        $unique_name = time() . '-' . $name;

        if ( ! file_exists($folder) ) {
            mkdir($folder, 0755, true);
        }

        if(! file_exists($folder . '/' . $unique_name) ) {
            $media->path = $folder;
            $media->file = $unique_name;
            $sizes['local']['full'] = $media->path . '/' . $media->file;
            $sizes = array_merge($media->sizes, $sizes);
            $media->sizes = $sizes;
            $file->move( $media->path, $media->file);
        } else {
            dd('file exists');
        }
    }

    public function down( MediaProvider $media )
    {
        if( !empty($media->sizes['local']) ) {
            foreach($media->sizes['local'] as $location) {
                if(file_exists($location)) {
                    unlink($location);
                }
            }
        }
    }
}