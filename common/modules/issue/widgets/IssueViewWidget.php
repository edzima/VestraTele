<?php

namespace common\modules\issue\widgets;

class IssueViewWidget extends IssueWidget {

	public $usersLinks = true;

	public function run(): string {
		return $this->render('issue-view', [
			'model' => $this->model,
			'usersLinks' => $this->usersLinks,
		]);
	}
}
