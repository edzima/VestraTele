<?php

namespace common\models\issue\search;

/**
 * Interface ArchivedIssueSearch
 *
 */
interface ArchivedIssueSearch {

	public function getWithArchive(): bool;

	public function getWithArchiveDeep(): bool;

}
