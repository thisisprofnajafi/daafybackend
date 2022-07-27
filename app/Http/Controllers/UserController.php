<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Interests;
use App\Models\PremiumSetting;
use App\Models\Special;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Nnjeim\World\Models\City;
use Nnjeim\World\Models\Country;
use Illuminate\Support\Str;
use Nnjeim\World\World;

class UserController extends Controller
{
    public function allUsers($page)
    {

        $users = User::all()->where('id', '!=', auth()->user()->id)->where('gender', '=', auth()->user()->interests->gender);

        

        $count = $users->count();
        $users = $users->skip($page * 24)->take(24);

        $users = UserController::listUsersForView($users);


        return response()->json(['status' => true, 'count' => $count, 'users' => $users,'premium'=>true], 200);
    }

    public function getUser($username)
    {
        $user = User::where('username', $username)->first();


        if (auth()->user()->hasBlocked($user)) {

            return RespondHandler::respond(['status' => true, 'block' => true, 'type' => 'self', 'avatar' => $user->avatar, 'name' => $user->firstname . ' ' . $user->lastname, 'id' => $user->id], 200);
        }

        if (auth()->user()->isBlockedBy($user)) {

            return RespondHandler::respond(['status' => true, 'block' => true, 'type' => 'other'], 200);
        }

       

        $info = [
            'id' => $user->id,
            'name' => $user->firstname . ' ' . $user->lastname,
            'avatar' => $user->avatar,
            'bio' => $user->bio,
            'description' => $user->description,
            'location' => Country::find($user->country),
            'relationship' => $user->relation_status,
            'gender' =>$user->gender
        ];


        $friendStatus = null;

        if ($user->isFriendWith(auth()->user())) {
            $friendStatus = 'friend';
        } else {
            $friendStatus = 'request';
        }
        if ($user->hasFriendRequestFrom(auth()->user())) {
            $friendStatus = 'pending';
        }

        $photos = $user->photos;
        $friendsCount = $user->getFriendsCount();
        $data = $user->matchingYou();
        $winked = auth()->user()->isWinkedAt($user);
        $faved = auth()->user()->isFaved($user);
        $recentViews = ViewController::getRecentViews();
        $tags = $user->tags();
        $tagsCount = $tags->count();
        $isPremium = $user->is_premium;
        if($isPremium){
            $preSettings = $user->premiumSetting;
        }else{
            $preSettings = null;
        }

        return response()->json([

            'premium'=>$isPremium,
            'premium_setting'=>$preSettings,
            'photos'=> $photos,
            'status' => true,
            'info' => $info,
            'tags' => $tags,
            'tagsCount' => $tagsCount,
            'friendStatus' => $friendStatus,
            'data' => $data,
            'winked' => $winked,
            'faved' => $faved,
            'friendsCount' => $friendsCount,
            'recentViews' => $recentViews,

        ], 200);

    }

    public function getUserSmall($username)
    {
        $user = User::all()->where('username', $username)->first();
        $data = [
            $user->username,
            $user->name,
            $user->avatar,
            $user->verified
        ];
    }

    // Friendship
    public function getRequests()
    {
        $user = auth()->user();

        if ($user) {

            $requests = $user->getFriendRequests();

            $users = array();

            foreach($requests as $req){

                $u = User::find($req->sender_id);
                $country = Country::find($user->country);

                $n = [
                    'id'=> $u->id,
                    'name'=>strtoupper($u->firstname).' '.strtoupper($u->lastname),
                    'username'=>$u->username,
                    'avatar'=>$u->avatar,
                    'country'=> strtoupper($country->name)
                ];
                array_push($users , $n);

            }
            return response()->json(['status' => true , 'users'=>$users , 'count'=>count($users)], 200);

        } else {
            return null;
        }
    }

    public function removeFriend($id)
    {
        $user = auth()->user();
        $user2 = User::query()->findOrFail($id);

        if ($user && $user2) {

            $query = $user->unfriend($user2);

            return response()->json(['status' => $query], 200);

        } else {
            return null;
        }
    }

    public function sendRequest($id)
    {
        $user = auth()->user();
        $user2 = User::query()->findOrFail($id);

        if ($user && $user2) {

            $user->befriend($user2);

            NotificationController::create($user2->id , $user->id,'request', 'Requested To Follow You');

            return response()->json(['status' => true], 200);

        } else {
            return response()->json(['status' => false], 200);
        }
    }

