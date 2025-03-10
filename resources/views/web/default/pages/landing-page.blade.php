@extends(getTemplate().'.layouts.app1')

@section('content')

 <link href="assets/assets/css/style.css" rel="stylesheet" />
<!-- <link href="assets/assets/css/bootstrap.min.css" rel="stylesheet" /> -->
        <!--<link rel="stylesheet" href="https://lms.shrinkcom.com/public/assets/assets/default/vendors/toast/jquery.toast.min.css">-->

    <link
      href="assets/assets/css/material-design-iconic-font.min.css"
      rel="stylesheet"
    />
    <link href="assets/assets/css/swiper-bundle.min.css" rel="stylesheet" />
    <!-- <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" /> -->
    <!--<link href="assets/assets/css/slicknav.min.css" rel="stylesheet" />-->

    <!--<link href="assets/assets/css/magnific-popup.css" rel="stylesheet" />-->

    <!--<link href="assets/assets/css/owl.carousel.css" rel="stylesheet" />-->

    <!--<link href="assets/assets/css/animate.min.css" rel="stylesheet" />-->

   
    <!--<script src="assets/assets/js/jquery.js"></script>-->
   
   
   
    <!--<script src="assets/assets/js/popper.min.js"></script>-->

    <!--<script src="assets/assets/js/bootstrap.min.js"></script>-->
 
    <!--<script src="assets/assets/js/jquery.slicknav.min.js"></script>-->
  
    <!--<script src="assets/assets/js/jquery.magnific-popup.min.js"></script>-->
   
    <!--<script src="assets/assets/js/jquery.sticky.js"></script>-->
  
    <!--<script src="assets/assets/js/owl.carousel.min.js"></script>-->
  
    <!--<script src="assets/assets/js/wow.min.js"></script>-->
  
    <!--<script src="assets/assets/js/main.js"></script>-->

    <!--<script src="https://lms.shrinkcom.com/public/assets/default/vendors/swiper/swiper-bundle.min.js"></script>-->

<section >
    <!--<div id="loader-wrapper">-->
    <!--  <div id="loader"></div>-->
    <!--  <div class="loader-section section-left"></div>-->
    <!--  <div class="loader-section section-right"></div>-->
    <!--</div>-->
        <!--  page loader end -->
        <div id="home"></div>
 
    <div class="main-hero-area cta3">
      <div class="container">
          <div class='row'>
               <h2 class="text_features text-center pb-15 mb_40 pb-20">
                  Thank you for subscribing! We’re excited to have you with us watch the video below to get started!
                </h2> 
          </div>
        <div class="row">
          <div class="col-md-8 wow fadeInLeft">
            <div class="hero-txt top-0">
               
                <h2 class="text_features mb-3">
                  Transform Your Life with Kemetic Wisdom
                </h2> 
                <p>
                    Access the hidden knowledge of the ancients and elevate your spiritual journey.
                </p>
                <iframe
                frameborder="0"
                src="//www.youtube.com/embed/9xBfox5lvLo"
                class="video_clip pt-3"
                height="360"
              ></iframe>

              </div>
          </div>
          <div class="col-md-4">
            <div class="home1-hero-mobile wow fadeInDown">
              <img src="assets/assets/img/hero-image.png" alt="" />
            </div>
          </div>
        </div>
      </div>
    </div>


    @php
$filteredSubscribe = $subscribes->firstWhere(function($subscribe) {
    return $subscribe->price == 99 && $subscribe->is_popular;
});
@endphp

