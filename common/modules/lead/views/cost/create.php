<?php

use common\modules\lead\models\forms\LeadCostForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var LeadCostForm $model */

$this->title = Yii::t('lead', 'Create Lead Cost');
$this->params['breadcrumbs'][] = ['url' => ['lead/index'], 'label' => Yii::t('lead', 'Leads')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Costs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-cost-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
