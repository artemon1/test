<?php

namespace app\controllers;

use Yii;
use app\helpers\WebConsole;

class InstallController extends \yii\web\Controller
{
  public function actionIndex()
  {
    WebConsole::migrate();
		return $this->redirect(['/site']);
  }
}