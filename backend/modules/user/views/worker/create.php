<?php

use backend\helpers\Html;
use backend\modules\user\models\WorkerUserForm;
use backend\modules\user\widgets\DuplicateUserGridView;

/* @var $this yii\web\View */
/* @var $model WorkerUserForm */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('backend', 'Create worker');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Workers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$duplicates = $model->getDuplicatesDataProvider();
if ($duplicates) {
	$duplicates->query->workers();
}
?>
<div class="user-create user-worker-create">

	<p>
		<?= Html::a(Html::icon('paste'), ['create-from-json'], [
			'class' => 'btn btn-sm btn-success',
			'title' => Yii::t('backend', 'Load from String'),
			'aria-label' => Yii::t('backend', 'Load from String'),
		]) ?>

	</p>


	<?= $duplicates !== null
		? DuplicateUserGridView::widget([
			'dataProvider' => $duplicates,
			'actionController' => '/user/worker',
		])
		: ''
	?>


	<?= $this->render('_form', [
			'model' => $model,
		]
	) ?>

</div>
