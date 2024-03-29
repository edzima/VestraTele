<?php

use common\models\provision\ProvisionReportSearch;
use common\models\user\Worker;
use common\widgets\provision\ProvisionUserReportWidget;
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
	'actionColumn' => [
		'visible' => false,
	],
]) ?>



