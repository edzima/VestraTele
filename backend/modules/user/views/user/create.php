<?php

use backend\modules\user\models\UserForm;
use backend\modules\user\widgets\DuplicateUserGridView;

/* @var $this yii\web\View */
/* @var $model UserForm */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('backend', 'Create user');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">


	<?= $model->getDuplicatesDataProvider() !== null
		? DuplicateUserGridView::widget([
			'dataProvider' => $model->getDuplicatesDataProvider(),
		])
		: ''
	?>


	<?= $this->render('_form', [
			'model' => $model,
		]
	) ?>


</div>
