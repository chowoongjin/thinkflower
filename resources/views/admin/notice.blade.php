@extends('layouts.admin')

@section('content')
    <div id="content__body">
        <div id="all-balju">

            <section>
                <h2 class="tt">✔️ 공지사항 관리</h2>
            </section>

            <form action="{{ route('admin.notice.store') }}" method="POST">
                @csrf

                <section class="mt20">
                    <table class="table-data style6">
                        <colgroup>
                            <col style="width:130px">
                            <col style="">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>주의사항</th>
                            <td>
                                칸 안에 내용이 전부 들어가야 정상적으로 작동됩니다
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </section>

                <section class="mt20">
                    <table class="table-data style6">
                        <colgroup>
                            <col style="width:130px">
                            <col style="">
                        </colgroup>
                        <tbody>
                        @foreach ($generalRows as $index => $row)
                            <tr>
                                @if ($index === 0)
                                    <th rowspan="3">본부공지사항</th>
                                @endif
                                <td>
                                    <input type="hidden" name="general[{{ $index }}][id]" value="{{ $row['id'] }}">
                                    <input
                                        type="text"
                                        name="general[{{ $index }}][title]"
                                        value="{{ old("general.$index.title", $row['title']) }}"
                                        placeholder="공지사항을 입력해주세요"
                                    >
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </section>

                <section class="mt20">
                    <table class="table-data style6">
                        <colgroup>
                            <col style="width:130px">
                            <col style="">
                        </colgroup>
                        <tbody>
                        @foreach ($specialRows as $index => $row)
                            <tr>
                                @if ($index === 0)
                                    <th rowspan="3"><span class="color-orange">특별</span>공지사항</th>
                                @endif
                                <td>
                                    <div class="input-group-pin">
                                        <input type="hidden" name="special[{{ $index }}][id]" value="{{ $row['id'] }}">
                                        <input
                                            type="hidden"
                                            name="special[{{ $index }}][is_pinned]"
                                            class="notice-pin-hidden"
                                            value="{{ old("special.$index.is_pinned", $row['is_pinned']) ? 1 : 0 }}"
                                        >
                                        <input
                                            type="text"
                                            name="special[{{ $index }}][title]"
                                            value="{{ old("special.$index.title", $row['title']) }}"
                                            placeholder="공지사항을 입력해주세요"
                                        >
                                        <span>
                                        <button
                                            type="button"
                                            class="btn btn-pin {{ old("special.$index.is_pinned", $row['is_pinned']) ? 'active' : '' }}"
                                        >
                                            <i class="bi bi-pin-angle-fill"></i> 상단고정
                                        </button>
                                    </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="mt20">
                        <button type="submit" class="btn btn-primary btn-fluid" style="border-radius:0">저장하기</button>
                    </div>
                </section>
            </form>

        </div>
    </div>

    <script>
        $(function () {
            @if (session('success'))
            alert(@json(session('success')));
            @endif

            $(document).on('click', '.btn-pin', function () {
                const $btn = $(this);
                const $wrap = $btn.closest('.input-group-pin');
                const $hidden = $wrap.find('.notice-pin-hidden');

                const isActive = $btn.hasClass('active');

                if (isActive) {
                    $btn.removeClass('active');
                    $hidden.val('0');
                } else {
                    $btn.addClass('active');
                    $hidden.val('1');
                }
            });
        });
    </script>
@endsection
