<?php

namespace common\modules\lead\widgets;

use common\helpers\Url;
use common\modules\lead\models\ActiveLead;
use Yii;

class CopyLeadBtnWidget extends LeadTypeBtnWidget {

	public ActiveLead $lead;

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
		return Url::toRoute([
			$this->routeItems,
			'id' => $this->lead->getId(),
			'hash' => $this->lead->getHash(),
			'typeId' => $typeId,
		]);
	}
}
