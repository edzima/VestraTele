<?php

use common\models\issue\IssueInterface;
use yii\widgets\DetailView;

/* @var IssueInterface $model */

?>

<?= !empty($model->getIssueModel()->details)
	? DetailView::widget([
		'model' => $model->getIssueModel(),
		'attributes' => [
			'details:ntext',
		],

	])
	: ''
?>
