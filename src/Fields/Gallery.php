<?php

namespace TypeRocket\Fields;

use Illuminate\Database\Eloquent\Model;
use \TypeRocket\Html\Generator,
	\TypeRocket\Config,
	\TypeRocket\Assets;
use TypeRocket\MediaProvider;

class Gallery extends Field implements ScriptField
{

	protected $mediaProviderClass;

	/**
	 * Run on construction
	 */
	protected function init()
	{
		$this->setType( 'gallery' );
	}

	public function enqueueScripts() {
		$paths = Config::getPaths();
        $v = Config::getAssetVersion();
		Assets::addToFooter('js', 'typerocket-image', $paths['urls']['js'] . '/image.js?v='.$v);
	}

	/**
	 * Covert Gallery to HTML string
	 */
	public function getString()
	{
		$name                = $this->getNameAttributeString();
		$this->setAttribute('class', 'image-picker');
		$images              = $this->getValue();
		$this->removeAttribute('name');
		$generator = new Generator();

		if (! $this->getSetting( 'button' )) {
			$this->setSetting('button', 'Insert Images');
		}

		$list = '';

		if (is_array( $images )) {
			foreach ($images as $id) {
				$input = $generator->newInput( 'hidden', $name . '[]', $id )->getString();
				$class = $this->mediaProviderClass;
				$img = new $class;

				if( $img instanceof MediaProvider && $img instanceof Model ) {
					$img = $img->find($id);
					$src = $img->getThumbSrc();
					$image = "<img src=\"{$src}\" />";
				} else {
					throw new \Exception('Media field requires an Eloquent Model implementing TypeRocket\MediaProvider');
				}

				$remove = '#remove';

				if ( ! empty( $image )) {
					$list .= $generator->newElement( 'li', array(
						'class' => 'image-picker-placeholder'
					),
						'<a class="glyphicon glyphicon-remove"  title="Remove Image" href="'.$remove.'"></a>' . $image . $input )->getString();
				}

			}
		}

		$this->removeAttribute('id');
		$container = new Generator();
		$html      = $generator->newInput( 'hidden', $name, '0', $this->getAttributes() )->getString();

		$button = $generator->newElement( 'input', array(
			'type'  => 'button',
			'class' => 'gallery-picker-button btn btn-default',
			'value' => $this->getSetting( 'button' )
		) )->getTag();

		$clear = $generator->newElement( 'input', array(
			'type'  => 'button',
			'class' => 'gallery-picker-clear btn btn-default',
			'value' => 'Clear'
		) )->getTag();

		$html .= $container->newElement( 'div',
			array( 'class' => 'btn-group' ) )->appendInside( $button )->appendInside( $clear )->getString();

		$html .= $generator->newElement( 'ul', array(
			'class' => 'tr-gallery-list cf'
		), $list )->getString();

		return $html;
	}

	public function setMediaProviderClass($class) {
		$this->mediaProviderClass = $class;

		return $this;
	}

}