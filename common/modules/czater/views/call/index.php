<?php

use common\helpers\Html;
use common\modules\czater\entities\Call;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\data\ArrayDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ArrayDataProvider */

$this->title = Yii::t('czater', 'Calls');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'columns' => [
		'clientFullNumber',
		'clientName',
		'duration',
		'statusName',
		'dateRequested:datetime',
		'consultantName',
		'consultantNumber',
		'dateStart:datetime',
		'dateFinish:datetime',
		[
			'attribute' => 'referer',
			'format' => 'raw',
			'value' => function (Call $model): ?string {
				if (!empty($model->referer)) {
					return Html::a(
						Html::encode($model->referer),
						$model->referer, [
							'data-target' => '_blank',
						]
					);
				}
				return null;
			},
		],
		[
			'class' => ActionColumn::class,
			'template' => '{view}',
		],
	],
]) ?>
