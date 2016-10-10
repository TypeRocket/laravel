<?php

namespace TypeRocket\MediaProcesses;

use Eventviva\ImageResize;
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
            $sizes['local']['full'] = $media->path . '/' . $media->file;
            $sizes = array_merge($media->sizes, $sizes);
            $file->move( $folder, $media->file);
            $thumb = $this->makeThumb( storage_path() . $sizes['local']['full'], $media);
            $sizes['local']['thumb'] = $media->path . '/' . $thumb;
            $media->sizes = $sizes;
        } else {
            dd('file exists');
        }
    }

    public function down( MediaProvider $media )
    {
        if( !empty($media->sizes['local']) ) {
            foreach($media->sizes['local'] as $location) {
                if(file_exists(storage_path() . $location)) {
                    unlink(storage_path() .  $location);
                }
            }
        }
    }

    protected function makeThumb( $file, MediaProvider $media ) {
        $name = 'thumb-' . $media->file;
        $new = $media->path . '/' . $name;

        $image = new ImageResize($file);
        $image->resizeToBestFit(150, 150);
        $image->save( storage_path() . $new );

        return $name;
    }
}