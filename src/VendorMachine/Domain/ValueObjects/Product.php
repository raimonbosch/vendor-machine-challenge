<?php

namespace VendorMachine\Domain\ValueObjects;

interface Product
{
    public function priceInCents(): int;

    public function name(): string;
}