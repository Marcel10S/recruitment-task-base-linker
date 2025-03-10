<?php

namespace Baselinker;

class ServicesRules
{
    const string RULE_LENGTH = 'length';
    const string RULE_COUNTRY_CONTAINS = 'country_contains';
    const string RULE_VALUE = 'value';

    const array PACKAGE_REQUIRED_PARAMS = [
        'service', 'sender_fullname', 'delivery_fullname',
        'delivery_address', 'delivery_city', 'delivery_country',
        'delivery_phone', 'delivery_email' // , 'delivery_postalcode' || 'delivery_state'
    ];
    const array FIELD_TYPES = [
        'delivery_fullname' => self::RULE_LENGTH,
        'delivery_company' => self::RULE_LENGTH,
        'delivery_address' => self::RULE_LENGTH,
        'delivery_state' => self::RULE_LENGTH,
        'delivery_postalcode' => self::RULE_LENGTH,
        'delivery_country' => self::RULE_COUNTRY_CONTAINS,
        'delivery_phone' => self::RULE_LENGTH,
    ];

    const array PPLEU_RULES = [
        'delivery_fullname' => 35,
        'delivery_company' => 35,
        'delivery_address' => 35,
        'delivery_city' => 35,
        'delivery_state' => 35,
        'delivery_postalcode' => 20,
        'delivery_phone' => 15,
        'delivery_country' => 'AT;BE;BG;CY;CZ;DK;DE;EE;FI;FR;GR;HR;HU;IE;LT;LU;LV;MT;NL;PL;RO;SE;SI;SK;'
    ];
    const array PPLGE_PPLGU_RULES = [
        'delivery_fullname' => 50,
        'delivery_company' => 30,
        'delivery_address' => 50,
        'delivery_city' => 50,
        'delivery_state' => 50,
        'delivery_postalcode' => 20,
        'delivery_phone' => 15,
    ];
    const array RM_RULES = [
        'delivery_fullname' => 30,
        'delivery_company' => 30,
        'delivery_address' => 30,
        'delivery_city' => 30,
        'delivery_state' => 30,
        'delivery_postalcode' => 20,
        'delivery_country' => 'GB;'
    ];
    const array PPTT_RULES = [
        'delivery_fullname' => 30,
        'delivery_company' => 30,
        'delivery_address' => 30,
        'delivery_city' => 30,
        'delivery_state' => 30,
        'delivery_postalcode' => 20,
        'delivery_country' => 'AU;AT;BE;BG;BR;BY;CA;CH;CN;CY;CZ;DK;DE;EE;ES;FI;FR;GB;GF;GI;GP;GR;HK;HR;HU;ID;IE;IL;IS;IT;JP;KR;LB;LT;LU;LV;MQ;MT;MY;NL;NO;NZ;PL;PT;RE;RO;RS;RU;SA;SE;SG;SI;SK;TH;TR;US;'
    ];
    const array PPTR_PPNT_RULES = [
        'delivery_fullname' => 30,
        'delivery_company' => 30,
        'delivery_address' => 30,
        'delivery_city' => 30,
        'delivery_state' => 30,
        'delivery_postalcode' => 20,
        'delivery_phone' => 15,
    ];
    const array SEND_RULES = [
        'delivery_fullname' => 20,
        'delivery_company' => 35,
        'delivery_address' => 35,
        'delivery_city' => 35,
        'delivery_state' => 35,
        'delivery_postalcode' => 6,
        'delivery_phone' => 15,
        'delivery_country' => 'EX;PT;'
    ];
    const array ITCR_RULES = [
        'delivery_fullname' => 60,
        'delivery_company' => 60,
        'delivery_address' => 35,
        'delivery_city' => 60,
        'delivery_state' => 50,
        'delivery_postalcode' => 5,
        'delivery_phone' => 15,
        'delivery_country' => 'IT;'
    ];
    const array HEHDS_RULES = [
        'delivery_fullname' => 20,
        'delivery_company' => 20,
        'delivery_address' => 35,
        'delivery_city' => 35,
        'delivery_postalcode' => 5,
        'delivery_phone' => 15,
        'delivery_country' => 'DE;'
    ];
    const array CPHD_RULES = [
        'delivery_fullname' => 35,
        'delivery_company' => 35,
        'delivery_address' => 35,
        'delivery_city' => 35,
        'delivery_state' => 35,
        'delivery_postalcode' => 20,
        'delivery_country' => 'FR;'
    ];
    const array SC_RULES = [
        'delivery_fullname' => 35,
        'delivery_company' => 35,
        'delivery_address' => 35,
        'delivery_city' => 35,
        'delivery_state' => 2,
        'delivery_postalcode' => 20,
        'delivery_country' => 'US;'
    ];
    const array PPND_PPHD_RULES = [
        'delivery_fullname' => 35,
        'delivery_company' => 35,
        'delivery_address' => 35,
        'delivery_city' => 35,
        'delivery_state' => 35,
        'delivery_postalcode' => 20,
        'delivery_phone' => 15,
        'delivery_country' => 'NL;BE;'
    ];

    public static function getRules(): array
    {
        return [
            'PPLEU' => static::PPLEU_RULES,
            'PPLGE' => static::PPLGE_PPLGU_RULES,
            'PPLGU' => static::PPLGE_PPLGU_RULES,
            'RM24' => static::RM_RULES,
            'RM24S' => static::RM_RULES,
            'RM48' => static::RM_RULES,
            'RM48S' => static::RM_RULES,
            'PPTT' => static::PPTT_RULES,
            'PPTR' => static::PPTR_PPNT_RULES,
            'PPNT' => static::PPTR_PPNT_RULES,
            'SEND' => static::SEND_RULES,
            'SEND2' => static::SEND_RULES,
            'ITCR' => static::ITCR_RULES,
            'HEHDS' => static::HEHDS_RULES,
            'CPHD' => static::CPHD_RULES,
            'CPHDS' => static::CPHD_RULES,
            'SCST' => static::SC_RULES,
            'SCSTS' => static::SC_RULES,
            'SCEX' => static::SC_RULES,
            'SCEXS' => static::SC_RULES,
            'PPND' => static::PPND_PPHD_RULES,
            'PPNDS' => static::PPND_PPHD_RULES,
            'PPHD' => static::PPND_PPHD_RULES,
            'PPHDS' => static::PPND_PPHD_RULES,
        ];
    }

    public static function getFieldType(string $fieldName): ?string
    {
        if (!isset(self::FIELD_TYPES[$fieldName])) {
            return null;
        }

        return self::FIELD_TYPES[$fieldName];
    }
}
