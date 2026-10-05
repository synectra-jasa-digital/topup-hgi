<?php

namespace Tests\Unit;

use App\Libraries\ReportExporter;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Guard untuk path traversal lewat parameter `period` pada export laporan.
 *
 * $period tiba dari dua sumber: query string HTTP (ReportController) dan
 * callback data Telegram (ReportCommand). Keduanya attacker-influenced,
 * jadi validasi harus hidup di ReportExporter — bukan hanya di controller.
 */
class ReportPeriodValidationTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Data provider: nilai period yang TIDAK boleh dipakai untuk membangun path.
     */
    public static function maliciousPeriods(): array
    {
        return [
            'parent traversal'        => ['../../../../pwned'],
            'absolute posix path'     => ['/etc/cron.d/pwned'],
            'windows absolute'        => ['C:\\Windows\\Temp\\pwned'],
            'null byte'               => ["this_month\0../../pwned"],
            'nested traversal'        => ['this_month/../../pwned'],
            'leading slash'           => ['/tmp/pwned'],
            'backslash traversal'     => ['..\\..\\..\\pwned'],
        ];
    }

    /**
     * @dataProvider maliciousPeriods
     */
    public function testMaliciousPeriodNeverEscapesSystemTempDir(string $period): void
    {
        $exporter = new ReportExporter();

        $path = $exporter->tempFilePathFor($period, 'xlsx');

        // File harus tetap berada di dalam system temp dir.
        $this->assertStringStartsWith(
            rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR,
            $path,
            "period '{$period}' menghasilkan path di luar system temp dir: {$path}"
        );

        // Dan tidak boleh mengandung segmen navigasi path.
        $this->assertStringNotContainsString('..', $path, "path masih mengandung '..': {$path}");
    }

    /**
     * @dataProvider maliciousPeriods
     */
    public function testMaliciousPeriodIsRejectedOutright(string $period): void
    {
        $exporter = new ReportExporter();

        $this->assertFalse(
            $exporter->isValidPeriod($period),
            "period '{$period}' seharusnya ditolak"
        );
    }

    public function testUnknownPeriodFallsBackToDefaultInsteadOfBeingUsed(): void
    {
        $exporter = new ReportExporter();

        $this->assertFalse($exporter->isValidPeriod('tahun_depan'));
        $this->assertSame('this_month', $exporter->normalizePeriod('tahun_depan'));
    }

    public function testKnownPeriodsAreAccepted(): void
    {
        $exporter = new ReportExporter();

        foreach (['today', '7days', 'this_month', 'last_month'] as $period) {
            $this->assertTrue($exporter->isValidPeriod($period), "period '{$period}' seharusnya valid");
            $this->assertSame($period, $exporter->normalizePeriod($period));
        }

        // Format YYYY-MM juga sah.
        $this->assertTrue($exporter->isValidPeriod('2026-09'));
        $this->assertSame('2026-09', $exporter->normalizePeriod('2026-09'));
    }

    public function testYearMonthOutOfRangeIsRejected(): void
    {
        $exporter = new ReportExporter();

        // 13 bukan bulan valid; 00 bukan tahun valid.
        $this->assertFalse($exporter->isValidPeriod('2026-13'));
        $this->assertFalse($exporter->isValidPeriod('0000-05'));
    }

    public function testPathUsesGeneratedNameNotUserPeriod(): void
    {
        $exporter = new ReportExporter();

        $path = $exporter->tempFilePathFor('this_month', 'xlsx');

        $this->assertStringEndsWith('.xlsx', $path);
        // Nama file tidak boleh memuat input mentah dari period.
        $this->assertStringNotContainsString('/', $path);
        $this->assertSame(
            1,
            preg_match('#' . preg_quote(basename($path), '#') . '$#', $path),
            'path harus berupa satu filename di dalam temp dir'
        );
    }
}