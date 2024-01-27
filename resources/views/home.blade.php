@extends('layouts.app')

@push('styles')
    <style>
        .required label:first-child::after {
            content: '*';
            color: red;
            font-weight: bold;
        }

        .dropify-message .file-icon p {
            font-size: 14px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #198754;
        }

        input:not(:checked)+.slider {
            background-color: #BB2D3B;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
    {{-- toastr css --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    {{-- datatable button css link --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    {{-- dropify css link --}}
    <link rel="stylesheet" href="{{ asset('dropify.min.css') }}">
    {{-- datatable responsive css link --}}
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    {{-- datatable jquery css link --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    {{-- fontawesome css link --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- sweetalert css link --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.4/dist/sweetalert2.min.css"
        integrity="sha256-h2Gkn+H33lnKlQTNntQyLXMWq7/9XI2rlPCsLsVcUBs=" crossorigin="anonymous">
@endpush
@section('content')
    <div id="messagesContainer" class="container"></div>
    <div class="row justify-content-center px-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-success fs-4 fw-bold"> User List </h5>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary float-end" onclick="showModal('Add New User','Save')">Add
                                New</button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <form id="form-filter" method="post">
                                <div class="row">
                                    <x-textbox labelName="Name" col="col-md-3" name="name" placeholder="Enter name" />
                                    <x-textbox type="email" labelName="Email" col="col-md-3" name="email"
                                        placeholder="Enter email" />
                                    <x-textbox type="number" labelName="Mobile No" col="col-md-3" name="mobile_no"
                                        placeholder="Enter Mobile Number" />
                                    <x-selectbox onchange="upazilaList(this.value,'form-filter')" labelName="District"
                                        col="col-md-3" name="district_id">
                                        @if (!$data['districts']->isEmpty())
                                            @foreach ($data['districts'] as $district)
                                                <option value="{{ $district->id }}">{{ $district->location_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </x-selectbox>
                                    <x-selectbox labelName="Upazila" col="col-md-3" name="upazila_id" />
                                    <x-selectbox labelName="Status" name="status" col="col-md-3">
                                        <option value="">Select Please</option>
                                        <option value="1">Active</option>
                                        <option value="2">Inactive</option>
                                    </x-selectbox>
                                    <x-selectbox labelName="Role" col="col-md-3" name="role_id">
                                        @if (!$data['roles']->isEmpty())
                                            @foreach ($data['roles'] as $role)
                                                <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                            @endforeach
                                        @endif
                                    </x-selectbox>
                                    <div class="form-group col-md-3" style="padding-top: 23px;">
                                        <button type="button" class="btn btn-success" id="btn-filter">Search</button>
                                        <button type="reset" class="btn btn-danger" id="btn-reset">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-4">
                            <table class="table table-bordered table-striped" id="dataTable">
                                <thead>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select_all"
                                                onchange="select_all()">
                                            <label class="form-check-label" for="select_all">
                                            </label>
                                        </div>
                                    </th>
                                    <th>SL</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>District</th>
                                    <th>Upazila</th>
                                    <th>Postal Code</th>
                                    <th>Verified Email</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <p class="fw-bold text-center fs-4 text-success">Developer Emon &copy;2024</p>
                </div>
            </div>
        </div>
    </div>
    @include('modal.modal-xl')
    @include('modal.modal-view')
@endsection
@push('scripts')
    {{-- jquery script link --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    {{-- toastr js script link --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    {{-- dropify js script link --}}
    <script src="{{ asset('dropify.min.js') }}"></script>
    {{-- main js script link --}}
    <script src="{{ asset('main.js') }}"></script>
    {{-- datatable jquery script link --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    {{-- fontawesome script link --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"
        integrity="sha512-GWzVrcGlo0TxTRvz9ttioyYJ+Wwk9Ck0G81D+eO63BaqHaJ3YZX9wuqjwgfcV/MrB2PhaVX9DkYVhbFpStnqpQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- datatable responsive script link --}}
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    {{-- sweetalert script link --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.4/dist/sweetalert2.all.min.js"
        integrity="sha256-SrfCZ78qS4YeGNB8awBuKLepMKtLR86uP4oomyg4pUc=" crossorigin="anonymous"></script>
    {{-- datatable buttons script links --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
    <script>
        let table;
        $('.dropify').dropify();
        // server side datatable show information with ajax request
        $(document).ready(function() {
            table = $('#dataTable').DataTable({
                "processing": true, //Features control the processing indicator
                "serverSide": true, //Features control datatable server side processing mode
                "order": [], //initial no order
                "responsive": true, // make table responsive in mobile devices
                "bInfo": true, // To show the total number of data
                "bFilter": false, // for default default search box show/hide 
                "lengthMenu": [
                    [5, 10, 25, 50, 100, 1000, 10000, -1],
                    [5, 10, 25, 50, 100, 1000, 10000, "All"],
                ],
                "pageLength": 5,
                "language": {
                    processing: `<img src="{{ asset('svg/table-loading.svg') }}" alt="Loading...."/>`,
                    emptyTable: '<strong class="text-danger">No Data Found</strong>',
                    infoEmpty: '',
                    zeroRecords: '<strong class="text-danger">No Data Found</strong>',
                },
                "ajax": {
                    "url": "{{ route('user.list') }}",
                    "type": "POST",
                    "data": function(data) {
                        data.name = $('#form-filter #name').val();
                        data.email = $('#form-filter #email').val();
                        data.mobile_no = $('#form-filter #mobile_no').val();
                        data.district_id = $('#form-filter #district_id').val();
                        data.upazila_id = $('#form-filter #upazila_id').val();
                        data.status = $('#form-filter #status').val();
                        data.role_id = $('#form-filter #role_id').val();
                        data._token = _token;
                    },
                },
                "columnDefs": [{
                    'targets': [0, 2, 11],
                    'orderable': false,
                    "className": "text-center",
                }, {
                    "targets": [3, 6, 7, 8, 9],
                    "className": "text-center",
                }],
                "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'B>>" +
                    "<'row'<'col-sm-12 col-md-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", // Add B to the dom option for buttons
                "buttons": [
                    'colvis',
                    {
                        "extend": 'print',
                        "title": "User List",
                        "orientation": "landscape",
                        "pageSize": "A4",
                        "exportOptions": {
                            columns: function(index, data, node) {
                                return table.column(index).visible();
                            }
                        },
                        customize: function(win) {
                            $(win.document.body).addClass('bg-white');
                        }
                    },
                    {
                        "extend": 'csv',
                        "title": "User List",
                        "filename": "user-list",
                        "exportOptions": {
                            columns: function(index, data, node) {
                                return table.column(index).visible();
                            }
                        }
                    },
                    {
                        "extend": 'excel',
                        "title": "User List",
                        "filename": "user-list",
                        "exportOptions": {
                            columns: function(index, data, node) {
                                return table.column(index).visible();
                            }
                        }
                    },
                    {
                        "extend": 'pdf',
                        "title": "User List",
                        "filename": "user-list",
                        "orientation": "landscape",
                        "pageSize": "A4",
                        "exportOptions": {
                            columns: [0, 2, 3, 4, 5, 6, 7, 8, 9]
                        },
                        customize: function(doc) {
                            doc.content[1].table.widths = ['5%', '15%', '10%', '15%', '10%', '10%',
                                    '15%', '10%', '10%'
                                ],
                                doc.styles.tableHeader.alignment = "left";

                            //Create a date string that we use in the footer. Format is dd-mm-yyyy
                            var now = new Date();
                            var jsDate = now.getDate() + '-' + (now.getMonth() + 1) + '-' + now
                                .getFullYear();
                            // Logo converted to base64
                            // var logo = getBase64FromImageUrl('https://datatables.net/media/images/logo.png');
                            // The above call should work, but not when called from codepen.io
                            // So we use a online converter and paste the string in.
                            // Done on http://codebeautify.org/image-to-base64-converter
                            // It's a LONG string scroll down to see the rest of the code !!!
                            var logo =
                                'data:image/jpeg;base64,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ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA=';
                            // A documentation reference can be found at
                            // https://github.com/bpampuch/pdfmake#getting-started
                            // Set page margins [left,top,right,bottom] or [horizontal,vertical]
                            // or one number for equal spread
                            // It's important to create enough space at the top for a header !!!
                            doc.pageMargins = [20, 20, 20, 20];
                            // Set the font size fot the entire document
                            doc.defaultStyle.fontSize = 10;
                            // Set the fontsize for the table header
                            doc.styles.tableHeader.fontSize = 10;
                            //Remove the title created by datatTables
                            doc.content.splice(0, 1, {
                                margin: [0, 0, 0, 5],
                                alignment: "center",
                                fontSize: 10,
                                image: logo,
                                width: 35,
                            }, {
                                alignment: "center",
                                text: ['User List'],
                                fontSize: 10,
                                margin: [0, 0, 0, 5],
                                bold: true
                            });
                            // Create a footer object with 2 columns
                            // Left side: report creation date
                            // Right side: current page and total pages
                            doc['footer'] = (function(page, pages) {
                                return {
                                    columns: [{
                                            alignment: 'left',
                                            text: ['Created on: ', {
                                                text: jsDate.toString()
                                            }]
                                        },
                                        {
                                            alignment: 'right',
                                            text: ['page ', {
                                                text: page.toString()
                                            }, ' of ', {
                                                text: pages.toString()
                                            }]
                                        }
                                    ],
                                    margin: [20, 5, 20, 5]
                                }
                            });
                            // Change dataTable layout (Table styling)
                            // To use predefined layouts uncomment the line below and comment the custom lines below
                            // doc.content[0].layout = 'lightHorizontalLines'; // noBorders , headerLineOnly
                            var objLayout = {};
                            objLayout['hLineWidth'] = function(i) {
                                return .5;
                            };
                            objLayout['vLineWidth'] = function(i) {
                                return .5;
                            };
                            objLayout['hLineColor'] = function(i) {
                                return '#aaa';
                            };
                            objLayout['vLineColor'] = function(i) {
                                return '#aaa';
                            };
                            objLayout['paddingLeft'] = function(i) {
                                return 4;
                            };
                            objLayout['paddingRight'] = function(i) {
                                return 4;
                            };
                            doc.content[0].layout = objLayout;
                        }
                    },
                ],
            });

            $('#dataTable_wrapper .dt-buttons').append(
                '<button type="button" class="btn btn-danger" id="bulk_action_delete"> <i class="fa-solid fa-trash"></i>Delete All</button>'
            )
        });

        // btn-filter for custom search
        $('#btn-filter').click(function() {
            table.ajax.reload();
        })
        // btn-reset for custom search
        $('#btn-reset').click(function() {
            $('#form-filter')[0].reset();
            table.ajax.reload();
        })
        // show modal function
        function showModal(title, btnText) {
            $('#storeForm')[0].reset();
            $('#storeForm').find('.is-invalid').removeClass('is-invalid');
            $('#storeForm').find('.error').remove();
            $('.dropify-clear').trigger('click');
            $('#password, #password_confirmation').parent().removeClass('d-none');
            $('#saveDataModal').modal({
                keyboard: false,
                backdrop: 'static'
            }).modal('show');
            $('#saveDataModal .modal-title').text(title);
            $('#saveDataModal #save-btn').text(btnText);
        };
        // Upazila dependency with ajax
        function upazilaList(district_id, form) {
            $.ajax({
                url: "{{ route('upazila.list') }}",
                type: "POST",
                data: {
                    district_id: district_id,
                    _token: _token,
                },
                dataType: "JSON",
                success: function(data) {
                    $('#' + form + ' #upazila_id').html('');
                    $('#' + form + ' #upazila_id').html(data);
                },
                error: function(xhr, ajaxOption, thrownError) {
                    console.log(thrownError + '\r\ n' + xhr.statusText + '\r\ n' +
                        xhr.responseText);
                }
            });
        };

        // global store funtion with image using ajax, jquery
        $(document).on('click', '#save-btn', function() {
            // event.preventDefault();
            let storeForm = document.getElementById('storeForm');
            let formData = new FormData(storeForm);
            let url = "{{ route('user.store') }}";
            let id = $('#update_id').val();
            let method;
            if (id) {
                method = 'update';
            } else {
                method = 'add';
            }
            store_form_data(table, method, url, formData);
        });

        function store_form_data(table, method, url, formData) {
            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                dataType: "JSON",
                contentType: false,
                processData: false,
                cache: false,
                success: function(data) {
                    $('#storeForm').find('.is-invalid').removeClass('is-invalid');
                    $('#storeForm').find('.error').remove();
                    if (data.status === false) {
                        $.each(data.error, function(key, value) {
                            $('#storeForm #' + key).addClass('is-invalid');
                            $('#storeForm #' + key).parent().append(
                                '<div class="error invalid-feedback d-block fw-bold">' +
                                value + '</div>');
                        });
                    } else {
                        toastrMessage(data.status, data.message);
                        if (data.status === 'Success') {
                            // const successMessage = data.message || 'Data has been saved successfully';
                            // showAlert('Success', successMessage);
                            if (method == 'update') {
                                table.ajax.reload(null, false);
                            } else {
                                table.ajax.reload();
                            }
                            $('#saveDataModal').modal('hide');
                        } else {
                            // const errorMessage = data.message || 'An error occurred';
                            // showAlert('Error', errorMessage);
                            toastrMessage(data.status, data.message);
                            $('#saveDataModal').modal('hide');

                        }
                    }
                },
                error: function(xhr, ajaxOption, thrownError) {
                    console.log('error');
                    console.log(thrownError + '\r\ n' + xhr.statusText + '\r\ n' +
                        xhr.responseText);
                }
            });
        };

        // show messages with toastr js
        function toastrMessage(status, message) {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }

            switch (status) {
                case "Success":
                    toastr.success(message, 'Success');
                    break;
                case "Error":
                    toastr.error(message, 'Error');
                    break;
                case "info":
                    toastr.info(message, 'Info');
                    break;
                case "warning":
                    toastr.warning(message, 'Warning');
                    break;
            }
        };

        // edit data catch with ajax 
        $(document).on('click', '.edit_data', function() {
            let id = $(this).data('id');
            if (id) {
                $.ajax({
                    url: "{{ route('user.edit') }}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: _token,
                    },
                    dataType: "JSON",
                    success: function(data) {
                        $("#storeForm #update_id").val(data.user.id);
                        $("#storeForm #name").val(data.user.name);
                        $("#storeForm #email ").val(data.user.email);
                        $("#storeForm #mobile_no").val(data.user.mobile_no);
                        $('#password, #password_confirmation').parent().addClass('d-none');
                        $("#storeForm #district_id").val(data.user.district_id);
                        upazilaList(data.user.district_id, 'storeForm');
                        setTimeout(() => {
                            $("#storeForm #upazila_id").val(data.user.upazila_id);
                        }, 1000);
                        if (data.user.avatar) {
                            let avatar = "{{ asset('storage/' . USER_AVATAR) }}/" + data.user.avatar;
                            $('#storeForm .dropify-preview').css('display', 'block');
                            $('#storeForm .dropify-render').html('<img src="' + avatar + '"/>');
                            $("#storeForm #old_avatar").val(data.user.avatar);
                        }
                        $("#storeForm #postal_code").val(data.user.postal_code);
                        $("#storeForm #address").val(data.user.address);
                        $("#storeForm #role_id").val(data.user.role_id);

                        $('#saveDataModal').modal({
                            keyboard: false,
                            backdrop: 'static'
                        }).modal('show');
                        $('#saveDataModal .modal-title').html(
                            '<i class="fa-solid fa-pen-to-square"><span>Edit' + data.user.name +
                            '</span></i>');
                        $('#saveDataModal #save-btn').text('update');
                    },
                    error: function(xhr, ajaxOption, thrownError) {
                        console.log(thrownError + '\r\ n' + xhr.statusText + '\r\ n' +
                            xhr.responseText);
                    }
                });
            }
        });

        // view user data ajax 
        $(document).on('click', '.view_data', function() {
            let id = $(this).data('id');
            if (id) {
                $.ajax({
                    url: "{{ route('user.show') }}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: _token
                    },
                    dataType: "JSON",
                    success: function(data) {
                        $("#view_data").html('');
                        $("#view_data").html(data.user_view);
                        $('#viewDataModal').modal({
                            keyboard: false,
                            backdrop: 'static'
                        }).modal('show');
                        $('#viewDataModal .modal-title').html(
                            '<i class="fa-solid fa-eye"></i> <span> ' +
                            data.name + ' Details</span>');
                    },
                    error: function(xhr, ajaxOption, thrownError) {
                        console.log(thrownError + '\r\ n' + xhr.statusText + '\r\ n' +
                            xhr.responseText);
                    }
                });
            }
        });

        // catch id for delete data
        $(document).on('click', '.delete_data', function() {
            let id = $(this).data('id');
            let url = "{{ route('user.delete') }}";
            let row = table.row($(this).parent('tr'));
            let name = $(this).data('name');
            delete_data(id, url, table, row, name);
        });

        // delete data with sweetalert2 and ajax
        function delete_data(id, url, table, row, name) {
            Swal.fire({
                title: "Are you sure to delete " + name + " data?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            id: id,
                            _token: _token
                        },
                        dataType: "JSON",
                    }).done(function(response) {
                        if (response.status == 'success') {
                            Swal.fire("Deleted", response.message, "success").then(function() {
                                table.row(row).remove().draw(false);
                            });
                        }
                    }).fail(function() {
                        swal.fire("Oops....", "Something went wrong!", "error");
                    });
                }
            });
        }

        // catch id for bulk action delete
        $(document).on('click', '#bulk_action_delete', function() {
            let id = [];
            let rows;
            $('.select_data:checked').each(function() {
                id.push($(this).val());
                rows = table.rows($('.select_data:checked').parents('tr'));
            });
            console.log(id);
            if (id.length == 0) {
                Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: 'Please checked at least one row of table!',
                });
            } else {
                let url = "{{ route('user.bulk.action.delete') }}";
                bulk_action_delete(id, table, url, rows);
            }
        });

        // bulk action delete function
        function bulk_action_delete(id, table, url, rows) {
            Swal.fire({
                title: "Are you sure to delete all checked data?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete all!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            id: id,
                            _token: _token
                        },
                        dataType: "JSON",
                    }).done(function(response) {
                        if (response.status == 'success') {
                            Swal.fire("Deleted", response.message, "success").then(function() {
                                table.rows(rows).remove().draw(false);
                                $('#select_all').prop('checked', false);
                            });
                        }
                    }).fail(function() {
                        swal.fire("Oops....", "Something went wrong!", "error");
                    });
                }
            });
        }

        // toggle button change status 
        $(document).on('change', '.change_status', function() {
            let id = $(this).data('id');
            let status;
            if ($(this).is(':checked')) {
                status = 1;
            } else {
                status = 2;
            }
            if (id && status) {
                $.ajax({
                    url: "{{ route('user.change.status') }}",
                    type: "POST",
                    data: {
                        id: id,
                        status: status,
                        _token: _token
                    },
                    dataType: "JSON",
                    success: function(data) {
                        toastrMessage(data.status, data.message);
                        if (data.status == 'Success') {
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr, ajaxOption, thrownError) {
                        console.log(thrownError + '\r\ n' + xhr.statusText + '\r\ n' +
                            xhr.responseText);
                    }
                });
            }
        });

        // show messages when data with function
        // function showAlert(type, message) {
        //     const alertClass = (type === 'Success') ? 'alert-success' : 'alert-danger';
        //     if (!alertClass) {
        //         alertClass = 'alert-danger';
        //     }
        //     const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
    //                   <strong>${type}:</strong> ${message}
    //                   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    //               </div>`;
        //     $('#messagesContainer').empty().append(alertHtml);
        //     setTimeout(function() {
        //         $('.alert').alert('close');
        //     }, 5000);
        // }
    </script>
@endpush
