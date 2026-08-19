<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConferenceResource;
use App\Models\Conference;
use App\Models\Salon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'upcoming');

        $query = Conference::query();

        if ($tab === 'past') {
            $query->where('scheduled_date', '<', Carbon::today());
        } else {
            $tab = 'upcoming';

            $query->where('scheduled_date', '>=', Carbon::today());
        }

        $conferences = $query
            ->orderBy('scheduled_date', 'desc')
            ->get();

        return Inertia::render('Conference/Index', [
            'conferences' => ConferenceResource::collection($conferences)->resolve(),
            'activeTab' => $tab,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'min:3'],
            'link' => ['required', 'url'],
            'comment' => ['nullable', 'string'],
            'scheduledDate' => ['required', 'date'],
            'deadlineAt' => ['required', 'date', 'after_or_equal:scheduledDate'],
            'active' => ['boolean'],
        ]);

        Conference::query()->create([
            'title' => $request->input('title'),
            'comment' => $request->input('comment'),
            'scheduled_date' => $request->input('scheduledDate'),
            'deadline_at' => $request->input('deadlineAt'),
            'link' => $request->input('link'),
            'active' => $request->input('active'),
        ]);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Conference $conference)
    {
        $salons = Salon::query()->orderBy('region', 'asc')->get();
        $conference = ConferenceResource::make($conference)->resolve();
        return Inertia::render('Conference/Show', compact('conference', 'salons'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conference $conference)
    {
        $conference->delete();
        return redirect()->back();
    }
}
