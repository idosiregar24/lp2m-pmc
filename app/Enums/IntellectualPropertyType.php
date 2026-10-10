<?php

namespace App\Enums;

/**
 * Jenis KI (PRD §4.8).
 */
enum IntellectualPropertyType: string
{
    case Copyright = 'COPYRIGHT';
    case Patent = 'PATENT';
    case SimplePatent = 'SIMPLE_PATENT';
    case Trademark = 'TRADEMARK';
    case IndustrialDesign = 'INDUSTRIAL_DESIGN';
    case IcLayoutDesign = 'IC_LAYOUT_DESIGN';
    case TradeSecret = 'TRADE_SECRET';
    case GeographicalIndication = 'GEOGRAPHICAL_INDICATION';
    case PlantVariety = 'PLANT_VARIETY';

    public function label(): string
    {
        return match ($this) {
            self::Copyright => 'Hak Cipta',
            self::Patent => 'Paten',
            self::SimplePatent => 'Paten Sederhana',
            self::Trademark => 'Merek',
            self::IndustrialDesign => 'Desain Industri',
            self::IcLayoutDesign => 'Desain Tata Letak Sirkuit Terpadu',
            self::TradeSecret => 'Rahasia Dagang',
            self::GeographicalIndication => 'Indikasi Geografis',
            self::PlantVariety => 'Perlindungan Varietas Tanaman',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
