<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Alumno;
use App\CursoSemestre;
use App\Alumnocurso;

class AlumnoController extends Controller
{
    public function login(Request $request){
        $json = $request -> input('json',null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        $validate = \Validator::make($params_array, [
            "matricula" => "required",
            "contrasena" => "required"
        ]);

        if($validate -> fails()){
            $data = array(
                "code" => 400,
                "status" => "error",
                "error" => "Faltan datos"
            );
        }else{
            $user = Alumno::where([
                'matricula' => $params -> matricula,
                'contrasena' => $params -> contrasena
            ]) -> first();

            $login = false;
            if(is_object($user))
                $login = true;
            else
                $login = false;

            if($login){
                $data = array(
                    'id' =>  $user -> idAlumno,
                    'matricula' => $user -> matricula,
                    'semestre' => $user -> semestre,
                    'nombre' => $user -> nombre,
                    'apellidos' => $user -> apellidos
                );
            }else{
                $data = array(
                    "code" => 400,
                    "status" => "error",
                    "mesage" => "Los datos son incorrectos"
                );
            }

            return response() -> json($data, 200);
        }
    }

    public function vCurso($id, $dia){
        $curso = Alumno::where([
            ['alumnos.idAlumno', "=", $id],
            ['cursos.dia', '=', $dia]])
            ->join('alumnocursos', 'alumnos.idAlumno', '=', 'alumnocursos.idAlumno')
            ->join('cursos', 'alumnocursos.idCurso', '=', 'cursos.idCurso')
            ->get()->first();

            if(is_object($curso)){
                $data = array(
                    'code' => '200',
                    'message' => 'Si hay cursos'
                );
            }else{
                $data = array(
                    'code' => '400',
                    'message' => 'No hay cursos'
                );      
            }

        return response() -> json($data, $data['code']);
    }

    
    public function register(Request $request){
        try{
            $json = $request -> input('json');
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            if(!empty($params) && !empty($params_array)){
                $validate = \Validator::make($params_array,[
                    'nombre' => 'required',
                    'apellidos' => 'required',
                    'matricula' => 'required',
                    'contrasena' => 'required',
                    'correo' => 'required|email',
                    'semestre' => 'required|numeric'
                ]);
        
                if($validate -> fails()){

                    $data = array(
                        'status' => 'error',
                        'message' => 'Datos ingresados incorrectamente o faltan datos',
                        'code' => 400,
                        'errors' => $validate -> errors()
                    );

                }else{

                    $alumno = new Alumno();
                    $alumno -> nombre = $params_array['nombre'];
                    $alumno -> apellidos = $params_array['apellidos'];
                    $alumno -> matricula = $params_array['matricula'];
                    $alumno -> contrasena = $params_array['contrasena'];
                    $alumno -> correo = $params_array['correo'];
                    $alumno -> semestre = $params_array['semestre'];
                    $alumno -> save();

                    $data = array(
                        'status' => 'success',
                        'message' => 'usuario registrado correctamente',
                        'code' => 200
                    );
                }
            }else{

                $data = array(
                    'status' => 'error',
                    'message' => 'Inserte los datos',
                    'code' => 400
                );
            }

            }catch(\Illuminate\Database\QueryException $ex){
                $data = array(
                    'status' => 'error',
                    'message' => 'Repetido',
                    'code' => 400
                );
            }

            return response() -> json($data, $data['code']); 
    }
}
