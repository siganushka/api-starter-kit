<?php

namespace App\Translation;

use Symfony\Contracts\Translation\TranslatorInterface;

class DecoratingTranslator implements TranslatorInterface
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function trans(?string $id, array $parameters = [], string $domain = null, string $locale = null)
    {
        foreach ($parameters as $key => $value) {
            unset($parameters[$key]);
            $parameters[sprintf('{{ %s }}', $key)] = $value;
        }

        return $this->translator->trans($id, $parameters, $domain, $locale);
    }
}
