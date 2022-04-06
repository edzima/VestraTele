<?php

namespace common\modules\czater\entities;

use common\modules\czater\Czater;
use yii\base\Model;

class Conv extends Model {

	public function __construct(Czater $owner, $config = []) {
		$this->owner = $owner;
		parent::__construct($config);
	}

	public int $id;
	public int $idOwner;
	public int $idConsultant;
	public ?string $consultantEmail;
	public int $idClient;
	public ?string $clientEmail;
	public ?string $dateBegin;
	public string $firstMessage;
	public ?string $referer;

	private ?Client $client;
	private Czater $owner;

	public function getClient(): Client {
		if ($this->client === null) {
			$this->client = $this->owner->getClient($this->idClient);
		}
		return $this->client;
	}

}
