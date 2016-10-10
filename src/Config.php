<?php
namespace TypeRocket;

class Config
{

    static private $paths = null;
    static private $form = Form::class;
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

    static public function getFormProviderClass() {
        self::$form = config('typerocket.form');

        return self::$form;
    }

    /**
     * Get debug status
     *
     * @return bool
     */
    static public function getDebugStatus()
    {
        self::$debug = config('typerocket.debug');

        return self::$debug;
    }

    /**
     * Use Vue JS
     *
     * @return bool
     */
    static public function useVueJs()
    {
        return config('typerocket.vue');
    }

    /**
     * Get Seed
     *
     * @return null|string
     */
    static public function getSeed()
    {
        self::$seed = config('typerocket.seed');

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
            'matrix_folder'  => config('typerocket.matrix_folder'),
            'matrix_api'  => config('typerocket.matrix_api'),
            'urls'    => array(
                'js'  => config('typerocket.css'),
                'css'  => config('typerocket.js'),
            )
        );
    }

}
