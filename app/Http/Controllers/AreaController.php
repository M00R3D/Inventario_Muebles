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
        // Listar todas las áreas
        $areas = Area::all();
        return view('areas.index', compact('areas'));
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
        // Validar y guardar nueva área
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        Area::create([
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('areas.index')->with('success', 'Área creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Area $area)
    {
        // Mostrar detalles de un área
        return view('areas.show', compact('area'));
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
        // Validar y actualizar área
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $area->update([
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('areas.index')->with('success', 'Área actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area)
    {
        // Eliminar área
        $area->delete();
        return redirect()->route('areas.index')->with('success', 'Área eliminada correctamente');
    }
}
