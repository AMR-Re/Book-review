<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Book;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BookController extends Controller
{
    //showing books
    public function index(Request $request)

    { 
        $books=Book::orderBy('created_at','DESC');
         if(!empty($request->Keyword))
            {
                $books->where('title','like','%'.$request->Keyword.'%');
            }
        $books=$books->paginate(5);
         return view('books.list',['books'=>$books]);
    }
     //creating books
    public function create()
    {
 return view('books.create');
    }
     //storing books

    public function store(Request $request)
    {
        $rules= [
            'title'=>'required|min:5',
            'author'=>'required|min:3',
            'status'=>'required',
        ];
        if(!empty($request->image))
        {
            $rules['image']='image';

        }
        $validator=Validator::make($request->all(),$rules);

         if($validator->fails())
         {
            return redirect()->route('books.create')->withInput()->withErrors($validator);
         }
//save book in DB 
    $book=new Book();
    $book->title=$request->title;
    $book->author=$request->author;
    $book->description=$request->description;
    $book->status=$request->status;
    $book->save();


    if(!empty($request->image))
    {
        File::delete(public_path('uploads/book/').$book->image);
        File::delete(public_path('uploads/book/thumb/').$book->image);
        $image=$request->image;
        $ext=$image->getClientOriginalExtension();
        $imageName=time().'.'.$ext;
        $image->move(public_path('uploads/book'),$imageName);

        $book->image=$imageName;
        $book->save();

        $manager = new ImageManager(Driver::class);
        $image = $manager->read(public_path('uploads/book/').$imageName); 
        $image->resize(990);
        $image->save(public_path('uploads/book/thumb/').$imageName);
    }

    return redirect()->route('books.index')->with('success','Book Created Successfully');


    }

    public function edit($id)
    {
        $book=Book::findOrFail($id);
        return view('books.edit',[
            'book'=>$book
        ]);

    }
    public function update($id,Request $request)
    {
        $book=Book::findOrFail($id);

        $rules= [
            'title'=>'required|min:5',
            'author'=>'required|min:3',
            'status'=>'required',
        ];
        if(!empty($request->image))
        {
            $rules['image']='image';

        }
        $validator=Validator::make($request->all(),$rules);

         if($validator->fails())
         {
            return redirect()->route('books.edit',$book->id)->withInput()->withErrors($validator);
         }
//update book in DB 

    $book->title=$request->title;
    $book->author=$request->author;
    $book->description=$request->description;
    $book->status=$request->status;
    $book->save();


    if(!empty($request->image))
    {
        //delete old book image
        File::delete(public_path('uploads/book/').$book->image);
        File::delete(public_path('uploads/book/thumb/').$book->image);
        $image=$request->image;
        $ext=$image->getClientOriginalExtension();
        $imageName=time().'.'.$ext;
        $image->move(public_path('uploads/book'),$imageName);

        $book->image=$imageName;
        $book->save();
        //Generate Image thumbanil here
        $manager = new ImageManager(Driver::class);
        $image = $manager->read(public_path('uploads/book/').$imageName); 
        $image->resize(990);
        $image->save(public_path('uploads/book/thumb/').$imageName);
    }

    return redirect()->route('books.index')->with('success','Book Edited Successfully');

 

    }
     //deleting books

    public function destroy(Request $request)
    {
        $book=Book::find($request->id);
        if($book==null)
        {
            session()->flash('error','book not found');
            return response()->json([
                'status'=>false,
                'message'=>'Book not found'
            ]);

        }
        else{
            File::delete('uploads/book/'.$book->image);
            File::delete('uploads/book/thumb/'.$book->image);
            $book->delete();
            session()->flash('success','book deleted !');

            return response()->json([
                'status'=>true,
                'message'=>'Book Deleted Successfully'
            ]);

        }
    }

    
}
