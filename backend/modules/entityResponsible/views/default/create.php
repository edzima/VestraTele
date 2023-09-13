<?php

use backend\modules\entityResponsible\models\EntityResponsibleForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model EntityResponsibleForm */

$this->title = Yii::t('backend', 'Create Entity Responsible');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Entities responsible'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-entity-responsible-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
