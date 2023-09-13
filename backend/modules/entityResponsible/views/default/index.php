<?php

use backend\helpers\Html;
use backend\widgets\GridView;
use common\models\entityResponsible\EntityResponsibleSearch;
use common\widgets\grid\AddressColumn;

/* @var $this yii\web\View */
/* @var $searchModel EntityResponsibleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Entities responsible');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-entity-responsible-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a(Yii::t('backend', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'name',
			'details',
			[
				'class' => AddressColumn::class,
			],
			'is_for_summon:boolean',
			'issuesCount',
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
</div>
