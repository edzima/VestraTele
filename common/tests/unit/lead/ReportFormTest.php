<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatusInterface;
use common\modules\lead\models\LeadUser;
use common\modules\lead\Module;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\base\Model;
use yii\helpers\Json;

class ReportFormTest extends Unit {

	use UnitModelTrait;

	private ReportForm $model;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::question(),
			LeadFixtureHelper::reports()
		);
	}

	public function testInvalidStatus(): void {
		$lead = $this->haveLead([
			'source_id' => 1,
			'status_id' => LeadStatusInterface::STATUS_NEW,
			'name' => __METHOD__,
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
			'name' => __METHOD__,
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
			'name' => __METHOD__,
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
			'name' => __METHOD__,
		]);
		$this->giveForm([
			'owner_id' => 1,
			'lead' => $lead,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Details cannot be blank when answers is empty.', 'details');
		$this->thenSeeError('Closed Questions must be set when details is blank.', 'closedQuestions');
	}

	public function testWithDetails(): void {
		$lead = $this->haveLead([
			'source_id' => 1,
			'status_id' => LeadStatusInterface::STATUS_NEW,
			'name' => __METHOD__,
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
			'name' => __METHOD__,
		]);

		$this->thenSeeReport([
			'lead_id' => $lead->getId(),
			'old_status_id' => LeadStatusInterface::STATUS_NEW,
			'status_id' => LeadStatusInterface::STATUS_NEW,
			'details' => 'Test report details',
		]);
	}

	public function testClosedQuestionWithOpenIdsWithoutDetails(): void {
		$this->giveForm([
			'owner_id' => 1,
			'lead' => $this->haveLead([
				'source_id' => 1,
				'status_id' => LeadStatusInterface::STATUS_NEW,
				'name' => __METHOD__,
			]),
			'closedQuestions' => [1, 2],
		]);

		$this->thenUnsuccessSave();
		$this->thenSeeError('Closed Questions is invalid.', 'closedQuestions');
	}

	public function testClosedQuestionWithValidIdsWithoutDetails(): void {
		$this->giveForm([
			'owner_id' => 1,
			'lead' => $this->haveLead([
				'source_id' => 1,
				'status_id' => LeadStatusInterface::STATUS_NEW,
				'name' => __METHOD__,
			]),
			'closedQuestions' => [3, 4],
		]);

		$this->thenSuccessSave();
		$this->thenSeeAnswer(3);
		$this->thenSeeAnswer(4);
	}

	public function testClosedQuestionAsEmptyTableWithoutDetails(): void {
		$this->giveForm([
			'owner_id' => 1,
			'lead' => $this->haveLead([
				'source_id' => 1,
				'status_id' => LeadStatusInterface::STATUS_NEW,
				'name' => __METHOD__,
			]),
			'closedQuestions' => [],
		]);

		$this->thenUnsuccessSave();
	}

	public function testOpenQuestions(): void {
		$this->giveForm([
			'owner_id' => 1,
			'lead' => $this->haveLead([
				'source_id' => 1,
				'status_id' => LeadStatusInterface::STATUS_NEW,
				'name' => __METHOD__,
			]),
			'closedQuestions' => [],
			'openAnswers' => [
				1 => 'answer-1',
				2 => 'answer-2',
			],
		]);
		$answered = [];
		foreach ($this->model->getAnswersModels() as $answerForm) {
			$answered[$answerForm->getQuestion()->id] = 'test-answer';
			$answerForm->answer = 'test-answer';
		}
		$this->thenSuccessSave();
		foreach ($answered as $questionId => $answer) {
			$this->thenSeeAnswer($questionId, $answer);
		}
	}

	public function testLeadWithoutOwner(): void {
		$this->giveForm([
			'owner_id' => 3,
			'lead' => $this->haveLead([
				'source_id' => 1,
				'status_id' => LeadStatusInterface::STATUS_NEW,
				'name' => __METHOD__,
			]),
			'details' => 'Report not self Lead',
		]);
		$this->tester->assertFalse($this->model->getLead()->isForUser(3));
		$this->thenSuccessSave();
		$this->tester->seeRecord(LeadUser::class, [
			'lead_id' => $this->model->getLead()->getId(),
			'user_id' => 3,
			'type' => LeadUser::TYPE_OWNER,
		]);
		$this->tester->assertTrue($this->model->getLead()->isForUser(3));
	}

	public function testNotSelfLeadWithOwner(): void {
		$lead = $this->haveLead([
			'source_id' => 1,
			'status_id' => LeadStatusInterface::STATUS_NEW,
			'name' => __METHOD__,
		]);
		$lead->linkUser(LeadUser::TYPE_OWNER, 1);
		$this->giveForm([
			'owner_id' => 3,
			'lead' => $lead,
			'details' => 'Report not self Lead with Owner',
		]);
		$this->tester->assertFalse($this->model->getLead()->isForUser(3));
		$this->thenSuccessSave();
		$this->tester->seeRecord(LeadUser::class, [
			'lead_id' => $this->model->getLead()->getId(),
			'user_id' => 3,
			'type' => LeadUser::TYPE_TELE,
		]);
		$this->tester->assertTrue($this->model->getLead()->isForUser(3));
	}

	public function testWithSameContactsWithEnableSameReports(): void {
		$lead1 = $this->haveLead([
			'source_id' => 1,
			'status_id' => LeadStatusInterface::STATUS_NEW,
			'name' => __METHOD__,
			'phone' => '123-123-123',
		]);
		$lead2 = $this->haveLead([
			'source_id' => 1,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
			'name' => __METHOD__,
			'phone' => '123-123-123',
		]);
		$lead3 = $this->haveLead([
			'source_id' => 1,
			'status_id' => 2,
			'name' => __METHOD__,
			'phone' => '123-123-123',
		]);

		$status = LeadStatusInterface::STATUS_ARCHIVE;
		$this->giveForm([
			'lead' => $lead1,
			'details' => 'Report same contacts as archive',
			'status_id' => $status,
			'owner_id' => 1,
		]);

		$this->model->withSameContacts = true;
		$this->thenSuccessSave();
		$this->thenSeeLead([
			'id' => $lead1->getId(),
			'status_id' => $status,
		]);
		$this->thenSeeLead([
			'id' => $lead2->getId(),
			'status_id' => $status,
		]);
		$this->thenSeeLead([
			'id' => $lead3->getId(),
			'status_id' => $status,
		]);

		$this->thenSeeReport([
			'lead_id' => $lead1->getId(),
			'status_id' => $status,
			'old_status_id' => LeadStatusInterface::STATUS_NEW,
			'details' => 'Report same contacts as archive',
		]);

		$sameDetails = 'Report from same contact Lead: #' . $lead1->getId();
		$this->thenSeeReport([
			'lead_id' => $lead2->getId(),
			'status_id' => $status,
			'old_status_id' => LeadStatusInterface::STATUS_ARCHIVE,
			'details' => $sameDetails,
		]);
		$this->thenSeeReport([
			'lead_id' => $lead3->getId(),
			'status_id' => $status,
			'old_status_id' => 2,
			'details' => $sameDetails,
		]);
	}

	public function testWithSameContactsWithDisableSameReports(): void {
		$lead1 = $this->haveLead([
			'source_id' => 1,
			'status_id' => LeadStatusInterface::STATUS_NEW,
			'name' => __METHOD__,
			'phone' => '123-123-123',
		]);
		$lead2 = $this->haveLead([
			'source_id' => 1,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
			'name' => __METHOD__,
			'phone' => '123-123-123',
		]);
		$lead3 = $this->haveLead([
			'source_id' => 1,
			'status_id' => 2,
			'name' => __METHOD__,
			'phone' => '123-123-123',
		]);

		$status = LeadStatusInterface::STATUS_ARCHIVE;
		$this->giveForm([
			'lead' => $lead1,
			'details' => 'Report same contacts as archive',
			'status_id' => $status,
			'owner_id' => 1,
		]);

		$this->model->withSameContacts = false;
		$this->thenSuccessSave();
		$this->thenSeeLead([
			'id' => $lead1->getId(),
			'status_id' => $status,
		]);
		$this->thenSeeLead([
			'id' => $lead2->getId(),
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		]);
		$this->thenSeeLead([
			'id' => $lead3->getId(),
			'status_id' => 2,
		]);

		$this->thenSeeReport([
			'lead_id' => $lead1->getId(),
			'status_id' => $status,
			'old_status_id' => LeadStatusInterface::STATUS_NEW,
			'details' => 'Report same contacts as archive',
		]);

		$this->thenDontSeeReport([
			'lead_id' => $lead2->getId(),
			'status_id' => $status,
			'old_status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		]);
		$this->thenDontSeeReport([
			'lead_id' => $lead3->getId(),
			'status_id' => $status,
			'old_status_id' => 2,
		]);
	}

	private function haveLead(array $attributes): ActiveLead {
		if (empty($attributes['data'])) {
			$attributes['data'] = Json::encode($attributes);
		}
		return Module::manager()->findById(
			$this->tester->haveRecord(
				Module::manager()->model,
				$attributes
			)
		);
	}

	private function giveForm(array $config): void {
		$this->model = new ReportForm($config);
	}

	private function thenSeeLead(array $attributes): void {
		$this->tester->seeRecord(Module::manager()->model, $attributes);
	}

	private function thenSeeReport(array $attributes): void {
		$this->tester->seeRecord(LeadReport::class, $attributes);
	}

	private function thenDontSeeReport(array $attributes) {
		$this->tester->dontSeeRecord(LeadReport::class, $attributes);
	}

	private function thenSeeAnswer(int $question_id, string $answer = null, int $report_id = null) {
		return $this->tester->seeRecord(LeadAnswer::class, [
			'question_id' => $question_id,
			'answer' => $answer,
			'report_id' => $report_id ?? $this->model->getModel()->id,
		]);
	}

	public function getModel(): Model {
		return $this->model;
	}

}
