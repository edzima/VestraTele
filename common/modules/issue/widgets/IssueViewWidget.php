<?php

namespace common\modules\issue\widgets;

class IssueViewWidget extends IssueWidget {

	public function run(): string {
		return $this->render('issue-view', ['model' => $this->model]);
	}
}
