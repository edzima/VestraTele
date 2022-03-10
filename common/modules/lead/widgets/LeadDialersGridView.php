<?php

namespace common\modules\lead\widgets;

use common\models\user\User;
use common\modules\lead\models\Lead;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;

class LeadDialersGridView extends GridView {

	public ?Lead $lead = null;
	public $showOnEmpty = false;
	public $emptyText = '';

	public function init(): void {
		if (empty($this->dataProvider)) {
			if (empty($this->lead)) {
				throw new InvalidConfigException('$lead must be set when dataProvider is null.');
			}
			$this->dataProvider = new ActiveDataProvider([
				'query' => $this->lead->getDialers(),
			]);
		}
		if (empty($this->caption)) {
			$this->caption = Yii::t('lead', 'Dialers');
		}
		if (empty($this->columns)) {
			$this->columns = $this->defaultColumns();
		}

		parent::init();
	}

	protected function defaultColumns(): array {
		return [
			[
				'attribute' => 'type.name',
				'label' => Yii::t('lead', 'Type'),
			],
			'statusName',
			'dialerStatusName',
			'last_at:datetime',
			[
				'class' => ActionColumn::class,
				'controller' => '/lead/dialer',
				'visible' => Yii::$app->user->can(User::PERMISSION_LEAD_DIALER_MANAGER),
			],
		];
	}

}
