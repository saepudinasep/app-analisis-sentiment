<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataLatihController;
use App\Http\Controllers\DataMentahController;
use App\Http\Controllers\DataSentimentController;
use App\Http\Controllers\DataUjiController;
use App\Http\Controllers\KNNController;
use App\Http\Controllers\LogisticController;
use App\Http\Controllers\NaiveController;
use App\Http\Controllers\RandomForestController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\SendEmail;
use App\Http\Controllers\SVMController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
})->name('login')->middleware('guest');
Route::post('/', [AuthController::class, 'index'])->middleware('guest');
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/profile', [AuthController::class, 'show'])->middleware('auth', 'verified');
Route::post('/profile', [AuthController::class, 'update']);
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');
Route::post('/forgot-password', function (Request $request) {
    // Validation rules for the 'email' field
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
    ], [
        'email.required' => 'Silakan masukkan alamat email.',
        'email.email' => 'Format alamat email tidak valid.',
    ]);

    if ($validator->fails()) {
        Session::flash('status', 'error');
        Session::flash('message', $validator->errors()->first('email'));
    } else {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            Session::flash('status', 'success');
            Session::flash('message', __('Kami telah mengirimkan tautan pengaturan ulang kata sandi Anda melalui email.'));
        } else {
            Session::flash('status', 'error');
            Session::flash('message', __('Kami tidak dapat menemukan user dengan alamat email tersebut.'));
        }
    }

    // Redirect back to the previous page
    return back();
})->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');
Route::post('/reset-password', function (Request $request) {
    // Validation rules for the password reset request
    $validator = Validator::make($request->all(), [
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ], [
        'token.required' => 'Token diperlukan untuk mengatur ulang kata sandi.',
        'email.required' => 'Silakan berikan alamat email Anda.',
        'email.email' => 'Format alamat email tidak valid.',
        'password.required' => 'Silakan masukkan kata sandi baru.',
        'password.min' => 'Kata sandi harus terdiri dari minimal 8 karakter.',
        'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
    ]);

    if ($validator->fails()) {
        Session::flash('status', 'error');
        Session::flash('message', $validator->errors()->first());
    } else {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
                Auth::login($user);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            Session::flash('status', 'success');
            Session::flash('message', __('Kata sandi Anda telah berhasil diatur ulang.'));
            return redirect('/home');
        } else {
            Session::flash('status', 'error');
            Session::flash('message', __('Penyetelan ulang kata sandi gagal. Silakan periksa email dan token Anda.'));
        }
    }

    // Redirect back to the previous page
    return back();
})->middleware('guest')->name('password.update');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'registerProses']);
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Route::get('/send-email', [SendEmail::class, 'index']);
// Route::post('/send-email', [SendEmail::class, 'store'])->middleware('guest');

Route::get('/home', [DashboardController::class, 'index'])->middleware('auth', 'verified');

Route::get('/data-mentah', [DataMentahController::class, 'index'])->middleware('auth', 'verified');
Route::get('/import-excel', [DataMentahController::class, 'importExcel'])->middleware('auth', 'verified');
Route::post('/importExcel', [DataMentahController::class, 'storeExcel'])->name('ImportExcel');
Route::get('/json-data', [DataMentahController::class, 'showJsonData'])->name('showJsonData');
Route::post('/simpan-data', [DataMentahController::class, 'simpanData'])->name('simpanData');
Route::get('/delete-data', [DataMentahController::class, 'delete'])->middleware('auth', 'verified');

Route::get('/data-sentiment', [DataSentimentController::class, 'index'])->middleware('auth', 'verified');

Route::get('/data-latih', [DataLatihController::class, 'index'])->middleware('auth', 'verified');

Route::get('/data-uji', [DataUjiController::class, 'index'])->middleware('auth', 'verified');

Route::get('/naive-bayes', [NaiveController::class, 'index'])->middleware('auth', 'verified');
Route::get('/naive-bayes-visual', [NaiveController::class, 'visual'])->middleware('auth', 'verified');

Route::get('/svm', [SVMController::class, 'index'])->middleware('auth', 'verified');
Route::get('/svm-visual', [SVMController::class, 'visual'])->middleware('auth', 'verified');

Route::get('/random-forest', [RandomForestController::class, 'index'])->middleware('auth', 'verified');
Route::get('/random-forest-visual', [RandomForestController::class, 'visual'])->middleware('auth', 'verified');

Route::get('/knn', [KNNController::class, 'index'])->middleware('auth', 'verified');
Route::get('/knn-visual', [KNNController::class, 'visual'])->middleware('auth', 'verified');

Route::get('/logistic-regression', [LogisticController::class, 'index'])->middleware('auth', 'verified');
Route::get('/logistic-regression-visual', [LogisticController::class, 'visual'])->middleware('auth', 'verified');
