<?php
namespace TypeRocket;

class Assets {

    public static $footer = [];
    public static $head = [];

    public static function addToHead( $type, $id, $path )
    {
        self::$head[$id] = [$type, $path];
    }

    public static function addToFooter( $type, $id, $path )
    {
        self::$footer[$id] = [$type, $path];
    }
	
    public static function removeFromFooter( $id )
    {
        unset(self::$footer[$id]);
    }
	
    public static function removeFromHead( $id )
    {
        unset(self::$footer[$id]);
    }

    protected static function buildTags($tags) {

        $html = '';

        foreach($tags as $tag) {

            switch($tag[0]) {
                case 'js':
                    $html .= "<script src='{$tag[1]}'></script>";
                    break;
                default :
                    $html .= "<link rel='stylesheet' href='{$tag[1]}' />";
                    break;
            }

        }

        return $html;
    }

    public static function getFooterString()
    {
	    $paths = Config::getPaths();
	    Assets::addToFooter('js', 'typerocket-core', $paths['urls']['js'] . '/typerocket.js');

        return self::buildTags(self::$footer);
    }

    public static function getHeadString()
    {
        return self::buildTags(self::$head);
    }

}
