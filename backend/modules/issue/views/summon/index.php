<?php

use backend\modules\issue\models\search\SummonSearch;
use backend\modules\issue\widgets\SummonGrid;
use yii\helpers\Html;
use common\models\user\Worker;

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
		<?= Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)
			? Html::a(Yii::t('backend', 'Summon Types'), ['summon-type/index'], ['class' => 'btn btn-info'])
			: ''
		?>

	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= SummonGrid::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'valueType' => SummonGrid::VALUE_TYPE_NAME_SHORT,
	]) ?>


</div>
