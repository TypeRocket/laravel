<?php

namespace TypeRocket;

class Builder
{
    public function route($group, $type, $folder)
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
        <div class="builder-field-group builder-type-<?php echo $type; ?> builder-group-<?php echo $group; ?>">
            <div class="builder-inputs">
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
        die();
    }

}