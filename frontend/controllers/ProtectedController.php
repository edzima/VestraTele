<?php

namespace frontend\controllers;

use common\models\user\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ProtectedController extends Controller {

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'permissions' => [User::PERMISSION_NEWS],
					],
				],
			],
		];
	}

	public function actionDownload(string $path) {
		$file = Yii::getAlias('@protected/files/' . $path);
		if (file_exists($file)) {
			return Yii::$app->response->sendFile($file);
		}
		throw new NotFoundHttpException();
	}

	public function actionLogo() {
		$file = Yii::getAlias('@storage/protected/icon.png');
		//	echo FileHelper::getMimeType($f`ile);
		//	header("Content-Type: ");
		Yii::$app->response->format = Response::FORMAT_RAW;
		if (!Yii::$app->user->isGuest) {
			Yii::$app->response->headers->set('Content-Type', 'image/png');
			https://ubiq.co/tech-blog/prevent-image-hotlinking-nginx/
			return $this->renderFile($file);
			Yii::$app->response->headers->set('X-Accel-Redirect', '/static/img/default.png');
		}
		Yii::$app->end();
	}

	public function actionIssue(int $id) {
		$file = Yii::getAlias('@storage/favicon.ico');
		Yii::$app->request->headers->add('X-Accel-Redirect', $file);
		return $this->renderFile($file);
	}
}
