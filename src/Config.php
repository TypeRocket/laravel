<?php
namespace TypeRocket;

class Config
{

    static private $paths = null;
    static private $debug = false;
    static private $seed = null;

    /**
     * Get paths array
     *
     * @return mixed|null|void
     */
    static public function getPaths()
    {

        if (self::$paths === null) {
            self::$paths = self::defaultPaths();
        }

        return self::$paths;
    }

    /**
     * Get debug status
     *
     * @return bool
     */
    static public function getDebugStatus()
    {
        self::$debug = env('TR_DEBUG', false);

        return self::$debug;
    }

    /**
     * Get Seed
     *
     * @return null|string
     */
    static public function getSeed()
    {
        self::$seed = env('TR_SEED', 'replaceThis');

        return self::$seed;
    }

    /**
     * Set default paths
     *
     * @return array
     */
    static private function defaultPaths()
    {
        return array(
            'matrix_folder'  => env('TR_MATRIX_FOLDER_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../matrix'),
            'matrix_api'  => env('TR_MATRIX_API_URL', $_SERVER['DOCUMENT_ROOT'] . '/../matrix'),
            'urls'    => array(
                'js'  => env('TR_JS_URL', '/js'),
                'css'  => env('TR_CSS_URL', '/css'),
            )
        );
    }

}
