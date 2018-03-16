<?php
namespace TypeRocket;

use \TypeRocket\Html\Generator,
    \TypeRocket\Html\Tag,
    \TypeRocket\Fields\Field,
    \Illuminate\Http\Request,
    \Illuminate\Database\Eloquent\Model;

class Form
{

    protected $resource = null;
    protected $action = null;
    protected $itemId = null;
    protected $path = null;

    /** @var \Illuminate\Database\Eloquent\Model $model */
    protected $model = null;
    protected $request = null;

    /** @var \TypeRocket\Fields\Field $currentField */
    protected $currentField = '';

    protected $populate = true;
    protected $group = null;
    protected $sub = null;
    protected $debugStatus = null;
    protected $settings = array();

    /**
     * Instance the From
     *
     * @param string $model the eloquent model
     * @param string $action update or create
     * @param null|int $itemId you can set this to null or an integer
     * @param string $path submit the form to this path
     */
    public function __construct( $model, $action = 'update', $itemId = null, $path = null )
    {
        $paths = Config::getPaths();
        Assets::addToFooter('js', 'typerocket-core', $paths['urls']['js'] . '/typerocket.js');
        Assets::addToHead('js', 'typerocket-global', $paths['urls']['js'] . '/global.js');

        $this->resource = $model;
        $this->action = $action;
        $this->itemId = $itemId;
        $this->path = $path;
	    
	if( $model instanceof Model ) {
            $this->model = $model;
            return;
        }

        if( ! class_exists($model) ) {
            $model = ucfirst($this->resource);
            $domain = env('TR_DOMAIN', 'App');
            $model = "\\$domain\\{$model}";
        }

        if(class_exists($model) && $this->itemId ) {
            $this->model = call_user_func( "{$model}::find", $this->itemId );
        } elseif(class_exists($model)) {
            $this->model = new $model();
        }
    }

    /**
     * Set Request
     *
     * @param Request $request
     */
    public function setRequest( Request $request )
    {
        $this->request = $request;
    }

    /**
     * Get Request
     *
     * @return null|Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Get controller
     *
     * @return null|string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set Action
     *
     * @return null|string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get Item ID
     *
     * @return null|string
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Get Item ID
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set Group into bracket syntax
     *
     * @param $group
     *
     * @return $this
     */
    public function setGroup( $group )
    {
        $this->group = null;

        if (Validate::bracket( $group )) {
            $this->group = $group;
        } elseif (is_string( $group )) {
            $this->group = "[{$group}]";
        }

        return $this;
    }

    /**
     * Get Group
     *
     * @return null|string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set Sub Group into bracket syntax
     *
     * @param $sub
     *
     * @return $this
     */
    public function setSub( $sub )
    {
        $this->sub = null;

        if (Validate::bracket( $sub )) {
            $this->sub = $sub;
        } elseif (is_string( $sub )) {
            $this->sub = "[{$sub}]";
        }

        return $this;
    }

    /**
     * Get Sub Group
     *
     * @return null
     */
    public function getSub()
    {
        return $this->sub;
    }

    /**
     * Set From settings
     *
     * @param array $settings
     *
     * @return $this
     */
    public function setSettings( array $settings )
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Get Form settings
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Set Form setting by key
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setSetting( $key, $value )
    {
        $this->settings[$key] = $value;

        return $this;
    }

    /**
     * Get From setting by key
     *
     * @param $key
     *
     * @return null
     */
    public function getSetting( $key )
    {
        if ( ! array_key_exists( $key, $this->settings )) {
            return null;
        }

        return $this->settings[$key];
    }

    /**
     * Remove setting bby key
     *
     * @param $key
     *
     * @return $this
     */
    public function removeSetting( $key )
    {
        if (array_key_exists( $key, $this->settings )) {
            unset( $this->settings[$key] );
        }

        return $this;
    }

    /**
     * Get the render setting of the form
     *
     * @return null
     */
    public function getRenderSetting()
    {
        if ( ! array_key_exists( 'render', $this->settings )) {
            return null;
        }

        return $this->settings['render'];
    }