@if($filteredSubscribe)
<div class="d-flex justify-content-center">
<div class="py-20 max_w_460">
    <div class="h-auto">
        <div class="card subscribe-plan text-center h-100 position-relative bg-white d-flex flex-column align-items-center rounded-sm shadow pt-50 pb-20 px-20">
            <div class="align-items-center card-body d-flex flex-column p-0 text-center">
                <div class="d-flex justify-content-end pe-2 text-end trial-div">
                    <span class="font-12">3 Days free trial</span>
                </div>
                <span class="badge badge-primary badge-popular px-15 py-5">{{ trans('panel.popular') }}</span>
                <div class="plan-icon mt-20">
                    <img src="{{ $filteredSubscribe->icon }}" class="img-cover" alt="">
                </div>
                <h3 class="mt-20 text-secondary">{{ $filteredSubscribe->title }}</h3>
                <p class="font-14 text-gray mt-10">{{ $filteredSubscribe->description }}</p>
                <div class="d-flex align-items-start mt-20">
                    <span class="font-36 text-primary line-height-1 hover-text">
                        {{ handlePrice($filteredSubscribe->price, true, true, false, null, true) }}
                    </span>
                </div>
                <ul class="mt-15 plan-feature">
                    <li class="mt-10 font-14">{{ $filteredSubscribe->days }} {{ trans('financial.days_of_subscription') }}</li>
                    <li class="mt-10 font-14">
                        @if($filteredSubscribe->infinite_use)
                        {{ trans('update.unlimited') }}
                        @else
                        {{ $filteredSubscribe->usable_count }}
                        @endif
                        <span class="ml-5">{{ trans('update.subscribes') }}</span>
                    </li>
                </ul>
            </div>
            <div class="card-footer p-0 bg-transparent border-0">
                @if(auth()->check())
                <form action="/panel/financial/pay-subscribes" method="post" class="w-100">
                    {{ csrf_field() }}
                    <input name="amount" value="{{ $filteredSubscribe->price }}" type="hidden">
                    <input name="id" value="{{ $filteredSubscribe->id }}" type="hidden">
                    <div class="d-flex align-items-center mt-25 w-100">
                        <button type="submit" class="btn btn-primary btn-block">{{ trans('update.purchase') }}</button>
                    </div>
                </form>
                @else
                <a href="/register?plan_id={{ $filteredSubscribe->id }}" class="btn btn-primary btn-block mt-25">{{ trans('update.purchase') }}</a>
                @endif
            </div>
        </div>
    </div>
</div>
</div>

@endif



    <div class="featured-area cta3" id="featured">
          <div class="container">
            <div class="row">
              <div class="col-lg-4 wow fadeInLeft">
                <div class="featured-mobile pt-md-15">
                  <img src="assets/assets/img/featured-img.png" alt="" />
                </div>
              </div>
              <div
                class="col-lg-7 offset-lg-1 col-md-12 wow fadeInRight margin-left-30"
              >
                <div class="featured-right-item">
                  <div class="featured-title">
                    <h2>Why Become a Member?</h2>

                  
                    <p class="text_features pb-2">
                      Access 100+ transformational courses & teachings Exclusive
                      live sessions & mentorship Connect with a vibrant
                      spiritual community Receive new ancient wisdom regularly
                      Special bonuses & member discounts
                    </p>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="featured-single-items">
                        <div class="featured-single">
                          <div class="featured-single-text">
                            <h3>🧘</h3>
                            <p class="text_features">
                              Access 100+ transformational courses & teachings
                            </p>
                          </div>
                        </div>
                        <div class="featured-single">
                          <div class="featured-single-text">
                            <h3>🌟</h3>
                            <p class="text_features">
                              Exclusive live sessions, mentorship, & Q&A with
                              spiritual guides
                            </p>
                          </div>
                        </div>
                        <div class="featured-single">
                          <div class="featured-single-text">
                            <h3>🔑</h3>
                            <p class="text_features">
                                Connect with a vibrant spiritual community
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="featured-single-items">
                        <div class="featured-single">
                          <div class="featured-single-text">
                            <h3>
                              📚
                            </h3>
                            <p class="text_features">
                                Receive new ancient wisdom regularly
                            </p>
                          </div>
                        </div>
                        <div class="featured-single">
                          <div class="featured-single-text">
                            <h3>🎁</h3>
                            <p class="text_features">Special bonuses & discounts</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial Section -->
        <div class="testimonial-area cta" id="testimonial">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="swiper testimonial-swiper h-100">
          <div class="swiper-wrapper">
            <!-- Testimonial Slide 1 -->
            <div class="swiper-slide testimonial-single-slide h-100 card d-flex flex-column">
              <div class="testimonial-slide-content card-body flex-grow-1">
                <h4 class="testimonial-heading">Transformation & Growth</h4>
                <p class='font-14'>
                  “Kemetic App has been life-changing! The courses have deepened my understanding of ancient wisdom, and the community support has been incredible. Highly recommend it to anyone on their awakening journey!”
                </p>
              </div>
              <div class="testimonial-slide-meta card-footer mt-auto">
                <span class="testimonial-meta">
                  <span class="meta-title">— Amina R., Spiritual Seeker</span>
                </span>
              </div>
            </div>

            <!-- Testimonial Slide 2 -->
            <div class="swiper-slide testimonial-single-slide h-100 card d-flex flex-column">
              <div class="testimonial-slide-content card-body flex-grow-1">
                <h4 class="testimonial-heading">Exclusive Knowledge & Community</h4>
                <p class='font-14'>
                  “I’ve explored many spiritual platforms, but nothing compares to Kemetic App. The teachings are authentic, profound, and truly enlightening. The membership is worth every penny!”
                </p>
              </div>
              <div class="testimonial-slide-meta card-footer mt-auto">
                <span class="testimonial-meta">
                  <span class="meta-title">— David M., Esoteric Researcher</span>
                </span>
              </div>
            </div>

            <!-- Testimonial Slide 3 -->
            <div class="swiper-slide testimonial-single-slide h-100 card d-flex flex-column">
              <div class="testimonial-slide-content card-body flex-grow-1">
                <h4 class="testimonial-heading">Practical & Life-Changing</h4>
                <p class='font-14'>
                  “Before joining Kemetic App, I struggled to find reliable, well-structured information on ancient spirituality. This platform has given me not just knowledge, but practical tools for growth and self-mastery.”
                </p>
              </div>
              <div class="testimonial-slide-meta card-footer mt-auto">
                <span class="testimonial-meta">
                  <span class="meta-title">— Sophia L., Energy Healer</span>
                </span>
              </div>
            </div>
          </div>

          <!-- Pagination & Navigation -->
          <div class="swiper-pagination mt-4"></div>
        </div>
      </div>
    </div>
  </div>
