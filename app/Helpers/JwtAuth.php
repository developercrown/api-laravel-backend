<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JWTAuth{

    public $key;
    public $algorithm;

    public function __construct(){
        $this->key = "claveDEMIapi10650149";
        $this->algorithm = "HS256";
    }

    public function signup($email, $password, $getToken=null){
        $user = User::where(array(
            'email' => $email,
            'password' => $password
        ))->first();

        $signup = false;

        if (is_object($user)) {
             $signup = true;
        }

        $output = array(
            'status' => 'init',
            'message' => '',
            'data' => null
        );

        $output = (object) $output;

        if ($signup) {
            //Generar el token y devolverlo

            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'iat' => time(), //timestamp de cuando se ha creado el token
                'exp' => time() + ( 7 * 24 * 60 * 60 ), // 7 dias * 24 hrs * 60 min * 60 seg = una semana ( Tiempo que comprende la validez del autenticacion)
            );

            $jwt = JWT::encode($token, $this->key, $this->algorithm); // Objeto del autenticación

            if(is_null($getToken)){
                $output->status = 'success';
                $output->message = 'Token de session Generado correctamente';
                $output->data = $jwt;
            }else{
                $output->status = 'success';
                $output->message = 'Información de session Obtenida correctamente';
                $output->data = JWT::decode( $jwt, $this->key, array($this->algorithm) );
            }
            return $output;

        } else {
            // Devolver un error
            $output->status = 'error';
            $output->message = 'Error al autenticar al usuario';
            return $output;
        }
    }

    public function checkToken($jwt, $getIdentitiy=false){
        $auth = false;
        $decoded = null;

        try {
            $decoded = JWT::decode($jwt, $this->key, array($this->algorithm));
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e){
            $auth = false;
        }

        if( isset($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;
        }else{
            $auth = false;
        }

        if ($getIdentitiy) {
            return $decoded;
        }

        return $auth;
    }
}
