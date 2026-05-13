<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Searching = 'searching';
    case Accepted = 'accepted';
    case OnTheWay = 'on_the_way';
    case Arrived = 'arrived';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * Human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Searching => 'Searching for Provider',
            self::Accepted => 'Accepted',
            self::OnTheWay => 'On the Way',
            self::Arrived => 'Arrived',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * Check if this status can transition to the given status.
     */
    public function canTransitionTo(BookingStatus $next): bool
    {
        return in_array($next, $this->allowedTransitions());
    }

    /**
     * Get allowed transitions from this status.
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Searching, self::Cancelled],
            self::Searching => [self::Accepted, self::Cancelled],
            self::Accepted => [self::OnTheWay, self::Cancelled],
            self::OnTheWay => [self::Arrived, self::Cancelled],
            self::Arrived => [self::InProgress, self::Cancelled],
            self::InProgress => [self::Completed],
            self::Completed => [],
            self::Cancelled => [],
        };
    }

    /**
     * Check if this is a terminal (final) status.
     */
    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled]);
    }
}
