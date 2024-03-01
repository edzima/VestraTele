<?php

namespace common\modules\credit\controllers;

use common\helpers\Inflector;
use common\modules\credit\models\CreditClientAnalyze;
use common\modules\credit\models\CreditSanctionCalc;
use Mpdf\Mpdf;
use Yii;
use yii\web\Controller;

class AnalyzeController extends Controller {

	public function actionCalc() {
		$model = new CreditSanctionCalc();
		$model->dateAt = date('Y-m-d');

		$analyze = null;
		if ($model->load(Yii::$app->request->queryParams) && $model->validate()) {
			$model->generateInstallments();
			$analyze = new CreditClientAnalyze();
			$analyze->setSanctionCalc($model);
			if ($analyze->load(Yii::$app->request->post()) && $analyze->validate()) {
				$pdf = $this->createPDF($analyze);
				return $pdf->OutputHttpDownload(Inflector::slug($pdf->title) . '.pdf');
			}
		}

		return $this->render('calc', [
			'model' => $model,
			'analyze' => $analyze,
		]);
	}

	public function actionPdf() {
		$model = new CreditClientAnalyze();
		if ($model->load(Yii::$app->request->queryParams)
			&& $model->validate()) {
			$mpdf = $this->createPDF($model);
			$mpdf->Output();
		}
		return $this->asJson($model->getErrors());
	}

	private function createPDF(CreditClientAnalyze $model): Mpdf {
		$content = $this->renderAjax('pdf-content', [
			'model' => $model,
		]);
		$mpdf = new Mpdf([
			'tempDir' => Yii::getAlias('@runtime/mpdf'),
			'margin_top' => 45,
			'margin_bottom' => 25,
		]);
		$mpdf->SetTitle(
			Yii::t('credit',
				'Analyze - {borrower} ({analyzeAt})', [
					'borrower' => $model->borrower,
					'analyzeAt' => Yii::$app->formatter->asDate($model->analyzeAt),
				]
			)
		);
		$mpdf->SetDocTemplate(Yii::getAlias('@storage/mpdf/layout.pdf'));
		$mpdf->WriteHTML($content);
		return $mpdf;
	}

}
