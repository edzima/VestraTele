<?php

namespace common\models\issue;

interface LinkedIssuesModel {

	public function getLinkedIssuesNames(): array;

	public function getLinkedIssuesIds(): array;

	public function saveLinkedIssues(): ?int;
}
