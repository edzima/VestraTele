<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\lead\models\LeadPhoneBlacklist $model */

$this->title = Yii::t('lead', 'Create Lead Phone Blacklist');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Phone Blacklists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-phone-blacklist-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
