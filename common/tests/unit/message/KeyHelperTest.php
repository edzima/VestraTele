<?php

namespace common\tests\unit\message;

use common\components\message\MessageTemplateKeyHelper;
use common\tests\unit\Unit;

class KeyHelperTest extends Unit {

	public function testWithoutArrayValues(): void {
		$this->tester->assertSame(MessageTemplateKeyHelper::generateKey(
			['test', 'string', 'array']
		), 'test.string.array');
	}

	public function testWithArrayAsSignleStringValue(): void {
		$this->tester->assertSame(MessageTemplateKeyHelper::generateKey(
			['test', 'string', ['value']]
		), 'test.string.value');
	}

	public function testWithArrayAsMultipleStringValue(): void {
		$this->tester->assertSame(MessageTemplateKeyHelper::generateKey(
			['test', 'string', ['first', 'second']]
		), 'test.string.first.second');
	}

	public function testWithArrayKeyAndValue(): void {
		$this->tester->assertSame(MessageTemplateKeyHelper::generateKey(
			[

				'key' => 'value',
				'double' => 'yes',

			]
		), 'key:value.double:yes');
	}

	public function testWithStringsAndArrayKeyAndValue(): void {
		$this->tester->assertSame(MessageTemplateKeyHelper::generateKey(
			[
				'test',
				'string',
				[
					'key' => 'value',
					'double' => 'yes',
				],
			]
		), 'test.string.key:value.double:yes');
	}

	public function testWithMultiArrayKeyAndValue(): void {
		$this->tester->assertSame(MessageTemplateKeyHelper::generateKey(
			[
				'test',
				'string',
				[
					'first' => 'no',
					'double' => 'yes',
				],
				[
					'second' => 'yes',
					'double' => 'yes',
				],
			]
		), 'test.string.first:no.double:yes.second:yes.double:yes');
	}

	public function testEmptyIssueType(): void {
		$this->tester->assertSame('issue.create', MessageTemplateKeyHelper::generateKey(
			['issue', 'create', MessageTemplateKeyHelper::issueTypesKeyPart([])]
		));
	}

	public function testSingleIssueType(): void {
		$this->tester->assertSame('issue.create.issueTypes:1', MessageTemplateKeyHelper::generateKey(
			['issue', 'create', MessageTemplateKeyHelper::issueTypesKeyPart([1])]
		));
	}

	public function testMultipleIssueTypes(): void {
		$this->tester->assertSame('issue.create.issueTypes:1,2', MessageTemplateKeyHelper::generateKey(
			['issue', 'create', MessageTemplateKeyHelper::issueTypesKeyPart([1, 2])]
		));
	}
}
