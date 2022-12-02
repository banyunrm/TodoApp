<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function todo()
    {
        //ambil data dari table todos dengan model Todo 
        //all() fungsinya utuk mengambil smua data di table
        //filter data di database -> where('column', 'perbandingan', 'value')
        //get() -> ambil data
        //filter data di table todos yang isi column user_id na sama dengan data histrory login bagian id
        $todos = Todo::where('user_id', Auth::user()->id)->where('status', 0)->get();
        //kirim data yang sudah diambil ke file blade / ke file yang menampilkan halaman
        //kirim melalui comact()
        //isi compact sesuaikan dengan nama variable
        return view('todo', compact('todos'));
    }
    


     public function login()
    {
        //
        return view('login.index');
    }
    public function index()
    {
        //
        return view('login');
    }

    public function register()
    {
        return view('register');
    }

    // public function todo()
    // {
    //     return view('todo');
    // }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    public function registerAccount(Request $request
    )
    {
        $request->validate([
            'email' => 'required|email:dns',
            'username' => 'required|min:4|max:8',
            'password' => 'required|min:4',
            'name' => 'required|min:3',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/')->with('success','Berhasil menambahkan akun! silahkan login');
    }

    public function auth(Request $request){
        $request->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required',
        ],[
            'username.exists' => 'username ini belum tersedia',
            'username.required' => 'username harus diisi',
            'password.required' => 'password harus diisi',
        ]);

        $user = $request->only('username','password');
        if(Auth::attempt($user)) {
            return redirect(route('todo'));
        }else {
            return redirect()->back()->with('error','Gagal login, silahkan cek dan coba lagi!');
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('create');
    }

    public function edit($id)
    {   
        //menampilkan halaman input form edit
        //mengambil data satu baris ketika column id pada baris tersebut sama dengan id dari parameter route
        $todo = Todo::where('id',$id)->first();
        //kirim data yang diambil ke file blade dengan compact
        return view('edit', compact('todo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // dd($request->all());
       $request->validate([
        'title' => 'required|min:3',
        'date' => 'required',
        'description' => 'required|min:5',
       ]);
       //mangirim data ke databse table todos denga model todo
       //''=nama column di table db
       //$request-> = value attribute name ada input
       //kenpa yg dikirim 5 data? karena table pada db todos membutuhkan 6 column input
       //salah satunya column 'done_time' yang tipenya nullable, karna nullable jadi ga perlu dikirim nilai
       //'user_id' untuk membertahu data todo ini milik siapa, diamil melalui fitur auth
       //'status' tipenya boolean, 0=blm dikerjakan, 1=sudah dikerjakan (todonya)
       Todo::create([
        'title' => $request->title,
        'date' => $request->date,
        'description' => $request->description,
        'status' =>0,
        'user_id' => Auth::user()->id,
       ]);

         //redirect ke route todo.index
            //route todo.index ada di file route/web.php
        return redirect(route('todo'))->with('success','Berhasil menambahkan todo!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function show(Todo $todo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|min:3',
            'date' => 'required',
            'description' => 'required|min:5',
        ]);
        todo::where('id',$id)->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'user_id' => Auth::user()->id,
            'status' => 0,
        ]);
        return redirect('/todo')->with('successUpdate', 'Data todo berhasil diperbaharui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //menghapus daa di database
        //filter / cari data yang mau dihapus, baru jalankan perintah hapusnya
        // Todo::where('id',$id)->delete();
        Todo::find($id)->delete();
        //kalau uda, balik lagi ke halaman awalnya dengan pemberitahuan
        return redirect()->back()->with('delete', 'Berhasil menghapus data ToDo!');
    }


    public function complated()
    {
        $todos = Todo::where('user_id', Auth::user()->id)->where('status', 1)->get();
        //kirim data yang sudah diambil ke file blade / ke file yang menampilkan halaman
        //kirim melalui comact()
        //isi compact sesuaikan dengan nama variable
        return view('complated', compact('todos'));
    }
    
    public function updateComplated($id)
    {
        //cari data yg mau diubah statusnya jadi 'complated' dan column 'done_time' yang tadinya null, diisi denga tanggal sekarang (tanggal ketika data todo di ubah statusnya)
        //karena status boolean, dan 0 itu untu kondisi todo on-progress, jadi 1 nya untuk kondisi todo complated
        Todo::where('id', '=', $id)->update([
            'status'=> 1,
            'done_time' => \Carbon\Carbon::now(),
        ]);
        //apabila berhasil , akan dikembalikan ke halaman awal dengan pemberitahuan
        return redirect()->back()->with('done', 'Todo telah selesai dikerjakan!');
    }
   
    public function selesai($id)
    {
        $todo = Todo::findOrfail($id);
        $todo->status = 1;
        $todo->done_time = Carbon::now();
        $todo->save();
        return redirect('/todo')->with('success', 'Todo berhasil diselesaikan!');
    }
  

}  