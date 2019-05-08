<?php

namespace App\Http\Controllers;

use \Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Job;
use App\User;
use App\Project;

class PmController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $jobs = Job::all();
        
        return view('pm_home', compact('jobs'));
    }

    public function showProject(){
        $projects = Project::all();

        return view('pm_project', compact('projects'));
    }

    public function showProgrammer(){
        $programmers = User::all();

        return view('pm_manajemen', compact('programmers'));
    }

    public function showDetail($id_project){
        $project = Project::find($id_project);

        $jobs = Job::where('project_id',$id_project)->get();

        return view('pm_detailProject')
            ->with(compact('jobs'))
            ->with(compact('project'));
    }

    public function edit($id){
        $job = Job::where('id',$id)->first();

        $programmers = User::all();

        $projects = Project::all();

        return view('pm_formEditTodo')
            ->with(compact('job'))
            ->with(compact('programmers'))
            ->with(compact('projects'));
    }

    public function editProgrammer($id){
        $programmer = User::find($id);

        return view('pm_formEditProgrammer', compact('programmer'));
    }

    public function editProject($id){
        $project = Project::find($id)->first();

        return view('pm_formEditProject', compact('project'));
    }

    public function editDetail($id_job){
        $job = Job::find($id_job);

        return view('pm_formEditDetail', compact('job'));
    }

    public function update(Request $request, $id){
        $job = Job::find($id);
        $job->name = $request->todo;
        $job->project_id = $request->project;
        $job->user_id = $request->programmer;
        $job->save();

        return redirect('/pm');
    }

    public function updateProgrammer(Request $request, $id){
        $user = User::find($id);
        $user->name = $request->programmer;
        $user->role = $request->role;
        $user->save();

        return redirect('/manage');
    }

    public function updateProject(Request $request, $id){
        $project = Project::find($id);
        $project->name = $request->project;
        $project->save();

        return redirect('/project');
    }

    public function updateDetail(Request $request, $id_project){
        $job = Job::find($request->id);
        $job->name = $request->todo;
        $job->save();

        return redirect('/detail/'.$id_project);
    }

    public function delete($id){
        $job = Job::find($id);
        $job->delete();
        
        return redirect('/pm');
    }

    public function deleteProgrammer($id){
        $job = User::find($id);
        $job->job->user_id=0;
        $job->save();

        $user = User::find($id);
        $user->delete();

        return redirect('/manage');
    }

    public function deleteProject($id){
        $project = Project::find($id)->delete();
        $job = Job::where('project_id','=',$id)->delete();

        return redirect('/project');
    }

    public function deleteDetail($id){
        // DB::table('todo')->where('id',$id)->delete();
        $job = Job::find($id)->delete();
        return redirect('/');
    }

    public function tambah(){
        $programmers = User::all();
        $projects = Project::all();

        return view('pm_formTambahTodo', ['projects'=>$projects, 'programmers'=>$programmers]);
    }

    public function tambahProgrammer(){
        $statuss = DB::table('status')->get();

        return view('pm_formTambahProgrammer', ['statuss'=>$statuss]);
    }

    public function tambahProject(){
        return view('pm_formTambahProject');
    }

    public function tambahDetail($id_project){
        $project = Project::find($id_project);

        return view('pm_formTambahDetail', compact('project'));
    }

    public function add(Request $request){
        $job = new Job([
            'name' => $request->todo,
            'project_id' => $request->project,
            'user_id' => $request->programmer
        ]);
        $job->save();

        return redirect('/pm');
    }

    public function addProgrammer(Request $request){
        $programmer = new User([
            'name' => $request->programmer,
            'email' =>$request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        $programmer->save();

        return redirect('/manage');
    }

    public function addProject(Request $request){
        $project = new Project([
            'name' => $request->project
        ]);
        $project->save();

        return redirect('/project');
    }

    public function addDetail(Request $request, $id){
        $job = new Job([
            'name' => $request->todo,
            'project_id' => $id,
            'user_id' => 0
        ]);
        $job->save();

        return redirect('/detail/'.$id);
    }
}
