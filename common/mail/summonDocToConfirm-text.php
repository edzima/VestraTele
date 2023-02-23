<?php

use backend\helpers\Url;
use common\models\issue\SummonDocLink;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model SummonDocLink */
$issueLink = Url::issueView($model->summon->getIssueId(), true);

?>
<?= Yii::t('issue', 'User: {user} mark Doc: {name} to Confirm.', [
	'user' => $model->doneUser->getFullName(),
	'name' => $model->doc->name,
]) ?>

<?= $issueLink ?>
