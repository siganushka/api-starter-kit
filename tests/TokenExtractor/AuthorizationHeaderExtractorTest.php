<?php

namespace App\Tests\TokenExtractor;

use App\TokenExtractor\AuthorizationHeaderExtractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class AuthorizationHeaderExtractorTest extends TestCase
{
    public function testExtractorToken()
    {
        $extractor = new AuthorizationHeaderExtractor('foo', 'Bearer');
        $extractorEmptyPrefix = new AuthorizationHeaderExtractor('bar', null);

        $request1 = Request::create('/');
        $request1->headers->set('foo', 'Bearer foo_value');

        $request2 = Request::create('/');
        $request2->headers->set('foo', '        Bearer    string_with_space  ');

        $request3 = new Request();
        $request3->headers->set('bar', 'bar_value');

        $this->assertEquals('foo_value', $extractor->extract($request1));
        $this->assertEquals('string_with_space', $extractor->extract($request2));
        $this->assertEquals('bar_value', $extractorEmptyPrefix->extract($request3));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEmptyNameException()
    {
        return new AuthorizationHeaderExtractor('');
    }
}
