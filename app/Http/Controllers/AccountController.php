<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Review;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
class AccountController extends Controller
{
    public function register()
    {
        return view('account.register');
    }

    public function processRegister(Request $request)
    {
        $validator=Validator::make($request->all(),
       [ 'name'=>'required|min:3',
        'email'=>'required|email|unique:users',
        'password'=>'required|min:6|confirmed',
        'password_confirmation'=>'required'
        ]);

    if($validator->fails())
    {
        return redirect()->route('account.register')->withInput()->withErrors($validator);
        
    }
 

    $user=new User();
    $user->name=$request->name;
    $user->email=$request->email;
    $user->password=Hash::make($request->password);
    $user->save();

     return redirect()->route('account.login')->with('success','You Have Register Successfully!');
}


 public function login()
 {
    return view('account.login');
 }


 public function authenticate(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required'
    ]);
    
    if ($validator->fails()) {
        return redirect()->route('account.login')
            ->withInput()
            ->withErrors($validator);
    }

    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        return redirect()->route('account.profile');
    } else {
        return redirect()->route('account.login')
            ->with('error', 'Invalid credentials');
    }
}
 //show user Profile page
 public function profile()
 {
    $user=User::find(Auth::user()->id);
   // dd($user);
    return view('account.profile',[
        'user'=>$user
    ]);
 }

 //update user profile page
 public function updateProfile(Request $request) 
  {
    $rules= [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email,'.Auth::user()->id.',id',
    ];
    if(!empty($request->image))
    {
        $rules['image']='image';
    }
    $validator = Validator::make($request->all(),$rules);
    if ($validator->fails()) {
        return redirect()->route('account.profile')
            ->withInput()
            ->withErrors($validator);
    }
    $user=User::find(Auth::user()->id);
    $user->name=$request->name;
    $user->email=$request->email;
    $user->save();
    //here we will update image
    if(!empty($request->image))
    {
        //Delete Old Image
    File::delete(public_path('uploads/profile/').$user->image);
    File::delete(public_path('uploads/profile/thumb/').$user->image);

    $image=$request->image;
    $ext=$image->getClientOriginalExtension();
    $imageName=time().'.'.$ext;
    $image->move(public_path('uploads/profile'),$imageName);
    $user->image=$imageName;
    $user->save();
    $manager = new ImageManager(Driver::class);
    $image = $manager->read(public_path('uploads/profile/').$imageName); // 800 x 600
    $image->cover(150, 150);
    $image->save(public_path('uploads/profile/thumb/').$imageName);
    }
    return redirect()->route('account.profile')->with('success','Profile Updated Successfully');

 }

 public function logout() 
  {
   Auth::logout();
   return redirect()->route('account.login');
  
 }

 public function myReviews(Request $request)
 {
    $reviews=Review::with('book')->where('user_id',Auth::user()->id);
    $reviews=$reviews->orderBy('created_at','DESC');
    if(!empty($request->Keyword))
        {
            $reviews=$reviews->where('reviews','like','%'.$request->Keyword.'%');

        }
    $reviews=$reviews->paginate(5);
    return view('account.myReviews.myReviews',[
        'reviews'=>$reviews,
    ]);
 }


 public function editReview($id) 
  {
    $reviews=Review::where(
        [
            'id'=>$id,
            'user_id'=>Auth::user()->id,

        ]
    )->with('book')->first();
    return view('account.myReviews.edit-review',[
        'reviews'=>$reviews,
    ]);
    
 }
 public function updateReview($id,Request $request)
 {
     $review=Review::findOrFail($id);

     $validator=Validator::make($request->all(),[
         'reviews'=>'required',
         'rating'=>'required'
     ]);
     if($validator->fails())
     {
         return redirect()->route('account.reviews.editReview',$id)->withInput()->withErrors($validator);
     }
     $review->reviews=$request->reviews;
     $review->rating=$request->rating;
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
