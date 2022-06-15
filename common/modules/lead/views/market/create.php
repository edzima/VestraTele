<?php

use common\modules\lead\models\LeadMarket;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model LeadMarket */

$this->title = Yii::t('lead', 'Create Lead Market');

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Markets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-market-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
