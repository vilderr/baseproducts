<?php

namespace app\modules\admin\modules\catalog\controllers;

use app\modules\admin\components\Controller;

/**
 * Default controller for the `catalog` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
