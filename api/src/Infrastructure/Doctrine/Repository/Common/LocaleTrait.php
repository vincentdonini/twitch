<?php

namespace App\Infrastructure\Doctrine\Repository\Common;

trait LocaleTrait
{
    private function getLocale(?string $forcedLocale = null): string
    {
        if ($forcedLocale) {
            return $forcedLocale;
        }

        $request = $this->requestStack->getCurrentRequest();

        if ($request) {
            $lang = $request->query->get('lang');
            if ($lang) {
                return $lang;
            }

            $requestLocale = $request->getLocale();
            if ($requestLocale) {
                return $requestLocale;
            }
        }

        return $this->locale;
    }
}