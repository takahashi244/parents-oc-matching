<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class ModerationLog
{
    public static function record(array $data): void
    {
        $defaults = [
            'timestamp'    => now()->toIso8601String(),
            'review_id'    => null,
            'action'       => null,
            'moderator_id' => auth()->id(),
            'reason'       => null,
        ];

        $payload = array_merge($defaults, $data);

        $line = implode(',', [
            $payload['timestamp'],
            $payload['review_id'],
            $payload['action'],
            $payload['moderator_id'],
            str_replace(["\n", "\r", ',' ], ' ', $payload['reason'] ?? ''),
        ]);

        Storage::append('moderation_logs.csv', $line);
    }
}
