<?php

namespace common\modules\credit\controllers;

use common\helpers\Flash;
use common\modules\credit\models\CreditClientAnalyze;
use common\modules\credit\models\CreditSanctionCalc;
use Dompdf\Dompdf;
use kartik\mpdf\Pdf;
use mikehaertl\wkhtmlto\Pdf as WPdf;
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
			Flash::add(Flash::TYPE_SUCCESS, 'Model load & validate');
			$analyze = new CreditClientAnalyze();
			$analyze->setSanctionCalc($model);
			if ($analyze->load(Yii::$app->request->post()) && $analyze->validate()) {
				Flash::add(Flash::TYPE_SUCCESS, 'Analyze load & validate');
			}
		}

		return $this->render('calc', [
			'model' => $model,
			'analyze' => $analyze,
		]);
	}

	public function actionPdfContent() {
		$model = new CreditSanctionCalc();
		$model->load(Yii::$app->request->queryParams);
		return $this->render('pdf-content', [
			'model' => $model,
		]);
	}

	public function actionHeader(): string {
		$this->layout = 'layout';
		return $this->render('pdf-content');
	}

	public function actionMike() {
		$this->layout = 'layout';
		$pdf = new WPdf();
		$pdf->setOptions([
			'encoding' => 'UTF-8',
			'disable-smart-shrinking',
			'enable-local-file-access',
			//	'log-level' => 'warn',
		]);
		$pdf->tmpDir = Yii::getAlias('@runtime');
		//$pdf->addPage('test2');
		$pdf->addPage($this->render('pdf-content'));

		if (!$pdf->send('name.pdf', true)) {
			Yii::warning($pdf->getError(), 'send');
			return $this->renderContent($pdf->getError());
		}
//
//		if (!$pdf->saveAs('test.pdf')) {
//			Yii::warning($pdf->getError(), 'save');
//		}
		//return $this->renderContent('test');
		//return $pdf->send('mike');
	}

	public function actionDom(): string {
		// instantiate and use the dompdf class
		$dompdf = new Dompdf();
		$this->layout = 'layout';

		$dompdf->loadHtml($this->render('pdf-content'));

		$dompdf->setPaper('A4', 'landscape');

		$dompdf->render();

		return $dompdf->outputHtml();
	}

	public function actionMpdf() {
		$model = new CreditSanctionCalc();

		$this->layout = 'layout';
		if ($model->load(Yii::$app->request->queryParams)
			&& $model->validate()) {
			$content = $this->renderAjax('pdf-content');
			$mpdf = new Mpdf([
				'tempDir' => Yii::getAlias('@runtime/mpdf'),
				'margin_top' => 40,
			]);
			$mpdf->SetSubject('Subject Test');
			$mpdf->SetTitle('Title Test');
			$mpdf->SetDocTemplate(Yii::getAlias('@storage/mpdf/analiza.pdf'));
			//	$mpdf->WriteHTML($cssContent, HTMLParserMode::HEADER_CSS);

			//$mpdf->SetHTMLHeader();
//			$cssFile = Yii::getAlias('@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css');
			//$cssContent = file_get_contents($cssFile);
			//$mpdf->WriteHTML($cssContent, HTMLParserMode::HEADER_CSS);
			$mpdf->WriteHTML($content);

			$mpdf->Output();
		}
	}

	public function actionPdf() {
		$model = new CreditSanctionCalc();

		if ($model->load(Yii::$app->request->queryParams)
			&& $model->validate()) {
			$this->layout = 'layout';
			$content = $this->render('pdf-content');

			$header =
			$pdf = new Pdf([
				// set to use core fonts only
				'mode' => Pdf::MODE_UTF8,
				// A4 paper format
				'format' => Pdf::FORMAT_A4,
				// portrait orientation
				'orientation' => Pdf::ORIENT_PORTRAIT,
				// stream to browser inline
				'destination' => Pdf::DEST_BROWSER,
				// your html content input
				'content' => $content,
				// format content from your own css file if needed or use the
				// enhanced bootstrap css built by Krajee for mPDF formatting
				'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
				// any css to be embedded if required
				//				'cssInline' => '.kv-heading-1{font-size:18px}',
				//				// set mPDF properties on the fly
				//				'options' => [
				//					'title' => 'WSTĘPNY WYNIK ANALIZY UMOWY KREDYTOWEJ/POŻYCZKI',
				//				],
				// call mPDF methods on the fly
				'methods' => [
					'SetHeader' => '<div class="row"><img src="' . Yii::getAlias('@storage/mpdf/logo.png') . ' " </div>',
					//	'SetFooter' => ['{PAGENO}'],
					//	'SetTitle' => ['Analiza Umowy Kredytowej'],
					//	'SetDocTemplate' => Yii::getAlias('@storage/mpdf/analiza.pdf'),
				],
			]);
			//	$pdf->getApi()->SetDocTemplate(Yii::getAlias('@storage/mpdf/analiza.pdf'));
			return $pdf->render();
		}
		Flash::add(Flash::TYPE_WARNING, 'Not send Valid Data');
		return $this->redirect(['index', Yii::$app->request->queryParams]);
	}
}
