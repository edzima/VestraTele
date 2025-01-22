<?php

namespace common\modules\court\modules\spi\models\search;

use common\modules\court\modules\spi\repository\NotificationsRepository;

class NotificationSearch extends SearchModel {

	public $read;
	public $courtName;
	public $signature;
	public string $type = '';
	public string $content = '';
	public $date;

	public function __construct(NotificationsRepository $repository, string $appeal, array $config = []) {
		parent::__construct($repository, $appeal, $config);
	}

	public function rules(): array {
		return [
			[['read'], 'boolean'],
			[['courtName', 'signature', 'type', 'content'], 'string'],
			[['date'], 'safe'],
		];
	}

	public function getApiParams(): array {
		return [
			'content.contains' => $this->content,
			'courtName.contains' => $this->courtName,
			'type.contains' => $this->type,
			'signature.equals' => $this->signature,
			'read.specified' => $this->read ? 'true' : 'false',
		];
	}
}
