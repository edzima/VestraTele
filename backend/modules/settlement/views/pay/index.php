<?php

use backend\modules\settlement\models\search\IssuePaySearch;
use backend\modules\settlement\widgets\IssuePayGrid;
use backend\widgets\CsvForm;
use common\models\user\User;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel IssuePaySearch */
/* @var $dataProvider ActiveDataProvider */
/* @var $withNav bool */

$this->title = Yii::t('settlement', 'Pays');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['/settlement/calculation/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="settlement-pay-index">

	<p>
		<?= $this->render('_nav', ['model' => $searchModel]) ?>
	</p>

	<?= $this->render('_search', ['model' => $searchModel]) ?>
	<?= Yii::$app->user->can(User::PERMISSION_EXPORT)
		? CsvForm::widget()
		: ''
	?>

	<?= Yii::$app->user->can(User::ROLE_ADMINISTRATOR) && $dataProvider->totalCount > 0
		? $this->render('_summary', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		])
		: ''
	?>

	<?= IssuePayGrid::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'showPageSummary' => Yii::$app->user->can(User::ROLE_ADMINISTRATOR),
		'visibleStatus' => !$searchModel->isPayed(),
		'rowColors' => !$searchModel->isPayed(),
	]) ?>

</div>
