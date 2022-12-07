<?php

use backend\modules\user\models\CustomerUserForm;
use backend\modules\user\widgets\DuplicateUserGridView;

/* @var $this yii\web\View */
/* @var $model CustomerUserForm */

$this->title = Yii::t('backend', 'Create customer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="customer-create">


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
