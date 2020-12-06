<?php

namespace common\widgets\grid;

use common\models\issue\IssueInterface;
use common\models\issue\IssueType;
use Yii;

class IssueTypeColumn extends DataColumn {

	public function init(): void {
		if (empty($this->label)) {
			$this->label = Yii::t('common', 'Type');
		}
		if (empty($this->value)) {
			$this->value = static function (IssueInterface $model): string {
				return $model->getIssueType()->name;
			};
		}
		if (empty($this->filter)) {
			$this->filter = IssueType::getTypesNames();
		}

		$this->options['style'] = 'width:260px';

		parent::init();
	}
}
