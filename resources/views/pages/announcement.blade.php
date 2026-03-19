@extends('layouts.app')

@section('content')
    <div id="content__head">
        <img src="{{ asset('assets/img/content_head2.png') }}" alt="">
    </div>
    <div id="content__body" class="p30 pt10">

        <form action="{{ route('announcement.index') }}" method="GET" class="board-search">
            <div class="col">
                <select name="option">
                    <option value="subject" {{ $option === 'subject' ? 'selected' : '' }}>제목</option>
                    <option value="content" {{ $option === 'content' ? 'selected' : '' }}>내용</option>
                </select>
            </div>
            <div class="col">
                <input type="text" name="keyword" id="keyword" value="{{ $keyword }}" placeholder="검색어를 입력하세요">
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary">검색</button>
            </div>
        </form>

        <table class="table-default mt20">
            <caption>공지사항 게시판</caption>
            <colgroup>
                <col style="width:70px">
                <col>
                <col style="width:100px">
                <col style="width:80px">
            </colgroup>
            <thead>
            <tr>
                <th class="align-center">순번</th>
                <th class="align-left">제목</th>
                <th>작성기간</th>
                <th class="align-center">조회수</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($announcements as $announcement)
                <tr>
                    <td class="align-center">{{ $announcements->total() - (($announcements->currentPage() - 1) * $announcements->perPage()) - $loop->index }}</td>
                    <td>
                        <a href="{{ route('announcement.show', $announcement) }}">
                            {{ $announcement->title }}
                        </a>
                    </td>
                    <td>{{ optional($announcement->created_at)->format('Y-m-d') }}</td>
                    <td class="align-center">{{ number_format((int) $announcement->view_count) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="align-center">등록된 공지사항이 없습니다.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if ($announcements->hasPages())
            <div class="mt20">
                {{ $announcements->links('vendor.pagination.custom') }}
            </div>
        @endif

    </div>
@endsection
