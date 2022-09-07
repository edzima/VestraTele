<?php

namespace console\jobs;

use common\models\message\IssueSmsForm;

class IssueSmsSendJob extends SmsSendJob {

	public int $issue_id;
	public int $owner_id;
	public string $note_title;

	public function run(): string {
		$model = IssueSmsForm::createFromJob($this);
		$smsId = parent::run();
		$model->note($smsId);
		return $smsId;
	}

}
