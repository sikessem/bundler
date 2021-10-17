<?php

namespace Phapp;

class Token {
    public function __construct(null|string|array $value = null) {
        $this->setValue($value);
    }

    protected null|string|array $value;

    public function setValue(null|string|array $value): void {
        $this->value = $value;
    }

    public function getName(): ?string {
        return is_array($this->value) ? token_name($this->value[0]) : $this->value;
    }

    public function getId(): ?int {
        return is_array($this->value) ? $this->value[0] : null;
    }

    public function getContent(): ?string {
        return is_array($this->value) ? $this->value[1] : $this->value;
    }

    public function getLine(): ?int {
        return is_array($this->value) ? $this->value[2] : null;
    }

    public function isString(): bool {
        return is_string($this->value);
    }

    public function isNotString(): bool {
        return !$this->isString();
    }

    public function isNull(): bool {
        return is_null($this->value);
    }

    public function isNotNull(): bool {
        return !$this->isNull();
    }

    public function isArray(): bool {
        return is_array($this->value);
    }

    public function isNotArray(): bool {
        return !$this->isArray();
    }

    public function is(int|string $ref): bool {
        return is_string($ref) || $this->isNotArray() ? $this->getContent() === $ref : $this->getId() === $ref;
    }

    public function isNot(int|string $ref): bool {
        return !$this->is($ref);
    }

    public function in(int|string|array ...$refs): bool {
        foreach ($refs as $ref)
            if (is_array($ref)) {
                if ($this->in(...$ref))
                    return true;
            }
            elseif ($this->is($ref))
                return true;
        return false;
    }

    public function notIn(int|string|array ...$refs): bool {
        return !$this->in(...$refs);
    }

    public function __toString(): string {
        return $this->getContent();  
    }
}