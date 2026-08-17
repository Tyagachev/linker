<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\ConferenceSalon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConferenceSalonController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'conference_id' => ['required', 'exists:conferences,id'],
            'salon_id' => ['required', 'exists:salons,id'],
            'visitor_token' => ['nullable', 'uuid'],
        ]);

        // Если этот браузер уже заходил в эту конференцию,
        // повторную запись не создаём.
        if ($validated['visitor_token']) {
            $existing = ConferenceSalon::query()
                ->where('conference_id', $validated['conference_id'])
                ->where('visitor_token', $validated['visitor_token'])
                ->first();

            if ($existing) {
                return response()->json([
                    'token' => $existing->visitor_token,
                    'link' => $existing->conference->link,
                ]);
            }
        }

        $attendance = ConferenceSalon::query()->create([
            'conference_id' => $validated['conference_id'],
            'salon_id' => $validated['salon_id'],
            'visitor_token' => Str::uuid(),
        ]);

        return response()->json([
            'token' => $attendance->visitor_token,
            'link' => $attendance->conference->link,
        ]);
    }
}
