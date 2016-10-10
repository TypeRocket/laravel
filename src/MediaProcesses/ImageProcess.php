<?php

namespace TypeRocket\MediaProcesses;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use TypeRocket\MediaProvider;

interface ImageProcess
{
    public function run(UploadedFile $file, MediaProvider $media);
    public function down(MediaProvider $media);
}