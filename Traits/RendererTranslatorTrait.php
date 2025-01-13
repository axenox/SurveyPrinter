<?php

namespace axenox\SurveyPrinter\Traits;

/**
 * Allows a renderer translate elements, before writing them to their output HTML.
 */
trait RendererTranslatorTrait
{
    /**
     * Translate an element.
     *
     * If `$element` is a localization array, this function translates it accordingly.
     * Otherwise, it returns the element as is.
     *
     * @param mixed $element
     * @return mixed
     */
    protected function translateElement(mixed $element) : mixed
    {
        if (!is_array($element)) {
            return $element;
        }

        $locale = $this->getLocale();
        return match (true) {
            key_exists($locale, $element) => $element[$locale],
            key_exists('default', $element) => $element['default'],
            default => $element[array_key_first($element)],
        };
    }
        
    /**
     * Get the locale for localization.
     *
     * TODO 2024-11-28 geb: This is a placeholder implementation.
     *
     * @return string
     */
    protected function getLocale() : string
    {
        return 'de';
    }
}