<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Auth;

class UserAddressesController extends Controller
{

    public function index(Request $request)
    {

        $addresses = Auth::user()->addresses;
        return view('user_address.index', compact('addresses'));
    }

    public function create()
    {
        $address = new UserAddress();
        return view('user_address.create_and_edit', compact('address'));
    }

    public function edit(UserAddress $userAddress)
    {
        $this->authorize('own', $userAddress);
        return view('user_address.create_and_edit', ['address'=>$userAddress]);
    }

    public function store(UserAddressRequest $request)
    {
        $address = new UserAddress();

        $address->user_id = Auth::user()->id;
        $address->province = $request->province;
        $address->city     = $request->city;
        $address->district = $request->district;
        $address->address  = $request->address;
        $address->zip      = $request->zip;
        $address->contact_name  = $request->contact_name;
        $address->contact_phone  = $request->contact_phone;

        $address->save();

        return redirect()->route('user_addresses.index');

    }

    public function update(UserAddressRequest $request, UserAddress $userAddress)
    {

        $this->authorize('own', $userAddress);
        $userAddress->province = $request->province;
        $userAddress->city     = $request->city;
        $userAddress->district = $request->district;
        $userAddress->address  = $request->address;
        $userAddress->zip      = $request->zip;
        $userAddress->contact_name  = $request->contact_name;
        $userAddress->contact_phone  = $request->contact_phone;

        $userAddress->save();

        return redirect()->route('user_addresses.index');

    }

    public function destroy(UserAddress $userAddress)
    {
        $this->authorize('own', $userAddress);
        $userAddress->delete();

        return [];
    }
}
