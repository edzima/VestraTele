<?php

namespace common\modules\lead\widgets;

use common\modules\lead\models\LeadUser;
use common\widgets\grid\DataColumn;
use Yii;
use yii\helpers\Html;

class LeadUsersColumn extends DataColumn {

	public $attribute = 'leadUsers';

	public $format = 'html';

	public ?array $types;

	public function init() {
		parent::init();
		if ($this->label === null) {
			$this->label = Yii::t('lead', 'Lead Users');
		}
	}

	function getDataCellValue($model, $key, $index) {
		$users = parent::getDataCellValue($model, $key, $index);
		if (empty($users)) {
			return null;
		}
		return $this->renderUsers($users);
	}

	/**
	 * @param LeadUser[] $leadUsers
	 * @return string
	 */
	private function renderUsers(array $leadUsers): string {
		$content = [];
		foreach ($leadUsers as $leadUser) {
			if ($this->userShouldRender($leadUser)) {
				$content[] = $this->renderUser($leadUser);
			}
		}
		return implode(', ', $content);
	}

	private function renderUser(LeadUser $leadUser): string {
		if ($leadUser->isOwner()) {
			return '<strong>' . Html::encode($leadUser->getUserName()) . '</strong>';
		}
		return Html::encode($leadUser->getUserWithTypeName());
	}

	private function userShouldRender(LeadUser $leadUser): bool {
		return empty($this->types) || in_array($leadUser->type, $this->types, true);
	}
}
