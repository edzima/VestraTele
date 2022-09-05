<?php

namespace common\modules\lead\models;

use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\query\IssueQuery;
use common\modules\lead\models\query\LeadQuery;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "lead_issue".
 *
 * @property int $lead_id
 * @property int $issue_id
 * @property int $crm_id
 * @property int $primary
 * @property string $created_at
 * @property string $updated_at
 * @property string $confirmed_at
 *
 * @property LeadCrm $crm
 * @property Lead $lead
 * @property-read IssueInterface|null $issue
 */
class LeadIssue extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_issue}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['lead_id', 'issue_id', 'crm_id'], 'required'],
			[['lead_id', 'issue_id', 'crm_id'], 'integer'],
			[['created_at', 'updated_at', 'confirmed_at'], 'safe'],
			[['lead_id', 'issue_id', 'crm_id'], 'unique', 'targetAttribute' => ['lead_id', 'issue_id', 'crm_id']],
			[['crm_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadCrm::class, 'targetAttribute' => ['crm_id' => 'id']],
			[['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'lead_id' => Yii::t('lead', 'Lead ID'),
			'issue_id' => Yii::t('lead', 'Issue ID'),
			'crm_id' => Yii::t('lead', 'Crm'),
			'created_at' => Yii::t('lead', 'Created At'),
			'updated_at' => Yii::t('lead', 'Updated At'),
			'confirmed_at' => Yii::t('lead', 'Confirmed At'),
		];
	}

	public function getCrm(): ActiveQuery {
		return $this->hasOne(LeadCrm::class, ['id' => 'crm_id']);
	}

	public function getLead(): LeadQuery {
		return $this->hasOne(Lead::class, ['id' => 'lead_id']);
	}

	public function getIssue(): IssueQuery {
		$relation = $this->hasOne(Issue::class, ['id' => 'issue_id']);
		if ($this->crm_id !== Yii::$app->issuesLeads->getCrmId()) {
			$relation->andWhere('0=1');
		}
		return $relation;
	}

	public function getIssueBackendUrl(): string {
		return $this->crm->backend_url . Yii::$app->urlManager->createUrl(['/issue/issue/view', 'id' => $this->issue_id]);
	}

	public function isConfirmed(): bool {
		return !empty($this->confirmed_at);
	}

}
