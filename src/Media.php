<?php

namespace TypeRocket;

use Illuminate\Database\Eloquent\Model;

class Media extends Model implements MediaProvider
{
    protected $fillable = ['sizes', 'meta'];

    protected $casts = [
        'sizes' => 'array',
        'meta' => 'array',
    ];

    public function getThumbSrc()
    {
        return '/' . $this->sizes['full'];
    }
}