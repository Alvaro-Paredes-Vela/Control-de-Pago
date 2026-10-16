
<?php

use Illuminate\Support\Facades\Route;

// Controladores de autenticación
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Controladores del sistema
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\CategoriaActividadController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\MovimientoCajaController;
use App\Http\Controllers\CampoActividadController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [LoginController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login']);

    // Registro
    Route::get('/register', [RegisterController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [RegisterController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Cerrar sesión
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    // Usuarios
    Route::resource('usuarios', UserController::class)
        ->parameters(['usuarios' => 'usuario']);

    // Estudiantes
    Route::resource('estudiantes', EstudianteController::class);

    // Categorías de actividades
    Route::resource('categorias', CategoriaActividadController::class)
        ->parameters(['categorias' => 'categoria']);

    // Actividades
    Route::resource('actividades', ActividadController::class)
        ->parameters(['actividades' => 'actividade']);

    // Inscripciones
    Route::resource('inscripciones', InscripcionController::class)
        ->parameters(['inscripciones' => 'inscripcione']);

    // Métodos de pago
    Route::resource('metodos-pago', MetodoPagoController::class)
        ->parameters(['metodos-pago' => 'metodo_pago']);

    // Pagos
    Route::resource('pagos', PagoController::class);

    // Movimientos de caja
    Route::resource('movimientos-caja', MovimientoCajaController::class)
        ->parameters(['movimientos-caja' => 'movimientos_caja']);

    // Campos personalizados de actividades
    Route::post(
        'actividades/{actividad}/campos',
        [CampoActividadController::class, 'store']
    )->name('actividades.campos.store');

    Route::delete(
        'actividades/{actividad}/campos/{campo}',
        [CampoActividadController::class, 'destroy']
    )->name('actividades.campos.destroy');
});

/*
|--------------------------------------------------------------------------
| RUTA PRINCIPAL
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');
