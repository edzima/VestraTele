<?php

use backend\helpers\Html;
use backend\widgets\GridView;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\widgets\LeadUsersColumn;
use kartik\grid\RadioColumn;
use yii\data\DataProviderInterface;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider DataProviderInterface */
/* @var $leadInputId string */

?>
	<div class="issue-leads">
		<?= GridView::widget([
				'id' => 'leads-grid',
				'dataProvider' => $dataProvider,
				'panel' => [
					'type' => GridView::TYPE_WARNING,
					'before' => false,
					'heading' => Yii::t('lead', 'Leads'),
					'after' => false,
					'footer' => false,
				],
				'columns' => [

					[
						'class' => RadioColumn::class,
						'radioOptions' => function ($model, $key, $index, $column) {
							return ['value' => $model->name];
						},
					],
					[
						'attribute' => 'lead_id',
						'value' => static function (ActiveLead $lead): string {
							return Html::a(Html::encode($lead->getName()), ['/lead/lead/view', 'id' => $lead->getId()]);
						},
						'format' => 'html',
					],
					'statusName',
					'typeName',
					'leadSource.name',
					'date_at:date',
					[
						'class' => LeadUsersColumn::class,
					],
				],
			]
		) ?>
	</div>
<?php

$script = <<<JS
    const leadGrid = $('#leads-grid');
    const leadInput = document.getElementById('$leadInputId');
    
    leadGrid.on('grid.radiochecked', function(ev, key, val) {
		//@todo maybe select Agent from prepared Value. 
		leadInput.value = key;
    });
     
    leadGrid.on('grid.radiocleared', function() {
		leadInput.value = null;
    }); 
JS;

$this->registerJs($script);
