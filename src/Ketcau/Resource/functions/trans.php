<?php

use Ketcau\DependencyInjection\Facade\TranslatorFacade;

function trans($id, array $parameters = [], $domain = null, $locale = null)
{
    $Translator = TranslatorFacade::create();
    return $Translator->trans($id, $parameters, $domain, $locale);
}
