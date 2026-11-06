<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReporteController extends Controller
{
    // Los tres roles pueden consultar los reportes.
    private function verificarPermiso(): void
    {
        abort_unless(
            in_array(Auth::user()?->rol, [
                'administrador',
                'tesorero',
                'organizador',
            ], true),
            403
        );
    }

    // Estudiantes con inscripción activa en la actividad, ordenados por apellido.
    private function consultaEstudiantes(Actividad $actividad)
    {
        return Estudiante::query()
            ->whereHas('inscripciones', function ($q) use ($actividad) {
                $q->where('actividad_id', $actividad->id)
                    ->where('estado', 'inscrito');
            })
            ->orderBy('apellidos')
            ->orderBy('nombres');
    }

    // Escapa texto para colocarlo dentro del XML del Excel.
    private function xml(string $texto): string
    {
        return htmlspecialchars($texto, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    // Reporte en pantalla: estudiantes inscritos en una actividad.
    public function estudiantesActividad(Request $request)
    {
        $this->verificarPermiso();

        $actividades = Actividad::orderBy('nombre')->get(['id', 'nombre']);

        $actividad = null;
        $estudiantes = null;

        if ($request->filled('actividad_id')) {
            $actividad = Actividad::findOrFail($request->query('actividad_id'));

            $estudiantes = $this->consultaEstudiantes($actividad)
                ->paginate(25)
                ->withQueryString();
        }

        return view('reportes.estudiantes-actividad', compact(
            'actividades',
            'actividad',
            'estudiantes'
        ));
    }

    // Descarga el reporte como archivo de Excel (sin paquetes externos).
    public function exportarEstudiantesActividad(Request $request)
    {
        $this->verificarPermiso();

        $datos = $request->validate([
            'actividad_id' => 'required|exists:actividades,id',
        ]);

        $actividad = Actividad::findOrFail($datos['actividad_id']);

        $estudiantes = $this->consultaEstudiantes($actividad)->get();

        // Nombre de la hoja: máximo 31 caracteres y sin símbolos prohibidos.
        $hoja = Str::limit(
            preg_replace('/[\\\\\/\?\*\[\]:]/', ' ', $actividad->nombre),
            31,
            ''
        );

        // Encabezado del libro de Excel (formato XML Spreadsheet).
        $contenido = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<?mso-application progid="Excel.Sheet"?>' . "\n"
            . '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"'
            . ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
            . '<Styles>'
            . '<Style ss:ID="cabecera"><Font ss:Bold="1"/>'
            . '<Interior ss:Color="#D9E1F2" ss:Pattern="Solid"/></Style>'
            . '</Styles>'
            . '<Worksheet ss:Name="' . $this->xml($hoja) . '"><Table>'
            . '<Column ss:Width="40"/>'
            . '<Column ss:Width="280"/>'
            . '<Column ss:Width="150"/>';

        // Fila de títulos.
        $contenido .= '<Row>'
            . '<Cell ss:StyleID="cabecera"><Data ss:Type="String">N°</Data></Cell>'
            . '<Cell ss:StyleID="cabecera"><Data ss:Type="String">Nombre completo</Data></Cell>'
            . '<Cell ss:StyleID="cabecera"><Data ss:Type="String">Registro universitario</Data></Cell>'
            . '</Row>';

        // Una fila por estudiante.
        foreach ($estudiantes as $i => $estudiante) {
            $nombre = trim($estudiante->apellidos . ' ' . $estudiante->nombres);

            $contenido .= '<Row>'
                . '<Cell><Data ss:Type="Number">' . ($i + 1) . '</Data></Cell>'
                . '<Cell><Data ss:Type="String">' . $this->xml($nombre) . '</Data></Cell>'
                // Como texto para no perder ceros iniciales.
                . '<Cell><Data ss:Type="String">'
                . $this->xml((string) $estudiante->registro_universitario)
                . '</Data></Cell>'
                . '</Row>';
        }

        $contenido .= '</Table></Worksheet></Workbook>';

        $archivo = 'estudiantes-' . Str::slug($actividad->nombre) . '.xls';

        return response($contenido, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $archivo . '"',
        ]);
    }
}
