<?php

namespace App\Exceptions;

class LaravelErrorsDecorator
{
    public function transform(array $errors_bag): array
    {
        $errors = [];
        if ($errors_bag) {
            foreach ($errors_bag as $errors_bag_key => $errors_bag_messages) {
                $error_key = strtolower($errors_bag_key . '_invalid');
                $errors[] = [
                    'error_key' => $error_key,
                    'error_messages' => $errors_bag_messages,
                ];
            }
        }

        return $errors;
    }
}
