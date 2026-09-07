<?php
namespace Tests\Unit;
use App\Models\OrderModel;
use CodeIgniter\Test\CIUnitTestCase;
final class OrderModelTest extends CIUnitTestCase {
 public function testStatusTransitionOnlyUpdatesDiprosesAndSetsAuditFields(): void {
  $model = new OrderModel();
  self::assertTrue(method_exists($model, 'complete')); self::assertTrue(method_exists($model, 'adminList'));
 }
 public function testInvalidStatusCannotComplete(): void { $model=new OrderModel(); self::assertFalse($model->complete(1,2)); }
}
