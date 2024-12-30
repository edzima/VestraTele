<?php

namespace common\modules\court\modules\spi\models\application;

use yii\base\Model;

class ApplicationViewDTO extends Model implements
	ApplicationStatus,
	ApplicationType {

	public int $id; // Long id
	public string $profileUuid;
	public ?string $comments; // String comments
	public string $roleInLawsuit; // String roleInLawsuit
	public ?string $commentary; // String commentary
	public int $status; // Long status
	public string $registerDate; // Instant registerDate
	public ?string $considerationDate; // Instant considerationDate
	public ?string $commentaryForEmployee; // String commentaryForEmployee
	public ?string $represented; // String represented
	public ?string $complaintDate; // Instant complaintDate
	public ?string $complaintConsiderationDate; // Instant complaintConsiderationDate
	public string $court; // String court
	public ?int $courtId; // Long courtId
	public ?string $department; // String department
	public int $departmentId; // Long departmentId
	public string $signature; // String signature
	public int $lawsuitId; // Long lawsuitId
	public ?int $userId; // Long userId
	public string $applicant; // String applicant
	public ?string $unitNumber; // String unitNumber
	public ?string $name; // String name
	public string $referent; // String referent
	public ?int $signId; // Long signId

	public string $statusString;
	public string $type;
	public string $createdDate;
	public string $modificationDate;
	public ?array $attachments = [];

	public function getApplicationStatus(): int {
		return $this->status;
	}

	public function getApplicationType(): string {
		return $this->type;
	}
}
