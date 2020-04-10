<?php

namespace App\Tests\Security\Extractor;

use App\Security\Extractor\QueryParameterExtractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class QueryParameterExtractorTest extends TestCase
{
    public function testAll()
    {
        $extractor = new QueryParameterExtractor('foo');

        $request1 = Request::create('/');
        $request1->query->set('foo', 'foo_value');

        $this->assertEquals('foo_value', $extractor->extract($request1));
    }

    public function testEmptyNameException()
    {
        $this->expectException(\InvalidArgumentException::class);

        new QueryParameterExtractor('');
    }
}
