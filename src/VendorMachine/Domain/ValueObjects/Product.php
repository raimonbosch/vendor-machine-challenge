<?php

namespace VendorMachine\Domain\ValueObjects;

interface Product
{
    public function price(): float;

    public function name(): string;
}