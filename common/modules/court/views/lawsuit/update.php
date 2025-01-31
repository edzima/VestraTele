<?php

use common\modules\court\models\LawsuitIssueForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var LawsuitIssueForm $model */

$this->title = Yii::t('court', 'Update Lawsuit: {name}', [
	'name' => $model->getModel()->getName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('court', 'Lawsuits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getName(), 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('court', 'Update');
?>
<div class="court-hearing-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
		'issue' => null,
	]) ?>

</div>
