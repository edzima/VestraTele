<?php

namespace common\modules\court\modules\spi\models\lawsuit;

use common\modules\court\modules\spi\Module;
use yii\base\Model;

class LawsuitDetailsDto extends Model {

	public int $id;
	public string $signature; // sygnatura
	public string $receiptDate; // data otrzymania
	public ?string $finishDate; // data zakończenia
	public string $settlement; // roztrzygnięcie
	public string $subject; // przedmiot sprawy
	public string $fileName; // nazwa pliku
	public string $judge; // sędzia
	public string $value; // wartość
	public string $repertory; // repertorium
	public string $department; // wydział
	public string $parties; // strony postępowania
	public string $court; // sąd
	public string $description; // opis
	public int $partyId; // id stron postępowania
	public bool $eprotocolEnabled; // czy jest dostępny e-protokół

	public string $publicationDate;
	public string $createdDate; // data utworzenia rekordu sprawy
	public string $modificationDate; // ostatnia data modyfikacji rekordu sprawy

	public function attributeLabels(): array {
		return [
			'signature' => Module::t('lawsuit', 'Signature'),
			'subject' => Module::t('lawsuit', 'Subject'),
			'number' => Module::t('lawsuit', 'Number'),
			'value' => Module::t('lawsuit', 'Value'),
			'receiptDate' => Module::t('lawsuit', 'Receipt Date'),
			'repertory' => Module::t('lawsuit', 'Repertory'),
			'department' => Module::t('lawsuit', 'Department'),
			'court' => Module::t('lawsuit', 'Court'),
			'judge' => Module::t('lawsuit', 'Judge'),
			'finishDate' => Module::t('lawsuit', 'Finish Date'),
		];
	}
}
