<?php

use backend\helpers\Url;
use backend\modules\issue\models\PayCalculationForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model PayCalculationForm */

$this->title = 'Nowe rozliczenie';

$this->params['breadcrumbs'][] = ['label' => 'Sprawy', 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getIssue(), 'url' => Url::issueView($model->getIssue()->id)];
$this->params['breadcrumbs'][] = ['label' => 'Rozliczenia', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-pay-calculation-create">


	<h1>Sprawa: <?= Html::a(
			$model->getIssue(),
			Url::issueView($model->getIssue()->id),
			['target' => '_blank']) ?>
	</h1>


	<?= $this->render('_form', [
		'model' => $model,
	]) ?>


</div>
