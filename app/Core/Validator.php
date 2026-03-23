<?php

declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            foreach ($fieldRules as $rule) {
                $parameters = [];
                if (str_contains((string) $rule, ':')) {
                    [$rule, $parameterString] = explode(':', (string) $rule, 2);
                    $parameters = explode(',', $parameterString);
                }

                match ($rule) {
                    'required' => $this->validateRequired($field, $value),
                    'accepted' => $this->validateAccepted($field, $value),
                    'string' => $this->validateString($field, $value),
                    'email' => $this->validateEmail($field, $value),
                    'min' => $this->validateMin($field, $value, (int) ($parameters[0] ?? 0)),
                    'max' => $this->validateMax($field, $value, (int) ($parameters[0] ?? 0)),
                    'in' => $this->validateIn($field, $value, $parameters),
                    default => null,
                };
            }
        }

        return $this->errors === [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    private function validateRequired(string $field, mixed $value): void
    {
        if ($value === null || $value === '') {
            $this->errors[$field][] = 'O campo é obrigatório.';
        }
    }

    private function validateAccepted(string $field, mixed $value): void
    {
        if (!in_array($value, ['1', 1, true, 'on'], true)) {
            $this->errors[$field][] = 'Você deve aceitar este campo.';
        }
    }

    private function validateString(string $field, mixed $value): void
    {
        if ($value !== null && !is_string($value)) {
            $this->errors[$field][] = 'Valor inválido.';
        }
    }

    private function validateEmail(string $field, mixed $value): void
    {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = 'Email inválido.';
        }
    }

    private function validateMin(string $field, mixed $value, int $min): void
    {
        if ($value !== null && mb_strlen((string) $value) < $min) {
            $this->errors[$field][] = sprintf('Informe pelo menos %d caracteres.', $min);
        }
    }

    private function validateMax(string $field, mixed $value, int $max): void
    {
        if ($value !== null && mb_strlen((string) $value) > $max) {
            $this->errors[$field][] = sprintf('Use no máximo %d caracteres.', $max);
        }
    }

    private function validateIn(string $field, mixed $value, array $allowed): void
    {
        if ($value !== null && $value !== '' && !in_array((string) $value, $allowed, true)) {
            $this->errors[$field][] = 'Valor não permitido.';
        }
    }
}
