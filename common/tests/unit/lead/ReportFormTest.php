<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatusInterface;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use Yii;
use yii\base\Model;

class ReportFormTest extends Unit {

	use UnitModelTrait;

	private ReportForm $model;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::source(),
			LeadFixtureHelper::question(),
			LeadFixtureHelper::reports()
		);
	}

	public function getModel(): Model {
		return $this->model;
	}

	public function testInvalidStatus(): void {
		$lead = $this->haveLead([
			'source_id' => 1,
			'status_id' => LeadStatusInterface::STATUS_NEW,
			'data' => 'test-lead',
		]);
		$this->giveForm([
			'owner_id' => 1,
			'lead' => $lead,
			'status_id' => 1000,
		]);

		$this->thenUnsuccessValidate();
		$this->thenSeeError('Status is invalid.', 'status_id');
	}

	public function testChangeStatus(): void {
		$lead = $this->haveLead([
			'source_id' => 1,
			'status_id' => LeadStatusInterface::STATUS_NEW,
			'data' => 'test-lead',
		]);
		$this->giveForm([
			'owner_id' => 1,
			'lead' => $lead,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
			'details' => 'Move to archive',
		]);
		$this->thenSuccessSave();
		$this->thenSeeLead([
			'source_id' => 1,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
			'data' => 'test-lead',
		]);

		$this->thenSeeReport([
			'lead_id' => $lead->getId(),
			'old_status_id' => LeadStatusInterface::STATUS_NEW,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
			'details' => 'Move to archive',
		]);
	}

	public function testSaveWithoutDetailsAndClosedQuestions(): void {
		$lead = $this->haveLead([
			'source_id' => 1,
			'status_id' => LeadStatusInterface::STATUS_NEW,
			'data' => 'test-lead',
		]);
		$this->giveForm([
			'owner_id' => 1,
			'lead' => $lead,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Details cannot be blank when closed questions is empty.', 'details');
		$this->thenSeeError('Closed questions must be set when details is blank.', 'closedQuestions');
	}

	public function testWithDetails(): void {
		$lead = $this->haveLead([
			'source_id' => 1,
			'status_id' => LeadStatusInterface::STATUS_NEW,
			'data' => 'test-lead',
		]);
		$this->giveForm([
			'owner_id' => 1,
			'lead' => $lead,
			'details' => 'Test report details',
		]);

		$this->thenSuccessSave();

		$this->thenSeeLead([
			'source_id' => 1,
			'status_id' => LeadStatusInterface::STATUS_NEW,
			'data' => 'test-lead',
		]);

		$this->thenSeeReport([
			'lead_id' => $lead->getId(),
			'old_status_id' => LeadStatusInterface::STATUS_NEW,
			'status_id' => LeadStatusInterface::STATUS_NEW,
			'details' => 'Test report details',
		]);
	}

	public function testClosedQuestionAsArrayWithoutDetails(): void {
		$this->giveForm([
			'owner_id' => 1,
			'lead' => $this->haveLead([
				'source_id' => 1,
				'status_id' => LeadStatusInterface::STATUS_NEW,
				'data' => 'test-lead',
			]),
			'closedQuestions' => [1, 2],
		]);

		$this->thenSuccessSave();
		$this->thenSeeAnswer(1);
		$this->thenSeeAnswer(2);
	}

	public function testClosedQuestionAsEmptyTableWithoutDetails(): void {
		$this->giveForm([
			'owner_id' => 1,
			'lead' => $this->haveLead([
				'source_id' => 1,
				'status_id' => LeadStatusInterface::STATUS_NEW,
				'data' => 'test-lead',
			]),
			'closedQuestions' => [],
		]);

		$this->thenUnsuccessSave();
	}

	private function haveLead(array $attributes): ActiveLead {
		return Yii::$app->leadManager->findById(
			$this->tester->haveRecord(
				Yii::$app->leadManager->model,
				$attributes
			)
		);
	}

	private function giveForm(array $config): void {
		$this->model = new ReportForm($config);
	}

	private function thenSeeLead(array $attributes): void {
		$this->tester->seeRecord(Yii::$app->leadManager->model, $attributes);
	}

	private function thenSeeReport(array $attributes): void {
		$this->tester->seeRecord(LeadReport::class, $attributes);
	}

	private function thenSeeAnswer(int $question_id, string $answer = null, int $report_id = null) {
		if ($report_id === null) {
			$report_id = $this->model->getModel()->id;
		}
		return $this->tester->seeRecord(LeadAnswer::class, [
			'report_id' => $report_id,
			'question_id' => $question_id,
			'answer' => $answer,
		]);
	}
}
