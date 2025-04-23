@extends(getTemplate().'.layouts.app')

@section('content')
    <!--<section class="cart-banner position-relative text-center">-->
    <!--    <div class="container h-100">-->
    <!--        <div class="row h-100 align-items-center justify-content-center text-center">-->
    <!--            <div class="col-12 col-md-9 col-lg-7">-->

    <!--                <h1 class="font-30 text-white font-weight-bold">{{ $post->title }}</h1>-->

    <!--                <div class="d-flex flex-column flex-sm-row align-items-center align-sm-items-start justify-content-between">-->
    <!--                    @if(!empty($post->author))-->
    <!--                        <span class="mt-10 mt-md-20 font-16 font-weight-500 text-white">{{ trans('public.created_by') }}-->
    <!--                            @if($post->author->isTeacher())-->
    <!--                                <a href="{{ $post->author->getProfileUrl() }}" target="_blank" class="text-white text-decoration-underline">{{ $post->author->full_name }}</a>-->
    <!--                            @elseif(!empty($post->author->full_name))-->
    <!--                                <span class="text-white text-decoration-underline">{{ $post->author->full_name }}</span>-->
    <!--                            @endif-->
    <!--                    </span>-->
    <!--                    @endif-->

    <!--                    <span class="mt-10 mt-md-20 font-16 font-weight-500 text-white">{{ trans('public.in') }}-->
    <!--                        <a href="{{ $post->category->getUrl() }}" class="text-white text-decoration-underline">{{ $post->category->title }}</a>-->
    <!--                    </span>-->

    <!--                    <span class="mt-10 mt-md-20 font-16 font-weight-500 text-white">{{ dateTimeFormat($post->created_at, 'j M Y') }}</span>-->

    <!--                    <div class="js-share-blog d-flex align-items-center cursor-pointer mt-10 mt-md-20">-->
    <!--                        <div class="icon-box ">-->
    <!--                            <i data-feather="share-2" class="text-white" width="20" height="20"></i>-->
    <!--                        </div>-->
    <!--                        <div class="ml-5 font-16 font-weight-500 text-white">{{ trans('public.share') }}</div>-->
    <!--                    </div>-->
    <!--                </div>-->

    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</section>-->
    
    @php
    use Illuminate\Support\Facades\DB;
    
    $userId = auth()->id();
    $systemIp = getSystemIP();
    
    // Get total stats (for all users)
    $totalStats = DB::table('stats')
    ->where('blog_id', $post->id)
    ->selectRaw('SUM(likes) as total_likes, SUM(views) as total_views, SUM(shares) as total_shares')
    ->first();
    
    // Check if the system IP or logged-in user has interacted
    $userStats = DB::table('stats')
    ->where('blog_id', $post->id)
    ->when($userId, function ($query) use ($userId) {
    return $query->where('user_id', $userId);
    }, function ($query) use ($systemIp) {
    return $query->where('ip_address', $systemIp);
    })
    ->selectRaw('SUM(likes) as user_likes, SUM(views) as user_views, SUM(shares) as user_shares')
    ->first();
    
    // Define icon classes based on user/system IP interaction
    $likeIconClass = ($userStats->user_likes ?? 0) > 0 ? 'text-primary' : 'stats_icon';
    $viewIconClass = ($userStats->user_views ?? 0) > 0 ? 'text-primary' : 'stats_icon';
    $shareIconClass = ($userStats->user_shares ?? 0) > 0 ? 'text-primary' : 'stats_icon';
    @endphp

    <section class="container mt-10 mt-md-40">
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="post-show mt-30">

                    <div class="post-img pb-30">
                        <img src="{{ $post->image }}" alt="">
                    </div>

                    <div class="mt-10 mb-20 align-items-center d-flex flex-fill gap-2 justify-content-around">
                        <div class="d-flex align-items-center pointer">
                            <i class="fas fa-thumbs-up interaction-icon{{ $post->id }} {{ $likeIconClass }}" data-type="like"></i>
                            <span class="ml-1 font-14">{{ $totalStats->total_likes ?? 0 }}</span>
                        </div>
            
                        <div class="d-flex align-items-center">
                            <i class="fas fa-eye text-primary" data-type="view"></i>
                            <span class="ml-1 font-14 view-count-{{ $post->id }}">{{ $totalStats->total_views ?? 0 }}</span>
                        </div>
            
                        <div class="js-share-blog d-flex align-items-center cursor-pointer">
                            <div class="icon-box ">
                                <i class="fa-share fas text-primary" width="20" height="20"></i>
                            </div>
                        </div>
                    </div>

                    {!! nl2br($post->content) !!}
                </div>

                {{-- post Comments --}}
                @if($post->enable_comment)
                    @include('web.default.includes.comments',[
                            'comments' => $post->comments,
                            'inputName' => 'blog_id',
                            'inputValue' => $post->id
                        ])
                @endif
                {{-- ./ post Comments --}}

            </div>
            <div class="col-12 col-lg-4">
                @if(!empty($post->author) and !empty($post->author->full_name))
                    <div class="rounded-lg shadow-sm mt-35 p-20 course-teacher-card d-flex align-items-center flex-column">
                        <div class="teacher-avatar mt-5">
                            <img src="{{ $post->author->getAvatar(100) }}" class="img-cover" alt="">
                        </div>
                        <h3 class="mt-10 font-20 font-weight-bold text-secondary">{{ $post->author->full_name }}</h3>

                        @if(!empty($post->author->role))
                            <span class="mt-5 font-weight-500 font-14 text-gray">{{ $post->author->role->caption }}</span>
                        @endif

                        <div class="mt-25 d-flex align-items-center  w-100">
                            <a href="/blog?author={{ $post->author->id }}" class="btn btn-sm btn-primary btn-block px-15">{{ trans('public.author_posts') }}</a>
                        </div>
                    </div>
                @endif

                {{-- categories --}}
                <div class="p-20 mt-30 rounded-sm shadow-lg border border-gray300">
                    <h3 class="category-filter-title font-16 font-weight-bold text-dark-blue">{{ trans('categories.categories') }}</h3>

                    <div class="pt-15">
                        @foreach($blogCategories as $blogCategory)
                            <a href="{{ $blogCategory->getUrl() }}" class="font-14 text-dark-blue d-block mt-15">{{ $blogCategory->title }}</a>
                        @endforeach
                    </div>
                </div>

                {{-- recent_posts --}}
                <div class="p-20 mt-30 rounded-sm shadow-lg border border-gray300">
                    <h3 class="category-filter-title font-20 font-weight-bold text-dark-blue">{{ trans('site.recent_posts') }}</h3>

                    <div class="pt-15">

                        @foreach($popularPosts as $popularPost)
                            <div class="popular-post d-flex align-items-start mt-20">
                                <div class="popular-post-image rounded">
                                    <img src="{{ $popularPost->image }}" class="img-cover rounded" alt="{{ $popularPost->title }}">
                                </div>
                                <div class="popular-post-content d-flex flex-column ml-10">
                                    <a href="{{ $popularPost->getUrl() }}">
                                        <h3 class="font-14 text-dark-blue">{{ truncate($popularPost->title,40) }}</h3>
                                    </a>
                                    <span class="mt-auto font-12 text-gray">{{ dateTimeFormat($popularPost->created_at, 'j M Y') }}</span>
                                </div>
                            </div>
                        @endforeach

                        <a href="/blog" class="btn btn-sm btn-primary btn-block mt-30">{{ trans('home.view_all') }} {{ trans('site.posts') }}</a>
                    </div>
                </div>

            </div>
        </div>
        
        <!-- Share modal -->
        <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="shareModalLabel">Share This Webinar</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class='mb-10'>Share this webinar on social media:</p>
                        <div class="d-flex justify-content-center gap10">
                            <a id="facebook-share" target="_blank" class="btn btn_facebook_share mx-2">
                                <i class="fa-facebook-f fab"></i>
                            </a>
                            <a id="twitter-share" target="_blank" class="btn btn_twitter_share mx-2">
                                <i class="fa-twitter fab"></i>
                            </a>
                            <a id="linkedin-share" target="_blank" class="btn btn_linkedin_share mx-2">
                                <i class="fa-linkedin fab"></i>
                            </a>
                            <a id="whatsapp-share" target="_blank" class="btn btn_whatsapp_share mx-2">
                                <i class="fa-whatsapp fab"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('web.default.blog.share_modal')
