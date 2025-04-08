<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserAddressResource;
use App\Models\UserAddress;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    use ApiResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->ok('users Address', UserAddressResource::collection(UserAddress::where('user_id', auth()->id())->get()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'recipient_name' => 'required|max:255',
            'phone' => 'required|max:255',
            'address' => 'required|max:255',
            'city' => 'required|max:255',
            'state' => 'required|max:255',
            'country' => 'nullable|max:255',
            'postal_code' => 'nullable|max:255',
        ]);
        $validatedData['user_id'] = auth()->id();
        $userAddress = UserAddress::create($validatedData);
        return $this->ok('Address added successfully!', new UserAddressResource($userAddress),201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $userAddress = UserAddress::query()->where('id', $id)->where('user_id', auth()->id())->first();
        if (!$userAddress) {
            return $this->error('Address not found!', 404);
        }
        return $this->ok('Address retrieved successfully!', new UserAddressResource($userAddress));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'recipient_name' => 'required|max:255',
            'phone' => 'required|max:255',
            'address' => 'required|max:255',
            'city' => 'required|max:255',
            'state' => 'required|max:255',
            'country' => 'nullable|max:255',
            'postal_code' => 'nullable|max:255',
        ]);
        $userAddress = UserAddress::query()->where('id', $id)->where('user_id', auth()->id())->first();
        if (!$userAddress) {
            return $this->error('Address not found!', 404);
        }
        $userAddress->update($validatedData);
        return $this->ok('Address updated successfully!', new UserAddressResource($userAddress));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $userAddress = UserAddress::query()->where('id', $id)->where('user_id', auth()->id())->first();
        if (!$userAddress) {
            return $this->error('Address not found!', 404);
        }
        $userAddress->delete();
        return $this->ok('Address deleted successfully!');
    }
}
