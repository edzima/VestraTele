<?php

namespace backend\controllers;

use yii\web\Controller;

class FileManagerController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
