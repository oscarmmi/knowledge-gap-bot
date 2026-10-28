<?php

namespace Database\Seeders;

use App\Models\DocChunk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Reset your Harbor Desk password',
                'body' => 'To reset your Harbor Desk password, open the sign-in page and choose Forgot password. Enter the email address on your account and select Send reset link. Use the link in the email within 30 minutes to choose a new password. Check your spam folder if the email does not arrive.',
            ],
            [
                'title' => 'Update your account email address',
                'body' => 'To change the email address on your Harbor Desk account, sign in and open Account settings, then Profile. Choose Edit beside your email address and enter the new address. Confirm the change using the verification link sent to the new inbox. Your sign-in email changes after verification.',
            ],
            [
                'title' => 'Track a Harbor Desk shipment',
                'body' => 'After an order ships, Harbor Desk sends a confirmation email with a tracking link. You can also sign in, open Orders, and select the order to see its latest shipment status. Tracking may take up to one business day to show movement after the carrier receives the package.',
            ],
            [
                'title' => 'Change or cancel an order',
                'body' => 'Orders can be changed or cancelled before they enter processing. Sign in, open Orders, select the order, and choose Change order or Cancel order when that option is available. If processing has started, contact Harbor Desk support with your order number and the team will explain the available options.',
            ],
            [
                'title' => 'Harbor Desk shipping and delivery times',
                'body' => 'Standard shipping usually arrives in three to five business days after dispatch. Expedited shipping usually arrives in one to two business days. Delivery estimates do not include weekends or public holidays, and the tracking page has the latest carrier estimate for your order.',
            ],
            [
                'title' => 'Return an item to Harbor Desk',
                'body' => 'Most unused items can be returned within 30 days of delivery. Sign in and open Orders, choose the item, then select Start a return to get return instructions and a label. Pack the item securely and include its original accessories. Refunds are issued to the original payment method after the return is inspected.',
            ],
        ];

        foreach ($articles as $article) {
            DocChunk::query()->updateOrCreate(['title' => $article['title']], $article);
        }
    }
}
