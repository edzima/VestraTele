<?php

use common\helpers\ArrayHelper;
use common\models\issue\Issue;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadUser;
use console\base\Migration;

/**
 * Class m221102_121226_user_types_timestamp
 */
class m221102_121226_user_types_timestamp extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_user}}', 'created_at', $this->timestamp()->notNull()->defaultExpression('current_timestamp()'));
		$this->addColumn('{{%issue_user}}', 'updated_at', $this->timestamp()->notNull()->defaultExpression('current_timestamp()'));

		$this->addColumn('{{%lead_user}}', 'created_at', $this->timestamp()->notNull()->defaultExpression('current_timestamp()'));
		$this->addColumn('{{%lead_user}}', 'updated_at', $this->timestamp()->notNull()->defaultExpression('current_timestamp()'));

		$issues = ArrayHelper::map(Issue::find()
			->select(['id', 'created_at'])
			->asArray()
			->all(), 'id', 'created_at');

		foreach ($issues as $id => $date) {
			$this->update('{{%issue_user}}',
				[
					'created_at' => $date,
					'updated_at' => $date,
				],
				[
					'issue_id' => $id,
				]);
		}

		$leadsUsers = LeadReport::find()
			->select(['lead_id', 'owner_id', 'created_at'])
			->orderBy(['created_at' => SORT_DESC])
			->groupBy('lead_id')
			->asArray()
			->all();

		foreach ($leadsUsers as $row) {
			$this->update('{{%lead_user}}',
				[
					'created_at' => $row['created_at'],
					'updated_at' => $row['created_at'],
				],
				[
					'lead_id' => $row['lead_id'],
					'user_id' => $row['owner_id'],
				]);
		}

		$leadsWithoutReport = Lead::find()
			->select(['lead.id', 'lead.date_at', 'lead_user.user_id'])
			->joinWith('reports', false, 'LEFT OUTER JOIN')
			->andWhere([LeadReport::tableName() . '.id' => null])
			->joinWith('leadUsers')
			->andWhere(['{{%lead_user}}.type' => LeadUser::TYPE_OWNER])
			->asArray()
			->all();

		foreach ($leadsWithoutReport as $row) {
			$this->update('{{%lead_user}}',
				[
					'created_at' => $row['date_at'],
					'updated_at' => $row['date_at'],
				],
				[
					'lead_id' => $row['id'],
					'user_id' => $row['user_id'],
				]);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_user}}', 'created_at');
		$this->dropColumn('{{%issue_user}}', 'updated_at');

		$this->dropColumn('{{%lead_user}}', 'created_at');
		$this->dropColumn('{{%lead_user}}', 'updated_at');
	}

}
