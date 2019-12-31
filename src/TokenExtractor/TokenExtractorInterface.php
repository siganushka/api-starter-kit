<?php

namespace App\TokenExtractor;

use Symfony\Component\HttpFoundation\Request;

interface TokenExtractorInterface
{
    /**
     * 从请求对象中提取访问令牌.
     *
     * @return string
     */
    public function extract(Request $request): ?string;
}