    public function cancelRequest($id)
    {
        $user = auth()->user();
        $user2 = User::query()->findOrFail($id);

        if ($user && $user2) {

            $user->unfriend($user2);

            return response()->json(['status' => true], 200);

        } else {
            return response()->json(['status' => false], 200);
        }
    }

    public function friendStatus($id)
    {
        $user = auth()->user();
        $user2 = User::query()->findOrFail($id);
        $status = null;
        if ($user && $user2) {
            if ($user->hasFriendRequestFrom($user2)) {
                $status = 'pending';
            } else {
                if ($user->isFriendWith($user2))
                    $status = 'friend';
                else
                    $status = 'null';
            }


            return response()->json(['status' => $status], 200);

        } else {
            return null;
        }
    }

    public function acceptRequest($id)
    {
        $user = auth()->user();

        $user2 = User::query()->findOrFail($id);

        $user->acceptFriendRequest($user2);

        if ($user->isFriendWith($user2)) {

            NotificationController::create($user2->id, $user->id, 'request', 'Accepted Your Request');

            return RespondHandler::respond(['status' => true], 200);
        } else {
            return RespondHandler::respond(['status' => true], 200);
        }
    }

    public function isMatching($id)
    {
        $user = auth()->user();
        $user2 = User::query()->findOrFail($id);

        if ($user) {

            $matching = $user->getIsMatchingWithUser($user2);

            return RespondHandler::respond(['matching' => $matching], 200);
        } else {
            return RespondHandler::notFound();
        }
    }

    // Block
    public function block($id)
    {
        $user = auth()->user();
        $user2 = User::findOrFail($id);

        if ($user && $user2) {

            $user->blockFriend($user2);
            $special = $user->specials()->where('special_id' , $user2->id);
            $special->delete();

            $wink = $user->winks()->where('wink',$user2->id);
            if ($wink) {
                $wink->delete();
            }
            $wink = $user2->winks()->where('wink',$user->id);
            if ($wink) {
                $wink->delete();
            }
            $fave = $user->faves()->where('fave', $user2->id);
            if ($fave) {
                $query = $fave->delete();
            }
            $fave = $user2->faves()->where('fave', $user->id);
            if ($fave) {
                $query = $fave->delete();
            }

            $views = $user->views()->where('view', $user2->id);
            if ($views) {
                $query = $views->delete();
            }

            $views = $user2->views()->where('view', $user->id);
            if ($views) {
                $query = $views->delete();
            }

            $conv = Conversation::query()->whereIn('user_a', [$user->id, $user2->id])->whereIn('user_b', [$user->id, $user2->id])->first();
            $conv->messages()->delete();
            $conv->delete();

            return RespondHandler::respond(['status' => true], 200);
        } else {
            return RespondHandler::respond(['status' => false], 200);
        }
    }

    public function unBlock($id)
    {
        $user = auth()->user();
        $user2 = User::findOrFail($id);

        if ($user && $user2) {

            $user->unblockFriend($user2);

            return RespondHandler::respond(['status' => true], 200);
        } else {
            return RespondHandler::respond(['status' => false], 200);
        }
    }

    public function gatBlockings($page)
    {
        $user = auth()->user();

        $blocks = $user->getBlockedFriendships();

        $count = $blocks->count();

        $blocks = $blocks->skip($page * 24)->take(24);$users = array();foreach($blocks  as $b){            $friend_id = ($b->sender_id == $user->id) ? $b->recipient_id : $b->sender_id ;                    $u = User::find($friend_id);            array_push($users , $u);        }        $users = $this->listUsersForView($users);

        return RespondHandler::respond(['status' => true, 'count' => $count, 'users' => $users , 'premium'=>true], 200);


    }

    public function setInformation(Request $request)
    {
        $user = auth()->user();
        if ($user) {

            $user->setInformation($request);

        } else {
            return RespondHandler::notFound();
        }
    }

