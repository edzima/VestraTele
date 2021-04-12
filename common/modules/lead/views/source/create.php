<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\lead\models\LeadType */

$this->title = Yii::t('lead', 'Create Lead Source');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Sources'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-source-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
