<?php

use common\helpers\Html;
use common\modules\czater\entities\Client;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\data\DataProviderInterface;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider DataProviderInterface */

$this->title = Yii::t('czater', 'Clients');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'columns' => [
		'id',
		'idClient',
		'name',
		'email:email',
		'phone:tel',
		//'skype',
		'custom',
		//	'google_id',
		//	'facebook_id',
		//	'description',
		'blocked:boolean',
		'dateInitalize:datetime',
		'sessionCounter',
		//		'IP',
		//	'customFields',
		[
			'attribute' => 'firstReferer',
			'format' => 'raw',
			'value' => static function (Client $model): ?string {
				if (!empty($model->firstReferer)) {
					return Html::a(
						Html::encode($model->firstReferer),
						$model->firstReferer, [
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
