<div id="modal-loading-common" style="display:none;">
    <div class="loading-modal-backdrop"></div>

    <div class="loading-modal-dialog">
        <div class="align-center">
            <img src="{{ asset('assets/img/symbol.png') }}" height="46">
            <h1 class="fs30 fw600" style="color:#222;" id="loading-modal-title">
                처리 중입니다.<br>잠시만 기다려주세요
            </h1>
            <p class="mt15 fs18 color-green" id="loading-modal-desc">
                내부 시스템 연동으로 시간이 소요됩니다.
            </p>

            <div class="dot-loading">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
</div>

<style>
    #modal-loading-common {
        position: fixed;
        inset: 0;
        z-index: 99999;
    }

    #modal-loading-common .loading-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
    }

    #modal-loading-common .loading-modal-dialog {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 420px;
        max-width: calc(100vw - 40px);
        background: #fff;
        border-radius: 16px;
        padding: 40px 24px;
        box-sizing: border-box;
        z-index: 2;
    }
</style>

<script>
    function openLoadingModal(title = null, desc = null) {
        const defaultTitle = '처리 중입니다.<br>잠시만 기다려주세요';
        const defaultDesc = '내부 시스템 연동으로 시간이 소요됩니다.';

        $('#loading-modal-title').html(title || defaultTitle);
        $('#loading-modal-desc').text(desc || defaultDesc);

        $('#modal-loading-common').show();
        $('body').addClass('overflow-hidden');
    }

    function closeLoadingModal() {
        $('#modal-loading-common').hide();
        $('body').removeClass('overflow-hidden');
    }
</script>
