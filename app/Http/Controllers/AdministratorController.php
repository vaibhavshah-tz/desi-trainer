<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\CommonHelper;
use App\Http\Requests\AdminChangePasswordRequest;
use App\Http\Requests\EditProfileRequest;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class AdministratorController extends Controller
{
    /**
     * Show the admin profile form.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $page_title = 'Admin Profile';

        return view('administrator.edit', compact(['page_title']));
    }

    /**
     * Update admin profile details
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function update(EditProfileRequest $request)
    {
        try {
            $admin = Auth::user();
            !empty($request->avatar_remove) ? $request->merge(['avatar' => null]) : '';

            if ($admin->update($request->all())) {
                $request->session()->flash('success', 'Admin details updated successfully.');
            } else {
                $request->session()->flash('error', 'Admin details not updated.');
            }

            return redirect()->route('admin.edit-profile');
        } catch (\Exception $ex) {
            $request->session()->flash('error', 'Admin details not updated.');

            return redirect()->route('admin.edit-profile');
        }
    }

    /**
     * Show the admin change password form.
     *
     * @return \Illuminate\View\View
     */
    public function changePassword()
    {
        $page_title = 'Admin Profile';

        return view('administrator.change-password', compact(['page_title']));
    }

    /**
     * Update admin's password
     * 
     * @param \App\Requests\AdminChangePasswordRequest $request
     */
    public function updatePassword(AdminChangePasswordRequest $request)
    {
        try {
            $admin = Auth::user();
            $admin->password = $request->new_password;
            if ($admin->save()) {
                $request->session()->flash('success', 'Password changed successfully.');
            } else {
                $request->session()->flash('error', 'Password not changed, Please try again.');
            }

            return redirect()->route('admin.change-password');
        } catch (\Exception $ex) {
            $request->session()->flash('error', 'Password not changed, Please try again.');

            return redirect()->route('admin.change-password');
        }
    }

    /**
     * Check unique email
     * 
     * @param \Illuminate\Http\Request
     * @return boolean
     */
    public function checkEmail(Request $request)
    {
        $user = new User();
        return CommonHelper::checkEmail($user, $request);
    }
}