</div>


    <!--  testimonial area end -->

        <div class="pricng-area cta3 wow fadeInUp" id="pricing">
      <div class="container">
        <div class="row">
          <div class="col-md-12 text-center px-0">
            <div class="section-title cta-pricing">
              <h2 class="plans_pricing">Our Best Plans</h2>
            </div>
          </div>
        </div>
        <!-- <div class="row">
          <div class="col-lg-10 offset-lg-1">
            <div class="row justify-content-center">
              <div
                class="col-md-6 position-relative col-xl-4 d-flex justify-content-center text-center my-4"
              >
                <div class="single-pricing h-100 cta m-0">
                  <span class="trial_pack"> </span>
                  <h5 class="text_features">
                    High Priest/Priestess - Yearly membership
                  </h5>
                  <h4
                    class="d-flex gap-2 justify-content-between px-3 align-items-center"
                  >
                    <span class="pop_tag">
                      Best Plan
                    </span>
                    <span class="amount_tag pop_tag">
                      €99/Yearly
                    </span>
                  </h4>
                  <div class="mt-30 annual_plan">
                    <p>
                      Access to all courses, Community, articles And more with
                      the yearly membership
                    </p>
                    <ul class="plan-desc">
                      <li>Annual plan subscription</li>
                      <li>Unlimited Subscribes</li>
                    </ul>
                  </div>
                  <div class="plan_footer">
                    <a href="https://kemetic.app/panel/financial/pay-subscribes" class="plans-btn">Purchase</a>
                  </div>
                </div>
              </div>

              <div
                class="col-md-6 position-relative col-xl-4 d-flex justify-content-center text-center my-4"
              >
                <div class="single-pricing h-100 cta m-0">
                  <span class="trial_pack"> </span>
                  <h5 class="text_features">Oracle - Monthly Membership</h5>

                  <h4
                    class="d-flex gap-2 justify-content-between px-3 align-items-center mb-0"
                  >
                    <span class="pop_tag">
                        Flexible Plan
                    </span>
                    <span class="amount_tag pop_tag">
                      €9/Monthly
                    </span>
                  </h4>
                  <div class="mt-30 monthly_plan">
                    <p>
                      Access to all courses, Community, Articles with this
                      montly membership
                    </p>
                    <ul class="plan-desc">
                      <li>Monthly plan subscription</li>
                      <li>Unlimited Subscribes</li>
                    </ul>
                  </div>
                  <div class="plan_footer">
                    <a href="https://kemetic.app/panel/financial/pay-subscribes" class="plans-btn">Purchase</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="text-center">
          
            <p class="my-2">
                ✓ Secure Payment | ✓
                Instant Access | ✓
                Cancel Anytime
            </p>
        </div> -->
      </div>
    </div>


    <div class="position-relative">


    <div class="swiper-container subscribes-swiper container px-12">
    <div class="swiper-wrapper py-20">
        @foreach($subscribes as $subscribe)
        @php
        $subscribeSpecialOffer = $subscribe->activeSpecialOffer();
        @endphp
        <div class="swiper-slide h-auto">
            <div class="card subscribe-plan text-center h-100 position-relative bg-white d-flex flex-column align-items-center rounded-sm shadow pt-50 pb-20 px-20">
                <div class="align-items-center card-body d-flex flex-column p-0 text-center">
                    <div class="d-flex justify-content-end pe-2 text-end trial-div">
                        <span class="font-12">3 Days free trial</span>
                    </div>
                    @if($subscribe->is_popular)
                    <span class="badge badge-primary badge-popular px-15 py-5">{{ trans('panel.popular') }}</span>
                    @elseif(!empty($subscribeSpecialOffer))
                    <span class="badge badge-danger badge-popular px-15 py-5">{{ trans('update.percent_off', ['percent' => $subscribeSpecialOffer->percent]) }}</span>
                    @endif
                    <div class="plan-icon mt-20">
                        <img src="{{ $subscribe->icon }}" class="img-cover" alt="">
                    </div>
                    <h3 class="mt-20 text-secondary">{{ $subscribe->title }}</h3>
                    <p class="font-14 text-gray mt-10">{{ $subscribe->description }}</p>
                    <div class="d-flex align-items-start mt-20">
                        @if(!empty($subscribe->price) and $subscribe->price > 0)
                        @if(!empty($subscribeSpecialOffer))
                        <div class="d-flex align-items-end line-height-1">
                            <span class="font-36 text-primary">{{ handlePrice($subscribe->getPrice(), true, true, false, null, true) }}</span>
                            <span class="font-14 text-gray ml-5 text-decoration-line-through">{{ handlePrice($subscribe->price, true, true, false, null, true) }}</span>
                        </div>
                        @else
                        <span class="font-36 text-primary line-height-1 hover-text">{{ handlePrice($subscribe->price, true, true, false, null, true) }}</span>
                        @endif
                        @else
                        <span class="font-36 text-primary line-height-1 hover-text">{{ trans('public.free') }}</span>
                        @endif
                    </div>
                    <ul class="mt-15 plan-feature">
                        <li class="mt-10 font-14">{{ $subscribe->days }} {{ trans('financial.days_of_subscription') }}</li>
                        <li class="mt-10 font-14">
                            @if($subscribe->infinite_use)
                            {{ trans('update.unlimited') }}
                            @else
                            {{ $subscribe->usable_count }}
                            @endif
                            <span class="ml-5">{{ trans('update.subscribes') }}</span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer p-0 bg-transparent border-0">
                    @if(auth()->check())
                    <form action="/panel/financial/pay-subscribes" method="post" class="w-100">
                        {{ csrf_field() }}
                        <input name="amount" value="{{ $subscribe->price }}" type="hidden">
                        <input name="id" value="{{ $subscribe->id }}" type="hidden">
                        <div class="d-flex align-items-center mt-25 w-100">
                            <button type="submit" class="btn btn-primary {{ !empty($subscribe->has_installment) ? '' : 'btn-block' }}">{{ trans('update.purchase') }}</button>
                            @if(!empty($subscribe->has_installment))
                            <a href="/panel/financial/subscribes/{{ $subscribe->id }}/installments" class="btn btn-outline-primary flex-grow-1 ml-10">{{ trans('update.installments') }}</a>
                            @endif
                        </div>
                    </form>
                    @else
                    <a href="/register?plan_id={{ $subscribe->id }}" class="btn btn-primary btn-block mt-25">{{ trans('update.purchase') }}</a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="swiper-pagination"></div>




                <div class="text-center">
          
          <p class="my-2">
              ✓ Secure Payment | ✓
              Instant Access | ✓
              Cancel Anytime
          </p>
      </div>

            </div>
            <div class="d-flex justify-content-center">
                <div class="swiper-pagination subscribes-swiper-pagination"></div>
            </div>

        </div>

  
</section>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>


<script>
  var testimonialSwiper = new Swiper(".testimonial-swiper", {
      slidesPerView: 1,
      spaceBetween: 20,
      loop: true,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
      navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
      },
      pagination: {
          el: ".swiper-pagination",
          clickable: true,
      },
      breakpoints: {
    768: { slidesPerView: 1 },    
    1200: { slidesPerView: 2 },   
    1201: { slidesPerView: 3 }, 
  }
  });
</script>

<script>
    var swiper = new Swiper(".subscribes-swiper", {
        slidesPerView: 1,
        spaceBetween: 10,
        loop: true,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        breakpoints: {
            640: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 2 }
        }
    });
</script>




@endsection
