<?php

namespace common\modules\issue\widgets;

use common\models\message\IssueMessagesForm;
use yii\base\Widget;
use yii\widgets\ActiveForm;

class IssueMessagesFormWidget extends Widget {

	public bool $withWorkersTypes = true;

	public ActiveForm $form;
	public IssueMessagesForm $model;
	public array $checkboxesAttributes = [
		'sendSmsToCustomer',
		'sendSmsToAgent',
		'sendEmailToCustomer',
		'sendEmailToWorkers',
	];

	public function run(): string {
		return $this->render('issue-messages_form', [
			'form' => $this->form,
			'model' => $this->model,
			'checkboxesAttributes' => $this->checkboxesAttributes,
			'withWorkersTypes' => $this->withWorkersTypes,
		]);
	}
}
