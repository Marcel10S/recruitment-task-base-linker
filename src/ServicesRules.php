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
        'ConsignorCompany' => self::RULE_LENGTH,
        'Name' => self::RULE_LENGTH,
        'Company' => self::RULE_LENGTH,
        'AddressLine1' => self::RULE_LENGTH,
        'AddressLine2' => self::RULE_LENGTH,
        'AddressLine3' => self::RULE_LENGTH,
        'State' => self::RULE_LENGTH,
        'Zip' => self::RULE_LENGTH,
        'ConsigneeCountry' => self::RULE_COUNTRY_CONTAINS,
        'Weight' => self::RULE_VALUE,
        'DisplayId' => self::RULE_LENGTH,
        'Description' => self::RULE_LENGTH,
        'HsCode' => self::RULE_LENGTH,
        'Phone' => self::RULE_LENGTH,
        'Email' => self::RULE_LENGTH,
    ];

    const array PPLEU_RULES = [
        'ConsignorCompany' => 30,
        'Name' => 35,
        'Company' => 35,
        'AddressLine1' => 35,
        'AddressLine2' => 35,
        'AddressLine3' => 35,
        'City' => 35,
        'State' => 35,
        'Zip' => 20,
        'Phone' => 15,
        'Weight' => 30.0,
        'DisplayId' => 15,
        'ConsigneeCountry' => 'AT;BE;BG;CY;CZ;DK;DE;EE;FI;FR;GR;HR;HU;IE;LT;LU;LV;MT;NL;PL;RO;SE;SI;SK;'
    ];
    const array PPLGE_PPLGU_RULES = [
        'ConsignorCompany' => 60,
        'Name' => 50,
        'Company' => 30,
        'AddressLine1' => 50,
        'AddressLine2' => 50,
        'AddressLine3' => 50,
        'City' => 50,
        'State' => 50,
        'Zip' => 20,
        'Phone' => 15,
        'Weight' => 30.0,
        'DisplayId' => 15,
        'Description' => 105,
        'HsCode' => 6
    ];
    const array RM_RULES = [
        'ConsignorCompany' => 25,
        'Name' => 30,
        'Company' => 30,
        'AddressLine1' => 30,
        'AddressLine2' => 30,
        'AddressLine3' => 30,
        'City' => 30,
        'State' => 30,
        'Zip' => 20,
        'Weight' => 20.0,
        'DisplayId' => 15,
        'ConsigneeCountry' => 'GB;'
    ];
    const array PPTT_RULES = [
        'ConsignorCompany' => 30,
        'Name' => 30,
        'Company' => 30,
        'AddressLine1' => 30,
        'AddressLine2' => 30,
        'AddressLine3' => 30,
        'City' => 30,
        'State' => 30,
        'Zip' => 20,
        'Weight' => 2.0,
        'DisplayId' => 15,
        'Description' => 20,
        'HsCode' => 25,
        'ConsigneeCountry' => 'AU;AT;BE;BG;BR;BY;CA;CH;CN;CY;CZ;DK;DE;EE;ES;FI;FR;GB;GF;GI;GP;GR;HK;HR;HU;ID;IE;IL;IS;IT;JP;KR;LB;LT;LU;LV;MQ;MT;MY;NL;NO;NZ;PL;PT;RE;RO;RS;RU;SA;SE;SG;SI;SK;TH;TR;US;'
    ];
    const array PPTR_PPNT_RULES = [
        'ConsignorCompany' => 30,
        'Name' => 30,
        'Company' => 30,
        'AddressLine1' => 30,
        'AddressLine2' => 30,
        'AddressLine3' => 30,
        'City' => 30,
        'State' => 30,
        'Zip' => 20,
        'Phone' => 15,
        'Weight' => 2.0,
        'DisplayId' => 15,
        'Description' => 20,
        'HsCode' => 25
    ];
    const array SEND_RULES = [
        'Name' => 20,
        'Company' => 35,
        'AddressLine1' => 35,
        'AddressLine2' => 35,
        'AddressLine3' => 35,
        'City' => 35,
        'State' => 35,
        'Zip' => 6,
        'Phone' => 15,
        'Weight' => 20.0,
        'DisplayId' => 15,
        'ConsigneeCountry' => 'EX;PT;'
    ];
    const array ITCR_RULES = [
        'Name' => 60,
        'Company' => 60,
        'AddressLine1' => 35,
        'AddressLine2' => 35,
        'AddressLine3' => 35,
        'City' => 60,
        'State' => 50,
        'Zip' => 5,
        'Phone' => 15,
        'Weight' => 20.0,
        'DisplayId' => 15,
        'Description' => 60,
        'ConsigneeCountry' => 'IT;'
    ];
    const array HEHDS_RULES = [
        'ConsignorCompany' => 40,
        'Name' => 20,
        'Company' => 20,
        'AddressLine1' => 35,
        'City' => 35,
        'Zip' => 5,
        'Phone' => 15,
        'Weight' => 20.0,
        'DisplayId' => 15,
        'ConsigneeCountry' => 'DE;'
    ];
    const array CPHD_RULES = [
        'ConsignorCompany' => 25,
        'Name' => 35,
        'Company' => 35,
        'AddressLine1' => 35,
        'AddressLine2' => 35,
        'AddressLine3' => 35,
        'City' => 35,
        'State' => 35,
        'Zip' => 20,
        'Weight' => 20.0,
        'DisplayId' => 15,
        'ConsigneeCountry' => 'FR;'
    ];
    const array SC_RULES = [
        'Name' => 35,
        'Company' => 35,
        'AddressLine1' => 35,
        'AddressLine2' => 35,
        'AddressLine3' => 35,
        'City' => 35,
        'State' => 2,
        'Zip' => 20,
        'Weight' => 10.5,
        'DisplayId' => 15,
        'ConsigneeCountry' => 'US;'
    ];
    const array PPND_PPHD_RULES = [
        'ConsignorCompany' => 30,
        'Name' => 35,
        'Company' => 35,
        'AddressLine1' => 35,
        'AddressLine2' => 35,
        'AddressLine3' => 35,
        'City' => 35,
        'State' => 35,
        'Zip' => 20,
        'Phone' => 15,
        'Weight' => 30.0,
        'DisplayId' => 15,
        'ConsigneeCountry' => 'NL;BE;'
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
