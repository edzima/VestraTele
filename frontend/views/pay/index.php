<?php

use common\models\user\User;
use frontend\helpers\Html;
use frontend\models\search\IssuePaySearch;
use frontend\widgets\IssuePayGrid;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel IssuePaySearch */
/* @var $dataProvider ActiveDataProvider */
/* @var $withNav bool */

$this->title = Yii::t('settlement', 'Pays');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['settlement/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="settlement-pay-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= $this->render('_nav', ['model' => $searchModel]) ?>
	</p>

	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= IssuePayGrid::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'showPageSummary' => Yii::$app->user->can(User::ROLE_ADMINISTRATOR),
		'visibleStatus' => !$searchModel->isPayed(),
		'rowColors' => !$searchModel->isPayed(),
		'payProvisionsRoute' => 'pay-provisions',
	]) ?>

</div>
