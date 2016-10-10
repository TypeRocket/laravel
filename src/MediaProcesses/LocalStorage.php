<?php

namespace TypeRocket\MediaProcesses;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use TypeRocket\MediaProvider;

class LocalStorage implements ImageProcess
{

    public function run( UploadedFile $file, MediaProvider $media )
    {
        $path = config('typerocket.media.uploads', '/uploads/media/') . date('Y') . '/' . date('m');
        $folder = storage_path() . $path;
        $name = preg_replace("/[^a-z0-9\._-]+/i", '-', $file->getClientOriginalName() );
        $stamp = time();
        $unique_name =  $stamp . '-' . $name;

        if ( ! file_exists($folder) ) {
            mkdir($folder, 0755, true);
        }

        if(! file_exists($folder . '/' . $unique_name) ) {
            $media->path = $path;
            $media->file = $unique_name;
            $sizes['local']['thumb'] = $media->path . '/thumb-' . $unique_name;
            $sizes['local']['full'] = $media->path . '/' . $media->file;
            $sizes = array_merge($media->sizes, $sizes);
            $media->sizes = $sizes;
            $file->move( $folder, $media->file);
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