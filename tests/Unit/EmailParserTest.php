<?php

use PHPUnit\Framework\TestCase;
use MailSo\Mime\Email;

class EmailParserTest extends TestCase
{
    public function testParenthesesInDisplayNameArePreserved(): void
    {
        $email = Email::Parse('Foo (Bar) <foo@example.com>');

        $this->assertSame('Foo (Bar)', $email->GetDisplayName());
        $this->assertSame('', $email->GetRemark());
        $this->assertSame('"Foo (Bar)" <foo@example.com>', $email->ToString(false, true));
    }

    public function testQuotedDisplayNameWithParenthesesIsPreserved(): void
    {
        $email = Email::Parse('"Foo (Bar)" <foo@example.com>');

        $this->assertSame('Foo (Bar)', $email->GetDisplayName());
        $this->assertSame('', $email->GetRemark());
        $this->assertSame('"Foo (Bar)" <foo@example.com>', $email->ToString(false, true));
    }
}
