<?php

use common\modules\lead\models\LeadCampaign;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadCampaign */

$this->title = Yii::t('lead', 'Create Lead Campaign');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Campaigns'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-campaign-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
