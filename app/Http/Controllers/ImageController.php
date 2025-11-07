<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Notificacion;
use App\Models\Usuario;
use Carbon\Carbon;

class ImageController extends Controller
{
    public function index()
    {
        $public = public_path();
        $entries = @scandir($public) ?: [];
        $dirs = [];
        foreach ($entries as $e) {
            if ($e === '.' || $e === '..') continue;
            $path = $public . DIRECTORY_SEPARATOR . $e;
            if (is_dir($path)) $dirs[] = $e;
        }
        sort($dirs);
        return view('images.index', ['dirs' => $dirs]);
    }

    public function dirs(Request $request)
    {
        $public = public_path();
        $entries = @scandir($public) ?: [];
        $dirs = [];
        foreach ($entries as $e) {
            if ($e === '.' || $e === '..') continue;
            $path = $public . DIRECTORY_SEPARATOR . $e;
            if (is_dir($path)) $dirs[] = $e;
        }
        sort($dirs);
        return response()->json(['dirs' => $dirs]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120', // max 5MB por archivo
            'folder' => 'nullable|string|max:80',
            'filename_base' => 'nullable|string|max:120',
        ]);
        $folder = trim((string)$request->input('folder', 'uploads'));
        $folder = preg_replace('/[^A-Za-z0-9\-_]/', '_', $folder) ?: 'uploads';
        $targetDir = public_path($folder);
        if (! is_dir($targetDir)) {
            if (! @mkdir($targetDir, 0755, true)) {
                return response()->json(['message' => 'No se pudo crear la carpeta de destino'], 500);
            }
        }
        $saved = [];
        $base = trim((string)$request->input('filename_base', ''));
        $base = preg_replace('/[^A-Za-z0-9\-_]/', '_', $base);
        foreach ($request->file('files') as $index => $f) {
            if (! $f->isValid()) continue;
            $ext = $f->getClientOriginalExtension();
            if ($base !== '') {
                $name = $base . ($index ? "_{$index}" : '') . '.' . $ext;
            } else {
                $orig = pathinfo($f->getClientOriginalName(), PATHINFO_FILENAME);
                $orig = preg_replace('/[^A-Za-z0-9\-_]/', '_', $orig);
                $name = $orig ?: 'file_' . time() . "_{$index}";
                $name .= '.' . $ext;
            }
            $dest = $targetDir . DIRECTORY_SEPARATOR . $name;
            if (file_exists($dest)) {
                $name = pathinfo($name, PATHINFO_FILENAME) . '_' . time() . '.' . $ext;
                $dest = $targetDir . DIRECTORY_SEPARATOR . $name;
            }
            try {
                $f->move($targetDir, $name);
                $saved[] = [
                    'url' => asset($folder . '/' . $name),
                    'path' => $folder . '/' . $name,
                    'name' => $name,
                ];

                try {
                    $adminId = session('usuario_id') ?? null;
                    $actor = $adminId ? Usuario::find($adminId) : null;
                    $actorName = $actor ? ($actor->nombre . ' ' . $actor->apellido) : 'Sistema';
                    Notificacion::create([
                        'id_admin' => $adminId,
                        'id_usuario' => null,
                        'audiencia' => 'admins',
                        'estado' => 'cerrada',
                        'tipo' => 'otra',
                        'descripcion' => "Imagen subida por el admin: {$actorName},ruta: {$folder}/{$name}",
                        'fecha_creacion' => Carbon::now()->toDateTimeString(),
                        'fecha_visto' => null,
                        'ruta' => asset($folder . '/' . $name)
                    ]);
                } catch (\Throwable $e) {
                    \Log::error('Error creando notificación de imagen: ' . $e->getMessage());
                }

            } catch (\Exception $e) {
            }
        }
        if (count($saved) === 0) {
            return response()->json(['message' => 'No se subieron archivos.'], 422);
        }
        return response()->json(['message' => 'Archivos subidos correctamente', 'files' => $saved], 201);
    }
    public function list(Request $request)
    {
        $folder = trim((string)$request->query('folder', 'uploads'));
        $folder = preg_replace('/[^A-Za-z0-9\-_\/]/', '_', $folder);
        $target = public_path($folder);
        if (! is_dir($target)) {
            return response()->json(['files' => []]);
        }
        $allowed = ['jpg','jpeg','png','webp','gif','svg'];
        $all = @scandir($target) ?: [];
        $files = [];
        foreach ($all as $f) {
            if ($f === '.' || $f === '..') continue;
            $path = $target . DIRECTORY_SEPARATOR . $f;
            if (! is_file($path)) continue;
            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
            if (! in_array($ext, $allowed)) continue;
            $files[] = $f;
        }
        sort($files);
        return response()->json(['files' => $files]);
    }
}