    // Sign up Setup
    public function setup(Request $request)
    {
        $user = auth()->user();

        if (Carbon::parse($request->birthdate)->diff(Carbon::now())->y > 18) {
            $user->gender = $request->gender;
            $user->birthdate = $request->birthdate;
            $user->relation_status = $request->relationship;
            $user->education_status = $request->education;
            $user->drink = $request->drink;
            $user->smoke = $request->smoke;
            $user->body_type = $request->bodytype;
            $user->employment_status = $request->employment;
            $user->living_status = $request->living;
            $user->height = $request->height;
            $user->bio = $request->bio;
            $user->description = $request->description;
            $user->country = $request->country;
            if ($request->state)
                $user->state = $request->state;
            if ($request->city)
                $user->city = $request->city;
            $user->is_setup = true;
            $interest = new Interests();
            $interest->gender = $request->lookingGender;
            $interest->seeking_for = json_encode($request->seekingFor);
            $user->interests()->save($interest);

            if($user->avatar){
                $user->save();
                return RespondHandler::respond(['status' => true], 200);
            }else{
                $savePath = env('SITE_URL') . 'images/user/avatars/default/' .strtolower($request->gender).'.png';
                $user->avatar = $savePath;
                $user->save();
                return RespondHandler::respond(['status' => true,'avatar'=>$user->avatar], 200);
            }
            
        } else {
            return RespondHandler::respond(['status' => false, 'error' => 'Your have to be at least 18 years old'], 200);
        }

    }

    // Verification
    public function isVerified()
    {
        $user = auth()->user();
        if ($user) {

            if ($user->verified) {

                return RespondHandler::ok();

            } else
                return RespondHandler::notOk();

        } else {
            return RespondHandler::notFound();
        }

    }

    public function verify(Request $request)
    {
        $user = auth()->user();
        if ($user) {

            $user->verify($request);

            if ($user->verified) {

                return RespondHandler::ok();

            } else
                return RespondHandler::notOk();

        } else {
            return RespondHandler::notFound();
        }
    }

    public function getUserPremium()
    {
        if (auth()->user()->is_premium) {
            return RespondHandler::respond(['upgrade' => false], 200);
        } else {
            return RespondHandler::respond(['upgrade' => true], 200);
        }

    }


    public function homepage()
    {


        $viewsCount = ViewController::getViewsCount();
        $winksCount = WinkController::getWinksCount();
        $favesCount = FavoriteController::getFavesCount();
        $messagesCount = ConversationController::getUnreadMessagesCount();
        $matches = MatchesController::getRecentMatches();
        $views = ViewController::getRecentViews();
        $winks = WinkController::getRecentWinks();
        $lovers = FavoriteController::getRecentFaves();

        $specialUser = SpecialController::getTodaySpecial();


        return RespondHandler::respond([

            'viewsCount' => $viewsCount,
            'winksCount' => $winksCount,
            'favesCount' => $favesCount,
            'messagesCount' => $messagesCount,
            'matches' => $matches,
            'views' => $views,
            'faves' => $lovers,
            'winks' => $winks,
            'specialUser' => $specialUser,

        ], 200);


    }

    public function is_setup()
    {

        if (auth()->user()->is_setup) {

            return RespondHandler::respond(['status' => true], 200);

        }

        return RespondHandler::respond(['status' => false], 200);

    }


    public function is_verified()
    {

        if (auth()->user()->hasVerifiedEmail()) {

            return RespondHandler::respond(['status' => true], 200);

        }

        return RespondHandler::respond(['status' => false], 200);

    }

    public function is_premium()
    {


        if (auth()->user()->premium && auth()->user()->premium->ends >= Carbon::now()) {

            return RespondHandler::respond(['status' => true], 200);

        }

        return RespondHandler::respond(['status' => false], 200);

    }

    public function decodeImage($file)
    {
        $image_64 = $file; //your base64 encoded data

        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf

        $replace = substr($image_64, 0, strpos($image_64, ',') + 1);

        // find substring fro replace here eg: data:image/png;base64,

        $image = str_replace($replace, '', $image_64);

        $image = str_replace(' ', '+', $image);

        $imageName = Str::random(40) . '.' . $extension;

        Storage::disk('public')->put($imageName, base64_decode($image));

        return $imageName;
    }


    public function upload_avatar(Request $request)
    {

        $validatedData = $request->validate([
            'image' => 'required'
        ]);


        $savedname = $this->decodeImage($request->image);


        $savedFile = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix();


        $user = auth()->user();
        $name = md5($user->username . $user->id . 'avatar') . '-avatar' . $savedname;


        $xPath = $savedFile . $savedname;

        $img = Image::make($xPath);

        if ($img->height() >= $img->width()) {

            $img->crop($img->width(), $img->width(), 0, (int)(($img->height() - $img->width()) / 2));

        } else {
            $img->crop($img->height(), $img->height(), (int)(($img->width() - $img->height()) / 2), 0);
        }

        $img->resize(500, 500);
        $path = Storage::disk('avatars')->getDriver()->getAdapter()->getPathPrefix() . $name;

        $img->save($path, 100);

        $savePath = env('SITE_URL') . 'images/user/avatars/' . $name;

        $user->avatar = $savePath;

        $user->save();

        return RespondHandler::respond(['status' => true, 'path' => $savePath], 200);

    }


