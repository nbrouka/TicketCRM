<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class RateLimitService
{
    private const RATE_LIMIT_KEY_PREFIX = 'ticket_submission_limit';

    private const TIME_WINDOW_SECONDS = 86400; // 24 hours in seconds

    /**
     * Check if a user can submit a ticket based on email or phone
     */
    public function canSubmitTicket(?string $email = null, ?string $phone = null): bool
    {
        if (! $email && ! $phone) {
            return false;
        }

        // Check if either email or phone already has a submission
        if ($email && ! $this->canSubmitWithKey($this->getEmailKey($email))) {
            return false;
        }

        if ($phone && ! $this->canSubmitWithKey($this->getPhoneKey($phone))) {
            return false;
        }

        return true;
    }

    /**
     * Check if a submission is allowed for a specific key
     */
    private function canSubmitWithKey(string $key): bool
    {
        if (empty($key)) {
            return true; // If key is empty, allow submission
        }

        $currentCount = Cache::get($key);

        return $currentCount === null || (int) $currentCount < 1;
    }

    /**
     * Record a ticket submission for an email or phone
     */
    public function recordTicketSubmission(?string $email = null, ?string $phone = null): void
    {
        if (! $email && ! $phone) {
            return;
        }

        // Record submission for both email and phone if provided
        if ($email) {
            $emailKey = $this->getEmailKey($email);
            if (! empty($emailKey)) {
                Cache::increment($emailKey);
                Cache::put($emailKey, Cache::get($emailKey), self::TIME_WINDOW_SECONDS);
            }
        }

        if ($phone) {
            $phoneKey = $this->getPhoneKey($phone);
            if (! empty($phoneKey)) {
                Cache::increment($phoneKey);
                Cache::put($phoneKey, Cache::get($phoneKey), self::TIME_WINDOW_SECONDS);
            }
        }
    }

    /**
     * Get the remaining time until the rate limit resets
     */
    public function getRemainingTime(?string $email = null, ?string $phone = null): ?int
    {
        if (! $email && ! $phone) {
            return null;
        }

        $key = $email ? $this->getEmailKey($email) : $this->getPhoneKey($phone);

        if (empty($key)) {
            return null;
        }

        return null;
    }

    /**
     * Get the email-based rate limit key
     */
    private function getEmailKey(?string $email): string
    {
        if (! $email) {
            return '';
        }

        return self::RATE_LIMIT_KEY_PREFIX.':email:'.md5($email);
    }

    /**
     * Get the phone-based rate limit key
     */
    private function getPhoneKey(?string $phone): string
    {
        if (! $phone) {
            return '';
        }

        return self::RATE_LIMIT_KEY_PREFIX.':phone:'.md5($phone);
    }
}
