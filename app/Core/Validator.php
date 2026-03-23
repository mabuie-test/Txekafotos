<?php

declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $this->errors[$field][] = 'O campo é obrigatório.';
                }
                if ($rule === 'accepted' && !in_array($value, ['1', 1, true, 'on'], true)) {
                    $this->errors[$field][] = 'Você deve aceitar este campo.';
                }
                if ($rule === 'string' && $value !== null && !is_string($value)) {
                    $this->errors[$field][] = 'Valor inválido.';
                }
            }
        }

        return $this->errors === [];
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
