<?php

use common\models\issue\IssueCost;
use common\models\issue\IssueInterface;
use common\models\provision\ProvisionReportSearch;
use common\models\user\Worker;
use common\widgets\grid\DataColumn;
use common\widgets\provision\ProvisionUserReportWidget;
use frontend\helpers\Url;
use frontend\widgets\ChildesSelect2Widget;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;

/* @var $this View */
/* @var $searchModel ProvisionReportSearch */

if ($searchModel->to_user_id === Yii::$app->user->getId()) {
	$this->title = Yii::t('provision', 'Provisions Report ({from} - {to})', [
		'from' => Yii::$app->formatter->asDate($searchModel->dateFrom),
		'to' => Yii::$app->formatter->asDate($searchModel->dateTo),
	]);
} else {
	$this->title = Yii::t('provision',
		'Provisions Report: {user} ({from} - {to})', [
			'user' => $searchModel->toUser->getFullName(),
			'from' => Yii::$app->formatter->asDate($searchModel->dateFrom),
			'to' => Yii::$app->formatter->asDate($searchModel->dateTo),
		]);
}
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php if (Yii::$app->user->can(Worker::PERMISSION_PROVISION_CHILDREN_VISIBLE)): ?>
	<p>
		<?= ChildesSelect2Widget::widget([
			'id' => 'childes-select',
			'name' => 'Childes',
			'redirectGetParam' => 'user_id',
		]) ?>
	</p>
<?php endif; ?>
<?= $this->render('_search', ['model' => $searchModel]) ?>

<?= ProvisionUserReportWidget::widget([
	'model' => $searchModel->summary(),
	'issueColumn' => [
		'class' => DataColumn::class,
		'attribute' => 'issue_id',
		'format' => 'html',
		'value' => function ($model): ?string {
			$issue = null;
			if ($model instanceof IssueInterface) {
				$issue = $model->getIssueModel();
			}
			if ($model instanceof IssueCost) {
				$issue = $model->issue;
			}
			if ($issue === null) {
				return null;
			}
			return Html::a($issue->getIssueName(), Url::issueView($issue->getIssueId()));
		},

	],
	'actionColumn' => [
		'visible' => false,
	],
]) ?>



