<?php

use backend\modules\user\models\CustomerUserForm;


/* @var $this yii\web\View */
/* @var $model CustomerUserForm */

$this->title = Yii::t('backend', 'Create customer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="customer-create">

	<?= $this->render('_form',[
			'model' => $model
		]
	)?>

</div>
