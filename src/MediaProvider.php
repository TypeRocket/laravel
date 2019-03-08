<?php
namespace TypeRocket;

interface MediaProvider
{
    public function getThumbSrc();
    public function getFullSrc();
    public function getEditorSrc();
    public function isImage();
    public function toArray();
    public function getCaption();
}
