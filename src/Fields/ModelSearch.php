<?php

namespace TypeRocket\Fields;

use TypeRocket\Assets;
use TypeRocket\Config;
use TypeRocket\Html\Generator;

class ModelSearch extends Field
{
    public $searchModel;
    public $searchAPI;
    public $searchTitle = 'title';
    public $searchId = 'id';

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
                'data-api' => $this->searchAPI,
                'data-column' => $this->searchTitle,
                'data-id' => $this->searchId,
            ], 'Get Content' );
        $input->newInput( 'hidden', $name, $value, $this->getAttributes() );

        $content = $search->getString() . $input->getString();
        $title_text = '';

        if($value) {
            $node = $this->searchModel::where( $this->searchId ,$value)->first();
            $title_text = "Content was deleted";

            if(!empty($node)) {
                $title_text = $node->{$this->searchTitle} . ' (Currently Saved)';
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

    /**
     * Set search title column
     *
     * @param $column
     *
     * @return $this
     */
    public function setSearchTitleColumn( $column )
    {
        $this->searchTitle = $column;

        return $this;
    }

    public function setSearchIdColumn($id)
    {
        $this->searchId = $id;

        return $this;
    }
}