<?php

use common\modules\lead\models\LeadPhoneBlacklist;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\modules\lead\models\searches\LeadPhoneBlacklistSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];

$this->title = Yii::t('lead', 'Lead Phone Blacklists');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-phone-blacklist-index">

	<h1><?= Html::encode($this->title) ?></h1>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			'phone',
			'created_at:datetime',
			'user',
			[
				'class' => ActionColumn::class,
				'template' => '{delete}',
				'urlCreator' => function ($action, LeadPhoneBlacklist $model) {
					return Url::toRoute([$action, 'phone' => $model->phone]);
				},
			],
		],
	]); ?>


</div>
