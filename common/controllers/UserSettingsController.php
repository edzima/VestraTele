<?php

namespace common\controllers;

use common\helpers\Flash;
use common\helpers\Url;
use common\models\issue\IssueType;
use common\models\user\UserSettingsForm;
use Yii;
use yii\web\Controller;

class UserSettingsController extends Controller {

	public function actionFavoriteIssueType(int $type_id = null, string $returnUrl = null) {
		$model = new UserSettingsForm();
		$model->userId = Yii::$app->user->getId();
		$model->favoriteIssueTypeId = $type_id;
		if ($model->validate() && $model->save(false)) {
			$type = IssueType::findOne($type_id);
			if ($type) {
				Flash::add(Flash::TYPE_SUCCESS, Yii::t('issue', 'Mark Type: {type} as Favorite.', [
					'type' => $type->name,
				]));
			} else {
				Flash::add(Flash::TYPE_INFO, Yii::t('issue', 'Unmark Favorite Issue Type.'));
			}
			return $this->redirect($returnUrl ?: Url::previous());
		}
		return $this->asJson($model->getErrors());
	}
}
