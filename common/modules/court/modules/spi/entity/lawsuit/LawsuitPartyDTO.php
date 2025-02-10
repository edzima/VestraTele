<?php

namespace common\modules\court\modules\spi\entity\lawsuit;

use common\modules\court\modules\spi\Module;
use yii\base\Model;

class LawsuitPartyDTO extends Model {

	public int $id;
	public ?string $role;
	public string $name;
	public ?string $address;
	public ?string $type;
	public ?int $priority;
	public ?int $parentId;
	public ?string $status;
	public ?string $dateFrom;
	public ?string $dateTo;

	public ?string $gainedAccessDate;
	protected array $representatives = [];
	public string $createdDate;
	public string $modificationDate;

	public function attributeLabels(): array {
		return [
			'role' => Module::t('lawsuit', 'Role'),
			'name' => Module::t('lawsuit', 'Name'),
			'address' => Module::t('lawsuit', 'Address'),
			'createdDate' => Module::t('lawsuit', 'Created Date'),
			'modificationDate' => Module::t('lawsuit', 'Modification Date'),
		];
	}

	public function setRepresentatives(array $representatives): void {
		$models = [];
		foreach ($representatives as $representative) {
			if (is_array($representative)) {
				$models[] = new static($representative);
			} elseif ($representative instanceof LawsuitPartyDTO) {
				$models[] = $representative;
			}
		}
		$this->representatives = $models;
	}

	/**
	 * @return static[]
	 */
	public function getRepresentatives(): array {
		return $this->representatives;
	}

}
