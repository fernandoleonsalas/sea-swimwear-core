<?php

use App\Models\Order;
use App\Http\Controllers\GestionPago;
use App\Http\Controllers\GestionProducto; 
use App\Http\Controllers\CatalogsController;
use App\Http\Controllers\configuracionCuenta;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;   

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí puedes registrar las rutas web de tu aplicación. Estas
| rutas son cargadas por el RouteServiceProvider y todas ellas serán
| asignadas al grupo de middleware "web".
|
*/

// Cargar las rutas de autenticación generadas por Breeze/Jetstream, etc. Siempre al inicio para evitar conflictos
require __DIR__.'/auth.php'; // <-- Esta línea carga todas las rutas de autenticación

// Redirigir la raíz al login (o a la página que sea la Landing Page por defecto)
Route::redirect('/', '/login');

// Rutas del Catálogo de Productos y Carrito
Route::get('/catalogo', [CatalogsController::class, 'catalogoMin'])->name('catalogoMin'); 
Route::get('/catalogoMay', [CatalogsController::class, 'catalogoMay'])->name('catalogoMay'); 
Route::get('/producto/{id}/{cat}', [CatalogsController::class, 'verVariantes'])->name('producto'); 
Route::get('/carrito', [CatalogsController::class, 'detalleCarrito'])->name('carrito'); 

// Rutas del Proceso de Pago
Route::get('/orden', [GestionPago::class, 'ordenForm'])->name('ordenForm');
Route::get('/pago/{token}', [GestionPago::class, 'formPago'])->name('pago');
Route::post('/validarToken', [GestionPago::class, 'validarToken'])->name('validarToken');

// Ruta vista de mensaje exitoso
Route::get('/msmPago/{msm}', [GestionPago::class, 'msmPago'])->name('msmPago'); 

// Rutas Privadas AUTH (Requieren autenticación)
Route::middleware('auth')->group(function () {
    // Rutas del Perfil de usuario
    Route::view('/profile', 'profile')->name('profile');

    // Ruta del panel administrativo (Requiere 'verified' adicional)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['verified'])->name('dashboard');

    // Agrupación para la Gestión de Productos
    Route::get('/crearProducto', [GestionProducto::class, 'crearProducto'])->name('crearProducto'); 
    Route::get('/listadoP', [GestionProducto::class, 'listaProducto'])->name('listadoP'); 
    
    // Agrupación para la Gestión de Pagos/Órdenes
    Route::get('/ordenes', [GestionPago::class, 'listaOrdenes'])->name('ordenes');
    
    // Ruta de los formularios metodo de pago
    Route::get('/metodo', [ConfiguracionCuenta::class, 'metodosPagos'])->name('metodo');

    // Ruta del formulario de tassa de cambio a bs
    Route::get('/tasa', [ConfiguracionCuenta::class, 'tasaCambioForm'])->name('tasa');

    // Ruta Especial para Archivos Privados
    Route::get('/ver-comprobante/{path}', function ($path) {
        // La ruta completa debe ser $path (asumiendo que $path es la ruta completa en storage/app/)
        $fullPath = $path; 

        // Verificar la existencia del archivo en el disco 'local'
        if (!Storage::disk('local')->exists($fullPath)) {
            abort(404, 'Archivo no encontrado.'); // Si Laravel no lo encuentra, aborta con 404
        }

        // Retorna el archivo con el encabezado MIME y Content-Disposition
        return response(Storage::disk('local')->get($fullPath), 200)
            ->header('Content-Type', Storage::mimeType($fullPath))
            ->header('Content-Disposition', 'inline; filename="' . basename($fullPath) . '"');

    })->where('path', '.*')->name('comprobante.privado'); // ESTE WHERE ES VITAL: permite que el parámetro {path} contenga barras (/)
});

/*
| Test de enrutamiento para descartar conflicto con carpeta pública.
| Si en el servidor existe /public/example, Apache servirá archivos estáticos y NO pasará por Laravel.
| Esta ruta alternativa debe renderizar SIEMPRE la vista alpine.blade.php desde Laravel.
*/
Route::get('/_diag/assets', function () {
    $manifestPath = public_path('build/manifest.json');
    $hotPath = public_path('hot');
    $buildDir = public_path('build');

    $manifestExists = file_exists($manifestPath);
    $hotExists = file_exists($hotPath);
    $buildDirExists = is_dir($buildDir);
    $buildFiles = $buildDirExists ? array_values(array_diff(scandir($buildDir), ['.', '..'])) : [];

    Log::info('diag.assets', [
        'manifest_exists' => $manifestExists,
        'hot_exists' => $hotExists,
        'build_dir_exists' => $buildDirExists,
        'build_files' => $buildFiles,
    ]);

    return response()->json([
        'manifest_exists' => $manifestExists,
        'hot_exists' => $hotExists,
        'build_dir_exists' => $buildDirExists,
        'build_files' => $buildFiles,
        'app_env' => config('app.env'),
        'timestamp' => now()->toDateTimeString(),
    ]);
});