@endsection

@push('scripts_bottom')
    <script>
        var webinarDemoLang = '{{ trans('webinars.webinar_demo') }}';
        var replyLang = '{{ trans('panel.reply') }}';
        var closeLang = '{{ trans('public.close') }}';
        var saveLang = '{{ trans('public.save') }}';
        var reportLang = '{{ trans('panel.report') }}';
        var reportSuccessLang = '{{ trans('panel.report_success') }}';
        var messageToReviewerLang = '{{ trans('public.message_to_reviewer') }}';
        var copyLang = '{{ trans('public.copy') }}';
        var copiedLang = '{{ trans('public.copied') }}';
    </script>
    
    <script>
        $(document).ready(function() {
            $(' #shareModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var url = button.data('url');
                $('#facebook-share').attr('href', 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url));
                $('#twitter-share').attr('href', 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(url));
                $('#linkedin-share').attr('href', 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(url));
                // $('#whatsapp-share').attr('href', 'https://wa.me/?text=' + encodeURIComponent(url));
                $('#whatsapp-share').attr('href', 'https://web.whatsapp.com/send?text=' + encodeURIComponent(url));
            });
        });
    </script>
    
    <!-- jQuery (Google CDN) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script>
        $(document).ready(function() {
            
            $.ajax({
                url: "/update-stats",
                type: 'POST',
                data: {
                    post_id: "{{ $post->id }}",
                    type: 'view',
                    action: 'add',
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        let countSpan = $('.view-count-{{ $post->id }}');
                        countSpan.text(response.updated_views);
                    }
                }
            });
            
            $('.interaction-icon{{ $post->id }}').click(function() {
                let icon = $(this);
                let type = icon.data('type'); // 'like', 'view', 'share'
                let countSpan = icon.siblings('span');
                let count = parseInt(countSpan.text()); // Get current count
                let isActive = icon.hasClass('text-primary'); // Check if it's already active
    
                $.ajax({
                    url: "/update-stats",
                    type: 'POST',
                    data: {
                        post_id: "{{ $post->id }}",
                        type: type,
                        action: isActive ? 'remove' : 'add', // Toggle between add/remove
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            // Toggle class
                            icon.toggleClass('text-primary stats_icon');
    
                            // Toggle count
                            countSpan.text(isActive ? count - 1 : count + 1);
                        }
                    }
                });
            });
        });
    </script>

    <script src="/assets/default/js/parts/comment.min.js"></script>
    <script src="/assets/default/js/parts/blog.min.js"></script>
@endpush
