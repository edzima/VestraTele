<?php

use backend\helpers\Url;
use backend\modules\issue\models\search\SummonSearch;
use backend\modules\issue\widgets\SummonGrid;
use common\models\user\Worker;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel SummonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Summons');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
if ($searchModel->getIssueParentType()) {
	$this->params['breadcrumbs'][] = ['label' => $searchModel->getIssueParentType()->name, 'url' => Url::issuesParentType($searchModel->issueParentTypeId)];
}

$this->params['breadcrumbs'][] = $this->title;

$this->params['issueParentTypeNav'] = [
	'route' => ['/issue/summon/index'],
];

?>
<div class="summon-index">

	<p>
		<?= Html::a(Yii::t('backend', 'Create summon'), ['create'], ['class' => 'btn btn-success']) ?>


		<?= Html::a(Yii::t('backend', 'Summon Docs'), ['summon-doc-link/to-do'], ['class' => 'btn btn-warning']) ?>

		<?= Html::a(Yii::t('issue', 'Calendar'), ['/calendar/summon-calendar/index', 'parentTypeId' => $searchModel->issueParentTypeId], ['class' => 'btn btn-primary']) ?>

		<?= Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)
			? Html::a(Yii::t('backend', 'Summon Types'), ['summon-type/index'], ['class' => 'btn btn-info'])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)
			? Html::a(Yii::t('issue', 'Summon Docs Types'), ['summon-doc/index'], ['class' => 'btn btn-info'])
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
