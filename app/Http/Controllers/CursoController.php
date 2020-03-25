<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Curso;
use App\Alumnocurso;
use App\Alumno;

class CursoController extends Controller
{
    public function horarios(){

        $curso = Curso::all();
        $data = array(
            'code' => 200,
            'cursos' => $curso,
        );

        return response() -> json($data);
    }

    public function cursos($dia, $semestre){

        $curso = Curso::where([
            ['semestres.semestre', '=', $semestre],
            ['cursos.dia', '=',$dia]])
            ->join('cursosemestres', 'cursos.idCurso', '=', 'cursosemestres.idCurso')
            ->join('semestres','cursosemestres.idSemestre', '=', 'semestres.idSemestre')
            ->select('cursos.idCurso', 'cursos.nombreCurso','cursos.cupo', 'cursos.responsable','cursos.portada', 'cursos.dia', 'cursos.horaInicio', 'cursos.horaFin', 'cursos.lugar', 'semestres.semestre')
            ->get();
    
        return response() -> json([
            'code' => 200,
            'cursos' => $curso,
        ], 200);
    }

    public function aCurso(Request $request){
        $json = $request -> input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if(!empty($params) && !empty($params_array)){
            $validate = \Validator::make($params_array, [
                'idAlumno' => 'required',
                'idCurso' => 'required'
            ]);
    
            if($validate -> fails()){
                $data = array(
                    'code' => '400',
                    'message' => 'Error en validación',
                    "error" => $validate -> errors()
                );
            }else{
                $curso = new AlumnoCurso();
                $curso -> idAlumno = $params_array['idAlumno'];
                $curso -> idCurso = $params_array['idCurso'];
                $curso -> save();
    
                $data = array(
                    'code' => '200',
                    'message' => 'exito'
                );
            }
        }else{
            $data = array(
                'code' => '400',
                'message' => 'Faltan datos',
                'params' => $params
            );
        }

        return response() -> json($data, $data['code']);
    }
    // SELECT curso.idCurso, curso.nombreCurso, curso.responsable, curso.dia, curso.horaInicio,
    // curso.horaFin, curso.lugar, semestres.semestre
    // FROM curso 
    // JOIN cursosemestres ON curso.idCurso = cursosemestres.idCurso
    // JOIN semestres ON cursosemestres.idSemestre = semestres.idSemestre
    // WHERE semestres.semestre = 4 AND curso.dia = 'Lunes';
}
