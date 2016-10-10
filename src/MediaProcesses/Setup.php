<?php

namespace TypeRocket\MediaProcesses;

use TypeRocket\MediaProvider;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Setup implements ImageProcess
{

    public function run( UploadedFile $file, MediaProvider $media )
    {
        $media->ext = $file->getClientOriginalExtension();
        $media->path = $file->getPath(); // by default /tmp
        $media->file = $file->getClientOriginalName();
        $media->meta = empty($media->meta) ? [] : $media->meta;
        $media->sizes = empty($media->sizes) ? [] : $media->sizes;
        $media->caption = $file->getClientOriginalName();
        $media->alt = $file->getClientOriginalName();
    }

    public function down( MediaProvider $media )
    {
        // nothing to undo
    }
}