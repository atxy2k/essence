<?php namespace Atxy2k\Essence\Constants;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 23:22
 */
class DateFormat
{
    /**************************************************************************
     * Format localized
     * If you need documentation, check this url
     * http://php.net/manual/en/function.strftime.php
     *
     * Deprecated: strftime() is deprecated since PHP 8.1 and Carbon 3 removes
     * formatLocalized(). Use the ISO_* formats below with isoFormat().
     **************************************************************************/
    /** @deprecated Use ISO_STANDART_DATE with isoFormat() */
    const LOCALIZED_STANDART_DATE = '%e de %B, %Y';
    /** @deprecated Use ISO_SIMPLE_DATE_WITH_HOUR with isoFormat() */
    const LOCALIZED_SIMPLE_DATE_WITH_HOUR = '%b %e, %Y a las %X hrs.';
    /** @deprecated Use ISO_FULL_DATE_WITH_HOUR with isoFormat() */
    const LOCALIZED_FULL_DATE_WITH_HOUR = '%B %e, %Y a las %X hrs.';
    /** @deprecated Use ISO_SIMPLE_DATE with isoFormat() */
    const LOCALIZED_SIMPLE_DATE = '%b %e, %Y';
    /** @deprecated Use ISO_STANDART_DATE_WITH_DAY with isoFormat() */
    const LOCALIZED_STANDART_DATE_WITH_DAY = '%a %e de %B, %Y';
    /** @deprecated Use ISO_DATE_MINIFY with isoFormat() */
    const LOCALIZED_DATE_MINIFY = '%e de %b';

    /**************************************************************************
     * ISO formats, equivalent to the LOCALIZED_* ones, for Carbon::isoFormat().
     * Month and day names come from the Carbon locale (Carbon::setLocale('es')),
     * not from the system locale.
     * https://carbon.nesbot.com/docs/#iso-format-available-replacements
     **************************************************************************/
    const ISO_STANDART_DATE = 'D [de] MMMM, YYYY';
    const ISO_SIMPLE_DATE_WITH_HOUR = 'MMM D, YYYY [a las] HH:mm:ss [hrs.]';
    const ISO_FULL_DATE_WITH_HOUR = 'MMMM D, YYYY [a las] HH:mm:ss [hrs.]';
    const ISO_SIMPLE_DATE = 'MMM D, YYYY';
    const ISO_STANDART_DATE_WITH_DAY = 'ddd D [de] MMMM, YYYY';
    const ISO_DATE_MINIFY = 'D [de] MMM';

}
