<?php

namespace App\Filters\RuleSets;

interface RuleSet
{
    public function apply(array $rules, array $lenders);
}
