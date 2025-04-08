<?php

namespace App\Http\Controllers;

use App\Http\Filters\UserFilter;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    use ApiResponses;
    public function getProfile(UserFilter $filter): JsonResponse
    {
        $user_id = auth()->id();
        $user = User::query()->where('id', $user_id)->filter($filter)->first();
        return $this->ok('my profile', new UserResource($user));
    }
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:255',
            'profile_photo' => 'mimes:jpeg,jpg,png|max:2048',
            'farm_name' => 'nullable|string|max:255',
            'delivery_fee' => 'nullable|string|max:255',
        ]);
        if ($request->hasFile('profile_photo')) {
            $location = getFilePath('user_profile');
            $path = fileUploader($request->profile_photo, $location);
            $validatedData['profile_photo'] = $path;
        }
        $user->update($validatedData);
        return $this->ok('my profile updated', new UserResource($user));
    }
    public function toggleUser()
    {
        $user = auth()->user();
        if($user->is_buyer)
        {
            $user->is_buyer = false;
            $user->is_seller = true;
            $user->save();
            $msg = 'Seller';
        }else{
            $user->is_buyer = true;
            $user->is_seller = false;
            $user->save();
            $msg = 'Buyer';
        }
        return $this->ok("User is now $msg", new UserResource($user));
    }
}
