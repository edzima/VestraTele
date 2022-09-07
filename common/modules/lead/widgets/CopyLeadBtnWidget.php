<?php

namespace common\modules\lead\widgets;

use common\helpers\Url;
use Yii;

class CopyLeadBtnWidget extends LeadTypeBtnWidget {

	public int $leadId;

	public string $routeItems = '/lead/lead/copy';
	public $options = [
		'class' => 'btn btn-warning',
	];

	public function init(): void {
		if ($this->label === 'Button') {
			$this->label = Yii::t('lead', 'Copy Lead');
		}
		parent::init();
	}

	protected function getItemUrl(int $typeId): string {
		return Url::toRoute([$this->routeItems, 'id' => $this->leadId, 'typeId' => $typeId]);
	}
}
