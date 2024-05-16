<?php

use backend\helpers\Html;
use backend\modules\issue\models\search\ShipmentPocztaPolskaSearch;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\IssueShipmentPocztaPolska;
use common\widgets\grid\ActionColumn;

/** @var yii\web\View $this */
/** @var ShipmentPocztaPolskaSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('issue', 'Issue Shipment Poczta Polska');
$this->params['breadcrumbs'][] = ['url' => ['issue/index'], 'label' => Yii::t('issue', 'Issues')];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-shipment-poczta-polska-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('issue', 'Create Issue Shipment Poczta Polska'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'class' => IssueColumn::class,
			],
			'shipment_number',
			'shipmentTypeName',
			'details',
			'shipment_at:date',
			'finished_at:date',
			'created_at:date',
			'updated_at:date',
			//'apiData:ntext',
			[
				'class' => ActionColumn::class,
				'template' => '{refresh} {view} {update} {delete}',
				'buttons' => [
					'refresh' => function (string $url, IssueShipmentPocztaPolska $model): string {
						if ($model->isFinished()) {
							return '';
						}
						return Html::a(Html::icon('refresh'), $url, [
							'data-method' => 'POST',
						]);
					},
				],
			],
		],
	]); ?>


</div>
