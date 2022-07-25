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

	public function testGetValueWithoutKey(): void {
		$key = MessageTemplateKeyHelper::generateKey([
			'foo' => 'bar',
			'cat',
		]);
		$this->tester->assertSame('foo:bar.cat', $key);
		$this->tester->assertNull(MessageTemplateKeyHelper::getValue($key, 'bar'));
	}

	public function testGetValueAsString(): void {
		$key = MessageTemplateKeyHelper::generateKey([
			'foo' => 'bar',
			'cat',
		]);
		$this->tester->assertSame('foo:bar.cat', $key);
		$this->tester->assertSame('bar', MessageTemplateKeyHelper::getValue($key, 'foo'));
	}

	public function testGetValueAsArray(): void {
		$key = MessageTemplateKeyHelper::generateKey([
			'foo' => ['bar', 'foo'],
			'cat',
		]);
		$this->tester->assertSame('foo:bar,foo.cat', $key);
		$this->tester->assertSame(['bar', 'foo'], MessageTemplateKeyHelper::getValue($key, 'foo'));
	}

	public function testIsSmsForSMSPartForKeyInStart(): void {
		$this->tester->assertTrue(MessageTemplateKeyHelper::isSMS(
			'sms.issueStage',
		));
	}

	public function testIsSmsForSMSPartForKeyInMiddle(): void {
		$this->tester->assertFalse(MessageTemplateKeyHelper::isSMS(
			'issue.sms.create',
		));
	}

	public function testIsSmsForSMSPartForKeyInEnd(): void {
		$this->tester->assertFalse(MessageTemplateKeyHelper::isSMS(
			'issue.sms',
		));
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
