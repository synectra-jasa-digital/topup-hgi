<?php

namespace Tests\Feature;

use App\Models\BannerCategoryModel;
use App\Models\BannerModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\StreamFilterTrait;

/**
 * Guard perintah `banner:permanen`.
 *
 * Banner yang punya end_date di masa lalu hilang dari beranda tanpa error,
 * karena listActiveForDisplay() menyaring lewat tanggal. Perintah ini mengubah
 * semua banner menjadi permanen: is_active = 1 dan jadwal dikosongkan.
 *
 * Semuanya harus idempoten — menjalankan perintah dua kali tidak boleh
 * merusak data atau menggandakan efek.
 */
final class MakeBannerPermanentCommandTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    // CLI::write() menulis ke STDOUT. StreamFilterTrait menangkapnya supaya
    // output perintah tidak bocor ke hasil PHPUnit.
    use StreamFilterTrait;

    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpStreamFilterTrait();
        $this->db = db_connect('tests');
        \Config\Services::migrations()->setNamespace('App')->setGroup('tests')->latest();
    }

    protected function tearDown(): void
    {
        $this->tearDownStreamFilterTrait();
        parent::tearDown();
    }

    /** Jalankan perintah dan kembalikan output yang dibuangnya. */
    private function runCommand(array $params = []): string
    {
        $this->resetStreamFilterBuffer();
        service('commands')->run('banner:permanen', $params);

        return $this->getStreamFilterBuffer();
    }

    private function categoryId(): int
    {
        $categories = new BannerCategoryModel();
        $categories->db->query('PRAGMA foreign_keys = OFF');

        $existing = $categories->orderBy('id')->first();
        if ($existing !== null) {
            return (int) $existing['id'];
        }

        return (int) $categories->insert([
            'name'      => 'Promo Utama',
            'is_active' => 1,
        ]);
    }

    private function makeBanner(array $overrides = []): int
    {
        $banners = new BannerModel();

        return (int) $banners->insert($overrides + [
            'banner_category_id' => $this->categoryId(),
            'image_path'         => 'assets/uploads/banners/' . bin2hex(random_bytes(4)) . '.png',
            'link_url'           => null,
            'start_date'         => null,
            'end_date'           => null,
            'sort_order'         => 0,
            'is_active'          => 1,
        ]);
    }

    public function testExpiredBannerBecomesVisible(): void
    {
        // Banner dengan end_date di masa lalu — inilah penyebab hero kosong.
        $id = $this->makeBanner([
            'start_date' => '2026-01-01',
            'end_date'   => '2026-02-01',
        ]);

        $this->assertCount(
            0,
            (new BannerModel())->listActiveForDisplay(),
            'setup: banner kedaluwarsa harusnya tidak tampil sebelum perintah dijalankan'
        );

        $this->runCommand();

        $this->assertCount(
            1,
            (new BannerModel())->listActiveForDisplay(),
            'setelah banner:permanen, banner harus tampil di beranda'
        );

        $banner = (new BannerModel())->find($id);
        $this->assertSame(1, (int) $banner['is_active']);
        $this->assertNull($banner['start_date'], 'start_date harus dikosongkan');
        $this->assertNull($banner['end_date'], 'end_date harus dikosongkan');
    }

    public function testInactiveBannerIsReactivated(): void
    {
        $id = $this->makeBanner(['is_active' => 0]);

        $this->runCommand();

        $this->assertSame(1, (int) (new BannerModel())->find($id)['is_active']);
        $this->assertCount(1, (new BannerModel())->listActiveForDisplay());
    }

    public function testNotYetStartedBannerBecomesVisible(): void
    {
        $future = date('Y-m-d', strtotime('+60 days'));
        $this->makeBanner(['start_date' => $future, 'end_date' => $future]);

        $this->assertCount(0, (new BannerModel())->listActiveForDisplay());

        $this->runCommand();

        $this->assertCount(1, (new BannerModel())->listActiveForDisplay());
    }

    public function testOtherColumnsAreNotTouched(): void
    {
        $image = 'assets/uploads/banners/jaga-ini.png';
        $id    = $this->makeBanner([
            'image_path' => $image,
            'link_url'   => 'https://example.test/promo',
            'sort_order' => 7,
        ]);

        $this->runCommand();

        $banner = (new BannerModel())->find($id);
        $this->assertSame($image, $banner['image_path'], 'image_path tidak boleh berubah');
        $this->assertSame('https://example.test/promo', $banner['link_url']);
        $this->assertSame(7, (int) $banner['sort_order']);
        $this->assertNotEmpty($banner['id']);
    }

    public function testRunningTwiceIsIdempotent(): void
    {
        $id = $this->makeBanner([
            'start_date' => '2026-01-01',
            'end_date'   => '2026-02-01',
        ]);

        $this->runCommand();
        $first = (new BannerModel())->find($id);

        $this->runCommand();
        $second = (new BannerModel())->find($id);

        $this->assertSame($first['updated_at'], $second['updated_at'], 'jalankan kedua shouldn mengubah updated_at');
        $this->assertSame($first['start_date'], $second['start_date']);
        $this->assertSame($first['end_date'], $second['end_date']);
        $this->assertSame($first['is_active'], $second['is_active']);
    }

    public function testNoBannerExistsIsHandledWithoutError(): void
    {
        // Jangan biarkan perintah gagal quando tabel kosong.
        $this->assertCount(0, (new BannerModel())->findAll());

        $this->runCommand();

        $this->assertCount(0, (new BannerModel())->findAll());
    }

    public function testDryRunReportsChangesWithoutSaving(): void
    {
        $id = $this->makeBanner([
            'start_date' => '2026-01-01',
            'end_date'   => '2026-02-01',
        ]);

        $output = $this->runCommand(['--dry-run']);

        $this->assertStringContainsString('dry-run', $output, 'perintah harus survive mode dry-run');
        $this->assertStringContainsString('SUDAH KEDALUWARSA', $output, 'alasan harus disebutkan eksplisit');

        $banner = (new BannerModel())->find($id);
        $this->assertSame('2026-01-01', $banner['start_date'], 'dry-run tidak boleh menyimpan');
        $this->assertSame('2026-02-01', $banner['end_date'], 'dry-run tidak boleh menyimpan');
    }

    public function testCommandReportsTheExpiredReasonForSilentFailures(): void
    {
        // Banner yang tanggalnya lewat hilang dari beranda tanpa error —
        // output perintah harus menyebut penyebabnya secara eksplisit.
        $this->makeBanner(['end_date' => '2026-02-01']);

        $output = $this->runCommand();

        $this->assertStringContainsString('SUDAH KEDALUWARSA', $output);
        $this->assertStringContainsString('sekarang permanen', $output);
    }

    public function testSortOrderIsPreservedForDisplayOrder(): void
    {
        $this->makeBanner(['sort_order' => 1, 'end_date' => '2026-02-01']);
        $this->makeBanner(['sort_order' => 2, 'end_date' => '2026-02-01']);
        $this->makeBanner(['sort_order' => 3, 'end_date' => '2026-02-01']);

        $this->runCommand();

        $order = array_column((new BannerModel())->listActiveForDisplay(), 'sort_order');
        $this->assertSame([1, 2, 3], array_map('intval', $order));
    }
}