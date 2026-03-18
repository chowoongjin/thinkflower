@extends('layouts.admin')

@section('content')
    <div id="content__body">

        <div id="adminBanner">


            <form action="{{ route('admin.banner-set.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="column-2">
                    <div class="col">
                        <section>
                            <h2 class="tt">✔️ 공지사항 관리</h2>
                            <table class="table-data style6 mt20">
                                <colgroup>
                                    <col style="width:130px">
                                    <col style="">
                                </colgroup>
                                <tbody>
                                <tr>
                                    <th>주의사항</th>
                                    <td>배너 사이즈: 948px / 200px</td>
                                </tr>
                                </tbody>
                            </table>

                            <!-- 일관성 유지를 위해 table 처리 함 -->

                            <div class="mt20">
                                @for ($i = 1; $i <= 8; $i++)
                                    @php
                                        $item = $mainBanners[$i] ?? null;
                                        $fileName = !empty($item?->image_path) ? basename($item->image_path) : '';
                                    @endphp
                                    <table class="table-data style7 mt5">
                                        <colgroup>
                                            <col style="width:130px">
                                            <col style="">
                                            <col style="width:50px">
                                        </colgroup>
                                        <tbody>
                                        <tr>
                                            <th>메인배너 [{{ $i }}]</th>
                                            <td>
                                                <div class="attachment">
                                                    <input type="text" class="file-name" readonly value="{{ $fileName }}" placeholder="등록된 이미지가 없습니다">
                                                    <input type="file" class="file-input" name="main_banner_files[{{ $i }}]" style="display:none;">
                                                    <input type="hidden" name="main_banner_delete[{{ $i }}]" class="file-delete-flag" value="0">
                                                </div>
                                            </td>
                                            <td><button type="button" class="btn-del --attachment">삭제</button></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                @endfor
                            </div>

                        </section>
                    </div>
                    <div class="col">
                        <section>
                            <h2 class="tt">✔️ 팝업창 관리</h2>
                            <table class="table-data style6 mt20">
                                <colgroup>
                                    <col style="width:130px">
                                    <col style="">
                                </colgroup>
                                <tbody>
                                <tr>
                                    <th>주의사항</th>
                                    <td>배너 사이즈: 600px / 500px</td>
                                </tr>
                                </tbody>
                            </table>

                            <!-- 일관성 유지를 위해 table 처리 함 -->

                            <div class="mt20">
                                @for ($i = 1; $i <= 6; $i++)
                                    @php
                                        $item = $popupBanners[$i] ?? null;
                                        $fileName = !empty($item?->image_path) ? basename($item->image_path) : '';
                                    @endphp
                                    <table class="table-data style7 mt5">
                                        <colgroup>
                                            <col style="width:130px">
                                            <col style="">
                                            <col style="width:50px">
                                        </colgroup>
                                        <tbody>
                                        <tr>
                                            <th>팝업창 [{{ $i }}]</th>
                                            <td>
                                                <div class="attachment">
                                                    <input type="text" class="file-name" readonly value="{{ $fileName }}" placeholder="등록된 이미지가 없습니다">
                                                    <input type="file" class="file-input" name="popup_banner_files[{{ $i }}]" style="display:none;">
                                                    <input type="hidden" name="popup_banner_delete[{{ $i }}]" class="file-delete-flag" value="0">
                                                </div>
                                            </td>
                                            <td><button type="button" class="btn-del --attachment">삭제</button></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                @endfor
                            </div>

                        </section>

                    </div>

                </div>

                <div class="mt20">
                    <button type="submit" class="btn btn-fluid btn-primary" style="border-radius:0;">저장하기</button>
                </div>

            </form>



        </div>

    </div>

    <script>
        $(function () {
            @if (session('success'))
            alert(@json(session('success')));
            @endif

            $('.attachment').each(function () {
                const $attachment = $(this);

                $attachment.off('click.bannerOpen').on('click.bannerOpen', function (e) {
                    if ($(e.target).closest('.btn-del.--attachment').length) {
                        return;
                    }

                    e.preventDefault();
                    e.stopPropagation();

                    const input = $attachment.find('.file-input').get(0);
                    if (input) {
                        input.click();
                    }
                });

                $attachment.find('.file-input').off('click.bannerInput').on('click.bannerInput', function (e) {
                    e.stopPropagation();
                });

                $attachment.find('.file-input').off('change.bannerInput').on('change.bannerInput', function () {
                    const file = this.files && this.files[0] ? this.files[0] : null;

                    if (file) {
                        $attachment.find('.file-name').val(file.name);
                        $attachment.find('.file-delete-flag').val('0');
                    }
                });
            });

            $('.btn-del.--attachment').off('click.bannerDelete').on('click.bannerDelete', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const $table = $(this).closest('table');
                $table.find('.file-name').val('');
                $table.find('.file-input').val('');
                $table.find('.file-delete-flag').val('1');
            });
        });
    </script>
@endsection
