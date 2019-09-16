<?php

namespace frontend\controllers;

use common\models\benefit\BenefitAmountDivider;
use frontend\models\BenefitAmountAlignmentForm;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\Controller;

/**
 * BenefitAmountController implements the CRUD actions for BenefitAmount model.
 */
class BenefitAmountController extends Controller {

	/**
	 * Lists all BenefitAmount models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$model = new BenefitAmountAlignmentForm();
		Yii::$app->session->addFlash('warning', 'W przypadku przerwy w pobieraniu zasiłku, skontaktuj się z prawnikiem, w celu indywidualnego wyliczenia.');

		$dataProvider = null;
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {

			$dividers = BenefitAmountDivider::createForRanges($model->calculateFrom(), $model->calculateTo());
			if (!empty($dividers)) {
				$dataProvider = new ArrayDataProvider(['models' => $dividers]);
			}
		}

		return $this->render('index', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}

}
