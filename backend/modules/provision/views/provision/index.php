<?php

use backend\helpers\Url;
use backend\widgets\GridView;
use common\models\provision\Provision;
use common\models\provision\ProvisionSearch;
use common\widgets\grid\CustomerDataColumn;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel ProvisionSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Prowizje';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provision-index">

	<h1><?= Html::encode($this->title) ?></h1>


	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute' => 'issue_id',
				'format' => 'raw',
				'label' => 'Sprawa',
				'value' => static function (Provision $data): string {
					return Html::a($data->pay->issue, Url::to(['/issue/pay-calculation/view', 'id' => $data->pay->calculation->issue_id], ['target' => '_blank']));
				},
			],
			[
				'class' => CustomerDataColumn::class,
				'value' => 'pay.issue.customer.fullName',
			],
			[
				'label' => 'Płatność',
				'value' => 'pay.partInfo',
			],
			'toUser',
			'fromUserString',
			'provision:percent',
			'pay.value:currency',
			'value:currency',
			$searchModel->isNotPayed() ? 'pay.deadline_at:date' : 'pay.pay_at:date',
			[
				'class' => ActionColumn::class,
				'template' => '{update} {delete}',
			],
		],
	]); ?>


</div>
