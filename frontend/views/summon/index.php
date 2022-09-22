<?php

use frontend\models\search\SummonSearch;
use frontend\widgets\SummonGrid;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel SummonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Summons');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="summon-index">

	<h1><?= Html::encode($this->title) ?></h1>


	<p>
		<?= Html::a(Yii::t('issue', 'Calendar'), ['summon-calendar/index'], [
			'class' => 'btn btn-success',
		]) ?>
	</p>

	<?= SummonGrid::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
	]) ?>


</div>
