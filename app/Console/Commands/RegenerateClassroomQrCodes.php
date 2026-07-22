<?php

namespace App\Console\Commands;

use App\Models\Classroom;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RegenerateClassroomQrCodes extends Command
{
    protected $signature   = 'classrooms:regenerate-qr {--id= : Regenerate satu kelas berdasarkan ID}';
    protected $description = 'Regenerate QR Code semua kelas ke format JSON baru (type+classroom_id+token)';

    public function handle(): int
    {
        $id = $this->option('id');

        $query = $id
            ? Classroom::where('id', $id)
            : Classroom::query();

        $classrooms = $query->get();

        if ($classrooms->isEmpty()) {
            $this->error($id ? "Kelas dengan ID {$id} tidak ditemukan." : 'Tidak ada kelas ditemukan.');
            return self::FAILURE;
        }

        $this->info("Regenerate QR Code untuk {$classrooms->count()} kelas...");
        $bar = $this->output->createProgressBar($classrooms->count());
        $bar->start();

        $directory = 'qrcodes/classrooms';
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $success = 0;
        $failed  = 0;

        foreach ($classrooms as $classroom) {
            try {
                // Hapus file lama jika ada
                if ($classroom->qr_code && Storage::disk('public')->exists($classroom->qr_code)) {
                    Storage::disk('public')->delete($classroom->qr_code);
                }

                // qr_data accessor mengembalikan JSON format baru:
                // {"type":"classroom","classroom_id":X,"token":"uuid"}
                $qrData   = $classroom->qr_data;
                $filename = "{$directory}/{$classroom->code}.svg";

                $qrImage = QrCode::format('svg')
                    ->size(400)
                    ->errorCorrection('H')
                    ->margin(2)
                    ->generate($qrData);

                Storage::disk('public')->put($filename, $qrImage);
                $classroom->update(['qr_code' => $filename]);
                $success++;

            } catch (\Exception $e) {
                $this->newLine();
                $this->warn("  ⚠ Gagal: {$classroom->name} — {$e->getMessage()}");
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Selesai: {$success} berhasil" . ($failed ? ", {$failed} gagal." : '.'));

        if ($failed > 0) {
            $this->warn('QR yang gagal dapat di-regenerate ulang lewat UI di halaman Data Kelas.');
        }

        return self::SUCCESS;
    }
}
