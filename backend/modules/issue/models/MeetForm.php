<?php

namespace backend\modules\issue\models;

use common\models\issue\IssueMeet;
use common\models\meet\MeetForm as BaseMeetForm;

/**
 * Class MeetForm for backend App.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class MeetForm extends BaseMeetForm {

	public $createdAt;
	public $campaignId;

	public function init(): void {
		parent::init();
		$this->createdAt = date(DATE_ATOM);
	}

	public function rules(): array {
		return array_merge(parent::rules(), [
			[['agentId', 'campaignId'], 'required'],
			[['campaignId', 'agentId'], 'integer'],
			[['createdAt'], 'safe'],
			[['createdAt'], 'date', 'format' => 'yyyy-MM-dd HH:mm'],
			['agentId', 'in', 'range' => array_keys(static::getAgentsNames())],
			['campaignId', 'in', 'range' => array_keys(static::getCampaignNames())],
		]);
	}

	public function setModel(IssueMeet $model): void {
		parent::setModel($model);

		$this->createdAt = $model->created_at ?: date(DATE_ATOM);
		$this->campaignId = $model->campaign_id;
	}

	protected function setModelValues(IssueMeet $model): void {
		parent::setModelValues($model);
		$model->campaign_id = $this->campaignId;
		$model->created_at = $this->createdAt;
	}

	public function attributeLabels(): array {
		return parent::attributeLabels()
			+ [
				'campaignId' => 'Kampania',
				'createdAt' => 'Data leada',
			];
	}

}
