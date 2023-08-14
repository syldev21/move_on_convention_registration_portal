<div class="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 p-r-0 title-margin-right">
                <div class="page-header">
                    <div class="page-title">
                                                <h1>Hello, <span class="text-uppercase text-success">{{explode(' ', \Illuminate\Support\Facades\Auth::user()->name)[0]}}.</span> <span> Welcome to Admin Membership Dashboard</span></h1>
                    </div>
                </div>
                <h3 class="mb-2">Summary according to Membership Status</h3>
            </div>
            <!-- /# column -->
            <div class="col-lg-4 p-l-0 title-margin-left">
                <div class="page-header">
                    <div class="page-title">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Home</li>
                        </ol>
                    </div>
                </div>
            </div>
            <!-- /# column -->
        </div>
        <!-- /# row -->
        <section id="main-content">
            <div class="row">
                <div class="col-lg-2">
                    <div class="card">
                        <div class="stat-widget-one">
                            <div class="stat-icon dib"><i class="fas fa-users color-primary border-primary"></i></div>
                            <div class="stat-content dib">
                                <div class="fw-bolder stat-text">All the Members</div>
                                <div
                                    class="stat-digit text-primary">{{ count(\App\Models\User::where('registration_status', 5)->get()) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="card">
                        <div class="stat-widget-one">
                            <div class="stat-icon dib"><i class="fas fa-user-times color-pink border-pink"></i></div>
                            <div class="stat-content dib">
                                <div class="fw-bolder stat-text">Inactive Members</div>
                                <div
                                    class="stat-digit color-pink">{{ count(\App\Models\User::where('registration_status', 5)->where(['active'=> 0, 'existing'=> 1])->get()) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="card">
                        <div class="stat-widget-one">
                            <div class="stat-icon dib"><i class="fas fa-user-slash color-danger border-danger"></i>
                            </div>
                            <div class="stat-content dib">
                                <div class="fw-bolder stat-text">Deleted Members</div>
                                <div
                                    class="stat-digit text-danger ">{{ count(\App\Models\User::where('registration_status', 5)->where(['existing'=> 0])->get()) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="card">
                        <div class="stat-widget-one">
                            <div class="stat-icon dib"><i class="fas fa-clock color-yellow border-yellow"></i></div>
                            <div class="stat-content dib">
                                <div class="fw-bolder stat-text">Pending Membership</div>
                                <div
                                    class="stat-digit">{{ count(\App\Models\User::where('registration_status', '!=', 5)->where(['active'=> 1])->get()) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="card">
                        <div class="stat-widget-one">
                            <div class="stat-icon dib"><i class="fas fa-thumbs-down text-danger  color-red border-red"></i></div>
                            <div class="stat-content dib">
                                <div class="fw-bolder stat-text">Declined Members</div>
                                <div
                                    class=" text-danger stat-digit">{{ count(\App\Models\User::where('registration_status', 0)->get()) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="card">
                        <div class="stat-widget-one">
                            <div class="stat-icon dib"><i class="fas fa-user-check color-success border-success"></i>
                            </div>
                            <div class="stat-content dib">
                                <div class="fw-bolder stat-text">Active Members</div>
                                <div
                                    class="stat-digit text-success">{{ count(\App\Models\User::where('registration_status', 5)->where(['active'=> 1])->get()) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="float: right">
                <form id="pdf-form" action="{{ route('generate-pdf') }}" method="POST">
                    @csrf
                </form>
                <div class="" style="float: right">
                    <button id="export-pdf" class="btn btn-primary">Export to PDF</button>
                </div>
                <div class="" style="float: right">
                    {{--            <label  class="fw-bold" for="estate">Member Status Category</label>--}}

                    <select name="member_status_category" id=""
                            class="form-select rounded  member_status_category bg-success">
                        <option value="active">Active Members</option>
                        <option value="inactive">Inactive Members</option>
                        <option value="deleted">Deleted Members</option>

                    </select>
                </div>
            </div>
    </div>
    <div class="container-fluid px-4 data" id="status-based-dashboard">
        <span class="active-dashboard">@include('dashboard.admin.active')</span>
        <span class="inactive-dashboard hidden">@include('dashboard.admin.inactive')</span>
        <span class="deleted-dashboard hidden">@include('dashboard.admin.deleted')</span>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>

    <script>
        $(document).ready(function (e) {
            $('.member_status_category').change(function (e) {
                e.preventDefault()
                let active_dashboard = $('.active-dashboard')
                let inactive_dashboard = $('.inactive-dashboard')
                let deleted_dashboard = $('.deleted-dashboard')
                if($(this).val() == 'active'){
                    active_dashboard.show()
                    inactive_dashboard.hide()
                    deleted_dashboard.hide()
                    $('.member_status_category').addClass('bg-success')
                    $('.member_status_category').removeClass('bg-warning')
                    $('.member_status_category').removeClass('bg-danger')
                }else if($(this).val() == 'inactive'){
                    active_dashboard.hide()
                    inactive_dashboard.show()
                    deleted_dashboard.hide()
                    $('.member_status_category').addClass('bg-warning')
                }else{
                    active_dashboard.hide()
                    inactive_dashboard.hide()
                    deleted_dashboard.show()
                    $('.member_status_category').addClass('bg-danger')
                }
            })
        })
    </script>


    @include('layouts.includes.admin.footer')
    </section>
</div>
</div>
