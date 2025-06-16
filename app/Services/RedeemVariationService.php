<?php
namespace App\Services;

use App\Models\User;
use App\Models\Variation;
use App\Models\Order;
use App\Models\GiftCard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RedeemVariationService
{
    /**
     * Execute the service to redeem a variation for a user.
     *
     * @param User $user
     * @param Variation $variation
     * @return GiftCard
     */
    public function execute(User $user, Variation $variation): GiftCard
    {
        $employee = $user->employee;

        if ($employee->credits < $variation->price_credits) {
            abort(422, 'Not enough credits.');
        }

        return DB::transaction(function () use ($user, $variation, $employee) {
            $employee->decrement('credits', $variation->price_credits);

            // Create a new gift card
            $giftCard = GiftCard::create([
                'card_number' => $this->generateCardNumber(),
                'expiration_date' => now()->addYear(),
                'security_code' => Str::random(4),
            ]);

            // Create an order for the gift card
            Order::create([
                'company_id' => $user->getCompanyId(),
                'variation_id' => $variation->id,
                'employee_id' => $employee->id,
                'gift_card_id' => $giftCard->id,
                'cost' => $variation->cost,
                'sale_price' => $variation->price,
                'sale_price_credits' => $variation->price_credits,
            ]);

            return $giftCard;
        });
    }

    /**
     * Generate a random card number in the format XXXX XXXX XXXX XXXX.
     *
     * @return string
     */
    private function generateCardNumber(): string
    {
        return collect(range(1, 4))
            ->map(fn () => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT))
            ->implode(' ');
    }
}