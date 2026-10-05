<?php

namespace Tests\Feature;

use App\Models\BannerCategoryModel;
use App\Models\BannerModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

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

    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        \Config\Services::migrations()->setNamespace('App')->setGroup('tests')->latest();
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

        service('commands')->run('banner:permanen', []);

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

        service('commands')->run('banner:permanen', []);

        $this->assertSame(1, (int) (new BannerModel())->find($id)['is_active']);
        $this->assertCount(1, (new BannerModel())->listActiveForDisplay());
    }

    public function testNotYetStartedBannerBecomesVisible(): void
    {
        $future = date('Y-m-d', strtotime('+60 days'));
        $this->makeBanner(['start_date' => $future, 'end_date' => $future]);

        $this->assertCount(0, (new BannerModel())->listActiveForDisplay());

        service('commands')->run('banner:permanen', []);

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

        service('commands')->run('banner:permanen', []);

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

        service('commands')->run('banner:permanen', []);
        $first = (new BannerModel())->find($id);

        service('commands')->run('banner:permanen', []);
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

        service('commands')->run('banner:permanen', []);

        $this->assertCount(0, (new BannerModel())->findAll());
    }

    public function testSortOrderIsPreservedForDisplayOrder(): void
    {
        $this->makeBanner(['sort_order' => 1, 'end_date' => '2026-02-01']);
        $this->makeBanner(['sort_order' => 2, 'end_date' => '2026-02-01']);
        $this->makeBanner(['sort_order' => 3, 'end_date' => '2026-02-01']);

        service('commands')->run('banner:permanen', []);

        $order = array_column((new BannerModel())->listActiveForDisplay(), 'sort_order');
        $this->assertSame([1, 2, 3], array_map('intval', $order));
    }
}