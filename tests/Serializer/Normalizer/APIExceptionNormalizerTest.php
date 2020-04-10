<?php

namespace App\Tests\Serializer\Normalizer;

use App\Exception\APIException;
use App\Serializer\Normalizer\APIExceptionNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;

class APIExceptionNormalizerTest extends TestCase
{
    public function testAll()
    {
        $resource = [
            'parameter.not_found' => 'Parameter {{ key }} not found.',
        ];

        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', $resource, 'en');

        $normalizer = new APIExceptionNormalizer($translator);

        $exception = new APIException(400, 'parameter.not_found', ['{{ key }}' => 'foo']);

        $this->assertTrue($normalizer->supportsNormalization($exception));

        $data = $normalizer->normalize($exception);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('detail', $data);

        $this->assertEquals(Response::$statusTexts[400], $data['title']);
        $this->assertEquals(400, $data['status']);
        $this->assertEquals('Parameter foo not found.', $data['detail']);
    }
}
