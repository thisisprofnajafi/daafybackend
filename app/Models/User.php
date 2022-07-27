<?php



namespace App\Models;
use App\Mail\VerificationEmail;
use Carbon\Carbon;
use Hootlex\Friendships\Traits\Friendable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;
use Nnjeim\World\Models\City;
use Nnjeim\World\Models\Country;
use Laravel\Cashier\Billable;
class User extends Authenticatable implements MustVerifyEmail

{
    use HasApiTokens, HasFactory, Notifiable, Billable;
    use Friendable;
    protected $fillable = [

        'name',

        'email',

        'password',

        'firstname',

        'lastname',

        'email',

        'username',

        'birthdate',

        'gender',

        'bio',

        'description',

        'height',

        'body_type',

        'country',

        'city',

        'drink',

        'smoke',

        'employment_status',

        'living_status',

        'relation_status',

        'education_status',

        'avatar',

        'verification_token',

        'verification_token_expires',

    ];





    protected $hidden = [

        'password',

        'remember_token',

    ];





    protected $casts = [

        'email_verified_at' => 'datetime',

    ];





    public function phone(){

        return $this->hasOne(Phone::class);

    }





    public function photos()

    {

        return $this->hasMany(Photo::class);

    }



    public function matches()

    {

        return $this->hasMany(Matches::class);

    }



    public function tags()

    {

        return $this->hasMany(Tag::class);

    }





    public function specials()

    {

        return $this->hasMany(Special::class);

    }



    public function savedSearches()

    {

        return $this->hasMany(SavedSearch::class);

    }



    public function notifications(){

        return $this->hasMany(Notification::class);

    }



    public function interests()

    {

        return $this->hasOne(Interests::class);

    }



    public function premiumSetting()

    {

        return $this->hasOne(PremiumSetting::class);

    }



    public function premium()

    {

        return $this->hasOne(Premium::class);

    }



    

    public function payments(){

        return $this->hasMany(User::class);

    }



    public function faves()

    {

        return $this->hasMany(Favorite::class);

    }



    public function winks()

    {

        return $this->hasMany(Wink::class);

    }



    public function views()

    {

        return $this->hasMany(View::class);

    }



    public function verification()

    {

        return $this->hasOne(Verification::class);

    }



    public function feedbacks(){

        return $this->hasMany(Feedback::class);

    }







    public function todaySpecial()

    {



        if (!$this->hasTodaySpecial()) {



            $query = $this->setTodaySpecial();



            if ($query){

                return $this->todaySpecial();

            }else{

                return false;

            }

        } else {

            return $this->getTodaySpecial();

        }



    }









    private function setTodaySpecial()

    {

        $special = null;



        $users = User::all()->where('id','!=',auth()->user()->id)->where('gender', $this->interests->gender);

        $prevs = $this->specials();



        foreach ($users as $key => $user) {

            if ($this->hasBlocked($user) || $this->isBlockedBy($user)) {

                $users->forget($key);

            }

        }



        foreach ($users as $key => $user) {

            if ($this->isFriendWith($user)) {

                $users->forget($key);

            }

        }



        $prevs = $this->specials();

        

        foreach($prevs as $perv){

            foreach ($users as $key => $user) {

                if($user->id == $prev->special_id)

                $users->forget($key);

            }

        }



        $notFriends = $users;



        foreach ($users as $key => $user) {

            if (!$this->isMatching($user)['matching']) {

                $users->forget($key);

            }

        }



        $matching = $users;



        $matchingWithN = array();



        foreach ($users as $user){

            array_push($matchingWithN,['user'=>$user,'n'=>$this->isMatching($user)['n']]);

        }

        $matchingWithN = $this->userSortMatches($matchingWithN);



        $userB = array();

        foreach ($matchingWithN as $u){

            array_push($userB, $u['user']);

        }





        if ($userB !== null && count($userB) > 0){

            $special = new Special();

            $special->special_id = $userB[0]->id;

            $special->date = Carbon::now();

            $this->specials()->save($special);

            return true;

        }elseif($matching!==null && count($matching) > 0){



            $special = new Special();



            $special->special_id = $matching[0]->id;



            $special->date = Carbon::now();



            $this->specials()->save($special);



            return true;

        }else{

            return false;

        }

    }



    private function getTodaySpecial()

