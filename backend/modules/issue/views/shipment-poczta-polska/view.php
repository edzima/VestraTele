<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Html;
use backend\modules\issue\widgets\IssueShipmentPocztaPolskaWidget;
use common\models\issue\IssueShipmentPocztaPolska;
use common\models\user\User;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var IssueShipmentPocztaPolska $model */

$this->title = Yii::t('issue', 'Shipment: {number}', [
	'number' => $model->shipment_number,
]);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model->issue);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Shipment Poczta Polska'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="issue-shipment-poczta-polska-view">

	<p>
		<?= !$model->isFinished()
			? Html::a(Html::icon('refresh'),
				['refresh', 'issue_id' => $model->issue_id, 'shipment_number' => $model->shipment_number],
				[
					'class' => 'btn btn-warning',
					'data-method' => 'POST',
					'title' => Yii::t('poczta-polska', 'Refresh'),
					'aria-label' => Yii::t('poczta-polska', 'Refresh'),
				])
			: ''
		?>
		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'issue_id' => $model->issue_id, 'shipment_number' => $model->shipment_number], ['class' => 'btn btn-primary']) ?>

		<?= Html::a(Yii::t('backend', 'Delete'), ['delete', 'issue_id' => $model->issue_id, 'shipment_number' => $model->shipment_number], [
			'class' => 'btn btn-danger pull-right',
			'data' => [
				'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<div class="row">


		<div class="col-md-8">

			<?= !empty($model->details)
				? Html::tag('p', Html::encode($model->details))
				: ''
			?>


			<?= IssueShipmentPocztaPolskaWidget::widget([
				'model' => $model,
			]) ?>

		</div>

		<div class="col-md-4">
			<?= Html::a(
				Html::encode(Yii::$app->pocztaPolska->externalTrackingUrl($model->shipment_number)),
				Yii::$app->pocztaPolska->externalTrackingUrl($model->shipment_number), [
					'target' => '_blank',
				]
			) ?>
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					'created_at:datetime',
					'updated_at:datetime',
					'shipment_at:datetime',
					'finished_at:datetime',
					[
						'attribute' => 'apiData',
						'format' => 'ntext',
						'visible' => Yii::$app->user->can(User::ROLE_ADMINISTRATOR) && Yii::$app->request->get('showRawApi'),
					],
				],
			]) ?>
		</div>


	</div>


</div>
