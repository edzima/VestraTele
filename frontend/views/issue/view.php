<?php

use common\models\issue\Issue;
use common\models\user\User;
use common\modules\issue\widgets\IssueNotesWidget;
use common\modules\issue\widgets\IssueViewWidget;
use frontend\helpers\Html;
use frontend\widgets\IssuePayCalculationGrid;
use frontend\widgets\SummonGrid;
use yii\data\DataProviderInterface;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $calculationsDataProvider DataProviderInterface|null */
/* @var $summonDataProvider DataProviderInterface|null */

$this->title = $model->longId;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-view">


	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Yii::$app->user->can(User::PERMISSION_NOTE)
			? Html::a(Yii::t('common', 'Create note'), ['/note/issue', 'id' => $model->id], [
				'class' => 'btn btn-info',
			])
			: ''
		?>
	</p>

	<?= $calculationsDataProvider !== null
	&& $calculationsDataProvider->getTotalCount() > 0
		? IssuePayCalculationGrid::widget([
			'dataProvider' => $calculationsDataProvider,
			'withIssue' => false,
			'withAgent' => false,
			'withCaption' => true,
			'withIssueType' => false,
			'withCustomer' => false,
			'withDates' => false,
			'userProvisionsId' => Yii::$app->user->getId(),
		])
		: ''
	?>

	<?= IssueViewWidget::widget([
		'model' => $model,
		'usersLinks' => false,
	]) ?>

	<?= $summonDataProvider->getTotalCount() > 0
		? SummonGrid::widget([
			'dataProvider' => $summonDataProvider,
			'summary' => '',
			'withCaption' => true,
			'withIssue' => false,
			'withCustomer' => false,
			'withOwner' => false,
			'withContractor' => true,
			'withUpdatedAt' => false,
		])
		: ''
	?>

	<?= IssueNotesWidget::widget([
		'model' => $model,
		'noteOptions' => [
			'removeBtn' => false,
		],
	]) ?>
</div>
