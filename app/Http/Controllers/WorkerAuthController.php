<?php
namespace App\Http\Controllers;

use App\Http\Requests\Worker\LoginRequest;
use App\Http\Requests\Worker\RegisterRequest;
use App\Mail\VerificationEmail;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Worker;
use App\Services\WorkerService\WorkerLoginService;
use App\Services\WorkerService\WorkerRegisterService;
use Validator;

class WorkerAuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:worker', ['except' => ['login', 'register','verify']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request){

        return (new WorkerLoginService())->login($request);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function register(RegisterRequest $request) {
    //     return (new WorkerRegisterService())->Register($request);
    // }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:Workers',
            'password' => 'required|string|max:16',
            'phone' => 'required|numeric',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg',
            'location'=> 'nullable'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $worker = Worker::create(array_merge(
                    $validator->validated(),
                    [
                        'password' => bcrypt($request->password),
                        'photo' => $request->file('photo')->store('Workers')
                    ]
                ));

                $token  = substr(md5(rand(0, 9) . $request->email . time()), 0, 32);
                $worker = Worker::whereEmail($request->email)->first();
                $worker->verification_token = $token ;
                $worker->update();
                Mail::to($worker->email)->send(new VerificationEmail($worker));
        return response()->json([
            'message' => 'worker successfully registered',
            'worker' => $worker
        ], 201);
    }

    public function verify($token){
        $worker = Worker::whereVerificationToken($token)->first();
        if(!$worker){
         return   response()->json(['message' => 'this token is invalid',]);
        }
        $worker->verification_token = null ;
        $worker->verified_at = now(); 
        $worker->save();
        return   response()->json(['message' => 'Your account has been verified successfully']);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->guard('worker')->logout();
        return response()->json(['message' => 'Worker successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function workerProfile() {
        return response()->json(auth()->guard('worker')->user());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'worker' => auth()->guard('worker')->user()
        ]);
    }
}
