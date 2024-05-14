<?php

use common\modules\lead\models\forms\LeadsUserForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadsUserForm */

$this->title = Yii::t('lead', 'Assign Campaign to Leads: {count}', [
	'count' => count($model->leadsIds),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Campaigns'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-user-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_assign_form', [
		'model' => $model,
	]) ?>

</div>
