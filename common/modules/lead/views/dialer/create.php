<?php

use common\modules\lead\models\forms\LeadDialerForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadDialerForm */

$this->title = Yii::t('lead', 'Create Lead Dialer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Dialers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-dialer-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
