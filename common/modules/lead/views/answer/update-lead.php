<?php

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\forms\MultipleAnswersForm;

/* @var $this yii\web\View */
/* @var $model MultipleAnswersForm */
/* @var $lead ActiveLead */

$this->title = Yii::t('lead', 'Update Leads Answers: {name}', [
	'name' => $lead->getName(),
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $lead->name, 'url' => ['lead/view', 'id' => $lead->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="leads-answers-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class="row">
		<div class="col-md-6">
			<?= $this->render('_lead-form', [
				'model' => $model,
			]) ?>
		</div>
	</div>

</div>
