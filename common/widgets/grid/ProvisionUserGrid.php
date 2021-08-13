<?php

namespace common\widgets\grid;

use common\helpers\Html;
use common\models\provision\Provision;
use common\models\user\User;
use common\widgets\GridView;
use Yii;

class ProvisionUserGrid extends GridView {

	public $summary = '';
	public ?bool $withLink = null;

	public function init(): void {
		if ($this->withLink === null) {
			$this->withLink = Yii::$app->user->can(User::PERMISSION_PROVISION);
		}

		if (empty($this->columns)) {
			$this->columns = $this->defaultColumns();
		}
		parent::init();
	}

	public function defaultColumns(): array {
		return [
			'type.name',
			[
				'attribute' => 'toUser',
				'format' => 'raw',
				'value' => function (Provision $provision): string {
					$value = Html::encode($provision->toUser->getFullName());
					if (!$this->withLink) {
						return $value;
					}

					return Html::a($value,
						['/provision/user/user-view', 'userId' => $provision->to_user_id, 'typeId' => $provision->type_id], [
							'target' => '_blank',
						]);
				},
			],
			[
				'attribute' => 'fromUserString',
				'format' => 'raw',
				'value' => function (Provision $provision): string {
					$value = Html::encode($provision->getFromUserString());
					if (!$this->withLink) {
						return $value;
					}
					return Html::a($value,
						['/provision/user/user-view', 'userId' => $provision->from_user_id, 'typeId' => $provision->type_id], [
							'target' => '_blank',
						]);
				},
			],
			'provision',
			'value:currency',
		];
	}
}
