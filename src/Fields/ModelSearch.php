<?php

namespace TypeRocket\Fields;

use TypeRocket\Assets;
use TypeRocket\Config;
use TypeRocket\Html\Generator;

class ModelSearch extends Field
{
    public $searchModel;
    public $searchAPI;

    /**
     * Run on construction
     */
    protected function init() {
        $this->setType( 'modelsearch' );
    }

    public function enqueueScripts() {
        $paths = Config::getPaths();
        if( Config::useVueJs() ) {
            Assets::addToFooter('js', 'typerocket-vue', $paths['urls']['js'] . '/vue.min.js');
        }
    }

    /**
     * Covert Test to HTML string
     */
    public function getString() {
        $input = new Generator();
        $search = new Generator();
        $name  = $this->getNameAttributeString();
        $value = $this->getValue();
        $search->newElement('b', [
                'class' => 'btn btn-default model-search-vue',
                'data-api' => $this->searchAPI
            ], 'Get Content' );
        $input->newInput( 'hidden', $name, $value, $this->getAttributes() );

        $content = $search->getString() . $input->getString();
        $title_text = '';

        if($value) {
            $node = $this->searchModel::where('id',$value)->first();
            $title_text = "Content was deleted";

            if(!empty($node)) {
                $title_text = $node->title . ' (Currently Saved)';
            }
        }

        $title = new Generator();
        $title->newElement( 'p', ['class' => 'node-title'], $title_text);
        $content .= $title->getString();

        return $content;
    }

    /**
     * Set Model class to Search
     *
     * @param $model
     *
     * @return $this
     */
    public function setSearchModel($model)
    {
        $model = '\\' . $model;
        $this->searchModel = $model;

        return $this;
    }

    /**
     * Set Search API
     *
     * This should be a URL for example: /nodes/search?q=
     *
     * @param $model
     */
    public function setSearchAPI($url)
    {
        $this->searchAPI = $url;

        return $this;
    }
}