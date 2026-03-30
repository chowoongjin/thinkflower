<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $taxEmailId = trim((string) ($this->tax_email_id ?? ''));
        $taxEmailDomain = trim((string) ($this->tax_email_domain ?? ''));

        $this->merge([
            'tax_email' => ($taxEmailId === '' && $taxEmailDomain === '')
                ? ''
                : $taxEmailId . '@' . $taxEmailDomain,
            'business_no' => preg_replace('/[^0-9]/', '', (string) $this->business_no),
            'main_phone' => preg_replace('/[^0-9]/', '', (string) $this->main_phone),
            'fax' => preg_replace('/[^0-9]/', '', (string) $this->fax),
            'bank_account' => preg_replace('/[^0-9]/', '', (string) $this->bank_account),
        ]);
    }

    public function rules(): array
    {
        return [
            'login_id' => ['required', 'string', 'min:4', 'max:50', 'unique:users,login_id'],
            'password' => ['required', 'string', 'min:5', 'confirmed'],
            'password_confirmation' => ['required'],

            'business_no' => ['required', 'string', 'size:10', 'unique:shops,business_no'],
            'owner_name' => ['required', 'string', 'max:50'],
            'business_addr1' => ['required', 'string', 'max:255'],
            'business_addr2' => ['required', 'string', 'max:255'],
            'business_license' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/gif,image/webp,application/pdf'],
            'tax_email' => ['required', 'email:rfc', 'max:100'],

            'shop_name' => ['required', 'string', 'max:100'],
            'career_years_label' => ['required', 'string', 'max:30'],
            'main_phone' => ['required', 'string', 'between:9,11'],
            'fax' => ['required', 'string', 'between:9,12'],

            'products' => ['required', 'string', 'max:255'],
            'delivery_areas' => ['required', 'string'],

            'bank_name' => ['required', 'string', 'max:50'],
            'bank_holder' => ['required', 'string', 'max:100'],
            'bank_account' => ['required', 'string', 'max:50'],

            'agree_service' => ['accepted'],
            'agree_privacy' => ['accepted'],
            'agree_third_party' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'login_id.required' => '아이디를 입력해 주세요.',
            'login_id.unique' => '이미 사용 중인 아이디입니다.',

            'password.required' => '비밀번호를 입력해 주세요.',
            'password_confirmation.required' => '비밀번호 확인을 입력해 주세요.',
            'password.confirmed' => '비밀번호 확인이 일치하지 않습니다.',

            'business_no.required' => '사업자등록번호를 입력해 주세요.',
            'business_no.size' => '사업자등록번호는 숫자 10자리여야 합니다.',
            'business_no.unique' => '이미 가입된 사업자등록번호입니다.',

            'owner_name.required' => '대표자명을 입력해 주세요.',
            'business_addr1.required' => '주소를 입력해 주세요.',
            'business_addr2.required' => '상세주소를 입력해 주세요.',

            'business_license.required' => '사업자등록증을 업로드해 주세요.',
            'business_license.mimetypes' => '사업자등록증은 이미지 또는 PDF 파일만 업로드할 수 있습니다.',

            'tax_email.required' => '계산서 수령 이메일을 입력해 주세요.',
            'tax_email.email' => '계산서 수령 이메일 형식이 올바르지 않습니다.',

            'shop_name.required' => '화원사명을 입력해 주세요.',
            'career_years_label.required' => '화원 운영경력을 선택해 주세요.',
            'main_phone.required' => '대표 연락망을 입력해 주세요.',
            'fax.required' => '팩스 수신번호를 입력해 주세요.',

            'products.required' => '수주 취급상품을 선택해 주세요.',
            'delivery_areas.required' => '배송 가능지역을 선택해 주세요.',

            'bank_name.required' => '입금은행을 선택해 주세요.',
            'bank_holder.required' => '예금주명을 입력해 주세요.',
            'bank_account.required' => '입금 계좌번호를 입력해 주세요.',

            'agree_service.accepted' => '서비스 이용약관에 동의해 주세요.',
            'agree_privacy.accepted' => '개인정보 수집 및 이용에 동의해 주세요.',
            'agree_third_party.accepted' => '개인정보 제3자 제공에 동의해 주세요.',
        ];
    }
}
