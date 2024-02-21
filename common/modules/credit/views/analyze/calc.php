<?php

use backend\widgets\GridView;
use common\helpers\Html;
use common\modules\credit\models\CreditClientAnalyze;
use common\modules\credit\models\CreditLoanInstallment;
use common\modules\credit\models\CreditSanctionCalc;
use common\widgets\grid\SerialColumn;
use yii\data\ArrayDataProvider;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model CreditSanctionCalc */
/* @var $analyze CreditClientAnalyze|null */

$this->title = 'Kalkulacja';

$pdfQueryParams = Yii::$app->request->getQueryParams();
$pdfQueryParams[0] = 'pdf';

?>

<div class="credit-sanction-calc">


	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

	<?php if ($analyze): ?>
		<p>
			<?= Html::a('PDF', $pdfQueryParams, [
				'class' => 'btn btn-success',
			]) ?>
		</p>
		<div class="row">
			<div class="col-md-6">

				<?= DetailView::widget([
					'model' => $model,
					'attributes' => [
						'interestPaid:currency',
						'interestToPay:currency',
						'interestTotal:currency',
					],
				]) ?>


			</div>
		</div>

		<?= $this->render('_form-analyze', [
			'model' => $analyze,
		]) ?>
	<?php endif; ?>


	<?= GridView::widget([
		'dataProvider' => new ArrayDataProvider([
			'allModels' => $model->getLoanInstallments(),
			'pagination' => false,
			'modelClass' => CreditLoanInstallment::class,
		]),
		'rowOptions' => function (CreditLoanInstallment $data) use ($model): array {
			$options = [];
			if ($model->installmentIsPaid($data)) {
				Html::addCssClass($options, 'success');
			}
			return $options;
		},
		'columns' => [
			['class' => SerialColumn::class],
			'debt:currency',
			'value:currency',
			'capitalValue:currency',
			'interestPart:currency',
			'date:date',
			'interestRate:percent',
		],
		'emptyText' => '',
		'showOnEmpty' => false,
	])
	?>



	<?php

	?>
</div>
