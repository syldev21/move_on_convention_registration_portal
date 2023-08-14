<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HelperFunctions;
use App\Http\Controllers\Controller;
use App\Models\User;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Services\DataTable;
use function PHPUnit\Framework\isEmpty;

class DashboardController extends Controller
{
    private $shared_array;
    public function index(){
        try {
            $stagecounts = [
            'stage1_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage1.id')),
            'stage2_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage2.id')),
            'stage3_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage3.id')),
            'stage4_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage4.id')),
            'stage5_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage5.id')),
            'stage6_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage6.id')),
            'stage7_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage7.id')),
            'stage8_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage8.id')),
            'stage9_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage9.id')),
            'stage10_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage10.id')),
            'stage11_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage11.id')),
            'stage12_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage12.id')),
            'stage13_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage13.id')),
            'stage14_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage14.id')),
            'stage15_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage15.id')),
            'stage16_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage16.id')),
            'stage17_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage17.id'))
            ];

            $age_clusters = config('membership.age_clusters');
            $cell_groups = config('membership.cell_group');

// Array to store status_counts for each combination of cell group and age cluster
            $status_counts = [];

// Loop through each cell group
            foreach ($cell_groups as $cell_group) {
                $cell_group_id = $cell_group['id'];
                // Array to store status_counts for each age cluster for the current cell group
                $cell_group_counts = [];

                // Loop through each age cluster
                foreach ($age_clusters as $age_cluster) {
                    // Get status_counts for active, inactive, and deleted statuses separately
                    $active_count = HelperFunctions::getCountForCriteria($cell_group_id, $age_cluster, 1, 1, 5);
                    $inactive_count = HelperFunctions::getCountForCriteria($cell_group_id, $age_cluster, 0, 1, 5);
                    $deleted_count = HelperFunctions::getCountForCriteria($cell_group_id, $age_cluster, 0, 0, 5);

                    // Add the status_counts for the current age cluster to the $cell_group_counts array
                    $cell_group_counts[$age_cluster['id']] = [
                        'active' => $active_count,
                        'inactive' => $inactive_count,
                        'deleted' => $deleted_count,
                    ];
                }

                // Check if the 'name' index exists in the $cell_group array
                if (isset($cell_group['text'])) {
                    // Array to store status_counts for the current cell group
                    $cell_group_data = ['cell_group' => $cell_group['text']];

                    // Loop through each age cluster and add the status_counts to the $cell_group_data array
                    foreach ($age_clusters as $age_cluster) {
                        $stage_key = 'stage' . $age_cluster['id'];
                        $cell_group_data[$stage_key] = $cell_group_counts[$age_cluster['id']]['active'] ?? 0;
                    }

                    // Add the cell group status_counts to the final status_counts array
                    $status_counts['active'][] = $cell_group_data;

                    // Loop through each age cluster and add the status_counts to the $cell_group_data array for 'inactive' and 'deleted'
                    foreach ($age_clusters as $age_cluster) {
                        $stage_key = 'stage' . $age_cluster['id'];
                        $cell_group_data[$stage_key] = $cell_group_counts[$age_cluster['id']]['inactive'] ?? 0;
                    }

                    // Add the cell group status_counts to the final status_counts array for 'inactive'
                    $status_counts['inactive'][] = $cell_group_data;

                    // Loop through each age cluster and add the status_counts to the $cell_group_data array for 'deleted'
                    foreach ($age_clusters as $age_cluster) {
                        $stage_key = 'stage' . $age_cluster['id'];
                        $cell_group_data[$stage_key] = $cell_group_counts[$age_cluster['id']]['deleted'] ?? 0;
                    }

                    $status_counts['deleted'][] = $cell_group_data;
                }
            }

            return View("dashboard.main", ["stagecounts"=> $stagecounts, 'status_counts'=>$status_counts]);
        }catch (\Exception $exception){
            dd($exception);
        }
    }
    public function adminDashboard(){
        try {
            $stagecounts = [
                'stage1_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage1.id')),
                'stage2_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage2.id')),
                'stage3_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage3.id')),
                'stage4_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage4.id')),
                'stage5_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage5.id')),
                'stage6_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage6.id')),
                'stage7_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage7.id')),
                'stage8_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage8.id')),
                'stage9_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage9.id')),
                'stage10_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage10.id')),
                'stage11_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage11.id')),
                'stage12_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage12.id')),
                'stage13_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage13.id')),
                'stage14_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage14.id')),
                'stage15_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage15.id')),
                'stage16_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage16.id')),
                'stage17_counts' => HelperFunctions::getStageCount(config('membership.age_clusters.stage17.id'))
            ];

            $age_clusters = config('membership.age_clusters');
            $cell_groups = config('membership.cell_group');