    {

        $special = $this->specials()->orderByDesc('created_at')->first();



        $user = User::query()->where('id',$special->special_id)->first();



       

        $country = Country::find($user->country);

       



        $spec = [

            'username'=>strtolower($user->username),

            'name'=>strtoupper($user->firstname),

            'age'=>$user->getAge(),

            'avatar'=>$user->avatar,

            'gender'=>strtolower($user->gender),

            'country'=>strtoupper($country->name),



            'smoke'=>$user->smoke,

            'drink'=>$user->drink,

            'relationship'=>$user->relation_status,

            'height'=>$user->height,

            'employment'=>$user->employment_status,

            'bodytype'=>$user->body_type,

            'living'=>$user->living_status,

            'education'=>$user->education_status,



            'tags'=> $user->tags,

            'bio'=> $user->bio,

            'description'=> $user->description,

        ];





        return $spec ;



    }





    private function hasTodaySpecial()

    {



        $today = Carbon::now();



        $last = $this->specials()->orderByDesc('date')->first();



        if($last){

            if (Carbon::parse($last->date)->diff(Carbon::now())->d > 1) {

                return false;

            } else {

                return true;

            }

        }else{

            return false;

        }

    }





    public function hadSpecial(User $user)

    {



        foreach ($this->specials() as $special){



            if ($special->special_id == $user->id){

                return true;

            }

        }

        return false;

    }



    public function isMatching(User $user)

    {

        $n = 0;

        $total = 0;





        if($this->interests->height)

            if ($this->interests->height[0] <= $user->height && $this->interests->height[1] >= $user->height ) {

                $n = $n + 1;

                $total = $total + 1;



            }

 

    if($this->interests->age)

        if($this->interests->age[0] <= $user->age && $this->interests->age[1] >= $user->age ){

            $n = $n + 1;

            $total = $total + 1;

        }else{

            $total = $total + 1;

        }





    if($this->interests->body_type && !empty(json_decode($this->interests->body_type)) )

        if(in_array($user->body_type,json_decode($this->interests->body_type))){

            $n = $n + 1;

            $total = $total + 1;

        }else{

            $total = $total + 1;

        }

   

    if($this->interests->smoke  && !empty(json_decode($this->interests->smoke)))

        if(in_array($user->smoke,json_decode($this->interests->smoke))){

            $n = $n + 1;

            $total = $total + 1;

        }else{

            $total = $total + 1;

        }

  

    if($this->interests->drink && !empty(json_decode($this->interests->drink)))

        if(in_array($user->drink,json_decode($this->interests->drink))){

            $n = $n + 1;

            $total = $total + 1;

        }else{

            $total = $total + 1;

        }

   

    if($this->interests->employment_status && !empty(json_decode($this->interests->employment_status)) )

        if(in_array($user->employment_status,json_decode($this->interests->employment_status)) ){

            $n = $n + 1;

            $total = $total + 1;

        }else{

            $total = $total + 1;

        }

   

    if($this->interests->living_status && !empty(json_decode($this->interests->living_status)))

        if(in_array($user->living_status,json_decode($this->interests->living_status)) ){

            $n = $n + 1;

            $total = $total + 1;

        }else{

            $total = $total + 1;

        }

   

    if($this->interests->education_status && !empty(json_decode($this->interests->education_status)))

        if(in_array($user->education_status,json_decode($this->interests->education_status)) ){

            $n = $n + 1;

            $total = $total + 1;

        }else{

            $total = $total + 1;

        }

  



        if ($n>= ($total/2)){

            return ['n'=>$n,'matching'=>true];

        }

        else{

            return ['n'=>$n,'matching'=>false];

        }

    }





    public function getAge()

    {

        return Carbon::parse($this->birthdate)->diff(Carbon::now())->y;

    }







    public function winkedAt($id){



        foreach($this->winks()->get() as $w){

            if ($w->wink == $id){

                return true;

            }

        }

        return false;

    }



    public function hasFaved($id){



        foreach($this->faves()->get() as $f){

            if ($f->fave == $id){

                return true;

            }

        }

        return false;

    }





    



    public function hasFavedUser($id){



        foreach($this->faves()->get() as $f){

            if ($f->fave == $id){

                return true;

            }

        }

        return false;

    }



    public function userSortMatches($array)

    {

        if ($length = count($array)) {

            for ($i = 0; $i < $length; $i++) {

                for ($j = 0; $j < $length; $j++) {

                    if ($array[$i]['n'] > $array[$j]['n']) {



                        $tmp = $array[$i];



                        $array[$i] = $array[$j];



                        $array[$j] = $tmp;



                    }

                }

            }

        }





        return $array;

    }



