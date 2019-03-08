<?php

namespace TypeRocket;

use Illuminate\Database\Eloquent\Model;

class TypeRocketMedia extends Model implements MediaProvider
{
    protected $table = 'tr_media';
    protected $fillable = ['sizes', 'meta'];

    protected $casts = [
        'sizes' => 'array',
        'meta' => 'array',
    ];

	public function getThumbSrc($suffix = '')
	{
		if(empty($this->sizes['local']['thumb'])) {
			return $this->sizes['local']['full'] . $suffix;
		}

		return $this->sizes['local']['thumb'] . $suffix;
	}

	public function getFullSrc($suffix = '') {
		if(empty($this->sizes['local']['full'])) {
			return $this->sizes['local']['thumb'] . $suffix;
		}

		return $this->sizes['local']['full'] . $suffix;
	}

	public function getEditorSrc($suffix = '') {
		return $this->getFullSrc($suffix);
	}

	public function toArray()
	{
		$array = parent::toArray();
		$array['thumbnail_image'] = '';
		if($this->isImage()) {
			$array['thumbnail_image'] = $this->getThumbSrc();
			$array['thumbnail_image_editor'] = $this->getEditorSrc();
		}
		return $array;
	}

	public function isImage() {
		return in_array(strtolower($this->ext), ['jpg', 'png', 'jpeg', 'gif']);
	}

    public function getCaption()
    {
        return $this->caption;
	}
}
