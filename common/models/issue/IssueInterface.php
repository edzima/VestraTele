<?php

namespace common\models\issue;

interface IssueInterface {

	public function getIssueId(): int;

	public function getIssueName(): string;

	public function getIssueModel(): Issue;

	public function getIssueType(): IssueType;

	public function getIssueStage(): IssueStage;

	public function getIssueStageId(): int;

	public function getIssueTypeId(): int;

	public function getArchivesNr(): ?string;

}
