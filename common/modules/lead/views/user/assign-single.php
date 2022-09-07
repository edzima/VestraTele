<?php

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\forms\LeadsUserForm;

/* @var $this \yii\web\View */
/* @var $model LeadsUserForm */
/* @var $lead ActiveLead */

$this->title = Yii::t('lead', 'Assign User to Lead');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => $lead->getName(), 'url' => ['lead/view', 'id' => $lead->getId()]];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>


<?= $this->render('_leads_form', [
	'model' => $model,
]) ?>
