<?php $thread = $options['data']['thread']; ?>
<?php $comments = $options['data']['comments']; ?>
<hr><div class="row justify-content-center h-100">

    <div class="col-md-11 col-xl-11 messageboard">
        <div class="card">
            <div class="card-header msg_head">
                <div class="d-flex bd-highlight">
                    <div class="img_cont">
                        <img src="{{ asset('images/chat-bubble.png') }}" class="user_img">
                    </div>
                    <div class="user_info">
                        <span>Add Discussion</span>
                        <p>{{ count($comments) }} Comments(s)</p>
                    </div>

                </div>

            </div>
            <div class="card-body msg_card_body">

                @foreach($comments as $comment)
                    <div class="d-flex justify-content-start mb-4">
                        <div class="img_cont_msg">
                            <img src="{{ asset('images/chat-bubble.png') }}" class=" user_img_msg">
                        </div>
                        <div class="msg_cotainer images">
                            <h6>{{ $comment->user->first_name.' '.$comment->user->last_name }}</h6>
                            <p>{{ $comment->comment }}</p>
                            @if(!is_null($comment->image))
                                <img height="300px" width="390px" src="{{ asset($comment->image) }}" style=" object-fit: cover; margin-top: 5px;">
                            @endif
                            <span class="msg_time">{{--<a href="#" class="reply">Reply</a> --}}<a href="#">{{ $comment->created_at->diffForHumans() }}</a></span>
                            <div id="image-viewer">
                                            <span class="close">X</span>
                                            <img class="viewer-modal-content" id="full-image">
                                        </div>
                          </div>
                        
                    </div>
                    
                @endforeach

            </div>
            <div class="card-footer">
                <form id="commentForm" method="post" action="{{ route('thread.postComment') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="img_wrp" style="display: none;">
                        <img id="displayImage" height="100px" width="140px" src="{{ asset('images/chat-bubble.png') }}" style=" object-fit: cover; margin-bottom: 3px;">
                        <span class="close" id="deleteImage"><i class="fa fa-times"></i></span>
                    </div>
                    <div class="input-group">

                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                        <input type="hidden" name="thread_id" value="{{ $thread->id }}">
                        <input id="comment_input" type="text" name="comment" class="form-control type_msg" placeholder="Type your message...">
                        <input type="file" name="image" id="attachimage" hidden>
                        <div class="input-group-append">
                            <span class="input-group-text send_btn"><span onclick="document.getElementById('attachimage').click()" class="fabutton"><i class="fa fa-camera"></i></span></span>
                            <span class="input-group-text send_btn"><button id="completed-task" class="fabutton"><i class="fa fa-paper-plane"></i></button></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .fabutton {
        background: none;
        padding: 0px;
        border: none;
    }
    .img_wrp {
        display: inline-block;
        position: relative;
    }
    .close {
        margin-left: 5px;
        top: 0;
        right: 0;
    }
</style>

<script>
  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function(e) {
        $('#displayImage').attr('src', e.target.result);
        $('.img_wrp').show();
      }

      reader.readAsDataURL(input.files[0]); // convert to base64 string
    }
  }

  $("#attachimage").change(function() {
    readURL(this);
  });

  $('#deleteImage').on('click', function () {
    $('.img_wrp').hide();
    $('#attachimage').val('');
  });

  $(document).on('click', '#completed-task', function (e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('user_id', $('input[name="user_id"]').val());
    formData.append('thread_id', $('input[name="thread_id"]').val());
    formData.append('comment', $('input[name="comment"]').val());

    var image = $('input[name="image"]')[0].files;
    if(image.length > 0){
      formData.append('image', image[0]);
    }

    console.log(formData);
    $.ajax({

      url : '{{ route('thread.postComment') }}',
      type : 'post',
      data : formData,
      processData: false,
      contentType: false,
      success : function(data) {
        var comment = data.comment;
        var image = '';
        var text = comment.comment;
        text = (text === null) ? '' : text;
        if(comment.image){
          image = '<img height="300px" width="390px" src="{{ URL::to('/') }}/'+ comment.image +'" style=" object-fit: cover; margin-top: 5px;">';
        }
        $('.msg_card_body').append('<div class="d-flex justify-content-start mb-4">\n' +
            '                        <div class="img_cont_msg">\n' +
            '                            <img src="{{ URL::to('/images/chat-bubble.png') }}" class=" user_img_msg">\n' +
            '                        </div>\n' +
            '                        <div class="msg_cotainer">\n' +
            '                            <h6>{{ Auth::user()->first_name. ' ' . Auth::user()->last_name }}</h6>\n' +
            '                            <p>'+text+'</p>\n' +
            '                            \n' + image +
            '                                \n' +
            '                            \n' +
            '                            <span class="msg_time">{{--<a href="#" class="reply">Reply</a> --}}<a href="#">'+ comment.time +'</a></span>\n' +
            '                        </div>\n' +
            '                    </div>');
        $('#deleteImage').trigger('click');
        $('input[name="comment"]').val('');
      },
      error : function(request,error)
      {
        console.log('server error');
      }
    });
  });
</script>