<?php

namespace common\modules\court\modules\spi\entity\lawsuit;


use common\modules\court\modules\spi\Module;
use yii\base\Model;

class LawsuitViewIntegratorDto extends Model {

	public int $id;
	public string $signature;
	public int $number;
	public int $year;
	public string $subject;
	public ?string $value;
	public ?string $result;
	public ?string $receiptDate;
	public ?string $finishDate;
	public string $lastUpdate;
	public int $repertory;
	public string $repertoryName;
	public int $department;
	public string $departmentName;
	public int $court;
	public string $courtName;
	public ?int $judge;
	public ?string $judgeName;
	public string $partyName;
	public ?string $description;
	public ?int $partyUserId;

	public string $partyProfileUuid;
	public ?int $representedPartyId;
	public bool $visible;

	/** @var Party[] */
	public ?array $parties = []; // Lista obiektów Party

	public string $roleName;
	public string $groupName;
	public ?bool $isAttorneyToLawsuit;

	public string $publicationDate;
	public string $createdDate;
	public string $modificationDate;

	/** @var LightParty[] */
	public ?array $lightParties = []; // Lista obiektów LightParty

	/** @var LawsuitPartyDTO[] */
	protected array $lawsuitParties = [];

	public function attributeLabels(): array {
		return [
			'description' => Module::t('lawsuit', 'Description'),
			'signature' => Module::t('lawsuit', 'Signature'),
			'subject' => Module::t('lawsuit', 'Subject'),
			'number' => Module::t('lawsuit', 'Number'),
			'value' => Module::t('lawsuit', 'Value'),
			'receiptDate' => Module::t('lawsuit', 'Receipt Date'),
			'repertoryName' => Module::t('lawsuit', 'Repertory'),
			'departmentName' => Module::t('lawsuit', 'Department'),
			'courtName' => Module::t('lawsuit', 'Court'),
			'lastUpdate' => Module::t('lawsuit', 'Last Update'),
			'judgeName' => Module::t('lawsuit', 'Judge'),
			'result' => Module::t('lawsuit', 'Result'),
			'finishDate' => Module::t('lawsuit', 'Finish Date'),
		];
	}

	public function setLawsuitParties(array $lawsuitParties): void {
		$models = [];
		foreach ($lawsuitParties as $lawsuitParty) {
			if (is_array($lawsuitParty)) {
				$models[] = new LawsuitPartyDTO($lawsuitParty);
			}
		}
		$this->lawsuitParties = $models;
	}

	/**
	 * @return LawsuitPartyDTO[]
	 */
	public function getLawsuitParties(): array {
		return $this->lawsuitParties;
	}
}
