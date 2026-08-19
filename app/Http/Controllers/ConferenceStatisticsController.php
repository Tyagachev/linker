<?php

namespace App\Http\Controllers;

use App\Http\Resources\StatisticsResource;
use App\Models\Conference;
use App\Models\Salon;
use Inertia\Inertia;

class ConferenceStatisticsController extends Controller
{
    public function index()
    {
        return Inertia::render('Statistic/Index', []);
    }

    public function show($current, $last)
    {
        $conferences = Conference::query()
            ->whereDate('scheduled_date', '>=', $current)
            ->whereDate('scheduled_date', '<=', $last)
            ->OrderBy('scheduled_date', 'DESC')
            ->get();
        $allSalons = Salon::all();
        $statistics = StatisticsResource::collection($conferences)->resolve();
        return response()->json([
            'statistics' => $statistics,
            'allSalons' => $allSalons,
        ]);
    }
}
