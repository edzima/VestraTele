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
		return $this->getIssueModel()->type;
	}

	public function getIssueStage(): IssueStage {
		return $this->getIssueModel()->stage;
	}

	public function getIssueModel(): Issue {
		return $this->{static::getIssueAttribute()};
	}

}