    /**
     * Render Setting
     *
     * By setting render to 'raw' the form will not add any special html wrappers.
     * You have more control of the design when render is set to raw.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setRenderSetting( $value )
    {
        $this->settings['render'] = $value;

        return $this;
    }

    /**
     * Set whether to populate fields in the form. If set to false fields will
     * always be left empty and with their default values.
     *
     * @param $populate
     *
     * @return $this
     */
    public function setPopulate( $populate )
    {
        $this->populate = (bool) $populate;

        return $this;
    }

    /**
     * Get Populate
     *
     * @return bool
     */
    public function getPopulate()
    {
        return $this->populate;
    }

    /**
     * Set the current Field to process
     *
     * @param Field $field
     *
     * @return $this
     */
    public function setCurrentField( Field $field )
    {
        $this->currentField = null;

        if ($field instanceof Field) {
            $this->currentField = $field;
        }

        return $this;
    }

    /**
     * Get the current Field the From is processing
     *
     * @return Field
     */
    public function getCurrentField()
    {
        return $this->currentField;
    }

    /**
     * Open Form Element
     *
     * Not needed post types, for example, since WordPress already opens this for you.
     *
     * @param array $attr
     * @param bool|true $use_rest
     *
     * @return string
     */
    public function open( $attr = array() )
    {
        switch ($this->action) {
            case 'update' :
                $method = 'PUT';
                break;
            case 'create' :
                $method = 'POST';
                break;
            default :
                $method = 'PUT';
                break;
        }

        $rest     = array();
        $defaults = array(
            'action'      => $this->path,
            'method'      => 'POST'
        );

        $attr = array_merge( $defaults, $attr, $rest );

        $form      = new Tag( 'form', $attr );
        $generator = new Generator();

        $r = $form->getStringOpenTag();
        $r .= $generator->newInput( 'hidden', '_method', $method )->getString();
        $r .= csrf_field();

        return $r;
    }

    /**
     * Close the From Element and add a submit button if value is string
     *
     * @param null|string $value
     *
     * @return string
     */
    public function close( $value = null )
    {
        $html = '';
        if (is_string( $value )) {
            $generator = new Generator();
            $html .= $generator->newInput( 'submit', '_tr_submit_form', $value,
                array( 'id' => '_tr_submit_form', 'class' => 'btn btn-primary' ) )->getString();
        }

        $html .= '</form>';

        return $html;
    }

    /**
     * Get the Form Field Label
     *
     * @return string
     */
    public function getLabel()
    {
        $open_html  = "<div class=\"control-label\"><span class=\"span-label\">";
        $close_html = '</span></div>';
        $html       = '';
        $label      = $this->currentField->getLabelOption();

        if ($label) {
            $label = $this->currentField->getSetting( 'label' );
	    $label .= $this->currentField->getSetting( 'required' ) ? '*' : '';
            $html  = "{$open_html}{$label} {$close_html}";
        }

        return $html;
    }

    /**
     * Set the form debug status
     *
     * @param bool $status
     *
     * @return $this
     */
    public function setDebugStatus( $status )
    {
        $this->debugStatus = (bool) $status;

        return $this;
    }

    /**
     * Get the From debug status
     *
     * @return bool|null
     */
    public function getDebugStatus()
    {
        return ( $this->debugStatus === false ) ? $this->debugStatus : Config::getDebugStatus();
    }

    /**
     * Get Form Field string
     *
     * @param Field $field
     *
     * @return string
     */
    public function getFromFieldString( Field $field )
    {
        $this->setCurrentField( $field );
        $label     = $this->getLabel();
        $field     = $field->getString();
        $id        = $this->getCurrentField()->getSetting( 'id' );
        $help      = $this->getCurrentField()->getSetting( 'help' );
        $fieldHtml = $this->getCurrentField()->getSetting( 'render' );
        $formHtml  = $this->getSetting( 'render' );

        $id   = $id ? "id=\"{$id}\"" : '';
        $help = $help ? "<div class=\"help\"><p>{$help}</p></div>" : '';

        if ($fieldHtml == 'raw' || $formHtml == 'raw') {
            $html = $field;
        } else {
            $type = strtolower( str_ireplace( '\\', '-', get_class( $this->getCurrentField() ) ) );
            $html = "<div class=\"control-section {$type}\" {$id}>{$label}<div class=\"control\">{$field}{$help}</div></div>";
        }

        $this->currentField = null;

        return $html;
    }

