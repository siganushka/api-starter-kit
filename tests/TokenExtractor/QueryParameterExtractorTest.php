<?php

namespace App\Tests\TokenExtractor;

use App\TokenExtractor\QueryParameterExtractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class QueryParameterExtractorTest extends TestCase
{
    public function testExtractorToken()
    {
        $extractor = new QueryParameterExtractor('foo');

        $request1 = Request::create('/');
        $request1->query->set('foo', 'foo_value');

        $this->assertEquals('foo_value', $extractor->extract($request1));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEmptyNameException()
    {
        return new QueryParameterExtractor('');
    }
}
