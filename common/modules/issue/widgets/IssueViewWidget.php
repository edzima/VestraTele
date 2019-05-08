<?php

namespace common\modules\issue\widgets;


class IssueViewWidget extends IssueWidget {

	public function run() {
		return $this->render('issue-view', ['model' => $this->model]);
	}
}