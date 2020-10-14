<?php

namespace common\fixtures\user;

use common\models\user\Worker;

class AgentFixture extends WorkerFixture {

	public array $roles = [Worker::ROLE_AGENT];

}
