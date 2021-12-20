<?php

use common\models\issue\IssueInterface;
use frontend\helpers\Breadcrumbs;
use frontend\models\SummonForm;

/* @var $this yii\web\View */
/* @var $issue IssueInterface */
/* @var $model SummonForm */

$this->title = Yii::t('issue', 'Create Summon');
$this->params['breadcrumbs'] = Breadcrumbs::issue($issue);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="summon-create">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
