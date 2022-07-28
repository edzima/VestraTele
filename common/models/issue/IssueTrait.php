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

	public function getIssueTypeId(): int {
		return $this->getIssueModel()->type_id;
	}

	public function getIssueType(): IssueType {
		return $this->getIssueModel()->type;
	}

	public function getIssueStageId(): int {
		return $this->getIssueModel()->stage_id;
	}

	public function getIssueStage(): IssueStage {
		return $this->getIssueModel()->stage;
	}

	public function getIssueModel(): Issue {
		return $this->{static::getIssueAttribute()};
	}

}
