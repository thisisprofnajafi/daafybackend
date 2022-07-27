<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Mail\ResetEmail;
use App\Models\Interests;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:6|max:20',
            'password_confirm' => 'required|string|same:password|min:6|max:20',
            'username' => 'required|string|unique:users,username'
        ]);

        $user = User::all()->where('username', $fields['username'])->first();

        if (!$user) {
            if ($fields['password'] === $fields['password_confirm']) {
                $user = new User();
                $user->firstname = $fields['firstname'];
                $user->lastname = $fields['lastname'];
                $user->username = $fields['username'];
                $user->email = $fields['email'];
                $user->password = bcrypt($fields['password']);
                $user->is_premium = false;

                $user->save();
                $user->sentEmailVerification();
                $token = $user->createToken('DaddyToken')->plainTextToken;

                return RespondHandler::respond(['status' => true, 'user' => $user, 'token' => $token], 201);
            } else {
                return RespondHandler::respond(['status' => false, 'message' => 'Password doesn\'t match'], 201);
            }

        }


    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return RespondHandler::respond(['message' => 'Logged Out', 'status' => true], 200);

    }

    public function login(Request $request)
    {


        $fields = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:6|max:20'
        ]);


        $user = User::query()->where('username', $fields['username'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return RespondHandler::respond(['status' => false, 'message' => 'Bad Cred'], 401);
        }

        $token = $user->createToken('DaddyToken')->plainTextToken;

        return RespondHandler::respond(['status' => true, 'user' => $user, 'token' => $token], 200);

    }

    public function resetEmail(Request $request)
    {


        $fields = $request->validate([
            'email' => 'required|string',
        ]);


        $user = User::query()->where('email', $fields['email'])->first();

        if ($user) {
            $code = $this->random();
            $user->reset_code = bcrypt($code);
            $user->reset_expire = Carbon::now()->addMinutes(5);
            $user->save();

            Mail::to($user->email)->send(new ResetEmail($code,$user->firstname));

            return RespondHandler::respond(['status' => true], 200);
        }

        return RespondHandler::respond(['status' => false], 200);
    }

    private function random()
    {
        return rand(100000, 999999);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'code' => 'required|numeric',
            'newPass' => 'required|string|min:6|max:20',
            'newPassConfirm' => 'required|string|min:6|max:20|same:newPass',
        ]);

        $user = User::all()->where('email', $request->email)->first();

        if ($user) {
            if (Carbon::now() <= $user->reset_expire) {
                if (Hash::check($request->code, $user->reset_code)) {
                    $user->password = bcrypt($request->newPass);
                    $user->reset_code = null;
                    $user->reset_expires = null;
                    $user->save();
                    return RespondHandler::respond(['status' => true], 200);
                } else {
                    return RespondHandler::respond(['status' => false, 'msg' => 'Wrong Code!'], 200);
                }
            } else {
                return RespondHandler::respond(['status' => false, 'msg' => 'Code Expired'], 200);
            }
        }else{
            return RespondHandler::respond(['status' => false, 'msg' => 'Wrong Credentials'], 200);
        }
    }

    public function resetpass(Request $request)
    {
    
        $user = auth()->user();
        $pass = $request->pass;
        $newPass = $request->newPass;
        $newPassConf = $request->newPassConf;

        if(!$pass && !$newPass && !$newPassConf){
            return RespondHandler::respond(['status' => false, 'error' => 'Fill all the inputs'], 200);
        }

        if (Hash::check($request->pass, $user->password)) {
            if ($request->newPass == $request->newPassConf) {

                $user->password = bcrypt($request->newPass);
                $user->save();

                return RespondHandler::respond(['status' => true], 200);

            }else{
                return RespondHandler::respond(['status' => false, 'error' => 'The password and confirmation password do not match.'], 200);
            }
        }else{
            return RespondHandler::respond(['status' => false, 'error' => 'Wrong password'], 200);
        }


           
    }
}
