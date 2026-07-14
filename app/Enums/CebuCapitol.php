<?php

namespace App\Enums;

class CebuCapitol
{
    const CPH_Danao = 'Cebu Provincial Hospital (Danao City)';
    const CPH_Carcar = 'Cebu Provincial Hospital (Carcar City)';
    const CPH_Bogo = 'Cebu Provincial Hospital - Bogo City';
    const CPH_Balamban = 'Cebu Provincial Hospital (Balamban)';
    const ICKMH = 'Isidro C. Kintanar Memorial Hospital (Argao)';
    const DJMBMH = 'Dr. Jose Ma. Borromeo Memorial Hospital (Pinamungajan)';
    const JBDMH = 'Juan B. Dosado Memorial Hospital (Sogod)';
    const MJCMH = 'Mariano Jesus Cuenco Memorial Hospital (Malabuyoc)';
    const RLMMH = 'RICARDO L. MANINGO MEMORIAL HOSPITAL - CAMOTES DISTRICT HOSPITAL';
    const DH_Badian = 'Badian District Hospital';
    const DH_Bantayan = 'Bantayan District Hospital';
    const DH_Barili = 'Barili District Hospital';
    const DH_Daanbantayan = 'Daanbantayan District Hospital';
    const DH_Minglanilla = 'Minglanilla District Hospital';
    const DH_Oslob = 'Oslob District Hospital';
    const DH_Tuburan = 'Tuburan District Hospital';

    public static function values()
    {
        return [
            self::CPH_Danao,
            self::CPH_Carcar,
            self::CPH_Bogo,
            self::CPH_Balamban,
            self::ICKMH,
            self::DJMBMH,
            self::JBDMH,
            self::MJCMH,
            self::RLMMH,
            self::DH_Badian,
            self::DH_Bantayan,
            self::DH_Barili,
            self::DH_Daanbantayan,
            self::DH_Minglanilla,
            self::DH_Oslob,
            self::DH_Tuburan
        ];
    }

}