<?php

namespace App\Http\Controllers\HT20;

use App\Http\Controllers\Controller;
use App\Models\HT20\Apartment;
use App\Http\Requests\ChangePasswordRequest;
use App\Services\HT20\UserService;
use App\Models\HT20\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserEditController extends Controller
{
    function __construct(UserService $userService)
    {
        $this->middleware('auth.api');
//        parent::__construct($userService, array('active' => '', 'group' => ''));
    }

    public function profile()
    {
        $apartment = Apartment::find(Auth::user()->apartment_id);
        if ($apartment) {
            $apartment = $apartment->name;
        } else $apartment = '';
        return view('users.profile', ['group' => '', 'active' => '', 'apartment' => $apartment]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->only(['skype', 'email_htauto', 'phone', 'birth_day']);
        if ($request->hasFile('avata')) {
            $name = time() . "-" . Auth::id() . "." . $request->avata->getClientOriginalExtension();
            $request->avata->move(public_path('storage'), $name);
            $data['avata'] = '/public/storage/' . $name;
        }
        $data['birth_day'] = Carbon::createFromFormat('d/m/Y', $data['birth_day'])->format('Y/m/d');
        return User::find(Auth::id())->update($data);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->only(['old_password', 'new_password', 'ver_password']);
        if (Hash::check($data['old_password'],Auth::user()->getAuthPassword())) {
            User::find(Auth::id())->update(['password' => Hash::make($request->new_password)]);
            return response()->json([
                'code' => 200,
                'message' => 'Thàng công!'
            ], 200);
        }
        return response()
            ->json([
                'code' => 400,
                'message' => $data['old_password']
            ], 400);
    }

    public function password()
    {
        return view('users.password', ['group' => '', 'active' => '']);
    }
}
