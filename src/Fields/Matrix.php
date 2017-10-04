<?php
namespace TypeRocket\Fields;

use TypeRocket\Assets;
use TypeRocket\Html\Generator,
    TypeRocket\Config,
    TypeRocket\Buffer,
    \TypeRocket\Sanitize;

class Matrix extends Field implements OptionField, ScriptField {

    protected $mxid = null;
    protected $options = null;
    protected $paths = null;
    protected $componentFolder = null;

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->mxid = md5( microtime( true ) ); // set id for matrix random
        $this->setType( 'matrix' );
    }

    public function enqueueScripts() {
        $paths = $this->paths = Config::getPaths();
        if( Config::useVueJs() ) {
            Assets::addToFooter('js', 'typerocket-vue', $paths['urls']['js'] . '/vue.min.js');
        }
        Assets::addToFooter('js', 'typerocket-booyah', $paths['urls']['js'] . '/booyah.js');
        Assets::addToFooter('js', 'typerocket-image', $paths['urls']['js'] . '/image.js');
        Assets::addToFooter('js', 'typerocket-matrix-core', $paths['urls']['js'] . '/matrix.js');
        Assets::addToFooter('js', 'typerocket-items-list', $paths['urls']['js'] . '/items.js');
    }

    /**
     * Covert Matrix to HTML string
     */
    public function getString()
    {
        $this->setAttribute('name', $this->getNameAttributeString());

        // setup select list of files
        $select = $this->getSelectHtml();
        $name = $folder = $this->getComponentFolder();
        $settings = $this->getSettings();
        $blocks = $this->getMatrixBlocks();

        // add controls
        if (isset( $settings['help'] )) {
            $help = "<div class=\"help\"> <p>{$settings['help']}</p> </div>";
            $this->removeSetting('help');
        } else {
            $help = '';
        }

        $generator = new Generator();
        $default_null = $generator->newInput('hidden', $this->getAttribute('name'), null)->getString();

        // add it all
        $html = "
<div class='tr-matrix control-section tr-repeater'>
<div class='matrix-controls controls'>
{$select}
<div class=\"tr-repeater-button-add\">
<input type=\"button\" value=\"Add New\" data-id='{$this->mxid}' data-folder='{$name}' class=\"btn btn-default matrix-button\">
</div>
<div class=\"btn-group\">
<input type=\"button\" value=\"Flip\" class=\"flip btn btn-default\">
<input type=\"button\" value=\"Contract\" class=\"tr_action_collapse btn btn-default\">
<input type=\"button\" value=\"Clear All\" class=\"clear btn btn-default\">
</div>
{$help}
</div>
<div>{$default_null}</div>
<div id=\"{$this->mxid}\" class='matrix-fields tr-repeater-fields ui-sortable'>{$blocks}</div></div>";

        return $html;
    }

    protected function cleanFileName( $name )
    {

        $name = Sanitize::underscore($name);
        $name = str_replace( '-', ' ', $name );

        return ucwords( $name );
    }

    /**
     * Get component folder
     *
     * @return null|string
     */
    public function getComponentFolder() {
        if( ! $this->componentFolder ) {
            $this->componentFolder = $this->getName();
        }
        return $this->componentFolder;
    }

    protected function getSelectHtml()
    {

        $name = $this->getName();
        $options = $this->getOptions();
        $options = $options ? $options : $this->setOptionsFromFolder()->getOptions();

        if ($options) {
            $generator = new Generator();
            $generator->newElement( 'select', array(
                'data-mxid' => $this->mxid,
                'class' => "matrix-select-{$name}",
                'data-group' => $this->getForm()->getGroup()
            ) );
            $default = $this->getSetting('default');

            foreach ($options as $name => $value) {

                $attr['value'] = $value;
                if ($default === $value) {
                    $attr['selected'] = 'selected';
                } else {
                    unset( $attr['selected'] );
                }

                $generator->appendInside( 'option', $attr, $name );
            }

            $select = $generator->getString();

        } else {

            $paths = Config::getPaths();
            $dir = $paths['matrix_folder'] . '/' . $name;

            $select = "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> Add a files for Matrix <code>{$dir}</code> and add your matrix files to it.</div>";
        }

        return $select;

    }

    public function setOptionsFromFolder() {
        $paths = Config::getPaths();
        $name = $this->getName();
        $dir = $paths['matrix_folder'] . '/' . $name;

        if (file_exists( $dir )) {

            $files = preg_grep( '/^([^.])/', scandir( $dir ) );

            foreach ($files as $file) {
                if (file_exists( $dir . '/' . $file )) {
                    $the_file = $file;
                    $path = pathinfo( $file );
                    $key = $this->cleanFileName( $path['filename'] );

                    $line = fgets(fopen( $dir . '/' . $the_file, 'r'));
                    if( preg_match("/<[h|H]\\d>(.*)<\\/[h|H]\\d>/U", $line, $matches) ) {
                        $key = $matches[1];
                    }

                    $this->options[$key] = $path['filename'];
                }
            }

        }

        return $this;
    }

    protected function getMatrixBlocks()
    {

        $val = $this->getValue();
        $utility = new Buffer();
        $blocks = '';
        $form = $this->getForm();
        $paths = Config::getPaths();

        if (is_array( $val )) {

            $utility->startBuffer();

            foreach ($val as $tr_matrix_key => $data) {
                foreach ($data as $tr_matrix_type => $fields) {

                    $tr_matrix_group = $this->getName();
                    $tr_matrix_type  = lcfirst( $tr_matrix_type );
                    $root_group        = $form->getGroup();
                    $form->setDebugStatus(false);

                    $form->setGroup($root_group . "[{$tr_matrix_group}][{$tr_matrix_key}][{$tr_matrix_type}]");
                    $file        = $paths['matrix_folder'] . "/" . $this->getName() . "/{$tr_matrix_type}.php";
                    $classes = "matrix-field-group tr-repeater-group matrix-type-{$tr_matrix_type} matrix-group-{$tr_matrix_group}";
                    $remove = '#remove';
                    ?>
                    <div class="<?php echo $classes; ?>">
                        <div class="repeater-controls">
                            <div class="collapse glyphicon glyphicon-chevron-down"></div>
                            <div class="move glyphicon glyphicon-menu-hamburger"></div>
                            <a href="<?php echo $remove; ?>" class="remove glyphicon glyphicon-remove" title="remove"></a>
                        </div>
                        <div class="repeater-inputs">
                            <?php
                            if (file_exists( $file )) {
                                /** @noinspection PhpIncludeInspection */
                                include( $file );
                            } else {
                                echo "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> No Matrix file found <code>{$file}</code></div>";
                            }
                            ?>
                        </div>
                    </div>
                    <?php

                    $form->setGroup($root_group);
                    $form->setCurrentField($this);

                }
            }

            $utility->indexBuffer('fields');

            $blocks = $utility->getBuffer('fields');
            $utility->cleanBuffer();

        }

        return trim($blocks);

    }

    public function setOption( $key, $value )
    {
        $this->options[ $key ] = $value;

        return $this;
    }

    public function setOptions( $options )
    {
        $this->options = $options;

        return $this;
    }

    public function getOption( $key, $default = null )
    {
        if ( ! array_key_exists( $key, $this->options ) ) {
            return $default;
        }

        return $this->options[ $key ];
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function removeOption( $key )
    {
        if ( array_key_exists( $key, $this->options ) ) {
            unset( $this->options[ $key ] );
        }

        return $this;
    }

}