    /**
     * Get From fields string from array
     *
     * @param array $fields
     *
     * @return string
     */
    public function getFromFieldsString( array $fields = array() )
    {
        $html = '';

        foreach ($fields as $field) {

            if($field instanceof Field) {
                $clone_field = clone $field;
                $html .= (string) $clone_field->configureToForm($this);
            } elseif(is_array($field) && count($field) > 1) {
                $function   = array_shift( $field );
                $parameters = array_pop( $field );

                if (method_exists( $this, $function ) && is_array( $parameters )) {
                    $html .= (string) call_user_func_array( array( $this, $function ), $parameters );
                }
            }

        }

        return $html;
    }

    /**
     * Text Input
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Text
     */
    public function text( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Text( $name, $attr, $settings, $label, $this );
    }

    /**
     * Password Input
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Text
     */
    public function password( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Text( $name, $attr, $settings, $label, $this );
        $field->setType( 'password' );

        return $field;
    }

    /**
     * Hidden Input
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|false $label
     *
     * @return Fields\Text
     */
    public function hidden( $name, array $attr = array(), array $settings = array(), $label = false )
    {
        $field = new Fields\Text( $name, $attr, $settings, $label, $this );
        $field->setType( 'hidden' )->setRenderSetting( 'raw' );

        return $field;
    }

    /**
     * Submit Button
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|false $label
     *
     * @return Fields\Submit
     */
    public function submit( $name, array $attr = array(), array $settings = array(), $label = false )
    {
        $field = new Fields\Submit( $name, $attr, $settings, $label, $this );
        $field->setAttribute( 'value', $name );

        return $field;
    }

    /**
     * Textarea Input
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Textarea
     */
    public function textarea( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Textarea( $name, $attr, $settings, $label, $this );
    }

    /**
     * Editor Input
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Editor
     */
    public function editor( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Editor( $name, $attr, $settings, $label, $this );
    }

    /**
     * Radio Input
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Radio
     */
    public function radio( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Radio( $name, $attr, $settings, $label, $this );
    }

    /**
     * Checkbox Input
     *
     * By default checkboxes don't have a normal control label
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Checkbox
     */
    public function checkbox( $name, array $attr = array(), array $settings = array(), $label = false )
    {
        return new Fields\Checkbox( $name, $attr, $settings, $label, $this );
    }

    /**
     * Select Input
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Select
     */
    public function select( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Select( $name, $attr, $settings, $label, $this );
    }

    /**
     * Media Input
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Media
     */
    public function media( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Media( $name, $attr, $settings, $label, $this );
    }

	/**
	 * Media Input
	 *
	 * @param string $name
	 * @param array $attr
	 * @param array $settings
	 * @param bool|true $label
	 *
	 * @return Fields\Gallery
	 */
	public function mediaGallery( $name, array $attr = array(), array $settings = array(), $label = true )
	{
		return new Fields\Gallery( $name, $attr, $settings, $label, $this );
	}

    /**
     * Model Search
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool $label
     *
     * @return \TypeRocket\Fields\ModelSearch
     */
    public function modelSearch( $name, array $attr = array(), array $settings = array(), $label = true ) {
        return new Fields\ModelSearch( $name, $attr, $settings, $label, $this );
    }

    /**
     * Items Input
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Items
     */
    public function items( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Items( $name, $attr, $settings, $label, $this );
    }

    /**
     * Matrix Input
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Matrix
     */
    public function matrix(
        $name,
        array $attr = array(),
        array $settings = array(),
        $label = true
    ) {
        return new Fields\Matrix( $name, $attr, $settings, $label, $this );
    }

    /**
     * Builder Input
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Matrix
     */
    public function builder(
        $name,
        array $attr = array(),
        array $settings = array(),
        $label = true
    ) {
        return new Fields\Builder( $name, $attr, $settings, $label, $this );
    }

    /**
     * Repeater Input
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Repeater
     */
    public function repeater( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Repeater( $name, $attr, $settings, $label, $this );
    }

    /**
     * Drop Zone
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool $label
     *
     * @return \TypeRocket\Fields\DropZone
     */
    public function dropzone( $name, array $attr = array(), array $settings = array(), $label = true ) {
        return new Fields\DropZone( $name, $attr, $settings, $label, $this );
    }

    /**
     * Field object into input
     *
     * @param Fields\Field $field
     *
     * @return Field $field
     */
    public function field( Field $field )
    {
        return $field;
    }

}
