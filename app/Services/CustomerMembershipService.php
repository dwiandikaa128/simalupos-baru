<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerMutation;
use App\Jobs\SendWaNotificationJob;
use Illuminate\Support\Facades\DB;
use Exception;

class CustomerMembershipService
{
    /**
     * Top-Up Saldo Member
     */
    public function topUp(int $customerId, float $amount, string $paymentMethod = 'cash', ?string $notes = null, ?int $userId = null): CustomerMutation
    {
        if ($amount <= 0) {
            throw new Exception('Nominal top-up harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($customerId, $amount, $paymentMethod, $notes, $userId) {
            $customer = Customer::lockForUpdate()->findOrFail($customerId);

            $balanceBefore = $customer->balance;
            $balanceAfter = $balanceBefore + $amount;

            $customer->balance = $balanceAfter;
            $customer->save();

            $mutation = CustomerMutation::create([
                'customer_id' => $customer->id,
                'type' => 'topup',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'payment_method' => $paymentMethod,
                'notes' => $notes ?? 'Top-Up Saldo Simalu Membership',
                'created_by' => $userId ?? auth()->id(),
            ]);

            try {
                SendWaNotificationJob::dispatchSync($mutation);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('WhatsApp Notification Dispatch Error: ' . $e->getMessage());
            }

            return $mutation;
        });
    }

    /**
     * Potong Saldo Member untuk Pembayaran Transaksi POS
     */
    public function payWithBalance(int $customerId, float $amount, int $orderId, ?int $userId = null): CustomerMutation
    {
        if ($amount <= 0) {
            throw new Exception('Nominal pembayaran harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($customerId, $amount, $orderId, $userId) {
            $customer = Customer::lockForUpdate()->findOrFail($customerId);

            if ($customer->balance < $amount) {
                throw new Exception("Saldo Simalu Membership tidak mencukupi. (Saldo: Rp " . number_format($customer->balance, 0, ',', '.') . ")");
            }

            $balanceBefore = $customer->balance;
            $balanceAfter = $balanceBefore - $amount;

            $customer->balance = $balanceAfter;
            $customer->save();

            $mutation = CustomerMutation::create([
                'customer_id' => $customer->id,
                'order_id' => $orderId,
                'type' => 'payment',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'notes' => 'Pembayaran Transaksi POS',
                'created_by' => $userId ?? auth()->id(),
            ]);

            try {
                SendWaNotificationJob::dispatchSync($mutation);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('WhatsApp Notification Dispatch Error: ' . $e->getMessage());
            }

            return $mutation;
        });
    }

    /**
     * Masukkan Kembalian Belanja ke Saldo Member
     */
    public function saveChangeToBalance(int $customerId, float $amount, int $orderId, ?int $userId = null): CustomerMutation
    {
        if ($amount <= 0) {
            throw new Exception('Nominal kembalian harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($customerId, $amount, $orderId, $userId) {
            $customer = Customer::lockForUpdate()->findOrFail($customerId);

            $balanceBefore = $customer->balance;
            $balanceAfter = $balanceBefore + $amount;

            $customer->balance = $balanceAfter;
            $customer->save();

            $mutation = CustomerMutation::create([
                'customer_id' => $customer->id,
                'order_id' => $orderId,
                'type' => 'change_deposit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'notes' => 'Kembalian Transaksi POS dimasukkan ke Saldo',
                'created_by' => $userId ?? auth()->id(),
            ]);

            try {
                SendWaNotificationJob::dispatchSync($mutation);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('WhatsApp Notification Dispatch Error: ' . $e->getMessage());
            }

            return $mutation;
        });
    }
}
