@extends('layout')
@section('content')
    <div class="wrapper w-50 mx-auto h-80 mt-5 px-4 mb-5" style="border-radius: 20px; padding: 20px;">
        @if (Session::get('notAllowed'))
            <div>
                {{ Session::get('notAllowed') }}
            </div>
        @endif
        @if (Session::get('successAdd'))
            <div>
                {{ Session::get('successAdd') }}
            </div>
        @endif
        @if (Session::get('successUpdate'))
            <div class="alert alert-info">
                <div>
                    {{ Session::get('successUpdate') }}
                </div>
            </div>
        @endif
        @if (Session::get('done'))
        <div class="alert alert-success">
            {{ Session::get('done') }}
        </div>
    @endif
        <div class="d-flex align-items-start justify-content-between">
            <div class="d-flex flex-column">
                <div class="h5 text-light">My Todo's</div>
                <p class="text-light text-justify">
                    Here's a list of activities you have to do
                </p>
                <span>
                    <a href="{{ route('create') }}" class="text-light text-decoration-none">Create</a> <a
                        href="{{ route('complated') }}" class="text-light text-decoration-none">Complated</a>
                </span>
            </div>
        </div>
        <div class="work pt-3">
            <div class="d-flex align-items-center py-2 mt-1 justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="text-light fas fa-comment btn"></span>
                    <div class="text-light">{{ count($todos) }} todos</div>
                </div>
                <div class="">
                    <button class=" btn btn-outline-light px-3" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <i class="fa-sharp fa-solid fa-caret-down"></i>
                    </button>
                </div>
            </div>
            <div id="comments" class="mt-1">
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                    data-bs-parent="#accordionExample">
                    @foreach ($todos as $todo)
                        <div class="comment p-2 d-flex align-items-center justify-content-start">
                           @if ($todo['status'] == 1)
                           {{-- cek kalau statusnya 1 (complated), maka yang ditampilin icon biasa yang gabisa di klik --}}
                           <span class="fa-solid fa-bookmark text-secondary btn"></span>
                           @else
                           <form action="/complated/{{$todo['id']}}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="fas fa-circle-check text-primary btn"></button>
                        </form>
                        @endif
                            {{-- <div class="mr-2">
                                <label class="option">
                                    <input type="checkbox">
                                    <span class="checkmark"></span>
                                </label>
                            </div> --}}
                            <div class="d-flex flex-column p-1">
                                {{-- menampilkan data dinamis/data yg diambil dari db pada blade harus menggunakan{{}} --}}
                                {{-- path yang {id} dikirim data dinamis (data dr db) makanya disitu pake {{}} --}}
                                <a href="/edit/{{ $todo['id'] }}" class="text-decoration-none">
                                    <p class="text-light fw-bold text-decoration-none" style="font-size: 23px">
                                        {{ $todo['title'] }}
                                    </p>
                                    <p class="text-light mb-0">{{ $todo['description'] }}</p>
                                    {{-- konsep ternary : if column status baris ini isinya 1 bakal mclin teks 'Complated' selain dari tiu akan menampilkan teks 'On-Process' --}}
                                    <p class="text-light mb-0">
                                        {{ $todo->status == 1 ? 'Complated' : 'On-Process' }} <span
                                            class="date">
                                            {{-- kalau statusnya 1 (complated), yang ditampilin itu tgl kapan dia selesainya yg diambil dari column done_time yg diisi pas update status nya ke complated--}}
                                            {{--alipganteng--}}
                                            @if ($todo['status'] == 1)
                                            selesai pada : {{ \Carbon\Carbon::parse($todo['done_time'])->format('j F, Y')}}
                                            {{--kalau statusnya masi 0 (on-progress), yg ditampilin tgl dia dibuat (data dr column date yg diisi dr input pilih tanggal di fitur create)--}}
                                            @else
                                            target selesai : {{\Carbon\Carbon::parse($todo['date'])->format('j F, Y')}}
                                            @endif
                                        </span>
                                    </p>
                                </a>

                                {{-- carboin itu package laravel untuk mengelola yg berhubngan dengan date. tedinya value column date di db kan 
                        bentuknya format 2022-11-22 nah kita pengaen ubh bentuk formatnya jadi 22 november, 2022 --}}
                            </div>
                           
                            <div class="p-3 mb-2 ms-auto delete">
            
                                <form action="{{ route('delete', $todo['id']) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    {{-- <button type="submit"<i class="fas fa-trash d-inline" style="font-size:20px; color:rgb(246, 112, 64);"></i></button> --}}
                                    <button type="submit" class="fas fa-trash text-denger btn" style="color: #f94d03"></button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endsection
