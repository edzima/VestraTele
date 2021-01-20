<?php

use common\models\issue\IssueInterface;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model IssueInterface */

?>

<div class="row">
	<div class="col-md-5 col-lg-4">
		<?= DetailView::widget([
			'model' => $model->getIssueModel(),
			'attributes' => [
				'customer.fullName:text:' . $model->getIssueModel()->getAttributeLabel('customer'),
				'type',
				'stage',
			],
		]) ?>

	</div>
</div>

