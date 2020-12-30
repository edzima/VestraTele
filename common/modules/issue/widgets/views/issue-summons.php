<?php

use common\models\issue\Issue;
use common\models\issue\Summon;
use common\widgets\GridView;
use kartik\grid\ActionColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $baseUrl string */
/* @var $addBtn bool */
/* @var $editBtn bool */
/* @var $dataProvider ActiveDataProvider */
/* @var $actionColumnTemplate string */

?>

<div id="summons-details">
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'caption' => Yii::t('common', 'Summons'),
		'summary' => '',
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			'typeName',
			'statusName',
			'termName',
			'title',
			'start_at:date',
			'realized_at:datetime',
			'deadline:date',
			'contractor',
			[
				'class' => ActionColumn::class,
				'urlCreator' => function (string $action, Summon $model) use ($baseUrl) {
					return Url::to([$baseUrl . $action, 'id' => $model->id]);
				},
				'template' => $actionColumnTemplate,
			],
		],
	]) ?>
</div>
