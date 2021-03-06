<?php

namespace App\Tests\Security\Extractor;

use App\Security\Extractor\AuthorizationHeaderExtractor;
use App\Security\Extractor\ChainExtractor;
use App\Security\Extractor\QueryParameterExtractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ChainExtractorTest extends TestCase
{
    public function testAll()
    {
        $extractor = new ChainExtractor();
        $extractor->addExtractor(new AuthorizationHeaderExtractor('foo', 'Bearer'));
        $extractor->addExtractor(new QueryParameterExtractor('bar'));

        $request1 = Request::create('/');
        $request1->headers->set('foo', 'Bearer foo_value');

        $request2 = Request::create('/');
        $request2->query->set('bar', 'bar_value');

        $this->assertEquals('foo_value', $extractor->extract($request1));
        $this->assertEquals('bar_value', $extractor->extract($request2));
    }
}
