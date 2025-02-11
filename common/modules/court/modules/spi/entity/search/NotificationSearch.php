<?php

namespace common\modules\court\modules\spi\entity\search;

use common\modules\court\modules\spi\components\LawsuitSignature;
use common\modules\court\modules\spi\repository\LawsuitRepository;
use common\modules\court\modules\spi\repository\NotificationsRepository;

class NotificationSearch extends SearchModel {

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
			[['signature', 'type', 'content'], 'string'],
			[['date'], 'safe'],
			['signature', 'trim'],
			['signature', 'match', 'pattern' => LawsuitSignature::DEFAULT_PATTERN],
		];
	}

	public function getApiParams(): array {
		if (!empty($this->signature) && !empty($this->lawsuitRepository)) {
			$repository = $this->lawsuitRepository;
			$repository->setAppeal($this->appeal);
			$lawsuit = $repository->findBySignature($this->signature);
			if ($lawsuit) {
				$this->lawsuitId = $lawsuit->id;
			}
		}
		return [
			'content.contains' => $this->content,
			'type.contains' => $this->type,
			'read.specified' => $this->read ? 'true' : 'false',
			'lawsuitId.equals' => $this->lawsuitId,
		];
	}
}
