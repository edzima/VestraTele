<?php

use backend\modules\issue\models\search\SummonSearch;
use backend\modules\issue\widgets\SummonGrid;
use common\models\user\Worker;
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

		<?= Html::a(Yii::t('issue', 'Calendar'), ['/calendar/summon-calendar/index'], ['class' => 'btn btn-primary']) ?>

		<?= Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)
			? Html::a(Yii::t('backend', 'Summon Types'), ['summon-type/index'], ['class' => 'btn btn-info'])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)
			? Html::a(Yii::t('backend', 'Summon Docs'), ['summon-doc/index'], ['class' => 'btn btn-warning'])
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
