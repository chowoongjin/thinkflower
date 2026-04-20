@extends('layouts.app')

@section('content')
    <div id="content__body" style="position:relative">

        <div id="content__head">
            <img src="{{ asset('assets/img/content_head3.png') }}" alt="">
        </div>

        <div id="marketingCenter">

            <ul class="list-block mt30">
                <li>
                    <div class="marketing-box">
                        <div class="marketing-box__left">
                            <div class="row">
                                <div class="col">
                                    <h3 class="row-title"><img src="{{ asset('assets/img/ico_bag.png') }}" alt="bag"> 영업용 쇼핑몰 무료제작</h3>
                                </div>
                                <div class="col">
                                    <span class="badge">제작비용 0원</span>
                                    <span class="badge">명함 디자인 무료</span>
                                </div>
                            </div>
                            <div class="row">
                                고객 및 지인들에게 가격표, 주문 방법 안내 등 반복적인 업무를 간단하게 해결할 수 있어요
                            </div>
                        </div>
                        <div class="marketing-box__right">
                            <a href="{{ route('shopping-mall-production') }}" class="btn btn-light">즉시제작 <i class="bi bi-arrow-right-short"></i></a>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="marketing-box">
                        <div class="marketing-box__left">
                            <div class="row">
                                <div class="col">
                                    <h3 class="row-title"><img src="{{ asset('assets/img/ico_speaker.png') }}" alt="speaker"> 지역카페 바이럴 마케팅</h3>
                                </div>
                                <div class="col">
                                    <span class="badge">입소문 마케팅</span>
                                    <span class="badge">소매 매출 상승</span>
                                    <span class="badge">국내 최저가</span>
                                </div>
                            </div>
                            <div class="row">
                                내가 꽃집을 운영하는 지역 카페에 쉽고 저렴하게 입소문을 퍼트릴 수 있어요
                            </div>
                        </div>
                        <div class="marketing-box__right">
                            <a href="{{ route('regional-cafe-viral-marketing') }}" class="btn btn-light">바로가기 <i class="bi bi-arrow-right-short"></i></a>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="marketing-box">
                        <div class="marketing-box__left">
                            <div class="row">
                                <div class="col">
                                    <h3 class="row-title"><img src="{{ asset('assets/img/ico_place.png') }}" alt="place"> 네이버 플레이스 부스터</h3>
                                </div>
                                <div class="col">
                                    <span class="badge">지도 순위상승</span>
                                    <span class="badge">소매 매출 상승</span>
                                    <span class="badge">국내 최저가</span>
                                </div>
                            </div>
                            <div class="row">
                                우리 꽃집 근처의 고객이 지도에 ‘꽃집’을 검색 했을 때 상단에 나타날 수 있어요
                            </div>
                        </div>
                        <div class="marketing-box__right">
                            <a href="{{ route('naver-place-booster') }}" class="btn btn-light">바로가기 <i class="bi bi-arrow-right-short"></i></a>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="marketing-box">
                        <div class="marketing-box__left">
                            <div class="row">
                                <div class="col">
                                    <h3 class="row-title"><img src="{{ asset('assets/img/ico_kakao.png') }}" alt="kakao"> 카카오 플러스친구 부스터</h3>
                                </div>
                                <div class="col">
                                    <span class="badge">신뢰도 상승</span>
                                    <span class="badge">소매 매출 상승</span>
                                    <span class="badge">국내 최저가</span>
                                </div>
                            </div>
                            <div class="row">
                                꽃집의 신뢰도와 비례하는 카카오 비즈니스 친구 수를 간편하게 증가 시킬 수 있어요
                            </div>
                        </div>
                        <div class="marketing-box__right">
                            <a href="{{ route('kakao-plus-friend-booster') }}" class="btn btn-light">바로가기 <i class="bi bi-arrow-right-short"></i></a>
                        </div>
                    </div>
                </li>
            </ul>

        </div>

    </div>
@endsection
