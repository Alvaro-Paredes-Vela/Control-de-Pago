
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;

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
use App\Http\Controllers\EstadoCuentaController;

/*
|--------------------------------------------------------------------------
| Permisos del sistema
|--------------------------------------------------------------------------
*/

// Solo el administrador puede gestionar usuarios.
Gate::define('administrar-usuarios', function ($user) {
    return $user->rol === 'administrador';
});

// Solo el administrador puede gestionar actividades y categorías.
Gate::define('administrar-actividades', function ($user) {
    return $user->rol === 'administrador';
});

// Administrador y tesorero pueden gestionar caja.
Gate::define('administrar-caja', function ($user) {
    return in_array($user->rol, ['administrador', 'tesorero']);
});

/*
|--------------------------------------------------------------------------
| Rutas públicas: login y registro
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [RegisterController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Rutas para usuarios autenticados
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Panel principal.
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Cerrar sesión.
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Usuarios: solo administrador
    |--------------------------------------------------------------------------
    */

    Route::middleware('can:administrar-usuarios')->group(function () {

        // Crear, listar, editar y eliminar usuarios.
        Route::resource('usuarios', UserController::class)
            ->parameters(['usuarios' => 'usuario']);

        // Aprobar cuentas registradas.
        Route::post(
            'usuarios/{usuario}/aprobar',
            [UserController::class, 'aprobar']
        )->name('usuarios.aprobar');
    });

    /*
    |--------------------------------------------------------------------------
    | Categorías y actividades: solo administrador
    |--------------------------------------------------------------------------
    */

    Route::middleware('can:administrar-actividades')->group(function () {

        Route::resource('categorias', CategoriaActividadController::class)
            ->parameters(['categorias' => 'categoria']);

        Route::resource('actividades', ActividadController::class)
            ->parameters(['actividades' => 'actividad']);

        // Agregar campos personalizados a una actividad.
        Route::post(
            'actividades/{actividad}/campos',
            [CampoActividadController::class, 'store']
        )->name('actividades.campos.store');

        // Eliminar campos personalizados.
        Route::delete(
            'actividades/{actividad}/campos/{campo}',
            [CampoActividadController::class, 'destroy']
        )->name('actividades.campos.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Caja: administrador y tesorero
    |--------------------------------------------------------------------------
    */

    Route::middleware('can:administrar-caja')->group(function () {

        Route::resource('metodos-pago', MetodoPagoController::class)
            ->parameters(['metodos-pago' => 'metodo_pago']);

        Route::resource('movimientos-caja', MovimientoCajaController::class)
            ->parameters(['movimientos-caja' => 'movimientos_caja']);
    });

    /*
    |--------------------------------------------------------------------------
    | Módulos permitidos para los organizadores
    |--------------------------------------------------------------------------
    */

    // Estudiantes.
    Route::resource('estudiantes', EstudianteController::class);

    // Inscripciones.
    Route::resource('inscripciones', InscripcionController::class)
        ->parameters(['inscripciones' => 'inscripcion']);

    // Pagos.
    Route::resource('pagos', PagoController::class);

    // Estado de cuentas.
    Route::get(
        '/estado-cuentas',
        [EstadoCuentaController::class, 'index']
    )->name('estado-cuentas.index');
});

// Si ingresan a la página principal, los enviamos al login.
Route::redirect('/', '/login');
