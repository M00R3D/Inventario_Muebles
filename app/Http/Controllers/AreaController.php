<?php
// app/Http/Controllers/AreaController.php
namespace App\Http\Controllers;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areas = Area::all();
        return response()->json($areas);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Mostrar formulario de creación de área
        return view('areas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);
        $area = Area::create(['nombre' => $request->nombre]);
        return response()->json($area, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Area $area)
    {
        return response()->json($area);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Area $area)
    {
        // Mostrar formulario de edición de área
        return view('areas.edit', compact('area'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Area $area)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);
        $area->update(['nombre' => $request->nombre]);
        return response()->json($area);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area)
    {
        $area->delete();
        return response()->json(['message' => 'Área eliminada correctamente']);
    }
}
