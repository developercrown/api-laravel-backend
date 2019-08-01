<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JWTAuth;
use Illuminate\Support\Facades\DB;
use App\User;


class UserController extends Controller
{
    private $algorithm;

    public function __construct()
    {
        $this->algorithm = 'sha256';
    }

    public function register(Request $request){
        // header('Access-Control-Allow-Origin: *');
        // Recoger post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        // $email = ( !is_null($json) && isset($params->email) ) ? $params->email : null;
        // $name = ( !is_null($json) && isset($params->name) ) ? $params->name : null;
        // $surname = ( !is_null($json) && isset($params->surname) ) ? $params->surname : null;
        // $role = "ROLE_USER";
        // $password = ( !is_null($json) && isset($params->password) ) ? $params->password : null;

        $data = null; // Objeto de salida json

        $validate = \Validator::make($params_array, [
            'email' => 'required',
            'name' => 'required',
            'surname' => 'required',
            'role' => 'required',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            $data = array(
                    'status' => 'error',
                    'code' => '300',
                    'data' => $validate->errors(),
                    'message' => 'Inanicion de datos'
                );
        } else {
            //Crear el usuario

            $user = new User();

            $user->email = $params->email;
            $user->name = $params->name;
            $user->surname = $params->surname;
            $user->role = 'ROLE_USER';
            $pwd = hash($this->algorithm, $params->password); //Contraseña cifrada
            $user->password = $pwd;

            // Comprobar usuario con email duplicado
            $isset_user = User::where('email', '=', $user->email)->first(); //Obtiene el primer registro correspondiente a la sentencia a comparar con email


            if(count($isset_user) == 0){ //El usuario no existe
                $isset_user = User::
                    where('name', 'like', $user->name)
                    ->where('surname', 'like', $user->surname)
                    ->first();
                if (count($isset_user) == 0) {
                    // Guardar el usuario
                    $user->save();
                    $data = array(
                        'status' => 'success',
                        'code' => '200',
                        'message' => 'Usuario Registrado Correctamente'
                    );
                }else{
                    $data = array(
                        'status' => 'error',
                        'code' => '400',
                        'message' => 'La persona que intenta registrar ya existe en el sistema'
                    );
                }
            }else{
                // No Guardarlo por que el correo ya fue utilizado en el registro
                $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'El correo que ha ingresado ya ha sido utilizado por otro usuario'
                );
            }

        }
        return response()->json($data, 200);
    }

    public function login(Request $request){
        $jwtAuth = new JWTAuth();

        //Recibir por POST
        $json = $request->input('json', null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $getToken = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken : false;

        //Cifrar el password

        $pwd = hash($this->algorithm, $password);
        $signup = null;

        if(!is_null($email) && !is_null($password) && !$getToken){
            $signup = array(
                'status' => 'success',
                'data' => $jwtAuth->signup($email, $pwd)
            );
        }else if($getToken){
            $signup = array(
                'status' => 'success',
                'data' => $jwtAuth->signup($email, $pwd, $getToken)
            );
        }else{
            $signup = array(
                'status' => 'error',
                'message' => 'Envia tus datos por post'
            );
        }

        return response()->json($signup, 200);
    }


}
