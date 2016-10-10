<?php
/**
 * Make From
 *
 * @param string $resource the eloquent model
 * @param string $action update or create
 * @param null|int $itemId you can set this to null or an integer
 * @param string $path submit the form to this path
 *
 * @return \TypeRocket\Form
 */
function tr_form($resource = null, $action = 'update', $itemId = null, $path = null) {
    $form = new \TypeRocket\Form($resource, $action, $itemId, $path);

    return $form;
}