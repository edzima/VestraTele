<?php

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


	<?= $duplicates !== null
		? DuplicateUserGridView::widget([
			'dataProvider' => $duplicates,
		])
		: ''
	?>


	<?= $this->render('_form', [
			'model' => $model,
		]
	) ?>

</div>
