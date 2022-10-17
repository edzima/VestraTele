<?php

namespace frontend\models;

use common\models\issue\IssueNote;
use common\models\issue\IssueNoteForm as BaseIssueNoteForm;

/**
 * IssueNoteForm for Frontend App.
 */
class IssueNoteForm extends BaseIssueNoteForm {

	public ?string $type = IssueNote::TYPE_USER_FRONT;
}
