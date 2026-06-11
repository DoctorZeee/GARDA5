<?php

namespace App\ValueObjects;

use InvalidArgumentException;

/**
 * Immutable value object representing a blood pressure reading.
 *
 * Usage:
 *   $bp = BloodPressure::fromString('140/90');
 *   $bp->systolic();        // 140
 *   $bp->diastolic();       // 90
 *   $bp->classification();  // 'Sedang'
 *   $bp->isHypertensive();  // true
 *   (string) $bp;           // '140/90'
 *
 *   BloodPressure::isValidFormat('120/abc'); // false
 */
final class BloodPressure
{
    private function __construct(
        private readonly int $systolic,
        private readonly int $diastolic,
    ) {}

    // ─── Factory ─────────────────────────────────────────────────────────────

    /**
     * Parse from "120/80" string.
     *
     * @throws InvalidArgumentException on invalid format.
     */
    public static function fromString(string $value): self
    {
        if (! self::isValidFormat($value)) {
            throw new InvalidArgumentException("Format tekanan darah tidak valid: '{$value}'. Gunakan format SIS/DIA, contoh: 120/80.");
        }

        [$sys, $dia] = array_map('intval', explode('/', trim($value), 2));

        return new self($sys, $dia);
    }

    /**
     * Safe factory — returns null instead of throwing.
     */
    public static function tryFromString(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return self::fromString($value);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    // ─── Validation ──────────────────────────────────────────────────────────

    /**
     * Validate the "SIS/DIA" string format without constructing the object.
     */
    public static function isValidFormat(?string $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        if (! preg_match('/^\d{2,3}\/\d{2,3}$/', trim($value))) {
            return false;
        }

        [$sys, $dia] = array_map('intval', explode('/', $value, 2));

        // Physiologically plausible ranges
        return $sys >= 50 && $sys <= 300
            && $dia >= 30 && $dia <= 200
            && $sys > $dia;
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function systolic(): int
    {
        return $this->systolic;
    }

    public function diastolic(): int
    {
        return $this->diastolic;
    }

    // ─── Classification ──────────────────────────────────────────────────────

    /**
     * WHO / JNC-8 simplified classification.
     * Returns 'Normal' | 'Ringan' | 'Sedang' | 'Berat'
     */
    public function classification(): string
    {
        if ($this->systolic >= 160 || $this->diastolic >= 100) {
            return 'Berat';
        }

        if ($this->systolic >= 140 || $this->diastolic >= 90) {
            return 'Sedang';
        }

        if ($this->systolic >= 130 || $this->diastolic >= 85) {
            return 'Ringan';
        }

        return 'Normal';
    }

    public function isHypertensive(): bool
    {
        return $this->classification() !== 'Normal';
    }

    // ─── Representation ──────────────────────────────────────────────────────

    public function toString(): string
    {
        return "{$this->systolic}/{$this->diastolic}";
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function equals(self $other): bool
    {
        return $this->systolic === $other->systolic
            && $this->diastolic === $other->diastolic;
    }
}
