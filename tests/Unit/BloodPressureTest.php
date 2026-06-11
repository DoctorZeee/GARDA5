<?php

namespace Tests\Unit;

use App\ValueObjects\BloodPressure;
use InvalidArgumentException;
use Tests\TestCase;

class BloodPressureTest extends TestCase
{
    // ─── Format Validation ────────────────────────────────────────────────────

    public function test_valid_format(): void
    {
        $this->assertTrue(BloodPressure::isValidFormat('120/80'));
        $this->assertTrue(BloodPressure::isValidFormat('140/90'));
        $this->assertTrue(BloodPressure::isValidFormat('90/60'));
    }

    public function test_invalid_formats(): void
    {
        $this->assertFalse(BloodPressure::isValidFormat(null));
        $this->assertFalse(BloodPressure::isValidFormat(''));
        $this->assertFalse(BloodPressure::isValidFormat('12080'));
        $this->assertFalse(BloodPressure::isValidFormat('abc/def'));
        $this->assertFalse(BloodPressure::isValidFormat('80/140'));   // diastolic > systolic
    }

    public function test_from_string_parses_correctly(): void
    {
        $bp = BloodPressure::fromString('120/80');
        $this->assertEquals(120, $bp->systolic());
        $this->assertEquals(80,  $bp->diastolic());
    }

    public function test_from_string_throws_on_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        BloodPressure::fromString('not-a-bp');
    }

    public function test_try_from_string_returns_null_on_invalid(): void
    {
        $this->assertNull(BloodPressure::tryFromString(null));
        $this->assertNull(BloodPressure::tryFromString(''));
        $this->assertNull(BloodPressure::tryFromString('invalid'));
    }

    // ─── Classification ───────────────────────────────────────────────────────

    /** @dataProvider classificationProvider */
    public function test_classification(string $bp, string $expected): void
    {
        $this->assertEquals($expected, BloodPressure::fromString($bp)->classification());
    }

    public static function classificationProvider(): array
    {
        return [
            ['120/80',  'Normal'],
            ['125/82',  'Normal'],
            ['130/85',  'Ringan'],
            ['135/88',  'Ringan'],
            ['140/90',  'Sedang'],
            ['155/95',  'Sedang'],
            ['160/100', 'Berat'],
            ['180/110', 'Berat'],
        ];
    }

    public function test_is_hypertensive_for_normal(): void
    {
        $this->assertFalse(BloodPressure::fromString('120/80')->isHypertensive());
    }

    public function test_is_hypertensive_for_elevated(): void
    {
        $this->assertTrue(BloodPressure::fromString('145/95')->isHypertensive());
    }

    // ─── Representation ──────────────────────────────────────────────────────

    public function test_to_string(): void
    {
        $bp = BloodPressure::fromString('140/90');
        $this->assertEquals('140/90', (string) $bp);
        $this->assertEquals('140/90', $bp->toString());
    }

    public function test_equals(): void
    {
        $a = BloodPressure::fromString('120/80');
        $b = BloodPressure::fromString('120/80');
        $c = BloodPressure::fromString('140/90');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}
