<?php

use backend\helpers\Breadcrumbs;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssueClaim */

$this->title = Yii::t('issue', 'Update Issue Claim: {name}', [
	'name' => $model->getTypeWithEntityName(),
]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->issue);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Claims'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="issue-claim-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
		'onlyField' => false,
	]) ?>

</div>
