@extends('layouts.app')

@section('content')
    <div id="content__head">
        <img src="{{ asset('assets/img/content_head2.png') }}" alt="">
    </div>
    <div id="content__body" class="p30 pt10">

        <table class="table-data style2 mt20">
            <caption>게시물 상세보기</caption>
            <colgroup>
                <col style="width:70px">
                <col>
                <col style="width:70px">
                <col>
                <col style="width:70px">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th>제목</th>
                <td colspan="5">{{ $announcement->title }}</td>
            </tr>
            <tr>
                <th>작성자</th>
                <td>
                    {{ $announcement->creator->name ?? '운영자' }}
                    ({{ $announcement->creator->login_id ?? 'admin' }})
                </td>
                <th>조회/추천</th>
                <td>{{ number_format((int) $announcement->view_count) }}/{{ number_format((int) $announcement->recommend_count) }}</td>
                <th>등록시간</th>
                <td>{{ optional($announcement->created_at)->format('Y-m-d H:i:s') }}</td>
            </tr>
            </tbody>
        </table>

        <div class="mt20" style="white-space: pre-line;">{{ $announcement->content }}</div>

        <div class="prevNextArticle">
            <ul>
                <li>
                    @if ($prevAnnouncement)
                        <a href="{{ route('announcement.show', $prevAnnouncement) }}">{{ $prevAnnouncement->title }}</a>
                        <span>{{ optional($prevAnnouncement->created_at)->format('y-m-d') }}</span>
                    @else
                        <a href="#none">이전글이 없습니다</a>
                        <span>-</span>
                    @endif
                </li>
                <li>
                    @if ($nextAnnouncement)
                        <a href="{{ route('announcement.show', $nextAnnouncement) }}">{{ $nextAnnouncement->title }}</a>
                        <span>{{ optional($nextAnnouncement->created_at)->format('y-m-d') }}</span>
                    @else
                        <a href="#none">다음글이 없습니다</a>
                        <span>-</span>
                    @endif
                </li>
            </ul>
        </div>

        <div class="flex mt boardAction">
            <div class="flex__col">
                <a href="{{ route('announcement.index') }}" class="btn btn-primary">목록</a>
            </div>
            <div class="flex__col">
                @php $isAdmin = auth()->check() && in_array(auth()->user()->role, ['admin', 'hq'], true); @endphp
                @if ($isAdmin)
                    <a href="#none" class="btn btn-secondary">수정</a>
                    <a href="#none" class="btn btn-danger">삭제</a>
                @endif
            </div>
        </div>
    </div>
@endsection
