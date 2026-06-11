<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

/**
 * Secure artisan command for provisioning the first admin account in production.
 *
 * Usage:
 *   php artisan app:create-admin
 *
 * Never seeds accounts in production via DatabaseSeeder.
 */
class CreateAdminCommand extends Command
{
    protected $signature   = 'app:create-admin';
    protected $description = 'Interactively create the first admin account for production deployment.';

    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->warn('⚠  Menjalankan di environment PRODUCTION.');
            if (! $this->confirm('Lanjutkan membuat admin?', false)) {
                $this->line('Dibatalkan.');
                return self::SUCCESS;
            }
        }

        $this->info('=== Buat Akun Admin GARDA ===');

        // Collect input
        $nik         = $this->ask('NIK (16 digit)');
        $namaLengkap = $this->ask('Nama Lengkap');
        $email       = $this->ask('Email');
        $password    = $this->secret('Password');
        $passwordConf = $this->secret('Konfirmasi Password');

        // Validate
        $validator = Validator::make(
            compact('nik', 'namaLengkap', 'email', 'password', 'passwordConf'),
            [
                'nik'          => ['required', 'string', 'size:16', 'unique:users,nik', 'regex:/^[0-9]+$/'],
                'namaLengkap'  => ['required', 'string', 'max:255'],
                'email'        => ['required', 'email', 'unique:users,email'],
                'password'     => ['required', 'string', Password::min(8)->letters()->numbers()],
                'passwordConf' => ['required', 'same:password'],
            ],
            [
                'nik.unique'      => 'NIK sudah terdaftar.',
                'email.unique'    => 'Email sudah terdaftar.',
                'nik.size'        => 'NIK harus 16 digit.',
                'nik.regex'       => 'NIK hanya boleh berisi angka.',
                'passwordConf.same' => 'Konfirmasi password tidak cocok.',
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        // Get or create default wilayah
        $wilayah = Wilayah::first();
        if (! $wilayah) {
            $wilayahName = $this->ask('Nama wilayah (default: Pusat)') ?: 'Pusat';
            $wilayah = Wilayah::create(['nama_wilayah' => $wilayahName]);
        }

        // Create admin
        $user = User::create([
            'nik'           => $nik,
            'nama_lengkap'  => $namaLengkap,
            'email'         => $email,
            'password'      => Hash::make($password),
            'tempat_lahir'  => '-',
            'tanggal_lahir' => now()->toDateString(),
            'jenis_kelamin' => 'L',
            'alamat'        => '-',
            'berat_badan'   => 70,
            'wilayah_id'    => $wilayah->id,
        ]);

        $user->role = UserRole::Admin->value;
        $user->save();

        $this->info("✅ Admin berhasil dibuat:");
        $this->table(['Field', 'Value'], [
            ['NIK',   $nik],
            ['Nama',  $namaLengkap],
            ['Email', $email],
            ['Role',  UserRole::Admin->label()],
        ]);

        $this->warn('⚠  Segera login dan perbarui data profil admin via dashboard.');

        return self::SUCCESS;
    }
}
