<?php

use common\modules\lead\models\forms\LeadsUserForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadsUserForm */

$this->title = Yii::t('lead', 'Assign User to Leads');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-user-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_leads_form', [
		'model' => $model,
	]) ?>

</div>
