<?php
namespace App\Services\WorkerService;

use App\Models\Worker;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WorkerRegisterService {
    protected $model;
    function __construct() {
        $this->model = new Worker();
    }

    function Validation($request) {
        $validator = Validator::make($request->all(), $request->rules());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        return $validator;
    }

    function generateToken($email) {
        $token  = substr(md5(rand(0, 9) . $email . time()), 0, 32);
        $worker = $this->model->whereEmail($email)->first();
        $worker->verification_token = $token ;
        $worker->save();
        return $worker;
    }

    function Store($data , $request) {
        $worker = $this->model->create(array_merge(
            $data->validated(),
            [
                'password' => bcrypt($request->password),
                'photo' => $request->file('photo')->store('workers')
            ]
        ));
        return $worker->email;
    }

    function sendEmail(){

    }

    function Register($request){
        try{
            DB::beginTransaction();
            $data  = $this->Validation($request);
            $email = $this->Store($data , $request);
            $storeToken = $this->generateToken($email);
            $sendEmail = $this->sendEmail() ;
            DB::commit();
            return response()->json([
                'message' => 'ypur accouny has been created successfully , please check your email',
            ] , 200);
        }catch(Exception $e){
            DB::rollBack();
        }

    }

}
