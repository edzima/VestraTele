<?php

use common\modules\court\modules\spi\entity\lawsuit\LawsuitViewIntegratorDto;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var LawsuitViewIntegratorDto $model */

?>
<div class="court-lawsuit-spi-details-view">
	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'subject',
			'description:ntext',
			'receiptDate:date',
			[
				'attribute' => 'finishDate',
				'format' => 'date',
				'visible' => !empty($model->finishDate),
			],
			'departmentName',
			'judgeName',
			'value',
		],
	]) ?>
</div>
