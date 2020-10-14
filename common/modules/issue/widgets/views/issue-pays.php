<?php

use common\models\issue\IssuePay;
use common\modules\issue\widgets\IssuePaysWidget;
use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $models IssuePay[] */
/* @var $notesOptions array */
/* @var $widget IssuePaysWidget */
/* @var $withProvisions bool */

?>

<fieldset>
	<legend>Wpłaty
		<button class="btn toggle pull-right" data-toggle="#pays-details">
			<i class="glyphicon glyphicon-chevron-down"></i></button>
	</legend>
	<div id="pays-details">
		<?php foreach ($models as $key => $pay): ?>
			<fieldset>
				<div class="pay-wrapper border <?= $pay->isPayed() ? 'border-green' : 'border-red' ?>">


					<legend>Wpłata <?= count($models) > 1 ? $key + 1 : '' ?></legend>
					<p>
						<?= $widget->editPayBtn ? Html::a($pay->isPayed() ? 'Edytuj' : 'Opłać',
							['/issue/pay/pay', 'id' => $pay->id], [
								'class' => 'btn btn-primary',
							]) : '' ?>
					</p>
					<?= DetailView::widget([
						'model' => $pay,
						'options' => [
							'class' => 'table table-striped table-bordered detail-view th-nowrap',
						],
						'attributes' => [
							'pay_at:date',
							'deadline_at:date',
							[
								'attribute' => 'transferTypeName',
								'label' => 'Płatność',
								'format' => 'raw',
							],
							'value:currency',
							'valueNetto:currency',
							'vatPercent:percent',

						],

					]) ?>
					<?php if ($withProvisions): ?>
						<fieldset>
							<legend>Prowizje</legend>

							<?
							if (($dataProvider = $widget->getProvisionsProvider($pay)) instanceof ActiveDataProvider) {
								echo GridView::widget([
									'dataProvider' => $dataProvider,
									'columns' => [
										'toUser',
										'fromUserString',
										'value:currency',
									],
								]);
							}
							?>
						</fieldset>
					<? endif; ?>
				</div>
			</fieldset>
		<?php endforeach; ?>
	</div>
</fieldset>
