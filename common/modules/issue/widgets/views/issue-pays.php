<?php

use common\models\issue\IssuePay;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $models IssuePay[] */
/* @var $notesOptions array */
?>

<fieldset>
	<legend>Wpłaty
		<button class="btn toggle pull-right" data-toggle="#pays-details">
			<i class="glyphicon glyphicon-chevron-down"></i></button>
	</legend>
	<div id="pays-details">
		<?php
		foreach ($models as $key => $pay) {
			echo DetailView::widget([
				'model' => $pay,
				'options' => [
					'class' => 'table table-striped table-bordered detail-view th-nowrap',
				],
				'attributes' => [
					'pay_at:date',
					'deadline_at:date',
					[
						'attribute' => 'typeName',
						'label' => 'Typ',
						'format' => 'raw',
					],
					[
						'attribute' => 'transferTypeName',
						'label' => 'Płatność',
						'format' => 'raw',
					],
					'value:decimal',
				],
			]);
		}
		?>
	</div>
</fieldset>