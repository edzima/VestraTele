<?php

use backend\modules\user\models\search\UserTraitSearch;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel UserTraitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'User Traits');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['customer/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-trait-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('backend', 'Create User Trait'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'name',
			'show_on_issue_view:boolean',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
