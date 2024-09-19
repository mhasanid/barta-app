<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private function getUserWithProfile()
    {
        return DB::table('users')
            ->leftjoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->where('users.id', Auth::id())
            ->select('users.*', 'user_profiles.bio', 'user_profiles.profile_picture')
            ->first();
    }

    public function viewDashboard()
    {
        $user = $this->getUserWithProfile();
        if(!$user){
            return redirect()->route('login');
        }
        return view('profile.dashboard', compact('user'));
    }

    public function viewProfile()
    {
        $user = $this->getUserWithProfile();
        return view('profile.view', compact('user'));
    }

    public function editProfile()
    {
        $user = $this->getUserWithProfile();
        return view('profile.edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = DB::table('users')
            ->where('id', Auth::id())
            ->first();

        $userProfile = DB::table('user_profiles')
            ->where('user_id', Auth::id())
            ->first();
        
        $validator = Validator::make($request->all(),[
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|in:' . $user->email,
            'password' => 'nullable|string|min:8',
            'newpassword' => 'nullable|required_with:password|string|min:8',
            'bio'=> 'nullable|string|max:255',
        ],[
            'firstname.required' => 'First name is required.',
            'lastname.required' => 'Last name is required.',
            'email.in' => 'Email cannot be changed.',
            'password.min' => 'Password must be at least 8 characters long.',
            'newpassword.min' => 'Password must be at least 8 characters long.',
            'newpassword.required_with' => 'If you wish to change the password, Fill in your current password as well.',
        ]);
        
        // dd($userProfile);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $validated = $validator->validated();
        
        $updatedUserData = [
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'updated_at' => now(),
        ];
        
        if ($validated['password'] && Hash::check($validated['password'], $user->password)) {
            $data['password'] = Hash::make($validated['newpassword']);
        }
        
        $updatedUserProfileData = [
            'bio'=>$validated['bio'],
            'updated_at' => now()
        ];

        
        try{
            DB::table('users')->where('id', $user->id)->update($updatedUserData);
            if($userProfile){
                DB::table('user_profiles')->where('user_id', $userProfile->user_id)->update($updatedUserProfileData);
            }
            DB::table('user_profiles')->insert([
                ...$updatedUserProfileData,
                'created_at' => now(),
                'user_id' => $user->id,
            ]);
            
            return redirect()->route('profile.view')->with('status', 'Profile updated successfully!');
        }catch (Exception $e){
            dd($e->getMessage());
            return redirect()->back()
                ->withErrors(['db_fails'=>'Something went wrong, failed to update your profile!']);  
        }
            
    }
}
