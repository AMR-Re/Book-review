<?php

namespace App\Http\Controllers;
use  App\Models\Book;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   $book=Book::orderBy('created_at','DESC');
        if(!empty($request->keyword))
        {
        $book->Where('keyword','like','%'.$request->keyword.'%');

        }
       $book=$book->Where('status',1)->paginate(3);
        return view('home',
        [
            'books'=>$book,
        ]);
    }


/**
     * Show book details
     */




    public fUnction detail($id)
    {
        $book=Book::findOrFail($id);
        if($book->status==0){
            abort(404);
        }
        $relatedBooks=Book::where('status',1)->take(3)->where('id','!=',$id)->inRandomOrder()->get();
        return view('book-details',[
            'book'=>$book,
            'relatedBooks'=>$relatedBooks,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
