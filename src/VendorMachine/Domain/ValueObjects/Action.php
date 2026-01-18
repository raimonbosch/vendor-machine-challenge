<?php

namespace VendorMachine\Domain\ValueObjects;

use VendorMachine\Domain\Exceptions\InvalidActionException;

class Action {
    public const GET_SODA = 'GET-SODA';
    public const GET_WATER = 'GET-WATER';
    public const GET_JUICE = 'GET-JUICE';
    public const RETURN_COIN = 'RETURN-COIN';
    public const SERVICE = 'SERVICE';
    public const UNKNOWN = 'UNKNOWN';

    public const acceptedActions = [
        self::GET_SODA,
        self::GET_WATER,
        self::GET_JUICE,
        self::RETURN_COIN,
        self::SERVICE,
        self::UNKNOWN,
    ];

    public function __construct(private readonly string $action) {
        if (!self::isValid($action)) {
            throw new InvalidActionException();
        }
    }

    public static function isValid(string $action): bool {
        return in_array($action, self::acceptedActions);
    }

    public function getName(): string {
        return $this->action;
    }
}
