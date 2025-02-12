<?php

namespace common\modules\court\modules\spi\entity\search;

use common\modules\court\modules\spi\Module;
use common\modules\court\modules\spi\repository\LawsuitRepository;
use common\modules\court\modules\spi\repository\NotificationsRepository;

class NotificationSearch extends SearchModel {

	public $fromAt;
	public $toAt;

	public $read;
	public $courtName;
	public $signature;
	public string $type = '';
	public string $content = '';
	public $date;
	public ?int $lawsuitId = null;
	public ?LawsuitRepository $lawsuitRepository = null;

	public function __construct(NotificationsRepository $repository, string $appeal, array $config = []) {
		parent::__construct($repository, $appeal, $config);
	}

	public function rules(): array {
		return [
			[['read'], 'boolean'],
			[['signature', 'type', 'content', 'courtName'], 'string'],
			[['date', 'fromAt', 'toAt'], 'safe'],
			[
				'signature', 'required', 'when' => function () {
				return !empty($this->courtName);
			},
			],
			[
				'courtName', 'required', 'when' => function () {
				return !empty($this->signature);
			},
			],
			[['signature', 'courtName'], 'trim'],
		];
	}

	public function attributeLabels(): array {
		return [
			'fromAt' => Module::t('common', 'From At'),
			'toAt' => Module::t('common', 'To At'),
			'courtName' => Module::t('notification', 'Court Name'),
			'signature' => Module::t('notification', 'Signature'),
		];
	}

	public function getApiParams(): array {
		if (!empty($this->lawsuitRepository) && (
				!empty($this->signature) && !empty($this->courtName)
			)) {
			$repository = $this->lawsuitRepository;
			$repository->setAppeal($this->appeal);
			$lawsuit = $repository->findBySignature($this->signature, $this->courtName, false);
			if ($lawsuit) {
				$this->lawsuitId = $lawsuit->id;
			}
		}
		$params = [
			'content.contains' => $this->content,
			'type.contains' => $this->type,
			'read.specified' => $this->read ? 'true' : 'false',
			'lawsuitId.equals' => $this->lawsuitId,
		];
		if (!empty($this->fromAt)) {
			$params['date.greaterOrEqualThan'] = date(DATE_ATOM, strtotime($this->fromAt));
		}
		if (!empty($this->toAt)) {
			$params['date.lessOrEqualThan='] = date(DATE_ATOM, strtotime($this->toAt));
		}
		return $params;
	}
}
