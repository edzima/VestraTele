<?php

namespace frontend\controllers;

use common\models\issue\SummonDoc;
use common\modules\issue\controllers\SummonDocLinkController;
use Yii;
use yii\web\Response;

class SummonDocController extends SummonDocLinkController {

	public bool $sendEmailAboutToConfirm = true;

	public function actionTypesList() {
		Yii::$app->response->format = Response::FORMAT_JSON;
		$out = [];
		if (isset($_POST['depdrop_parents'])) {
			$parents = $_POST['depdrop_parents'];
			if ($parents != null) {
				$type_id = (int) $parents[0];
				$names = SummonDoc::getNames($type_id);
				foreach ($names as $id => $name) {
					$out[] = [
						'id' => $id,
						'name' => $name,
					];
				}
				return ['output' => $out, 'selected' => ''];
			}
		}
		return ['output' => '', 'selected' => ''];
	}
}
