<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Http\Controller\AccountController;
use Illuminate\Http\Request;
use App\Models\Review;
class ReviewController extends Controller
{
    public function index(Request $request) 
     {
        
        $reviews=Review::with('book','user')->orderBy('created_at','DESC');
         

        if(!empty($request->Keyword))
        {
            $reviews=$reviews->where('reviews','like','%'.$request->Keyword.'%');

        }
        $reviews=$reviews->paginate(10);
        // dd($reviews);
        return view('account.reviews.list',[
            'reviews'=>$reviews,
        ]);


    }
    public function edit($id)
    {
        $review=Review::findOrFail($id);
        return view('account.reviews.edit',[
            'reviews'=>$review,
        ]);
    } 
    public function updateReview($id,Request $request)
 {
     $review=Review::findOrFail($id);

     $validator=Validator::make($request->all(),[
         'reviews'=>'required',
         'status'=>'required'
     ]);
     if($validator->fails())
     {
         return redirect()->route('account.reviews.editReview',$id)->withInput()->withErrors($validator);
     }
     $review->reviews=$request->reviews;
     $review->status=$request->status;
     $review->save();

     session()->flash('success','review updated successfully');
     return redirect()->route('account.reviews');
 }


   
    public function deleteReview(Request $request)
    {
        $id =$request->id;
        $review=Review::find($id);
        if($review==null)
        {
            session()->flash('error','Review Not found');
            return response()->json([
                'status'=>false,
            ]);
        }else{
            $review->delete();
            session()->flash('success','Review deleted Successfully');
            return response()->json([
                'status'=>true,
            ]);
        }
    }
}
