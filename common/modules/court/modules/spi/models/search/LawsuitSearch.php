<?php

namespace common\modules\court\modules\spi\models\search;

use common\modules\court\modules\spi\models\lawsuit\LawsuitViewIntegratorDto;
use common\modules\court\modules\spi\repository\LawsuitRepository;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;

/**
 * @see LawsuitViewIntegratorDto
 */
class LawsuitSearch extends LawsuitViewIntegratorDto {

	public string $courtName = '';

	public string $signature = '';
	public string $subject = '';
	public string $repertoryName = '';
	public string $departmentName = '';

	private LawsuitRepository $repository;

	public function __construct(LawsuitRepository $repository, array $config = []) {
		$this->repository = $repository;
		parent::__construct($config);
	}

	public function rules(): array {
		return [
			[
				['courtName', 'signature', 'subject', 'repertoryName', 'departmentName'], 'string',
			],
		];
	}

	public function search(array $params = []): DataProviderInterface {
		$this->load($params);
		if (!$this->validate()) {
			Yii::warning($this->errors, __METHOD__);
			return new ArrayDataProvider([]);
		}
		return $this->repository->getLawsuits($this->getApiParams());
	}

	public function getApiParams(): array {
		$params = [
			'courtName.contains' => $this->courtName,
			'departmentName.contains' => $this->departmentName,
			'signature.contains' => $this->signature,
			'subject.contains' => $this->subject,
			'repertoryName.contains' => $this->repertoryName,
		];
		return array_filter($params, function ($value) {
			return !empty($value);
		});
	}
}
