<?php

use common\models\issue\IssuePayCalculation;
use common\models\user\User;
use common\modules\issue\widgets\IssueNotesWidget;
use common\widgets\settlement\SettlementDetailView;
use frontend\helpers\Html;
use frontend\widgets\IssuePayGrid;
use yii\data\ActiveDataProvider;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $model IssuePayCalculation */

$this->title = Yii::t('settlement', 'Settlement {type}', ['type' => $model->getTypeName()]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/index']];
$this->params['breadcrumbs'][] = ['label' => $model->issue->longId, 'url' => ['/issue/view', 'id' => $model->getIssueId()]];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="settlement-view">

	<p>
		<?= !$model->isPayed() && Yii::$app->user->can(User::PERMISSION_CALCULATION_PAYS)
			? Html::a(Yii::t('settlement', 'Generate pays'), ['pays', 'id' => $model->id], ['class' => 'btn btn-primary'])
			: ''
		?>

		<?= Yii::$app->user->can(User::PERMISSION_NOTE)
			? Html::a(
				Yii::t('common', 'Create note'),
				['note/settlement', 'id' => $model->id],
				['class' => 'btn btn-info',])
			: ''
		?>
	</p>

	<?= SettlementDetailView::widget([
		'model' => $model,
		'userIdProvisions' => Yii::$app->user->getId(),
	]) ?>

	<?= IssuePayGrid::widget([
		'dataProvider' => new ActiveDataProvider([
			'query' => $model->getPays(),
		]),
		'visibleAgent' => $model->issue->agent->id !== Yii::$app->user->getId(),
		'visibleCustomer' => false,
		'caption' => Yii::t('settlement', 'Pays'),
		'summary' => '',
		'visibleIssueType' => false,
		'visibleProvisionsDetails' => $model->issue->isForUser(Yii::$app->user->getId())
			|| $model->issue->isForAgents(Yii::$app->userHierarchy->getAllChildesIds(Yii::$app->user->getId())),
		'visibleSettlementType' => false,
	])
	?>


	<?= IssueNotesWidget::widget([
		'model' => $model->issue,
		'notes' => $model->issue->getIssueNotes()->joinWith('user.userProfile')->onlySettlement($model->id)->all(),
		'type' => IssueNotesWidget::TYPE_SETTLEMENT,
	])
	?>

</div>
