<?php

namespace App\Contracts;

/**
 * @property $name
 * @property $title
 * @property $description
 * @property $keywords
 */
interface Seoable
{
    public function h1_tag(): string;

    public function canonical_url(): string;
}
