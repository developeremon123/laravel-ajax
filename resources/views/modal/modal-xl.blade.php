<div class="modal fade" id="saveDataModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="storeForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="update_id" id="update_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <span class="text-danger fw-bold">All (*) mark fields are required.</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <x-textbox labelName="Name" col="col-md-12" name="name" required="required"
                                placeholder="Enter name" />
                            <x-textbox type="email" labelName="Email" col="col-md-12" name="email"
                                required="required" placeholder="Enter email" />
                            <x-textbox type="number" labelName="Mobile No" col="col-md-12" name="mobile_no"
                                required="required" placeholder="Enter Mobile Number" />
                            <x-textbox labelName="Password" type="password" col="col-md-12" name="password"
                                required="required" placeholder="Enter Password" />
                            <x-textbox type="password" labelName="Confirm Password" col="col-md-12"
                                name="password_confirmation" required="required" placeholder="Enter Password Again" />

                            <x-selectbox onchange="upazilaList(this.value,'storeForm')" labelName="District"
                                col="col-md-12" name="district_id" required="required">
                                @if (!$data['districts']->isEmpty())
                                    @foreach ($data['districts'] as $district)
                                        <option value="{{ $district->id }}">{{ $district->location_name }}</option>
                                    @endforeach
                                @endif
                            </x-selectbox>
                            <x-selectbox labelName="Upazila" col="col-md-12" name="upazila_id"
                                required="required"></x-selectbox>
                            <x-textbox type="number" labelName="Postal Code" col="col-md-12" name="postal_code"
                                required="required" placeholder="Enter Postal Code" />
                            <x-textarea labelName="Address" col="col-md-12" name="address" required="required"
                                placeholder="Enter Address" />
                        </div>
                        <div class="col-md-4">
                            <div class="form-group col-md-12">
                                <input type="file" name="avatar" id="avatar" class="dropify"
                                    data-show-errors="true">
                                <input type="hidden" name="old_avatar" id="old_avatar">
                            </div>
                            <x-selectbox labelName="Role" col="col-md-12" name="role_id" required="required">
                                @if (!$data['roles']->isEmpty())
                                    @foreach ($data['roles'] as $role)
                                        <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                    @endforeach
                                @endif
                            </x-selectbox>
                        </div>



                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="save-btn"></button>
                </div>
            </form>
        </div>
    </div>
</div>
