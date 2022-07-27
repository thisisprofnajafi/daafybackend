<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PhotosController;
use App\Http\Controllers\InterestsController;
use App\Http\Controllers\MatchesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SavedSearchController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SpecialController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\WinkController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PhoneController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/send/reset/email', [AuthController::class, 'resetEmail']);
Route::post('/send/reset/password', [AuthController::class, 'resetPassword']);
Route::get('/check/username/{username}', [AppController::class, 'checkUsername']);

Route::group(['middleware' => ['auth:sanctum', 'online']], function () {

      Route::post('/reset/password', [AuthController::class, 'resetpass']);

    /* ================================================================>>>>>>>>
     *                              User
     * ================================================================>>>>>>>>
     */

    Route::post('/upload-avatar', [UserController::class, 'upload_avatar']);

    /* ================================================================>>>>>>>>
     *                              Middle
     * ================================================================>>>>>>>>
     */
    Route::get('is-setup', [UserController::class, 'is_setup']);
    Route::get('is-verified', [UserController::class, 'is_verified']);
    Route::get('check-premium', [UserController::class, 'is_premium']);

    /* ================================================================>>>>>>>>
     *                              Verify Email
     * ================================================================>>>>>>>>
     */

    Route::post('email/verify', [VerificationController::class, 'verify']);
    Route::post('email/resend', [VerificationController::class, 'resend']);

    /* ================================================================>>>>>>>>
     *                              Chat
     * ================================================================>>>>>>>>
     */

    Route::get('chat/conversations', [ConversationController::class, 'conversations']);
    Route::get('chats/conversation/{id}/messages', [ConversationController::class, 'messages']);
    Route::post('chats/conversation/{id}/message', [ConversationController::class, 'newMessage']);
    Route::post('chat/conversation/check/{id}', [ConversationController::class, 'checkConverstiaion']);
    Route::post('chat/conversation/send/{id}', [ConversationController::class, 'createNewConversation']);
    Route::post('chat/conversation/markasreed/{id}', [ConversationController::class, 'markAsReed']);
    
    /* ================================================================>>>>>>>>
     *                              Home page
     * ================================================================>>>>>>>>
     */

    Route::get('/homepage', [UserController::class, 'homepage']);

    /* ================================================================>>>>>>>>
     *                              Auth
     * ================================================================>>>>>>>>
     */

    Route::post('/logout', [AuthController::class, 'logout']);

    /* ================================================================>>>>>>>>
     *                              Users
     * ================================================================>>>>>>>>
     */

    Route::get('/users/{page}', [UserController::class, 'allUsers']);
    Route::get('/users/online/{page}', [UserController::class, 'onlineUsers']);
    Route::get('/user/{username}', [UserController::class, 'getUser']);
    Route::get('/user/this/getinfo', [UserController::class, 'getOurInfo']);
    Route::post('/user/this/edit', [UserController::class, 'edit']);


    Route::get('/user/this/interests', [InterestsController::class, 'getInterests']);
    Route::post('/user/this/interests/edit', [InterestsController::class, 'setInterests']);


    Route::get('/is-premium', [UserController::class, 'getUserPremium']);


    /* ================================================================>>>>>>>>
     *                              Friendship
     * ================================================================>>>>>>>>
     */
    Route::post('user/sendRequest/{id}', [UserController::class, 'sendRequest']);
    Route::post('user/cencelRequest/{id}', [UserController::class, 'cancelRequest']);
    Route::post('user/removeFriend/{id}', [UserController::class, 'removeFriend']);

    Route::post('user/acceptRequest/{id}', [UserController::class, 'acceptRequest']);

    Route::get('/friendshipStatus/{id}', [UserController::class, 'friendStatus']);
    Route::get('/friends/{page}', [UserController::class, 'getFriends']);
    Route::get('getRequests', [UserController::class, 'getRequests']);

    /* ================================================================>>>>>>>>
     *                              World - App
     * ================================================================>>>>>>>>
     */

    Route::get('getCountries', [AppController::class, 'getCountries']);
    Route::get('{code}/getStates', [AppController::class, 'getStates']);
    Route::get('{code}/getCities', [AppController::class, 'getCities']);

    Route::group(['middleware' => ['verifyEmail']], function () {


    });

    /* ================================================================>>>>>>>>
     *                              Special
     * ================================================================>>>>>>>>
     */

    Route::get('special', [SpecialController::class, 'getTodaySpecial']);


    /* ================================================================>>>>>>>>
    *                              Setup
    * ================================================================>>>>>>>>
    */

    Route::post('setup', [UserController::class, 'setup']);

    /* ================================================================>>>>>>>>
     *                              Winks
     * ================================================================>>>>>>>>
     */

    Route::get('winks/count', [WinkController::class, 'getWinksCount']);
    Route::get('winks/recent', [WinkController::class, 'getRecentWinks']);
    Route::get('winks/{page}', [WinkController::class, 'getWinks']);
    Route::get('winks/reverse/{page}', [WinkController::class, 'getReverseWinks']);
    Route::get('winks/mutual/{page}', [WinkController::class, 'getMutualWinks']);

    Route::post('wink/{id}', [WinkController::class, 'wink']);
    Route::post('dewink/{id}', [WinkController::class, 'dewink']);

    /* ================================================================>>>>>>>>
     *                              Favorites
     * ================================================================>>>>>>>>
     */

    Route::get('favorites/count', [FavoriteController::class, 'getFavesCount']);
    Route::get('favorites/recent', [FavoriteController::class, 'getRecentFaves']);
    Route::get('favorites/{page}', [FavoriteController::class, 'getFaves']);
    Route::get('favorites/reverse/{page}', [FavoriteController::class, 'getReverseFaves']);
    Route::get('favorites/mutual/{page}', [FavoriteController::class, 'getMutualFaves']);
    Route::post('favorite/{id}', [FavoriteController::class, 'favorite']);
    Route::post('unFavorite/{id}', [FavoriteController::class, 'unFavorite']);


    /* ================================================================>>>>>>>>
     *                              Matches
     * ================================================================>>>>>>>>
     */


    Route::get('matches/reverse/{page}', [MatchesController::class, 'getReverseMatches']);
    Route::get('matches/mutual/{page}', [MatchesController::class, 'getMutualMatches']);
    Route::get('matches/recent/', [MatchesController::class, 'getRecentMatches']);
    Route::get('matches/{page}', [MatchesController::class, 'getMatches']);


    /* ================================================================>>>>>>>>
     *                              Block
     * ================================================================>>>>>>>>
     */

    Route::get('blocks/{page}', [UserController::class, 'gatBlockings']);
    Route::post('block/{id}', [UserController::class, 'block']);
    Route::post('unblock/{id}', [UserController::class, 'unBlock']);


    /* ================================================================>>>>>>>>
     *                              View
     * ================================================================>>>>>>>>
     */

    Route::get('views/count', [ViewController::class, 'getViewsCount']);
    Route::get('views/recent', [ViewController::class, 'getRecentViews']);
    Route::get('views/{page}', [ViewController::class, 'getViews']);
    Route::get('viewers/{page}', [ViewController::class, 'getViewers']);
    Route::post('view/{id}', [ViewController::class, 'view']);

    /* ================================================================>>>>>>>>
     *                              Searches
     * ================================================================>>>>>>>>
     */

    Route::post('users/search/basic/{page}', [SearchController::class, 'basic']);
    Route::post('users/search/advanced/{page}', [SearchController::class, 'advanced']);
    Route::post('users/search/tag/{page}', [SearchController::class, 'tag']);

    /* ================================================================>>>>>>>>
     *                             Advanced Searches
     * ================================================================>>>>>>>>
     */

    Route::post('users/search/save', [SavedSearchController::class, 'save']);
    Route::get('users/search/get', [SavedSearchController::class, 'getSaves']);
    Route::post('users/search/save/delete', [SavedSearchController::class, 'deleteSave']);


    /* ================================================================>>>>>>>>
     *                              Tags
     * ================================================================>>>>>>>>
     */

    Route::post('/manage/tags', [TagsController::class, 'manageTags']);

    /* ================================================================>>>>>>>>
     *                              Interests
     * ================================================================>>>>>>>>
     */

    Route::post('/manage/interests', [InterestsController::class, 'manageInterests']);


    /* ================================================================>>>>>>>>
   *                              Contact Us
   * ================================================================>>>>>>>>
   */

    Route::post('help', [SupportController::class, 'help']);


    /* ================================================================>>>>>>>>
    *                              Notification
    * ================================================================>>>>>>>>
    */

    Route::post('notification/markasseen/mini', [NotificationController::class, 'markAsSeenMini']);
    Route::post('notification/markasseen/{load}', [NotificationController::class, 'markAsSeen']);
    Route::get('notification/mini', [NotificationController::class, 'getNotificationsMin']);
    Route::get('notification/get/{load}', [NotificationController::class, 'getNotifications']);

    /* ================================================================>>>>>>>>
    *                              Images
    * ================================================================>>>>>>>>
    */

    Route::post('/photos/new', [PhotosController::class, 'newPhoto']);
    Route::post('/photos/delete/{id}', [PhotosController::class, 'deletePhoto']);
    Route::get('/getPhotos', [PhotosController::class, 'getPhotos']);
      /* ================================================================>>>>>>>>
       *                              feedback
       * ================================================================>>>>>>>>
       */
    Route::post('/feedback', [FeedbackController::class, 'feedback']);



 /* ================================================================>>>>>>>>
       *                              Payments
       * ================================================================>>>>>>>>
       */
      Route::post('/setpay', [PaymentController::class, 'setPayment']);



 /* ================================================================>>>>>>>>
       *                              Payments
       * ================================================================>>>>>>>>
       */
      Route::post('/setPhone', [PhoneController::class, 'setPhone']);
      Route::post('/newCode', [PhoneController::class, 'requestNewCode']);
      Route::post('/checkCode', [PhoneController::class, 'checkCode']);
});
