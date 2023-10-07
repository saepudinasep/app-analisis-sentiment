<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required']
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();


            Session::flash('status', 'success');
            Session::flash('message', 'Selamat Datang ' . Auth::user()->name);

            return redirect()->intended('/home');
        } else {
            $user = User::where('email', $request->input('email'))->first();
            if (!$user) {

                Session::flash('status', 'error');
                Session::flash('message', 'Akun tidak ditemukan');

                return redirect('/');
            } else {
                Session::flash('status', 'warning');
                Session::flash('message', 'Password Salah');

                return redirect('/');
            }
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function register()
    {
        return view('register');
    }

    public function registerProses(Request $request)
    {
        $password = $request->password;
        $rePassword = $request->rePassword;
        if ($password != $rePassword) {
            Session::flash('status', 'error');
            Session::flash('message', 'Pastikan Password Anda Sama!');

            return redirect('/register');
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            event(new Registered($user));

            Auth::login($user);


            $userName = Auth::user()->name;
            $userName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $userName);
            $path = resource_path('json/' . $userName);
            File::makeDirectory($path, 0777, true, true);

            return redirect('/email/verify');
        }
    }

    public function forgot_password()
    {
        return view('forgot-password');
    }

    public function resetPassword()
    {
        return view('reset-password');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $id = Auth::user()->id;
        $data = User::findOrFail($id);
        return view('profile', ['data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Validation rules for the uploaded file
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'photo.required' => 'Silakan pilih gambar yang akan diunggah.',
            'photo.image' => 'File yang diupload harus berupa gambar.',
            'photo.mimes' => 'Hanya file JPEG, PNG, dan JPG yang diperbolehkan.',
            'photo.max' => 'Ukuran gambar tidak boleh lebih besar dari 2MB.',
        ]);

        if ($validator->fails()) {
            Session::flash('status', 'error');
            Session::flash('message', $validator->errors()->first('photo'));
        } else {
            // Upload file and process if validation passes
            // Get the authenticated user's name
            $userName = Auth::user()->name;

            // Replace any characters in the username that are not letters, digits, hyphens, or underscores with underscores
            $userName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $userName);

            // Check if a new photo is uploaded
            if ($request->hasFile('photo')) {
                // Generate a new filename based on the user's name and a timestamp
                $extension = $request->file('photo')->getClientOriginalExtension();
                $newName = $userName . '.' . $extension;

                // Delete the old image (if it exists)
                if (Storage::exists('public/image/' . $request['image'])) {
                    Storage::delete('public/image/' . $request['image']);
                }

                // Store the new image with the generated filename
                $request->file('photo')->storeAs('public/image', $newName);

                // Update the 'image' key in the request with the new filename
                $request['image'] = $newName;

                $id = Auth::user()->id;
                // Save the updated data or perform any necessary database operations
                // For example, you might update the user's record in the database with the new image filename
                User::where('id', $id)->update(['photo' => $newName]);
                // Auth::user()->update(['photo' => $newName]);

                // Flash a success message
                Session::flash('status', 'success');
                Session::flash('message', 'Photo berhasil diperbarui.');
            } else {
                // Flash an error message if no file was uploaded
                Session::flash('status', 'error');
                Session::flash('message', 'No file uploaded.');
            }
        }

        // Redirect back to the previous page
        return redirect('/profile');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
