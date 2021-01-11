<?php

use backend\modules\issue\models\search\SummonSearch;
use backend\modules\issue\widgets\SummonGrid;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel SummonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Summons');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="summon-index">

	<p>
		<?= Html::a(Yii::t('backend', 'Create summon'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= SummonGrid::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
	]) ?>


</div>
