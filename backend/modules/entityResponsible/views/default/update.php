<?php

use backend\modules\entityResponsible\models\EntityResponsibleForm;

/* @var $this yii\web\View */
/* @var $model EntityResponsibleForm */

$this->title = Yii::t('backend', 'Update entity: {name}', [
	'name' => $model->getModel()->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Entities responsible'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->name, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="issue-entity-responsible-update">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
