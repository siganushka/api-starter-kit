<?php

namespace App\Tests\Exception;

use App\Exception\APIException;
use PHPUnit\Framework\TestCase;

class APIExceptionTest extends TestCase
{
    public function testAll()
    {
        $exception = new APIException(400, 'Hello Symfony!');

        $this->assertEquals(400, $exception->getCode());
        $this->assertEquals(400, $exception->getStatusCode());
        $this->assertEquals('Hello Symfony!', $exception->getMessage());
        $this->assertEquals('Hello Symfony!', $exception->getMessageId());
        $this->assertEquals([], $exception->getMessageParameters());
        $this->assertNull($exception->getDomain());
    }

    public function testTranslationParameters()
    {
        $exception = new APIException(400, 'Hello {{ name }}!', ['name' => 'Symfony'], 'messages');

        $this->assertEquals('Hello {{ name }}!', $exception->getMessageId());
        $this->assertEquals(['name' => 'Symfony'], $exception->getMessageParameters());
        $this->assertEquals('messages', $exception->getDomain());
    }

    public function testInvalidStatusCode()
    {
        $this->expectException(\InvalidArgumentException::class);

        new APIException(1024, 'foo');
    }
}
