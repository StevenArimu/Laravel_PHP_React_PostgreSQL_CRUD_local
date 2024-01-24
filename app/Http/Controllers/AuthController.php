<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Data validation
            $request->validate([
                'f_name' => 'required',
                'email' => 'required|unique:users',
                'password' => 'required|confirmed',
            ]);

            $user = new User();
            $user->_id = (string) Str::uuid();
            $user->f_name = $request->f_name;
            $user->l_name = $request->l_name;
            $user->email = $request->email;
            $user->location = $request->location;
            $user->password = Hash::make($request->password);
            $user->remember_token = Str::random(20);
            $user->save();
            $token = $user->createToken("token")->accessToken;
            return response()->json([
                'status' => true,
                'msg' => 'User registered successfully',
                'newUser' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    public function login(Request $request)
    {
        try {
            $input = $request->all();

            $request->validate([
                'email' => 'email|required',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(
                    [
                        'msg' => 'User not found',
                        'Target' => $input,
                    ],
                    401
                );
            }

            // Create user token with roles then return token with cookie
            $token = $user->createToken('token')->accessToken;

            $response = [
                'status' => true,
                'message' => 'User login successful',
                'user' => $user,
                'token' => $token,
            ];

            return response()->json($response, 201);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function loginUserDetail()
    {
        try {
            $user = auth()->user();
            return response()->json(['user' => $user], 200);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['status' => false, 'msg' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function findOne(Request $request)
    {
        try {
            $id = $request->id;

            if (!$id) {
                return response()->json(['status' => false, 'msg' => 'userId is required']);
            }

            $user = User::where('_id', $id)->first();

            if ($user) {
                return response()->json([
                    'status' => true,
                    'msg' => 'User found successfully',
                    'user' => $user,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'User does not exist',
                    'user' => null,
                ]);
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['msg' => $e->getMessage()], 500);
        }
    }

    public function findAll()
    {
        try {
            $users = User::all();

            if (count($users) > 0) {
                return response()->json([
                    'status' => true,
                    'message' => 'Profile Information',
                    'users' => $users
                ]);
            } else {
                return response()->json(['status' => false, 'message' => 'No Existing users']);
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
    // public function update(Request $request)
    // {
    //     // //Data validation
    //     $request->validate([
    //         'f_name' => 'required',
    //         'email' => 'required|unique:users',
    //         'password' => 'required|confirmed',
    //     ]);
    //     $user = User::where('_id', $request->updateId)->first();
    //     $user->f_name = $request->f_name;
    //     $user->l_name = $request->l_name;
    //     $user->email = $request->email;
    //     $user->location = $request->location;
    //     $user->password = Hash::make($request->password);
    //     $user->remember_token = Str::random(20);
    //     $user->save();
    //     // User::create([
    //     //     'name' => $request->name,
    //     //     'email' => $request->email,
    //     //     'password' => Hash::make($request->password),
    //     //     'remember_token' => Str::random(50),
    //     // ]);
    //     // $token = $user->createToken("token")->accessToken;

    //     return response()->json([
    //         'status' => true,
    //         'msg' => 'user update Successfully',
    //         "updateUser" => $user
    //         // 'token' => $token,
    //     ]);
    // }
    public function updateUser(Request $request)
    {
        try {
            $id = $request->id;

            // Validation
            $request->validate([
                'id' => 'required',
                'f_name' => 'string|nullable',
                'l_name' => 'string|nullable',
                'email' => 'email|nullable|unique:users,email,' . $id,
                'location' => 'string|nullable',
                'password' => 'string|nullable',
            ]);

            // Find the user
            $user = User::where('_id', $id)->first();

            if (!$user) {
                return response()->json(['status' => false, 'msg' => 'User not found']);
            }

            // Update user fields
            $user->f_name = $request->input('f_name') ?? $user->f_name;
            $user->l_name = $request->input('l_name') ?? $user->l_name;
            $user->email = $request->input('email') ?? $user->email;
            $user->location = $request->input('location') ?? $user->location;
            $user->password = $request->input('password') ? Hash::make($request->input('password')) : $user->password;

            // Save changes
            $user->save();

            return response()->json(['status' => true, 'msg' => 'User updated successfully', 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }
    }



    public function addUser(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'email' => 'required|unique:users',
        ]);
        $user = new User();
        $user->f_name = $request->f_name;
        $user->l_name = $request->l_name;
        $user->email = $request->email;
        $user->location = $request->location;
        $user->remember_token = Str::random(20);
        $user->save();
        return response()->json([
            'status' => true,
            'message' => 'user update Successfully',
            'addUser' => $user,
        ]);
    }
    //Profile API GET

    //Profile Logout
    public function logout()
    {
        // Auth::user()->token()->revoke();
        $user = Auth::user();
        return response()->json([
            'status' => true,
            'message' => 'user logout Successfully',
            "user" => $user
        ]);
    }
    public function deleteUser(Request $request)
    {
        try {
            $id = $request->id;
            if (!$id) {
                return response()->json(['status' => false, 'msg' => 'userId is required']);
            }
            $user = User::where('_id', $id)->first();

            if ($user) {
                $user->delete();
                return response()->json(['status' => true, 'msg' => 'user deleted successfully', 'user' => $user]);
            } else {
                return response()->json(['status' => false, 'msg' => 'user does not exist']);
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['msg' => $e->getMessage()], 500);
        }
    }
    public function deleteAllUser(Request $request)
    {
        try {
            $users = User::query()->delete();

            return response()->json(["status" => 200, "msg" => "Data Deleted Successfully", "users" => $users]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }
    }
}