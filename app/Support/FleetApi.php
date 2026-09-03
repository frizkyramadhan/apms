<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class FleetApi
{
    public function enabled(): bool
    {
        $flag = config('fleet.enabled');

        if ($flag === null || $flag === '') {
            return true;
        }

        return ! in_array(strtolower((string) $flag), ['false', '0', 'no', 'off'], true);
    }

    public function projects(): array
    {
        return $this->list(config('fleet.projects_url'));
    }

    public function equipments(): array
    {
        return $this->list(config('fleet.units_url'));
    }

    private function list(string $url): array
    {
        if (! $this->enabled()) {
            throw new RuntimeException('Fleet API is disabled (FLEET_API_ENABLED=false)');
        }

        try {
            $response = Http::timeout(max(1, (int) round(config('fleet.timeout_ms') / 1000)))
                ->acceptJson()
                ->get($url);
        } catch (\Throwable $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }

        if ($response->failed()) {
            throw new RuntimeException("Fleet API error: {$response->status()} {$url}");
        }

        $payload = $response->json();
        if ($payload === null) {
            throw new RuntimeException("Fleet API returned non-JSON response for {$url}");
        }

        return $this->unwrapList($payload);
    }

    private function unwrapList(mixed $payload): array
    {
        if (is_array($payload) && array_is_list($payload)) {
            return $payload;
        }

        if (is_array($payload) && isset($payload['data']) && is_array($payload['data'])) {
            return $payload['data'];
        }

        throw new RuntimeException('Fleet API returned unexpected list payload');
    }
}
