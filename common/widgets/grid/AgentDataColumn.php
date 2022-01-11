<?php

namespace common\widgets\grid;

use common\models\AgentSearchInterface;
use common\widgets\GridView;
use kartik\select2\Select2;
use Yii;

class AgentDataColumn extends DataColumn {

	public $noWrap = true;

	public $attribute = 'agent_id';
	public $value = 'agent.fullName';
	public $width = '250px';

	public function init(): void {
		if (empty($this->label)) {
			$this->label = Yii::t('rbac', 'agent');
		}
		if (!isset($this->filterInputOptions['placeholder'])) {
			$this->filterInputOptions['placeholder'] = Yii::t('rbac', 'agent');
		}

		if (empty($this->filter)) {
			$filterModel = $this->grid->filterModel;
			if ($filterModel instanceof AgentSearchInterface) {
				$this->filter = $filterModel->getAgentsNames();
			}
		}
		if (empty($this->filterType)) {
			$this->filterType = GridView::FILTER_SELECT2;
		}

		if (empty($this->filterWidgetOptions)) {
			$this->filterWidgetOptions = [
				'options' => [
					'multiple' => true,
					'placeholder' => $this->label,
				],
				'size' => Select2::SIZE_SMALL,
				'showToggleAll' => false,
			];
		}
		parent::init();
	}
}
