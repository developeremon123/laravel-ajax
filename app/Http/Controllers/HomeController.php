<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Role;
use App\Models\User;
use App\Models\Location;
use App\Traits\Uploadable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\UserFormRequest;

class HomeController extends Controller
{
    use Uploadable;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['roles'] = Role::all();
        $data['districts'] = Location::where('parent_id', 0)->orderBy('location_name', 'asc')->get();
        return view('home', compact('data'));
    }

    // Upazila List
    public function upazilaList(Request $request)
    {
        if ($request->ajax()) {
            if ($request->district_id) {
                $output = "<option value=''>Select Please</option>";
                $upazilas = Location::where('parent_id', $request->district_id)->orderBy('location_name', 'asc')->get();
                if (!$upazilas->isEmpty()) {
                    foreach ($upazilas as $upazila) {
                        $output .= '<option value="' . $upazila->id . '">' . $upazila->location_name . "</option>";
                    }
                }
                return response()->json($output);
            }
        }
    }

    // store data with ajax
    public function store(UserFormRequest $request)
    {
        $data = $request->validated();
        $collection = collect($data)->except(['avatar', 'password_confirmation']);
        if ($request->file('avatar')) {
            $avatar = $this->upload_file($request->file('avatar'), USER_AVATAR);
            $collection = $collection->merge(compact('avatar'));
            if (!empty($request->old_avatar)) {
                $this->delete_file($request->old_avatar, USER_AVATAR);
            }
        }
        $result = User::updateOrCreate(['id' => $request->update_id], $collection->all());

        if ($result) {
            $output = ['status' => 'Success', 'message' => 'Data has been saved successfully'];
        } else {
            if (empty($avatar)) {
                $this->delete_file($avatar, USER_AVATAR);
            }
            $output = ['status' => 'Error', 'message' => 'Data cannot be saved!!'];
        }

        return response()->json($output);
    }

    // show data with ajax 
    public function userList(Request $request)
    {
        if ($request->ajax()) {
            $user = new User();

            if (!empty($request->name)) {
                $user->setName($request->name);
            }
            if (!empty($request->email)) {
                $user->setEmail($request->email);
            }
            if (!empty($request->mobile_no)) {
                $user->setMobileNo($request->mobile_no);
            }
            if (!empty($request->district_id)) {
                $user->setDistrictId($request->district_id);
            }
            if (!empty($request->upazila_id)) {
                $user->setUpazilaId($request->upazila_id);
            }
            if (!empty($request->status)) {
                $user->setStatus($request->status);
            }
            if (!empty($request->role_id)) {
                $user->setRoleId($request->role_id);
            }

            $user->setOrderValue($request->input('order.0.column'));
            $user->setDirValue($request->input('order.0.dir'));
            $user->setLengthValue($request->input('length'));
            $user->setStartValue($request->input('start'));

            $list = $user->getList();
            $data = [];
            $no = $request->input('start');
            foreach ($list as $value) {
                $no++;
                $action    = '';
                $action   .= '<a class="dropdown-item edit_data" data-id="' . $value->id . '"><i class="fa-solid fa-pen-to-square text-primary"></i> Edit</a>';
                $action   .= '<a class="dropdown-item view_data" data-id="' . $value->id . '"><i class="fa-solid fa-eye text-warning"></i>View</a>';
                $action   .= '<a class="dropdown-item delete_data" data-name="' . $value->name . '" data-id="' . $value->id . '"><i class="fa-solid fa-trash text-danger"></i>Delete</a>';
                $btnGroup  = '<div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-list"></i>
                </button>
                <ul class="dropdown-menu">
                  <li>' . $action . '</li>
                </ul>
              </div>';

                $row    = [];
                $row[]  = '<div class="form-check">
                <input class="form-check-input select_data" onchange="select_single_item(' . $value->id . ')" type="checkbox" value="' . $value->id . '" name ="did[]" id="checkbox' . $value->id . '">
                <label class="form-check-label" for="checkbox' . $value->id . '">
                </label>
              </div>';
                $row[]  = $no;
                $row[]  = $this->avatar($value->avatar, $value->name);
                $row[]  = $value->name;
                $row[]  = $value->role->role_name;
                $row[]  = $value->email;
                $row[]  = $value->mobile_no;
                $row[]  = $value->district->location_name;
                $row[]  = $value->upazila->location_name;
                $row[]  = $value->postal_code;
                $row[]  = $value->email_verified_at ? '<span class="badge rounded-pill text-bg-success">Verified</span>' : '<span class="badge rounded-pill text-bg-danger">Unverified</span>';
                $row[]  = $this->toggle_button($value->status, $value->id);
                $row[]  = $btnGroup;
                $data[] = $row;
            }
            $output = [
                "draw"            => $request->input('draw'),
                "recordsTotal"    => $user->count_all(),
                "recordsFiltered" => $user->count_filtered(),
                "data"            => $data
            ];
            echo json_encode($output);
        }
    }

    // avatar function
    public function avatar($avatar = null, $name)
    {
        return !empty($avatar) ? '<img style="width:50px;" src="' . asset('storage/' . USER_AVATAR . $avatar) . '" alt="' . $name . '"/>' : '<img style="width:50px;" src="' . asset("svg/user.svg") . '" alt="User Avatar"/>';
    }

    // edit data 
    public function userEdit(Request $request)
    {
        if ($request->ajax()) {
            $data = User::toBase()->find($request->id);
            if ($data) {
                $output['user'] = $data;
            } else {
                $output['user'] = '';
            }
            return response()->json($output);
        }
    }

    // view data 
    public function userShow(Request $request)
    {
        if ($request->ajax()) {
            $data = User::with(['role:id,role_name', 'district:id,location_name', 'upazila:id,location_name'])->find($request->id);
            if ($data) {
                $output['user_view'] = view('user_details', compact('data'))->render();
                $output['name'] = $data->name;
            } else {
                $output['user_view'] = '';
                $output['name'] = '';
            }
            return response()->json($output);
        }
    }

    // delete data 
    public function userDelete(Request $request)
    {
        if ($request->ajax()) {
            $data = User::find($request->id);
            if ($data) {
                $avatar = $data->avatar;
                if ($data->delete()) {
                    if (!empty($avatar)) {
                        $this->delete_file($avatar, USER_AVATAR);
                    }
                    $output = ['status' => 'success', 'message' => 'Data deleted successfully'];
                } else {
                    $output = ['status' => 'error', 'message' => 'Data cannot delete!'];
                }
            } else {
                $output = ['status' => 'error', 'message' => 'Data cannot delete!'];
            }
            return response()->json($output);
        }
    }

    // bulk action delete data 
    public function bulkActionDelete(Request $request)
    {
        if ($request->ajax()) {
            $avatars = User::toBase()->select('avatar')->whereIn('id', $request->id)->get();
            $result = User::destroy($request->id);
            if ($result) {
                if (!empty($avatars)) {
                    foreach ($avatars as $value) {
                        if (!empty($value->avatar)) {
                            $this->delete_file($value->avatar, USER_AVATAR);
                        }
                    }
                }
                $output = ['status' => 'success', 'message' => 'Data has been deleted successfully'];
            } else {
                $output = ['status' => 'error', 'message' => 'Data cannot delete!'];
            }
            return response()->json($output);
        }
    }

    // toggle button change status 
    public function changeStatus(Request $request)
    {
        if ($request->ajax()) {
            if ($request->id && $request->status) {
                $result = User::find($request->id)->update(['status' => $request->status]);
                if ($result) {

                    $output = ['status' => 'Success', 'message' => 'Status updated successfully'];
                } else {
                    $output = ['status' => 'Error', 'message' => 'Status cannot change!'];
                }
            } else {
                $output = ['status' => 'Error', 'message' => 'Status cannot change!'];
            }
            return response()->json($output);
        }
    }

    // toggle button
    private function toggle_button($status, $id)
    {
        $checked = $status == 1 ? 'checked' : '';
        return '<label class="switch">
        <input type="checkbox" ' . $checked . ' class="change_status" data-id="' . $id . '">
        <span class="slider round"></span>
      </label>';
    }
}
