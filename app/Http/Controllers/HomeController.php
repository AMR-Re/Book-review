<?php

namespace App\Http\Controllers;
use  App\Models\Book;
use  App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


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
        $book=Book::with(['reviews.user','reviews'=>function($query){
            $query->where('status',1);
        }])->findOrFail($id);

        if($book->status==0){
            abort(404);
        }
        $relatedBooks=Book::where('status',1)->take(3)->where('id','!=',$id)->inRandomOrder()->get();
        return view('book-details',[
            'book'=>$book,
            'relatedBooks'=>$relatedBooks,
        ]);
    }
    // save user 's review 
    public function saveReview(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'review'=>'required|min:5',
            'rating'=>'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
        $countReview=Review::where('user_id',Auth::user()->id)->where('book_id',$request->book_id)->count();
        if($countReview > 0)
        {
        session()->flash('error','You already submitted a review');
            return response()->json([
                'status'=>true,
            ]);
        }
        $review=new Review();
        $review->reviews=$request->review;
        $review->rating=$request->rating;
        $review->user_id=Auth::user()->id;
        $review->book_id=$request->book_id;
        $review->save();
        session()->flash('success','Review submitted');
        return response()->json([
            'status'=>true,
           
        ]);



    } 
    
}
