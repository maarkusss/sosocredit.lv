<?php

namespace App\Entity;

/**
 * LendersDTO
 *
 * Object to work with $lenders array and UTM data altogether
 */
class LendersDTO
{
    public function __construct(public $lenders = [], public $utm_data = null)
    {
    }
}
