<?php

namespace common\modules\issue\widgets;

class IssueViewWidget extends IssueWidget {

	public bool $usersLinks = true;
	public bool $userMailVisibilityCheck = false;
	public bool $relationActionColumn = true;

	public function run(): string {
		return $this->render('issue-view', [
			'model' => $this->model,
			'userMailVisibilityCheck' => $this->userMailVisibilityCheck,
			'usersLinks' => $this->usersLinks,
			'relationActionColumn' => $this->relationActionColumn,
		]);
	}
}
