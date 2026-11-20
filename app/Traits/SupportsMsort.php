<?php

namespace App\Traits;

trait SupportsMsort
{
    public function msort(): array|false
    {
        $params = func_get_args();
        $array = array_pop($params);

        if (!is_array($array)) {
            return false;
        }

        $multisort_params = [];
        foreach ($params as $i => $param) {
            if (is_string($param)) {
                ${"param_$i"} = [];
                foreach ($array as $index => $row) {
                    ${"param_$i"}[$index] = $row[$param];
                }
            } else {
                ${"param_$i"} = $params[$i];
            }

            $multisort_params[] = &${"param_$i"};
        }
        $multisort_params[] = &$array;

        call_user_func_array("array_multisort", $multisort_params);

        return $array;
    }
}