// Array to store status_counts for each combination of cell group and age cluster
            $status_counts = [];

// Loop through each cell group
            foreach ($cell_groups as $cell_group) {
                $cell_group_id = $cell_group['id'];
                // Array to store status_counts for each age cluster for the current cell group
                $cell_group_counts = [];

                // Loop through each age cluster
                foreach ($age_clusters as $age_cluster) {
                    // Get status_counts for active, inactive, and deleted statuses separately
                    $active_count = HelperFunctions::getCountForCriteria($cell_group_id, $age_cluster, 1, 1, 5);
                    $inactive_count = HelperFunctions::getCountForCriteria($cell_group_id, $age_cluster, 0, 1, 5);
                    $deleted_count = HelperFunctions::getCountForCriteria($cell_group_id, $age_cluster, 0, 0, 5);

                    // Add the status_counts for the current age cluster to the $cell_group_counts array
                    $cell_group_counts[$age_cluster['id']] = [
                        'active' => $active_count,
                        'inactive' => $inactive_count,
                        'deleted' => $deleted_count,
                    ];
                }

                // Check if the 'name' index exists in the $cell_group array
                if (isset($cell_group['text'])) {
                    // Array to store status_counts for the current cell group
                    $cell_group_data = ['cell_group' => $cell_group['text']];

                    // Loop through each age cluster and add the status_counts to the $cell_group_data array
                    foreach ($age_clusters as $age_cluster) {
                        $stage_key = 'stage' . $age_cluster['id'];
                        $cell_group_data[$stage_key] = $cell_group_counts[$age_cluster['id']]['active'] ?? 0;
                    }

                    // Add the cell group status_counts to the final status_counts array
                    $status_counts['active'][] = $cell_group_data;

                    // Loop through each age cluster and add the status_counts to the $cell_group_data array for 'inactive' and 'deleted'
                    foreach ($age_clusters as $age_cluster) {
                        $stage_key = 'stage' . $age_cluster['id'];
                        $cell_group_data[$stage_key] = $cell_group_counts[$age_cluster['id']]['inactive'] ?? 0;
                    }

                    // Add the cell group status_counts to the final status_counts array for 'inactive'
                    $status_counts['inactive'][] = $cell_group_data;

                    // Loop through each age cluster and add the status_counts to the $cell_group_data array for 'deleted'
                    foreach ($age_clusters as $age_cluster) {
                        $stage_key = 'stage' . $age_cluster['id'];
                        $cell_group_data[$stage_key] = $cell_group_counts[$age_cluster['id']]['deleted'] ?? 0;
                    }

                    $status_counts['deleted'][] = $cell_group_data;
                }
            }

            return view('dashboard.index', ["stagecounts"=> $stagecounts, 'status_counts'=>$status_counts]);
        }catch (\Exception $exception){
            dd($exception);
        }
    }
    public function statusBasedDashboard(Request $request){
        $stagecounts = $request->stage_count;
        $counts = $request->status_count;
        $view = $request->view;
        return View($view, ["stagecounts"=> $stagecounts, 'status_counts'=>$counts]);
    }
    public function insertActiveDashboard(Request $request){
        $stagecounts = $request->stage_count;
        $counts = $request->status_count;
        $view = $request->view;
        return view($view, ["stagecounts"=> $stagecounts, 'status_counts'=>$counts]);
    }
    public function getPdf(){
        $html = view('dashboard.main')->render();

        // Generate the PDF using Snappy
        $pdf = SnappyPdf::loadHTML($html);

        // Download the PDF
        return $pdf->download('dashboard.pdf');
    }
    public function alignPhone(Request $request){
        if (preg_match('/^[0-9]{10}$/', $request->unique_id)) {
            // Check if the number starts with zero and remove the zero
            $uniqueId = $request->unique_id;
            if (substr($uniqueId, 0, 1) === '0') {
                $uniqueId = substr($uniqueId, 1);
            }

            return response()->json([
                'status' => 200,
                'unique_id' => $uniqueId
            ]);
        }else{
            return response()->json([
                'status' => 400,
            ]);
        }
    }
    public function conditionTitleArray(Request $request){
        $email = $request->email;
        return view('admin.conditional_title_array', ['email'=>$email]);
    }
    public function statusBasedMembers(Request $request){
        $gender_cell_group = $request->gender_cell;
        $member_category = $request->member_category;
        $member_age_cluster_category_text = $request->category_name;
        $category = $request->category;
        $active_status = $request->active;

        $progressive_registration = $request->progressive_registration??null;
        $class_name = $request->class_name;

        if (isset($class_name) && strpos($class_name, 'progressive-registration')){

            $display_for_progress = true;
            $members= User::where('existing', 1)->where('registration_status', config('membership.statuses.registration_statuses')[$progressive_registration]);
        }else{
            $display_for_progress = false;
            if (isset($category) && strpos($category, 'church-members')){
                if ($member_category == config('membership.age_clusters.All_members')['id']){
                    if ($active_status == 'active') {
                        $members = User::where('dob', '!=', null)->where('cell_group_id', '!=', null)->where('existing', 1)->where('active', 1)->where('registration_status', 5);
                    }elseif ($active_status == 'inactive'){
                        $members= User::where('dob', '!=', null)->where('cell_group_id', '!=', null)->where('existing', 1)->where('active', 0)->where('registration_status', 5);
                    }elseif($active_status == 'all'){
                        $members= User::where('id', '!=', null);
                    }else{
                        $members= User::where('dob', '!=', null)->where('cell_group_id', '!=', null)->where('existing', 0)->where('registration_status', 5);
                    }
                }elseif (strpos($category, 'gender-based')){
                    if ($active_status == 'active') {
                        if (isset($gender_cell_group)){
                            $members= User::where('dob', '!=', null)->where('cell_group_id', $gender_cell_group)->where('active', 1)->where('registration_status', 5)->where('gender', $member_category);
                        }else{
                            $members = User::where('dob', '!=', null)->where('cell_group_id', '!=', null)->where('existing', 1)->where('active', 1)->where('registration_status', 5)->where('gender', $member_category);
                        }
                    }elseif ($active_status == 'inactive'){
                        if (isset($gender_cell_group)){
                            $members= User::where('dob', '!=', null)->where('cell_group_id', $gender_cell_group)->where('existing', 1)->where('active', 0)->where('registration_status', 5)->where('gender', $member_category);
                        }else{
                            $members= User::where('dob', '!=', null)->where('cell_group_id', '!=', null)->where('existing', 1)->where('active', 0)->where('registration_status', 5)->where('gender', $member_category);
                        }

                    }elseif($active_status == 'all'){
                        if (isset($gender_cell_group)){
                            $members= User::where('id', '!=', null)->where('cell_group_id', $gender_cell_group);
                        }else{
                            $members= User::where('id', '!=', null)->where('cell_group_id', $member_category);
                        }
                    }else{
                        if (isset($gender_cell_group)){
                            $members= User::where('dob', '!=', null)->where('cell_group_id', $gender_cell_group)->where('existing', 0)->where('registration_status', 5)->where('gender', $member_category);
                        }else{
                            $members= User::where('dob', '!=', null)->where('cell_group_id', '!=', null)->where('existing', 0)->where('registration_status', 5)->where('gender', $member_category);
                        }
                    }
                }else{

                    $category_details = config('membership.statuses.age_clusters')[$member_category];

                    if ($active_status == 'active') {
                        $members_pending_age_cluster_filter = User::where('cell_group_id', '!=', null)
                            ->where('existing', 1)
                            ->where('active', 1)
                            ->where('registration_status', 5);
                    }elseif ($active_status == 'inactive'){
                        $members_pending_age_cluster_filter = User::where('cell_group_id', '!=', null)
                            ->where('existing', 1)
                            ->where('active', 0)
                            ->where('registration_status', 5);
                    }elseif ($active_status == 'all'){
                        $members_pending_age_cluster_filter = User::where('dob', '!=', null);
                    }else{
                        $members_pending_age_cluster_filter = User::where('cell_group_id', '!=', null)
                            ->where('existing', 0)
                            ->where('registration_status', 5);
                    }

                    if (isset($category_details['end'])) {
                        $age_cluster_array= [$category_details['start'], $category_details['end']];
                        $members = $members_pending_age_cluster_filter->where(function ($query) use ($age_cluster_array) {
                            $query->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, dob, CURDATE())'), $age_cluster_array);
                        });
                    } else {
                        $members = $members_pending_age_cluster_filter->where(function ($query) use ($category_details) {
                            $query->where(DB::raw('TIMESTAMPDIFF(YEAR, dob, CURDATE())'), '>=', $category_details['start']);
                        });
                    }
                }
            }else{
                if ($active_status == 'active') {
                    $members = User::where('dob', '!=', null)->where('cell_group_id', $member_category)->where('existing', 1)->where('active', 1)->where('registration_status', 5);
                }elseif ($active_status == 'inactive'){
                    $members = User::where('dob', '!=', null)->where('cell_group_id', $member_category)->where('existing', 1)->where('active', 0)->where('registration_status', 5);
                }elseif($active_status == 'all'){
                    $members = User::where('cell_group_id', $member_category);
                }else{
                    $members = User::where('dob', '!=', null)->where('cell_group_id', $member_category)->where('existing', 0)->where('registration_status', 5);
                }

            }
        }

        $loggedin_user = Auth::user();
        if ($loggedin_user->roles()->first()->name == config('membership.roles.prepaper.text')) {

            $filteredMembers = collect([]);

            foreach ($members->get() as $member) {
                if ($member->cell_group_id == $loggedin_user->cell_group_id) {
                    $filteredMembers->push($member);
                }
            }

            $members = $filteredMembers;
        } else {
            $members = $members->get();
        }

        $category_detail_description = '';
        if ($request->category_name == 'Privileged Users'){
            $priv = true;
        }else{
            $priv = false;
        }

        return view('admin.status_based_members',
            [
                'auth_user_role'=>$loggedin_user->roles()->first()->name,
                'category_detail_description'=>$category_detail_description,
                'members' => $members,
                'category_name'=>$member_age_cluster_category_text,
                'display_for_progress'=>$display_for_progress,
                'progressive_registration'=>$progressive_registration,
                'priv'=>$priv
            ]);
    }

    public function churchMembers(Request $request){
        try {
            $gender_cell_group = $request->gender_cell;
            $member_age_cluster_category_id = $request->member_category;
            $member_age_cluster_category_text = $request->category_name;
            $category = $request->category;

            $progressive_registration = $request->progressive_registration ?? null;
            $class_name = $request->class_name;

//            $loggedin_user = User::where('id', auth()->id())->with('customRoles')->first();

            if (isset($class_name) && strpos($class_name, 'progressive-registration')) {
                $display_for_progress = true;
                $members = User::where('dob', '!=', null)
                    ->where('cell_group_id', '!=', null)
                    ->where('existing', 1)
                    ->where('registration_status', config('membership.statuses.registration_statuses')[$progressive_registration]);
            } else {
                $display_for_progress = false;
                if (isset($category) && strpos($category, 'church-members')) {
                    if ($member_age_cluster_category_id == config('membership.age_clusters.All_members')['id']) {
                        $members = User::where('dob', '!=', null)
                            ->where('cell_group_id', '!=', null)
                            ->where('active', 1)
                            ->where('registration_status', 5);
                        $category_detail_description = '(All Stages)';
                    } elseif (strpos($category, 'gender-based')) {
                        if (isset($gender_cell_group)) {
                            $members = User::where('dob', '!=', null)
                                ->where('cell_group_id', $gender_cell_group)
                                ->where('active', 1)
                                ->where('registration_status', 5)
                                ->where('gender', $member_age_cluster_category_id)
                                ->where('dob', '!=', null)
                            ;
                        } else {
                            $members = User::where('dob', '!=', null)
                                ->where('cell_group_id', '!=', null)
                                ->where('active', 1)
                                ->where('registration_status', 5)
                                ->where('gender', $member_age_cluster_category_id)
                                ->where('dob', '!=', null)
                            ;
                        }
                    } else {
                        $category_details = config('membership.statuses.age_clusters')[$member_age_cluster_category_id];
                        $members_pending_age_cluster_filter = User::where('cell_group_id', '!=', null)
                            ->where('active', 1)
                            ->where('registration_status', 5);
                        if (isset($category_details['end'])) {
                            $age_cluster_array= [$category_details['start'], $category_details['end']];
                            $members = $members_pending_age_cluster_filter->where(function ($query) use ($age_cluster_array) {
                                $query->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, dob, CURDATE())'), $age_cluster_array);
                            });
                            $category_detail_description = 'Age between ' . $category_details['start'] . ' - ' . $category_details['end'];
                        } else {
                            $members = $members_pending_age_cluster_filter->where(function ($query) use ($category_details) {
                                $query->where(DB::raw('TIMESTAMPDIFF(YEAR, dob, CURDATE())'), '>=', $category_details['start']);
                            });
                            $category_detail_description = 'Age ' . $category_details['start'].' and above';
                        }
                        $category_detail_description = '(' . $category_detail_description . ')';
                    }
                } else {
                    $members = User::where('dob', '!=', null)
                        ->where('cell_group_id', $member_age_cluster_category_id)
                        ->where('active', 1)
                        ->where('registration_status', 5)
                    ;
                }
            }

            if ($request->priviledged_id) {
                $members = User::has('roles')->get();
                $priv = true;
            } else if (Auth::user()->roles()->first()->name == config('membership.roles.preparer.text')) {


                $loggedin_user = Auth::user();
                $filteredMembers = collect([]);
                $unfiltered_members = $members->get();
//                dump($filteredMembers);
                foreach ($unfiltered_members as $member) {
                    if ($member->cell_group_id == $loggedin_user->cell_group_id) {
                        $filteredMembers->push($member);
//                        dump($filteredMembers);
                    }
                }
//                dd($filteredMembers);
                $members = $filteredMembers;
                $priv = false;
            } else {
                $members = $members->get();
                $priv = false;
            }

            return view('admin.church-members', [
                'auth_user_role'=>\auth()->user()->roles()->first()->name,
                'category_detail_description' => $category_detail_description ?? null,
                'priv' => $priv,
                'members' => $members,
                'category_name' => $member_age_cluster_category_text,
                'member_age_cluster_category_id' => $member_age_cluster_category_id,
                'category' => $category,
                'display_for_progress' => $display_for_progress,
                'progressive_registration' => $progressive_registration
            ]);
        }catch (\Exception $e){
            dd($e);
        }
    }
    public function editMember($id){
        $member = User::where('id', $id)->first();
        return view('profile', ['userInfo'=>$member]);
    }
    public function adminRegisterMember(Request $request){
        $member = User::orderBy("id","DESC")->first();
        if(isset($member->member_number))
        {
            $memberNoArray = explode('/',$member->member_number);

            $lastArrayElement = end($memberNoArray);
            $lastArrayElement++;
            array_pop($memberNoArray);
            array_push($memberNoArray,$lastArrayElement);
            $member_number = implode('/',$memberNoArray);
        }else
        {
            $member_number = 'VOSHC/BB/1';
        }
        $memberNoArray = explode('/',$member_number);
        $lastArrayElement = end($memberNoArray);
        $updatedLastArrayElement = str_pad($lastArrayElement, 5, '0', STR_PAD_LEFT);
        array_pop($memberNoArray);
        array_push($memberNoArray,$updatedLastArrayElement);
        $member_number = implode('/',$memberNoArray);
        if (isset($request->dob)){
            $age = Carbon::parse($request->dob)->age;
            if ($age<18){
                $value = 'N/A';
            }
        }
        if (isset($request->firstName) || isset($request->otherNames)){
            $fullName = implode(' ', [$request->firstName, $request->otherNames]);
        }

        if (isset($request->id)){
            $udpate_data_array = [
                'name' => $fullName??'',
                'email' => $request->email,
                'gender' => $request->gender,
                'dob' => $request->dob,
                'phone' => $value ?? $request->phone,
                'marital_status_id' => $value ?? $request->marital_status,
                'estate_id' => $request->estate,
                'ward' => $request->ward,
                'cell_group_id' => $request->cell_group,
                'employment_status_id' => $value ?? $request->employment_status,
                'born_again_id' => $request->born_again,
                'leadership_status_id' => $request->leadership_status,
                'ministry_id' => $request->ministry,
                'occupation_id' => $value ?? $request->occupation,
                'education_level_id' => $request->education_level,
                'ministries_of_interest' => isset($request->check_box)?implode (',', $request->check_box):null,
                'title' => $request->title ?? null,
                'year_joined' => $request->year_joined ?? null,
            ];


            foreach ($udpate_data_array as  $key=>$value){
                if (is_null($value)){
                    unset($udpate_data_array[$key]);
                }
            }

            $user = User::where('id', $request->id);
            $user->update(
                $udpate_data_array
            );

            return response()->json([
                'status'=>200,
                'messages'=>'Profile updated successfully!',
            ]);

        }else{


            $validator_array = [
                'firstName'=>'required|regex:/^[a-zA-Z]+$/',
                'otherNames'=>'required',
                'unique_id' => [
                    'required',
                    'max:100',
                    function ($attribute, $value, $fail) {
                        // Check if the value is a valid email address or phone number
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL) && !preg_match('/^[0-9]{10}$/', $value)) {
                            $attribute = 'unique identifier';
                            $fail('The '.$attribute.' must be a valid email address or phone number.');
                        }
                    },
//                'unique:users',
                ],
                'title'=>'required',
                'dob' => 'required|date|before_or_equal:today',
                'cell_group'=>'required',
                'phone' => ['required', 'digits:9', 'numeric', 'unique:users'],
                'year_joined' => 'required|date|before_or_equal:today',
                'unique_id.required' => 'The unique ID field is required.',
                'unique_id.max' => 'The unique ID must not exceed :max characters.',
            ];
            if (isset($age) && $age<18) {
                unset($validator_array['phone']);
            }

            $validator = Validator::make($request->all(),
                $validator_array,
                [
                'title.required'=>'Choose a title  from the drop down menu!',
                'dob.required'=>'Pick the date of birth from the calender!',
                'year_joined.required'=>"Pick the year you joined VOSH Church Int'l from the calender!",
                'year_joined.before_or_equal'=>'The date of you joined cannot be later than today!',
                'dob.before_or_equal'=>'The date of birth cannot be later than today!',
                'cell_group.required'=>'Choose the member cell group from the drop down menu!',
            ]
            );

            if ($validator->fails()){
                return response()->json([
                    'status'=>400,
                    'messages'=>$validator->getMessageBag()
                ]);
            }
            else{
                if (filter_var($request->unique_id, FILTER_VALIDATE_EMAIL)){
                    $attribute_text = 'email';
                }elseif (preg_match('/^[0-9]{10}$/', $request->unique_id)){
                    $attribute_text = 'phone';
                }
                // Get the last part of the member number after the last forward slash
                $parts = explode('/', $member_number);
                $last_part = end($parts);

// Determine the length of the last part of the member number
                $length = strlen($last_part);

// Determine the number of leading zeros in the last part of the member number
                $num_zeros = strspn($last_part, "0");

// Extract the appropriate number of digits from the last part of the member number
                if ($num_zeros >= 2) {
                    $digits = substr($last_part, -3);
//              $last_three = substr($string, -3)
                } elseif ($num_zeros == 1) {
                    $digits = substr($last_part, -4);
                } else {
                    $digits = $last_part;
                }

// Pad the digits with leading zeros if necessary
//          $digits = str_pad($digits, 4, "0", STR_PAD_LEFT);

// Concatenate the first name and digits to create the username
                $username = $request->firstName . $digits;
                $fullName = implode(' ', [$request->firstName, $request->otherNames]);
                $user_name = $username;
                $user = new User();
                $user->name = $fullName;
                $attribute_text == 'email'?$user->email=$request->unique_id:$user->email=null;
                $user->gender=$request->gender??null;
                $user->dob=$request->dob??null;
                $user->phone=$value ?? $request->phone??null;
                $user->marital_status_id=$value ?? ($request->marital_status??null);
                $user->estate_id=$request->estate ?? null;
                $user->ward=$request->ward ?? null;
                $user->cell_group_id=$request->cell_group ?? null;
                $user->education_level_id=$request->education_level ?? null;
                $user->born_again_id=$request->born_again ?? null;
                $user->leadership_status_id=$request->leadership_status ?? null;
                $user->ministry_id=$request->ministry ?? null;
                $user->ministries_of_interest=isset($request->check_box)?implode (',', $request->check_box):null;
                $user->occupation_id=$value ?? ($request->occupation ?? null);
                $user->employment_status_id=$value ?? ($request->employment_status ?? null);
                $user->member_number = $member_number??null;
                $user->user_name = $username;
                $user->title = $request->title??null;
                $user->year_joined = $request->year_joined??null;
                $user->save();
                return  response()->json([
                    'status'=>200,
                    'messages'=>'Member Registered Successfully;&nbsp; with username; '.$user_name,
                ]);
            }
        }

    }
    public function adminAssignRole(Request $request){
      // Get the user ID and role ID from the request
        $userId = $request->input('id');
        $roleId = $request->input('role_id');

        // Find the user
        $user = User::find($userId);

        // Get the currently assigned role
        $currentRole = $user->roles->first();


        // Get the requested role
        $requestedRole = Role::find($roleId);

        // Find the user
        $user = User::find($userId);

        if ($roleId) {
            // A role is selected
            // Find the role based on the ID
            $role = Role::find($roleId);

            // Assign the role to the user
            $user->syncRoles([$role]);

            // Retrieve the associated permissions of the role
            $permissions = $role->permissions->pluck('name')->toArray();

            // Assign the permissions to the user
            $user->syncPermissions($permissions);
            User::where('id', $userId)->update(['registration_status'=>config('membership.registration_statuses.church_approved')['id']]);

            if (isset($currentRole->name)){
                if ($currentRole->name == $requestedRole->name){
                    return response()->json([
                        'status'=>200,
                        'messages'=>$currentRole->name.' role and associated permissions confirmed successfully'
                    ]);
                }else{
                    return response()->json([
                        'status'=>200,
                        'messages'=>'Role changed from '.$currentRole->name.' to '.$requestedRole->name.' and associated permissions assigned successfully'
                    ]);
                }
            }else{
                return response()->json([
                    'status'=>200,
                    'messages'=>$requestedRole->name.' and associated permissions assigned successfully'
                ]);
            }
        } else {
            // No role is selected, unassign the current role and permissions
            $user->syncRoles([]);
            $user->syncPermissions([]);
            if (isset($currentRole->name)){
                return response()->json([
                    'status'=>200,
                    'messages'=>$currentRole->name.' role and associated permissions unassigned successfully'
                ]);
            }else{
                return response()->json([
                    'status'=>200,
                    'messages'=>'Member has no role and this has been  confirmed successfully'
                ]);
            }

        }
    }
    public function adminAssignId(Request $request){
        return view('admin.with-user-id', ['id'=>$request->id]);
    }
    public function reviewMembership(Request $request){$member_id = $request->id;
        $review_reason = $request->decline_data;
        $registration_status = $request->registration_status;
        $user = User::where('id', $member_id);
        $declining_membership = $request->decline_action;

        if ($declining_membership){
            $validator = Validator::make($request->all(),[
                'decline_data'=>'required',
            ],[
                'decline_data.required'=>'Kindly select a reason for declining '.$request->member_first_name." 's membership",
            ]);

            if ($validator->fails()){
                return response()->json([
                    'status'=>400,
                    'messages'=>$validator->getMessageBag()
                ]);
            }else{
                $user->update(['previous_registration_status'=>config('membership.registration_statuses.church_approved.id'), 'registration_status'=>config('membership.registration_statuses.declined_members.id'), 'decline_reason'=>$review_reason]);

                if ($user->first()->hasAnyRole()){
                    $user->first()->roles()->detach(); // Unassign all roles from the user
                    $user->first()->permissions()->sync([]); // Remove all associated permissions
                }
                return response()->json([
                    'status'=>200,
                    'messages'=>$request->member_first_name."'s membership status declined successfully!"
                ]);
            }
        }else{
            $name =  User::where('id', $request->id)->first()->name;
            $first_name = $request->member_first_name ?? explode(' ', $name)[0];
            $response = response()->json([
                'status'=>200,
                'messages'=>$first_name."'s membership status approved successfully!"
            ]);
            if ($registration_status == 0){
                $user->update(['registration_status'=>$user->first()->previous_registration_status, 'previous_registration_status'=>null]);
                return response()->json([
                    'status'=>200,
                    'messages'=>$request->member_first_name."'s membership status status reinstated successfully!"
                ]);
            }else if ($registration_status == 1){
                $user->update(['registration_status'=>config('membership.registration_statuses.church_registered.id')]);
                return $response;
            }
//            else if ($registration_status == 2){
//                $user->update(['registration_status'=>config('membership.registration_statuses.church_registered.id')]);
//                return $response;
//            }
            else if ($registration_status == 3){
                $user->update(['registration_status'=>config('membership.registration_statuses.church_provisionally_approved.id')]);

                return $response;
            }else if ($registration_status == 4){
                $user->update(['registration_status'=>config('membership.registration_statuses.church_approved.id')]);

                return $response;
            }
        }
    }
}
//mysqldump -h 10.157.2.52  -p 3320 -u JubiApis -p digital_apps > local_digital_app.sql
