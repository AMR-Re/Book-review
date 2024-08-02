
@extends('layouts.app')

@section('main')

<div class="container">
    <div class="row my-5">
        <div class="col-md-3">
         @include('layouts.sidebar')           
        </div>
        <div class="col-md-9">
            @include('layouts.message')
            <div class="card border-0 shadow">
                <div class="card-header  text-white">
                    Edit Review
                </div>       
                <div class="card-body pb-3">  
                    <form action="{{route('account.myReviews.updateReview',$reviews->id)}}" method="post" >
                        @csrf
                        <div class="mb-3">
                        <label for="name" class="form-label">Book</label>
                      <div><strong>{{$reviews->book->title}}</strong></div>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Review</label>
                       <textarea class="form-control  @error('review') is-invalid @enderror" name="reviews" id="reviews" >{{old('review',$reviews->reviews)}}</textarea>
                        @error('review')
                        <p class="invalid-feedback">{{$message}}</p>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label @error('rating') is-invalid @enderror">Rating</label>
                            <select name="rating" id="rating" class="form-control">
                                <option value="1"{{($reviews->rating==1)?'selected':''}}>1</option>
                                <option value="2" {{($reviews->rating==2)?'selected':''}} >2</option>
                                <option value="3" {{($reviews->rating==3)?'selected':''}} >3</option>
                                <option value="4" {{($reviews->rating==4)?'selected':''}} >4</option>
                                <option value="5" {{($reviews->rating==5)?'selected':''}} >5</option>

                            </select>
                        @error('rating')
                        <p class="invalid-feedback">{{$message}}</p>
                        @enderror
                    </div>
                 
                    <button class="btn btn-primary mt-2">Update</button>                     
                    </form>
                 
                </div>
                
            </div>                
        </div>
    </div>       
</div>
@endsection