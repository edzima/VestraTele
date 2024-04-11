<?php

use backend\modules\user\models\WorkerUserForm;
use backend\modules\user\widgets\CopyToCliboardFormAttributesBtn;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model WorkerUserForm */
/* @var $form ActiveForm */

$this->title = Yii::t('backend', 'Update worker: {username}', ['username' => $model->username]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Workers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getFullName(), 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');

?>
<div class="user-worker-update">
	<p>
		<?= CopyToCliboardFormAttributesBtn::widget([
			'formModel' => $model,
		])
		?>
	</p>
	<div class="clearfix"></div>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
