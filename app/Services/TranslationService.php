<?php
namespace App\Services;

class TranslationService
{

    /**
     * @function get the common app translations as array
     * @param string $locale
     * @return array
     */
    public function getAppTranslations(string $locale): array
    {
        $translations = require lang_path($locale."/app.php");
        $allLocales = config('mbo.app.supportedLocales');
        // get all other locales that we support without the current locale
        $tryLocales = array_values(
            array_filter($allLocales, function ($element) use ($locale) {
                return $element != $locale;
            })
        );
        // Fill missing entries from the fallback locales (that is, all other supported locales)
        $result = $translations;
        foreach ($tryLocales as $tryLocale) {
            $fallback = require lang_path($tryLocale . '/app.php');;
            $result = array_replace_recursive($fallback, $result);
        }
        return $result;
    }

}
