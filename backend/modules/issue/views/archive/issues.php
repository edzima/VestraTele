<?php

use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\widgets\grid\CustomerDataColumn;
use common\widgets\grid\IssueStageColumn;
use common\widgets\grid\IssueTypeColumn;
use common\widgets\grid\SerialColumn;
use yii\data\ActiveDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */

?>
<div class="issue-archive-issues">


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			['class' => SerialColumn::class,],
			[
				'class' => IssueColumn::class,
			],
			[
				'class' => CustomerDataColumn::class,
				'value' => 'issueModel.customer.fullName',
			],
			[
				'class' => IssueTypeColumn::class,
			],
			[
				'class' => IssueStageColumn::class,
				'valueType' => IssueStageColumn::VALUE_NAME,
			],
			[
				'attribute' => 'stage_change_at',
				'format' => 'date',
			],
		],
	]); ?>


</div>
