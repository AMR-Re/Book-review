
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
                    My Reviews
                </div>       
                <div class="card-body pb-0">  
                      
                    <div class="d-flex justify-content-end">
               
                        <form action="" method="get">
                            <div class="d-flex">
                                <input type="text" class="form-control" value="{{Request::get('Keyword')}}" placeholder="Keyword"  name="Keyword">    
                                <button type="submit" class="btn btn-primary ms-2">Search</button>
                                <a href="{{route('account.reviews')}}" class="btn btn-secondary ms-2">Clear</a>
                            </div> 
                        </form>      
                    </div>
                
                    
                    <table class="table  table-striped mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Review</th>
                                <th>Book</th>
                                <th>Rating</th>
                                <th>Created At</th>

                                <th>Status</th>                                  
                                <th width="100">Action</th>
                            </tr>
                            <tbody>
                                @if($reviews->isNotEmpty())
                                 @foreach($reviews as $review)
                                <tr>
                                    <td>{{$review->reviews}}<br/>{{$review->user->name}}</td>                                        

                                    <td>{{$review->book->title}}</td>
                                    <td>{{$review->rating}}</td>
                                    <td>{{\Carbon\Carbon::parse($review->created_at)->format('d M,Y')}}</td>

                                    <td>
                                        @if($review->status==1)
                                          <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-danger" >block</span>
                                        @endif
                                        </td>
                                    <td>
                                        <a href="{{route('account.reviews.edit',$review->id)}}" class="btn btn-primary btn-sm"><i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                        <a href="#" onclick="deleteReview({{$review->id}})" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                              @endif
                            </tbody>
                        </thead>
                    </table>   
                   {{$reviews->links()}}                
                </div>
                
            </div>                
        </div>
    </div>       
</div>
@endsection
@section('script')
<script>
    function deleteReview(id)
    {
        if(confirm("Are You sure you want to delete review ?"))
        {
            $.ajax({
                url: '{{route("account.reviews.deleteReview")}}',
                type: 'post',
                data:{id:id},
                headers:
                {
                    'X-CSRF-TOKEN':'{{csrf_token()}}'
                },
                success: function(response)
                {
                    window.location.href='{{route("account.reviews")}}';

                }

            });
        }

    }
</script>
@endsection