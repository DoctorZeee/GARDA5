<?php

namespace App\DTOs;

use App\ValueObjects\BloodPressure;

/**
 * Typed DTO for creating a HealthLog record.
 *
 * Usage in controller:
 *   $dto = HealthLogData::fromRequest($request);
 *   $dto->statusHipertensi; // computed automatically
 */
final class HealthLogData
{
    public readonly string $statusHipertensi;

    public function __construct(
        public readonly int $userId,
        public readonly ?BloodPressure $tekananDarah,
        public readonly float $beratBadan,
        public readonly int $tinggiBadan,
        public readonly string $konsumsiGaram,   // 'less'|'ideal'|'more'
        public readonly ?string $keluhan,
        public readonly string $tanggalInput,    // Y-m-d
    ) {
        $this->statusHipertensi = $tekananDarah?->classification() ?? 'Normal';
    }

    public static function fromRequest(\Illuminate\Http\Request $request, int $userId): self
    {
        return new self(
            userId:        $userId,
            tekananDarah:  BloodPressure::tryFromString($request->input('tekanan_darah')),
            beratBadan:    (float) $request->input('berat_badan'),
            tinggiBadan:   (int)   $request->input('tinggi_badan'),
            konsumsiGaram: $request->input('konsumsi_garam'),
            keluhan:       $request->input('keluhan'),
            tanggalInput:  now()->toDateString(),
        );
    }

    public function toArray(): array
    {
        return [
            'user_id'           => $this->userId,
            'tekanan_darah'     => $this->tekananDarah?->toString(),
            'berat_badan'       => $this->beratBadan,
            'tinggi_badan'      => $this->tinggiBadan,
            'konsumsi_garam'    => $this->konsumsiGaram,
            'status_hipertensi' => $this->statusHipertensi,
            'keluhan'           => $this->keluhan,
            'tanggal_input'     => $this->tanggalInput,
        ];
    }
}
