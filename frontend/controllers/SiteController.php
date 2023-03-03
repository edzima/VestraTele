<?php

namespace frontend\controllers;

use common\helpers\Flash;
use common\models\Article;
use common\models\user\LoginForm;
use common\models\user\User;
use frontend\models\ContactForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\ResetPasswordForm;
use frontend\models\VerifyEmailForm;
use vova07\fileapi\actions\UploadAction as FileAPIUpload;
use Yii;
use yii\base\InvalidArgumentException;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Class SiteController.
 */
class SiteController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function actions() {
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
			'fileapi-upload' => [
				'class' => FileAPIUpload::class,
				'path' => '@storage/tmp',
			],

		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'only' => ['logout'],
				'rules' => [
					[
						'actions' => ['logout'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}

	/**
	 * Displays homepage.
	 *
	 * @return mixed
	 */
	public function actionIndex() {

		$articlesDataProvider = null;
		if (Yii::$app->user->can(User::PERMISSION_NEWS)) {
			$articlesDataProvider = new ActiveDataProvider([
				'query' => Article::find()
					->published()
					->mainpage(),
			]);
		}
		return $this->render('index', [
			'articlesDataProvider' => $articlesDataProvider,
		]);
	}

	/**
	 * Displays contact page.
	 *
	 * @return mixed
	 */
	public function actionContact() {
		$model = new ContactForm();
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
				Flash::add(
					Flash::TYPE_SUCCESS,
					Yii::t('frontend',
						'Thank you for contacting us. We will respond to you as soon as possible.'
					)
				);
			} else {
				Flash::add(
					Flash::TYPE_ERROR,
					Yii::t('frontend', 'There was an error sending your message.')
				);
			}

			return $this->refresh();
		}
		return $this->render('contact', [
			'model' => $model,
		]);
	}

	/**
	 * Logs in a user.
	 *
	 * @return mixed
	 */
	public function actionLogin() {
		if (!Yii::$app->user->isGuest) {
			return $this->goHome();
		}

		$model = new LoginForm();
		if ($model->load(Yii::$app->request->post()) && $model->login()) {
			return $this->goBack();
		}
		$model->password = '';

		return $this->render('login', [
			'model' => $model,
		]);
	}

	/**
	 * Logs out the current user.
	 *
	 * @return mixed
	 */
	public function actionLogout() {
		Yii::$app->user->logout();

		return $this->goHome();
	}

	/**
	 * Requests password reset.
	 *
	 * @return mixed
	 */
	public function actionRequestPasswordReset() {
		$model = new PasswordResetRequestForm();
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			if ($model->sendEmail()) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('frontend', 'Check your email for further instructions.')
				);

				return $this->goHome();
			}
			Flash::add(Flash::TYPE_ERROR,
				Yii::t('frontend', 'Sorry, we are unable to reset password for the provided email address.')
			);
		}

		return $this->render('requestPasswordResetToken', [
			'model' => $model,
		]);
	}

	/**
	 * Resets password.
	 *
	 * @param string $token
	 * @return mixed
	 * @throws BadRequestHttpException
	 */
	public function actionResetPassword(string $token) {
		try {
			$model = new ResetPasswordForm($token);
		} catch (InvalidArgumentException $e) {
			throw new BadRequestHttpException($e->getMessage());
		}

		if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('frontend', 'New password saved.')
			);

			return $this->goHome();
		}

		return $this->render('resetPassword', [
			'model' => $model,
		]);
	}

	/**
	 * Verify email address
	 *
	 * @param string $token
	 * @return yii\web\Response
	 * @throws BadRequestHttpException
	 */
	public function actionVerifyEmail($token) {
		try {
			$model = new VerifyEmailForm($token);
		} catch (InvalidArgumentException $e) {
			throw new BadRequestHttpException($e->getMessage());
		}
		if ($user = $model->verifyEmail()) {
			if (Yii::$app->user->login($user)) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('frontend', 'Your email has been confirmed!')
				);
				return $this->goHome();
			}
		}

		Flash::add(Flash::TYPE_ERROR,
			Yii::t('frontend', 'Sorry, we are unable to verify your account with provided token.')
		);
		return $this->goHome();
	}

	/**
	 * Resend verification email
	 *
	 * @return mixed
	 */
	public function actionResendVerificationEmail() {
		$model = new ResendVerificationEmailForm();
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			if ($model->sendEmail()) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('frontend', 'Check your email for further instructions.')
				);

				return $this->goHome();
			}
			Flash::add(Flash::TYPE_ERROR,
				Yii::t('frontend', 'Sorry, we are unable to resend verification email for the provided email address.')
			);
		}

		return $this->render('resendVerificationEmail', [
			'model' => $model,
		]);
	}
}
