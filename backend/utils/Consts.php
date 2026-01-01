<?php
class Consts{
    /**
     * Fuel Types
     */
    public const FUEL_PETROL = 'petrol';
    public const FUEL_DIESEL = 'diesel';
    public const FUEL_ELECTRIC = 'electric';
    public const FUEL_HYBRID = 'hybrid';

    public static function getFuelTypes(): array
    {
        return [
            self::FUEL_PETROL,
            self::FUEL_DIESEL,
            self::FUEL_ELECTRIC,
            self::FUEL_HYBRID,
        ];
    }

    /**
     * Transmission Types
     */
    public const TRANSMISSION_MANUAL = 'manual';
    public const TRANSMISSION_AUTOMATIC = 'automatic';

    public static function getTransmissionTypes(): array
    {
        return [
            self::TRANSMISSION_MANUAL,
            self::TRANSMISSION_AUTOMATIC,
        ];
    }

    /**
     * Body Types
     */
    public const BODY_SEDAN = 'sedan';
    public const BODY_HATCHBACK = 'hatchback';
    public const BODY_SUV = 'suv';
    public const BODY_COUPE = 'coupe';
    public const BODY_CONVERTIBLE = 'convertible';
    public const BODY_WAGON = 'wagon';
    public const BODY_VAN = 'van';
    public const BODY_PICKUP = 'pickup';

    public static function getBodyTypes(): array
    {
        return [
            self::BODY_SEDAN,
            self::BODY_HATCHBACK,
            self::BODY_SUV,
            self::BODY_COUPE,
            self::BODY_CONVERTIBLE,
            self::BODY_WAGON,
            self::BODY_VAN,
            self::BODY_PICKUP,
        ];
    }

    /**
     * Countries
     */
    public static function getCountries(): array
    {
        return [
            'United States',
            'Canada',
            'United Kingdom',
            'Germany',
            'France',
            'Italy',
            'Spain',
            'Australia',
            'Japan',
            'China',
            'India',
            'Brazil',
            'Mexico',
            'Russia',
            'South Africa',
            'Poland',
            'Netherlands',
            'Sweden',
            'Norway',
            'Denmark',
            'Finland',
            'Belgium',
            'Switzerland',
            'Austria',
            'Portugal',
            'Greece',
            'Turkey',
            'Argentina',
            'Chile',
            'Colombia',
            'Peru',
        ];
    }
}