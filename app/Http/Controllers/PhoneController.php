<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Twilio\Rest\Client;
use App\Models\phone;
use App\Models\premium;
use Illuminate\Support\Facades\Hash;
class PhoneController extends Controller
{
    public function setPhone(Request $request){

        $user = auth()->user();

        if($user->phone){
            $phone = $user->phone;
            $phone->phone = $request->phone;
            $phone->save();
            $code = $this->SendSmsToNumber();
            return RespondHandler::respond(['status' => true,$code], 200);
        }else{
            $phone = new Phone();
            $phone->phone = $request->phone;
            $user->phone()->save($phone);
            $code = $this->SendSmsToNumber();
            return RespondHandler::respond(['status' => true,$code], 200);
        }
        return RespondHandler::respond(['status' => false], 200);
    }
    private function getARandomNumber(){
        return rand(100000, 999999);
    }

    private function SendSmsToNumber(){

        $user = auth()->user();
        $code = $this->getARandomNumber();
        $phone = phone::where('user_id', '=', $user->id)->first();;
        $phone->code = bcrypt($code);
        $phone->expire = Carbon::now()->addMinutes(10);
        $phone->save();

        $account_sid = 'AC5a35796d9b149390338fe647946d5684';
        $auth_token = '734c8e1380aca4dc8f65960f255acdaa';
        $twilio_number = "+19378263078";

        // $client = new Client($account_sid, $auth_token);

        // $client->messages->create(
        //     $user->phone->phone,
        //     [
        //         'from' => $twilio_number,
        //         'body' => 'hellow'
        //         // 'body' => 'Dear '.ucfirst($user->firstname).' this is your phone verification code:\n'.$code
        //     ]
        // );
        
        return $code;
    }

    public function checkCode(Request $request){

        $user = auth()->user();

            if($user->phone->expire >= Carbon::now()){

                if(Hash::check($request->code, $user->phone->code)){
                    if($user->country == 105 && str_starts_with($user->phone->phone , '+98')){

                    if($user->premium){
                        $user->premium->ends = Carbon::now()->addYears(100);
                        $user->premium->type = 'iranian';
                        $user->premium->save();
                        $user->is_premium = true;
                        $user->save();
                    }else{
                        $premium = new Premium();
                        $premium->type = 'iranian';
                        $premium->started = Carbon::now();
                        $premium->ends =  Carbon::now()->addYears(100);
                        $user->premium()->save($premium);
                        $user->is_premium = true;
                        $user->save();
                    }
                    return RespondHandler::respond(['status' => true , 'premium'=>true], 200);
                }
                return RespondHandler::respond(['status' => true], 200);
            }else{
                return RespondHandler::respond(['status' => false], 200);
            }
        }
    }
    public function requestNewCode(){
        $user = auth()->user();

        if($user->phone){
            $code = $this->SendSmsToNumber();
            return RespondHandler::respond(['status' => true , $code], 200);
        }
        return RespondHandler::respond(['status' => false], 200);
    }
}