    public function userSortMatchesByUser($array)

    {

        if ($length = count($array)) {

            for ($i = 0; $i < $length; $i++) {

                for ($j = 0; $j < $length; $j++) {

                    if ($array[$i]['user']->created_at > $array[$j]['user']->created_at) {



                        $tmp = $array[$i];



                        $array[$i] = $array[$j];



                        $array[$j] = $tmp;



                    }

                }

            }

        }



        return $array;

    }

    public function sentEmailVerification(){
        $token = $this->getVerificationToken();
        $this->verification_token = $token;
        $this->verification_token_expire = Carbon::now()->addMinutes(5);
        $this->save();
        Mail::to($this->email)->send(new VerificationEmail($token,$this->firstname));
    }
    private function getVerificationToken()
    {
        return md5(rand(1, 7000) . microtime(true));
    }
    public function matchingYou(){
        $user = auth()->user();
        $data = array();
        if($this->interests->height)
            $status = (json_decode($this->interests->height)[0] <= $user->height && json_decode($this->interests->height)[1] >= $user->height ) ? 'yes' : 'no';
        else
            $status = 'notset';
        array_push($data,['id'=>0,'name'=>'Height','useris'=>$this->height,'looksfor'=>$this->interests->height,'status'=>$status]);
        if($this->interests->age  && !empty(json_decode($this->interests->age)))
            $status = (json_decode($this->interests->age)[0] <= $user->age && json_decode($this->interests->age)[1] >= $user->age ) ? 'yes' : 'no';
        else
            $status = 'notset';
        array_push($data,['id'=>1,'name'=>'Age','useris'=>$this->getAge(),'looksfor'=>$this->interests->age,'status'=>$status]);
        if($this->interests->body_type && !empty(json_decode($this->interests->body_type)))
            $status = (in_array($user->body_type,json_decode($this->interests->body_type)) ) ? 'yes' : 'no';
        else
            $status = 'notset';
        array_push($data,['id'=>2,'name'=>'Body Type','useris'=>$this->body_type,'looksfor'=>$this->interests->body_type,'status'=>$status]);
        if($this->interests->smoke && !empty(json_decode($this->interests->smoke)))
            $status = (in_array($user->smoke,json_decode($this->interests->smoke))) ? 'yes' : 'no';
        else
            $status = 'notset';
        array_push($data,['id'=>3,'name'=>'Smoke','useris'=>$this->smoke,'looksfor'=>$this->interests->smoke,'status'=>$status]);
        if($this->interests->drink && !empty(json_decode($this->interests->drink)))
            $status = (in_array($user->drink,json_decode($this->interests->drink)) ) ? 'yes' : 'no';
        else
            $status = 'notset';
        array_push($data,['id'=>4,'name'=>'Drink','useris'=>$this->drink,'looksfor'=>$this->interests->drink,'status'=>$status]);
        if($this->interests->employment_status && !empty(json_decode($this->interests->employment_status)))
            $status = (in_array($user->employment_status,json_decode($this->interests->employment_status)) ) ? 'yes' : 'no';
        else
            $status = 'notset';
        array_push($data,['id'=>5,'name'=>'Employment Status','useris'=>$this->employment_status,'looksfor'=>$this->interests->employment_status,'status'=>$status]);
        if($this->interests->living_status && !empty(json_decode($this->interests->living_status)))
            $status = (in_array($user->living_status,json_decode($this->interests->living_status)) ) ? 'yes' : 'no';
        else
            $status = 'notset';
        array_push($data,['id'=>6,'name'=>'Living Status','useris'=>$this->living_status,'looksfor'=>$this->interests->living_status,'status'=>$status]);
        if($this->interests->education_status && !empty(json_decode($this->interests->education_status)))
            $status = (in_array($user->education_status,json_decode($this->interests->education_status)) ) ? 'yes' : 'no';
        else
            $status = 'notset';
        array_push($data,['id'=>8,'name'=>'Education Status','useris'=>$this->education_status,'looksfor'=>$this->interests->education_status,'status'=>$status]);
        return $data;
    }
    public function isWinkedAt($user){
        $winks = $this->winks()->where('wink',$user->id);
        if($winks->count() > 0){
            return true;
        }else{
            return false;
        }
    }
    public function isFaved($user){
        $faves = $this->faves()->where('fave',$user->id);
        if($faves->count() > 0){
            return true;
        }else{
            return false;
        }
    }
}

