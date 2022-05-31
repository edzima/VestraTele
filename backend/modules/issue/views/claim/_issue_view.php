<?php

use common\models\issue\IssueInterface;
use yii\widgets\DetailView;

/* @var IssueInterface $model */

?>

<?= DetailView::widget([
	'model' => $model,
	'attributes' => [
		'details:ntext',
	],
]);
