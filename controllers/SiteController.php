<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\UserSearch;

class SiteController extends Controller
{
		private $_installed;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['index','login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
										[
                        //'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
				if(!Yii::$app->user->isGuest && Yii::$app->user->identity->nikname)
					$user = UserSearch::findOne(['nikname' => Yii::$app->user->identity->nikname]);
				else
					$user = new UserSearch;
        return $this->render('index', ['user' => $user]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
		
		public function beforeAction($action)
    {
			if(!$this->getInstalled()) {
				$this->redirect(['/install']);
				return false;
			}
      if (!parent::beforeAction($action)) {
        return false;
			}
			return true;
    }
		
		public function getInstalled()
		{
			if($this->_installed === null) {
				try {
					$this->_installed = Yii::$app->db->createCommand("SHOW TABLES LIKE '%'")->query()->count() > 0 ? true : false;
				} catch (\Exception $e) {
					$this->_installed = false;
					}
			}
			return $this->_installed;
  }
}
