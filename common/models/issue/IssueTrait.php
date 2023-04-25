<?php

namespace common\models\issue;

trait IssueTrait {

	public static function getIssueIdAttribute(): string {
		return 'issue_id';
	}

	public static function getIssueAttribute(): string {
		return 'issue';
	}

	public function getIssueId(): int {
		return $this->{static::getIssueIdAttribute()};
	}

	public function getIssueName(): string {
		return $this->getIssueModel()->longId;
	}

	public function getIssueType(): IssueType {
		return IssueType::get($this->getIssueTypeId());
	}

	public function getIssueTypeId(): int {
		return $this->getIssueModel()->type_id;
	}

	public function getIssueStage(): IssueStage {
		return IssueStage::get($this->getIssueStageId());
	}

	public function getIssueStageId(): int {
		return $this->getIssueModel()->stage_id;
	}

	public function getArchivesNr(): ?string {
		return $this->getIssueModel()->archives_nr;
	}

	public function getIssueModel(): Issue {
		return $this->{static::getIssueAttribute()};
	}

}
