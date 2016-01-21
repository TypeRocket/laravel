<?php
namespace TypeRocket;

class Matrix {

    public function route($group, $type)
    {
        $formGroup = $_POST['form_group'];
        $tr_matrix_id = time(); // id for repeater

        $formClass = Config::getFormProviderClass();
        /** @var Form $form */
        $form = new $formClass(null);

        if( $form instanceof Form) {
            $form->setPopulate(false);
            $form->setDebugStatus(false);
            if( ! Validate::bracket($formGroup) ) {
                $formGroup = '';
            }
            $paths = Config::getPaths();
            $form->setGroup($formGroup . "[{$group}][{$tr_matrix_id}][{$type}]");
            $file = $paths['matrix_folder'] . "/{$group}/{$type}.php";
        } else {
            $file = 'Bad form provider. Set .env TR_FORM_PROVIDER. Class must extend ' . Form::class;
        }

        ?>
        <div class="matrix-field-group tr-repeater-group matrix-type-<?php echo $type; ?> matrix-group-<?php echo $group; ?>">
            <div class="repeater-controls">
                <div class="collapse glyphicon glyphicon-chevron-down"></div>
                <div class="move glyphicon glyphicon-menu-hamburger"></div>
                <a href="#remove" class="remove glyphicon glyphicon-remove" title="remove"></a>
            </div>
            <div class="repeater-inputs">
                <?php
                if(file_exists($file)) {
                    /** @noinspection PhpIncludeInspection */
                    include($file);
                } else {
                    echo "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> No Matrix file found <code>{$file}</code></div>";
                }
                ?>
            </div>
        </div>
        <?php
    }

}