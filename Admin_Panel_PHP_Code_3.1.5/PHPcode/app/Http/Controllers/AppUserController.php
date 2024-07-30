<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class AppUserController extends Controller
{
    public function index()
    {
        $roleList = Role::all(); // Replace with your actual query to fetch user roles
        return view('users', compact('roleList'));
    }

    public function show(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');

        $sql = User::with('roles');
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $sql = $sql->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
                $fields = ['id', 'profile', 'name', 'type', 'mobile'];
                foreach ($fields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$search}%");
                }
            });
        }
        $total = $sql->count('id');
        $sql = $sql->skip($offset)->take($limit)->orderBy($sort, $order);
        $rows = $sql->get()->map(function ($row) {
            $icon = [
                'email' => 'far fa-envelope-open',
                'gmail' => 'fab fa-google-plus-square text-danger',
                'fb' => 'fab fa-facebook-square text-primary',
                'apple' => 'fab fa-apple',
                'mobile' => 'fas fa-phone-square',
                'Google' => 'fab fa-google-plus-square text-danger',
                'Facebook' => 'fab fa-facebook-square text-primary',
            ];
            $operate =
                '
            <a class="btn btn-icon  btn-primary text-white edit-data" data-id="' .
                $row->id .
                '" data-toggle="modal" data-target="#editDataModal" title="'.__('edit').'"><em class="fa fa-edit"></em></a>';
            return [
                'id' => $row->id,
                'firebase_id' => $row->firebase_id ?? '',
                'name' => $row->name ?? '',
                'type' => isset($row->type) && $row->type != '' ? '<em class="' . $icon[trim($row->type)] . ' fa-2x"></em>' : '<em class="' . $icon['email'] . ' fa-2x"></em>',
                'email' => hideEmailAddress($row->email) ?? '',
                'mobile' => hideMobileNumber($row->mobile) ?? '',
                'profile' => !empty($row->profile) ? '<a href="' . $row->profile . '" data-toggle="lightbox" data-title="Image"><img  class = "images_border" src="' . $row->profile . '" height="50" width="50"></a>' : '-',
                'fcm_id' => $row->fcm_id ?? '',
                'status1' => $row->status ? __('active') : __('deactive'),
                'status' => $row->status,
                'date' => $row->date ?? '',
                'role_id' => $row->role ?? '',
                'role' => $row->roles->role ?? '',
                'created_at' => $row->created_at ?? '',
                'updated_at' => $row->updated_at ?? '',
                'operate' => $operate,
            ];
        });
        return response()->json([
            'total' => $total,
            'rows' => $rows,
        ]);
    }

    public function update(Request $request)
    {
        $appUser = User::find($request->edit_id);
        $appUser->role = $request->input('edit_role');
        $appUser->status = $request->edit_status;
        $appUser->save();
        $response = [
            'error' => false,
            'message' => __('updated_success'),
        ];
        return response()->json($response);
    }
}
