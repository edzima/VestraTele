<?php

use backend\widgets\GridView;
use common\helpers\Html;
use common\modules\credit\models\CreditClientAnalyze;
use common\modules\credit\models\CreditLoanInstallment;
use common\modules\credit\models\CreditSanctionCalc;
use common\widgets\grid\CurrencyColumn;
use common\widgets\grid\SerialColumn;
use yii\data\ArrayDataProvider;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model CreditSanctionCalc */
/* @var $analyze CreditClientAnalyze|null */

$this->title = Yii::t('credit', 'Calculation');

?>

<div class="credit-sanction-calc">


	<div class="row">
		<div class="col-md-8 col-lg-7">
			<?= $this->render('_form', [
				'model' => $model,
			]) ?>

			<?php if ($analyze): ?>

				<?= $this->render('_form-analyze', [
					'model' => $analyze,
				]) ?>

			<?php endif; ?>
		</div>
		<div class="col-md-5 col-lg-5">
			<?php if ($analyze): ?>
				<?= DetailView::widget([
					'model' => $model,
					'attributes' => [
						'interestsPaid:currency',
						'interestsToPay:currency',
						'interestsTotal:currency',
					],
				]) ?>
				<?= GridView::widget([
					'caption' => Yii::t('credit', 'Installments'),
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
						[
							'attribute' => 'debt',
							'format' => 'currency',
							'value' => function (CreditLoanInstallment $installment): ?float {
								if ($installment->debt > 0) {
									return $installment->debt;
								}
								Yii::warning([
									'message' => 'Debt <= 0',
									'attributes' => $installment->getAttributes(),
								]);
								return null;
							},
						],
						[
							'class' => CurrencyColumn::class,
							'attribute' => 'value',
							'pageSummary' => true,
						],
						[
							'class' => CurrencyColumn::class,
							'attribute' => 'capitalValue',
							'contentBold' => false,
							'pageSummary' => true,
						],
						[
							'class' => CurrencyColumn::class,
							'attribute' => 'interestPart',
							'contentBold' => false,
							'pageSummary' => true,
						],
						'date:date',
						'interestRate:percent',
					],
					'showPageSummary' => true,
					'summary' => '',
					'emptyText' => '',
					'showOnEmpty' => false,
				])
				?>

			<?php endif; ?>
		</div>
	</div>

</div>
