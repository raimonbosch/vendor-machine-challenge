<?php

namespace VendorMachine\Domain\ValueObjects;

use VendorMachine\Domain\Exceptions\InvalidCoinException;

class Coin {
    public const FIVE_CENTS = '0.05';
    public const TEN_CENTS = '0.10';
    public const QUARTER = '0.25';
    public const ONE_EURO = '1';

    public const acceptedCoins = [
        self::FIVE_CENTS,
        self::TEN_CENTS,
        self::QUARTER,
        self::ONE_EURO,
    ];

    private $cents;
    private $name;

    /**
     * @throws InvalidCoinException
     */
    public function __construct(string $coin) {
        if (!is_numeric($coin)) {
            throw new InvalidCoinException("Invalid coin");
        }

        if (!self::isValid($coin)) {
            throw new InvalidCoinException("Coin value is not accepted");
        }

        $this->name = $coin;
        $this->cents = (int)(100*((float) $coin));
    }

    public static function isValid(string $action): bool {
        return in_array($action, self::acceptedCoins);
    }

    public function getName(): string {
        return $this->name;
    }

    public function getCents(): int {
        return $this->cents;
    }

    public static function euro(): self
    {
        return new self(self::ONE_EURO);
    }

    public static function quarter(): self
    {
        return new self(self::QUARTER);
    }

    public static function tenCents(): self
    {
        return new self(self::TEN_CENTS);
    }

    public static function fiveCents(): self
    {
        return new self(self::FIVE_CENTS);
    }
}
