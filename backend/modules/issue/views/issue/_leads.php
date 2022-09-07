<?php

use backend\helpers\Html;
use backend\widgets\GridView;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\widgets\LeadUsersColumn;
use kartik\grid\RadioColumn;
use yii\data\DataProviderInterface;
use yii\helpers\Json;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider DataProviderInterface */
/* @var $selectedLeadId int|null */
/* @var $leadInputId string */
/* @var $agentInputId string */
/* @var $teleInputId string */

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
						'radioOptions' => function (ActiveLead $model, $key, $index, $column) use ($selectedLeadId) {
							return [
								'value' => Json::encode($model->getUsers()),
								'checked' => $selectedLeadId !== null && $model->getId() === $selectedLeadId,
							];
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
    const leadGrid = jQuery('#leads-grid');
    const leadInput = document.getElementById('$leadInputId');
	const agentInput = jQuery('#$agentInputId');
	const teleinput = jQuery('#$teleInputId');

    
    leadGrid.on('grid.radiochecked', function(ev, key, value) {
		leadInput.value = key;
		if(value.length){
			const users = JSON.parse(value);
			if(users.owner){
				agentInput.val(users.owner);
				agentInput.trigger('change');
			}
			if(users.agent){
				agentInput.val(users.agent);
				agentInput.trigger('change');
			}
			if(users.telemarketer){
				teleinput.val(users.telemarketer);
				teleinput.trigger('change');
			}
		}
    });
     
    leadGrid.on('grid.radiocleared', function() {
		leadInput.value = null;
    }); 
JS;

$this->registerJs($script);
