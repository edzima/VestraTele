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

	public string $estimatedRefundAmount = '';
	private CreditSanctionCalc $sanctionCalc;

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
			[['borrower', 'entityResponsibleId', 'agreement'], 'required'],
			[['entityResponsibleId'], 'integer'],
			[['borrower', 'agreement', 'analyzeResult'], 'string', 'min' => 10],
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
		];
	}

	public function setSanctionCalc(CreditSanctionCalc $model): void {
		$this->sanctionCalc = $model;
		$this->agreementAt = $model->dateAt;
		$loans = $model->getLoanInstallments();
		$endLoan = end($loans);
		if ($endLoan) {
			$this->repaymentAt = $endLoan->date;
		}
	}

	public function getCreditor(): string {
		return $this->getEntityResponsibleNames()[$this->entityResponsibleId];
	}

	public function getEntityResponsibleNames(): array {
		return ArrayHelper::map(
			EntityResponsible::find()->all(),
			'id',
			'name'
		);
	}

}
