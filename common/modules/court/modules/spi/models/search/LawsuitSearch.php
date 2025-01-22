<?php

namespace common\modules\court\modules\spi\models\search;

use common\modules\court\modules\spi\models\lawsuit\LawsuitViewIntegratorDto;
use common\modules\court\modules\spi\repository\LawsuitRepository;

/**
 * @see LawsuitViewIntegratorDto
 */
class LawsuitSearch extends SearchModel {

	public string $courtName = '';

	public string $signature = '';
	public string $subject = '';
	public string $repertoryName = '';
	public string $departmentName = '';

	public function __construct(LawsuitRepository $repository, string $appeal, array $config = []) {
		parent::__construct($repository, $appeal, $config);
	}

	public function rules(): array {
		return [
			[
				['courtName', 'signature', 'subject', 'repertoryName', 'departmentName'], 'string',
			],
		];
	}

	public function getApiParams(): array {
		return [
			'courtName.contains' => $this->courtName,
			'departmentName.contains' => $this->departmentName,
			'signature.contains' => $this->signature,
			'subject.contains' => $this->subject,
			'repertoryName.contains' => $this->repertoryName,
		];
	}
}
