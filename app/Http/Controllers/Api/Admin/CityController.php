<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CityRequest;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller 
{
    public function index()
    {
        return City::query()->orderBy('name')->get();
    }

    public function store(CityRequest $request)
    {
        $city = City::create($request->validated());
        return response()->json($city, 201);
    }

    public function show(City $city)
    {
        return $city;
    }

    public function update(CityRequest $request, City $city)
    {
        $city->update($request->validated());
        return response()->json($city);
    }

    public function destroy(City $city)
    {
        $city->delete();
        return response()->noContent();
    }
}
