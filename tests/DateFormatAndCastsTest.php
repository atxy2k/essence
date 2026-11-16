<?php namespace Atxy2k\Essence\Tests;

use Atxy2k\Essence\Constants\DateFormat;
use Atxy2k\Essence\Eloquent\ChangeEmailRequest;
use Atxy2k\Essence\Eloquent\Device;
use Atxy2k\Essence\Eloquent\DeviceLocationHistory;
use Atxy2k\Essence\Eloquent\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class DateFormatAndCastsTest extends TestCase
{
    /**
     * Each ISO_* format must render like its LOCALIZED_* (strftime) counterpart.
     * Compared in English with the "C" system locale, since strftime depends on
     * the OS locales installed. Day >= 10 avoids strftime's space-padded %e.
     */
    public function testIsoFormatsMatchLocalizedFormats()
    {
        $pairs = [
            [DateFormat::LOCALIZED_STANDART_DATE, DateFormat::ISO_STANDART_DATE],
            [DateFormat::LOCALIZED_SIMPLE_DATE_WITH_HOUR, DateFormat::ISO_SIMPLE_DATE_WITH_HOUR],
            [DateFormat::LOCALIZED_FULL_DATE_WITH_HOUR, DateFormat::ISO_FULL_DATE_WITH_HOUR],
            [DateFormat::LOCALIZED_SIMPLE_DATE, DateFormat::ISO_SIMPLE_DATE],
            [DateFormat::LOCALIZED_STANDART_DATE_WITH_DAY, DateFormat::ISO_STANDART_DATE_WITH_DAY],
            [DateFormat::LOCALIZED_DATE_MINIFY, DateFormat::ISO_DATE_MINIFY],
        ];
        $previous = setlocale(LC_TIME, '0');
        setlocale(LC_TIME, 'C');
        $date = Carbon::create(2026, 11, 16, 13, 5, 9)->locale('en');
        try {
            foreach ($pairs as [$localized, $iso]) {
                $this->assertSame(@strftime($localized, $date->getTimestamp()), $date->isoFormat($iso), $iso);
            }
        } finally {
            setlocale(LC_TIME, $previous);
        }
    }

    public function testIsoFormatsUseCarbonLocale()
    {
        $date = Carbon::create(2026, 11, 16, 13, 5, 9)->locale('es');
        $this->assertSame('noviembre 16, 2026 a las 13:05:09 hrs.', $date->isoFormat(DateFormat::ISO_FULL_DATE_WITH_HOUR));
        $this->assertSame('16 de noviembre, 2026', $date->isoFormat(DateFormat::ISO_STANDART_DATE));
    }

    /**
     * Laravel 10 removed Model::$dates; these attributes must still be Carbon instances.
     */
    public function testDateAttributesAreCastToCarbon()
    {
        $cases = [
            [new User(), 'activated_at'],
            [new Device(), 'last_connection'],
            [new DeviceLocationHistory(), 'date'],
            [new ChangeEmailRequest(), 'created_at'],
        ];
        foreach ($cases as [$model, $attribute]) {
            $model->setRawAttributes([$attribute => '2026-11-16 13:05:09']);
            $this->assertInstanceOf(CarbonInterface::class, $model->{$attribute}, get_class($model) . '::' . $attribute);
            $this->assertSame('2026-11-16 13:05:09', $model->{$attribute}->format('Y-m-d H:i:s'));
        }
    }
}
