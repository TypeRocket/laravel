<?php
namespace TypeRocket;

interface MediaProvider
{
    public function findById($value);
    public function getThumbsrc($options);
}
