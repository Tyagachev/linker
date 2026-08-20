<?php

namespace App\Http\Controllers;

use App\Http\Resources\SalonResource;
use App\Models\Region;
use App\Models\Salon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SalonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $regions = Region::query()->orderBy('created_at', 'desc')
            ->get();

        $salons = Salon::query() ->orderBy('created_at', 'desc')
            ->get();
        return Inertia::render('Salon/Index', compact('regions','salons'));
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
        $name = $request->input('name');
        $region = $request->input('region');
        Salon::query()
            ->create([
                'name' => $name,
                'region' => $region
            ]);
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Salon $salon)
    {
        $regions = Region::query()->orderBy('created_at', 'desc')->get();
        $salon = SalonResource::make($salon)->resolve();
        return Inertia::render('Salon/Edit', compact('regions','salon'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Salon $salon)
    {
        $salon->update([
            'name' => $request->input('name'),
            'region' => $request->input('region')
        ]);
        return redirect()->route('salons.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salon $salon)
    {
        $salon->delete();
        return redirect()->back();
    }

    /*public function searchSalon(Request $request): JsonResponse
    {
        $request->input('query');
        $salons = Salon::query()
            ->where('name', 'like', '%' . $request->input('query') . '%')
            ->limit(15)->get();

        if (count($salons)) {
            return response()->json(['res' => SalonResource::collection($salons)->resolve()]);
        }
        return response()->json(['res' => 'Совпадения не найдены']);
    }*/
}
