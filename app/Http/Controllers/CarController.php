<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JWTAuth;
use App\Car;

class CarController extends Controller
{
    public function index(){
        // $hash =  $request->header('Authorization', null);
        // $jwtAuth = new JWTAuth();
        // $checkToken = $jwtAuth->checkToken($hash);

        // if ($checkToken) {
        //     echo "Index de CarController Autenticado"; die();
        // } else {
        //     echo "No autenticado -> Index de CarController"; die();
        // }

        // $cars = Car::all();//Obtiene todos los registros de carros
        $cars = Car::all()->load('user');//Obtiene todos los registros de carros con la informacion de usuarios asociados

        return response()->json(array(
            'cars' => $cars,
            'status' => 'success'
        ), 200);
    }

    public function show($id){
        $car = Car::find($id);
        if($car){
            $car = $car->load('user');
            return response()->json(array(
                'car' => $car,
                'status' => 'success'
            ), 200);
        }else{
            return response()->json(array(
                'message' => 'El vehiculo que solicitó no existe',
                'status' => 'error'
            ), 300);
        }
    }

    public function store(Request $request){

        $hash =  $request->header('Authorization', null);
        $jwtAuth = new JWTAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            //Recoger los datos por POST
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            //Conseguir el usuario identificado
            $user = $jwtAuth->checkToken($hash, true);


            // Validación de datos

            //Opcion con validacion de laravel

            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }

            // if ( // Opcion con issets
            //     isset($params->title) &&
            //     isset($params->description) &&
            //     isset($params->price) &&
            //     isset($params->status)
            // ){
            //     # code...
            // }

            //Guarda el coche
            $car = new Car();

            $car->user_id = $user->sub;

            $car->title =  $params->title;
            $car->description =  $params->description;
            $car->price =  $params->price;
            $car->status =  $params->status;

            $car->save();

            $data = array(
                'car' => $car,
                'status' => 'success',
                'code' => 200
            );
        } else {
            $data = array(
                'message' => 'Login incorrecto',
                'status' => 'error',
                'code' => 300
            );
        }

        return response()->json($data, 200);
    }

    public function update($id, Request $request){
        $hash =  $request->header('Authorization', null);
        $jwtAuth = new JWTAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            //Recoger los datos por POST
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            // Validación de datos
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
                die();
            }

            unset($params_array['user']);
            unset($params_array['userId']);
            unset($params_array['id']);
            unset($params_array['createdAt']);
            unset($params_array['updatedAt']);
            // var_dump($params_array);

            // Actualizar el coche
            $car = Car::where('id', $id)->update($params_array);

            $data = array(
                'car' => $params,
                'status' => 'success',
                'code' => 200
            );

        } else {
            $data = array(
                'message' => 'Acceso incorrecto',
                'status' => 'error',
                'code' => 300
            );
        }

        return response()->json($data, 200);
    }

    public function destroy($id, Request $request){
        $hash =  $request->header('Authorization', null);
        $jwtAuth = new JWTAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            // Comprobar que existe el registro
            $car = Car::find($id);

            if ($car) {
                // Borrarlo
                $car->delete();

                //Devolver lo que se ha eliminado
                $data = array(
                    'car' => $car,
                    'status' => 'success',
                    'code' => 200
                );
            }else{
                $data = array(
                    'message' => 'No existe el vehiculo que solicitó',
                    'status' => 'error',
                    'code' => 300
                );
            }



        } else {
            $data = array(
                'message' => 'Acceso incorrecto',
                'status' => 'error',
                'code' => 300
            );
        }

        return response()->json($data, 200);
    }

    public function ping()
    {
        return 'prueba superada';
    }
}
