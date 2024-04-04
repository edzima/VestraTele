<?php

namespace common\modules\credit\models;

use common\helpers\ArrayHelper;
use common\models\entityResponsible\EntityResponsible;
use Yii;
use yii\base\Model;

class CreditClientAnalyze extends Model {

	public string $borrower = '';
	public $entityResponsibleId;

	public string $analyzeAt = '';
	public string $analyzeResult = '';
	public string $amountOfCanceledInterestOnFuture = '';
	public string $agreement = '';
	public string $agreementAt = '';

	public string $repaymentAt = '';

	public string $totalLoanAmount = '';
	public string $amountOfLoanGranted = '';

	public string $estimatedRefundAmount = '';
	private ?CreditSanctionCalc $sanctionCalc = null;

	public function init() {
		parent::init();
		if (empty($this->analyzeResult)) {
			$this->analyzeResult = $this->getDefaultAnalyzeResult();
		}
	}

	protected function getDefaultAnalyzeResult(): string {
		return Yii::t('credit', 'Default analyze result');
	}

	public function rules(): array {
		return [
			[
				[
					'agreement', 'agreementAt', 'amountOfLoanGranted', 'analyzeAt', 'borrower', 'entityResponsibleId',
					'estimatedRefundAmount', 'repaymentAt', 'totalLoanAmount',
				], 'required',
			],
			[['entityResponsibleId'], 'integer'],
			[
				['amountOfCanceledInterestOnFuture'], 'number',
				'min' => $this->sanctionCalc ? $this->sanctionCalc->getInterestsToPay() - 1 : null,
				'max' => $this->sanctionCalc ? $this->sanctionCalc->getInterestsToPay() + 1 : null,
			],
			[['totalLoanAmount', 'amountOfLoanGranted',], 'number', 'min' => 0],
			['agreement', 'string', 'min' => 3],
			[['borrower', 'analyzeResult'], 'string', 'min' => 8],
			[['entityResponsibleId'], 'in', 'range' => array_keys($this->getEntityResponsibleNames())],
		];
	}

	public function attributeLabels(): array {
		return [
			'amountOfCanceledInterestOnFuture' => Yii::t('credit', 'Amount of cancelled interest on future'),
			'amountOfLoanGranted' => Yii::t('credit', 'Amount of loan granted'),
			'analyzeAt' => Yii::t('credit', 'Analyze At'),
			'analyzeResult' => Yii::t('credit', 'Analyze result'),
			'agreement' => Yii::t('credit', 'Agreement'),
			'agreementAt' => Yii::t('credit', 'Agreement At'),
			'borrower' => Yii::t('credit', 'Borrower'),
			'creditor' => Yii::t('credit', 'Creditor'),
			'estimatedRefundAmount' => Yii::t('credit', 'Estimated refund amount'),
			'repaymentAt' => Yii::t('credit', 'Repayment At'),
			'totalLoanAmount' => Yii::t('credit', 'Total loan amount'),
			'entityResponsibleId' => Yii::t('credit', 'Creditor / Lender'),
		];
	}

	public function setSanctionCalc(CreditSanctionCalc $model): void {
		$this->sanctionCalc = $model;
		$this->analyzeAt = $model->dateAt;
		$this->estimateRefund();
		$loans = $model->getLoanInstallments();
		$firstLoan = reset($loans);
		$this->totalLoanAmount = $model->sumCredit - $model->provision - $model->insurance;
		$this->amountOfLoanGranted = $model->sumCredit;
		if ($firstLoan) {
			$this->agreementAt = $firstLoan->date;
		}
		$endLoan = end($loans);
		if ($endLoan) {
			$this->repaymentAt = $endLoan->date;
		}
	}

	public function getCreditor(): string {
		return $this->getEntityResponsibleNames()[$this->entityResponsibleId];
	}

	public function getEntityResponsibleName(): string {
		return $this->getEntityResponsibleNames()[$this->entityResponsibleId];
	}

	public function getEntityResponsibleNames(): array {
		return ArrayHelper::map(
			EntityResponsible::find()->all(),
			'id',
			'name'
		);
	}

	private function estimateRefund(): void {
		$this->estimatedRefundAmount = $this->sanctionCalc->provision
			? Yii::t('credit',
				'{interests} (interests) + {provision} (provision) = {sum}', [
					'interests' => Yii::$app->formatter->asCurrency($this->sanctionCalc->getInterestsPaid()),
					'provision' => Yii::$app->formatter->asCurrency($this->sanctionCalc->provision),
					'sum' => Yii::$app->formatter->asCurrency($this->sanctionCalc->getInterestsPaid() + $this->sanctionCalc->provision),
				])
			: Yii::t('credit',
				'{interests} (interests)', [
					'interests' => Yii::$app->formatter->asCurrency($this->sanctionCalc->getInterestsPaid()),
				]);
		$this->amountOfCanceledInterestOnFuture = $this->sanctionCalc->getInterestsToPay();
	}

}
