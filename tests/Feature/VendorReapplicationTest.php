<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vendor;
use App\Repositories\VendorApprovalRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorReapplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejected_application_page_is_available(): void
    {
        $this->get('/vendor/rejected')
            ->assertOk()
            ->assertSee('Your application can be corrected and resubmitted.')
            ->assertSee('Reapply for verification')
            ->assertSee("path==='address'&&Array.isArray(data)", false);
    }

    public function test_rejected_vendor_can_correct_and_reapply_without_creating_a_new_account(): void
    {
        [$user, $vendor] = $this->rejectedVendor();
        $this->completeApplication($vendor);

        $this->actingAs($user)->postJson('/api/vendor/submit')
            ->assertOk()
            ->assertJsonPath('data.id', $vendor->id)
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.rejection_reason', null);

        $this->assertDatabaseCount('vendors', 1);
        $this->assertDatabaseHas('vendors', ['id' => $vendor->id, 'status' => 'pending', 'rejection_reason' => null]);
        $this->assertNotNull($vendor->fresh()->submitted_at);
    }

    public function test_vendor_must_replace_rejected_required_document_before_reapplying(): void
    {
        [$user, $vendor] = $this->rejectedVendor();
        $this->completeApplication($vendor, panStatus: 'rejected');

        $this->actingAs($user)->postJson('/api/vendor/submit')
            ->assertUnprocessable()
            ->assertJsonPath('errors.documents.0', 'PAN document must be corrected before reapplying.');

        $this->assertDatabaseHas('vendors', ['id' => $vendor->id, 'status' => 'rejected']);
    }

    public function test_pending_application_is_read_only_but_remains_viewable(): void
    {
        [$user, $vendor] = $this->rejectedVendor();
        $this->completeApplication($vendor);
        $vendor->update(['status' => 'pending']);

        $this->actingAs($user)->getJson('/api/vendor/address')->assertOk();
        $this->actingAs($user)->getJson('/api/vendor/documents')->assertOk();
        $this->actingAs($user)->putJson('/api/vendor/profile', ['store_name' => 'Changed While Pending'])
            ->assertUnprocessable()
            ->assertJsonPath('errors.status.0', 'Your application is under review and cannot be edited.');
        $this->actingAs($user)->putJson('/api/vendor/address', [
            'type' => 'business', 'address_line_1' => 'Changed Address', 'city' => 'Delhi',
            'state' => 'Delhi', 'country' => 'India', 'postal_code' => '110001',
        ])->assertUnprocessable();
        $this->actingAs($user)->putJson('/api/vendor/bank-account', [
            'account_holder_name' => 'Changed Name', 'bank_name' => 'Changed Bank',
            'account_number' => '9999999999', 'ifsc_code' => 'DEMO0009999', 'account_type' => 'savings',
        ])->assertUnprocessable();
        $document = $vendor->documents()->firstOrFail();
        $this->actingAs($user)->deleteJson('/api/vendor/documents/'.$document->id)->assertUnprocessable();
        $this->actingAs($user)->postJson('/api/vendor/submit')
            ->assertUnprocessable()
            ->assertJsonPath('errors.status.0', 'Your application is already under review.');

        $this->assertDatabaseHas('vendors', ['id' => $vendor->id, 'store_name' => 'Reapply Crafts', 'status' => 'pending']);
    }

    public function test_admin_pending_queue_uses_latest_submission_time(): void
    {
        $olderUser = User::create(['first_name' => 'Older', 'last_name' => 'Vendor', 'email' => 'older@example.com', 'password' => 'password', 'status' => 'active']);
        $newerUser = User::create(['first_name' => 'Newer', 'last_name' => 'Vendor', 'email' => 'newer@example.com', 'password' => 'password', 'status' => 'active']);
        $older = Vendor::create(['user_id' => $olderUser->id, 'store_name' => 'Older Store', 'store_slug' => 'older-store', 'business_name' => 'Older Store', 'status' => 'pending', 'submitted_at' => now()->subDay()]);
        $newer = Vendor::create(['user_id' => $newerUser->id, 'store_name' => 'Reapplied Store', 'store_slug' => 'reapplied-store', 'business_name' => 'Reapplied Store', 'status' => 'pending', 'submitted_at' => now()]);

        $queue = app(VendorApprovalRepository::class)->pending();

        $this->assertSame([$newer->id, $older->id], $queue->pluck('id')->all());
    }

    private function rejectedVendor(): array
    {
        $user = User::create(['first_name' => 'Rejected', 'last_name' => 'Artisan', 'email' => 'reapply@example.com', 'password' => 'password', 'status' => 'active']);
        $vendor = Vendor::create([
            'user_id' => $user->id, 'store_name' => 'Reapply Crafts', 'store_slug' => 'reapply-crafts',
            'business_name' => 'Reapply Crafts Private Limited', 'status' => 'rejected',
            'rejection_reason' => 'Upload a clear PAN copy and verify the business address.',
        ]);

        return [$user, $vendor];
    }

    private function completeApplication(Vendor $vendor, string $panStatus = 'pending'): void
    {
        $vendor->addresses()->create([
            'type' => 'business', 'address_line_1' => '20 Craft Road', 'city' => 'Jaipur',
            'state' => 'Rajasthan', 'country' => 'India', 'postal_code' => '302001',
        ]);
        $vendor->bankAccount()->create([
            'account_holder_name' => 'Rejected Artisan', 'bank_name' => 'Demo Bank',
            'account_number' => '1234567890', 'ifsc_code' => 'DEMO0001234', 'account_type' => 'savings',
        ]);
        foreach (['pan' => $panStatus, 'aadhaar' => 'pending', 'cancelled_cheque' => 'pending'] as $type => $status) {
            $vendor->documents()->create([
                'document_type' => $type, 'document_number' => strtoupper($type).'-1234',
                'file_name' => $type.'.pdf', 'file_path' => 'vendors/documents/'.$type.'.pdf',
                'mime_type' => 'application/pdf', 'file_size' => 1000, 'status' => $status,
                'remarks' => $status === 'rejected' ? 'Document is blurred.' : null,
            ]);
        }
    }
}