    public function getFriends($page)
    {
        $user = auth()->user();

        $count = $user->getFriendsCount();
        $friends = $user->getAllFriendships();

        // return RespondHandler::respond(['users' => $friends], 200);

        $users = array();

        foreach($friends as $f){
            $friend_id = ($f->sender_id == $user->id) ? $f->recipient_id : $f->sender_id ;        
            $u = User::find($friend_id);
            array_push($users , $u);
        }

        $users = $this->listUsersForView($users);

        return RespondHandler::respond(['status' => true, 'count' => $count, 'users' => $users , 'premium'=>true], 200);

    }

    public function onlineUsers($page)
    {

        $users = User::all()
            ->where('last_seen', '>', Carbon::now()->subMinutes(5))
            ->where('id', '!=', auth()->user()->id)
            ->where('gender', '=', auth()->user()->interests->gender);

        $count = $users->count();

        $users = $users->skip($page * 24)->take(24);

        $users = UserController::listUsersForView($users);

        return RespondHandler::respond(['status' => true, 'count' => $count, 'users' => $users ,'premium'=> true], 200);

    }


    public static function listUsersForView($users)
    {

        $list = array();

        foreach ($users as $user) {

            $country = Country::find($user->country);
            
            array_push($list, [
                'username' => strtolower($user->username),
                'name' => strtoupper($user->firstname),
                'age' => $user->getAge(),
                'avatar' => $user->avatar,
                'gender' => strtolower($user->gender),
                'country' => strtoupper($country->name),
            ]);
        }
        return $list;
    }


    public function getOurInfo()
    {

        $me = auth()->user();
        $premium = ($me->is_premium) ? true : false;
        $action = World::countries();
        $countries = $action->data;
        $user = [
            'firstname' => $me->firstname,
            'lastname' => $me->lastname,
            'username'=>$me->username,
            'email' => $me->email,
            'country' => $me->country,
            'tags' => $me->tags,
            'gender' => $me->gender,
            'birthdate' => $me->birthdate,
            'bio' => $me->bio,
            'description' => $me->description,
            'premium' => $premium,
            'preOption' => $me->premiumSettings,
            'smoke' => strtolower($me->smoke),
            'height' => strtolower($me->height),
            'drink' => strtolower($me->drink),
            'body_type' => strtolower($me->body_type),
            'education' => strtolower($me->education_status),
            'employment' => strtolower($me->employment_status),
            'living' => strtolower($me->living_status),
            'relationship' => strtolower($me->relation_status),
        ];

        return RespondHandler::respond(['status' => true, 'user' => $user, 'countries' => $countries], 200);

    }


    public function edit(Request $request)
    {

        $user = auth()->user();


        if (Carbon::parse($request->birthdate)->diff(Carbon::now())->y > 18) {

            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->country = $request->country;
            $user->bio = $request->bio;
            $user->description = $request->description;
            $user->username = $request->username;

            $user->smoke = $request->smoke;
            $user->height = $request->height;
            $user->drink = $request->drink;
            $user->body_type = $request->body_type;
            $user->education_status = $request->education;
            $user->employment_status = $request->employment;
            $user->living_status = $request->living;
            $user->relation_status = $request->relationship;


            $user->save();
            
            if ($user->is_premium) {

                if($user->premiumSetting){

                    $user->premiumSetting->instagram_url = $request->instagram;
                    $user->premiumSetting->twitter_url = $request->twitter;
                    $user->premiumSetting->custom_link = $request->custom_link;
                    $user->premiumSetting->facebook_url = $request->facebook;

                    $user->premiumSetting->save();

                }else{

                    $ps = new premiumSetting();

                    $ps->instagram_url = $request->instagram;
                    $ps->twitter_url = $request->twitter;
                    $ps->custom_link = $request->custom_link;
                    $ps->facebook_url = $request->facebook;

                    $user->premiumSetting()->save($ps);
                }
            }
            return RespondHandler::respond(['status' => true], 200);

        } else {
            return RespondHandler::respond(['status' => false, 'error' => 'age'], 200);
        }
    }
}
