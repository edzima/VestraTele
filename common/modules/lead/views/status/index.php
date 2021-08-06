<?php

use common\modules\lead\models\LeadStatus;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\lead\models\searches\LeadStatusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Statuses');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-status-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Create Lead Status'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'id',
			'name',
			'description',
			'short_report:boolean',
			'sort_index',
			[
				'attribute' => 'filterOptions.color',
				'value' => static function (LeadStatus $model): ?string {
					$color = Html::decode($model->getFilterOptions()->color);
					if (empty($color)) {
						return null;
					}
					return "<span class='badge' style='background-color: $color'> </span>  <code>" . $color . '</code>';
				},
				'format' => 'html',
			],

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
