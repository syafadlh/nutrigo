@extends('onboarding.layout')
@php $currentStep = 1; @endphp

@section('content')
<div class="bg-white rounded-3xl shadow-xl p-8">
    <div class="text-center mb-8">
        <span class="text-5xl">👋</span>
        <h2 class="text-2xl font-bold text-gray-800 mt-3">Halo! Kenalan dulu yuk</h2>
        <p class="text-gray-500 mt-2">Kami ingin mengenalmu lebih dekat</p>
    </div>

    <form method="POST" action="{{ route('onboarding.save.1') }}">
        @csrf
        <div class="space-y-5">
            <div>
                <label class="text-sm font-semibold text-gray-700 block mb-2">
                    Kamu mau dipanggil apa? 😊
                </label>
                <input type="text" name="nickname" value="{{ old('nickname', $user->nickname) }}"
                       class="input-field" placeholder="Panggil aku..." required>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700 block mb-2">Tanggal Lahir</label>
                <div class="grid grid-cols-3 gap-3">
                    <select name="birth_day" class="input-field" required>
                        <option value="">Hari</option>
                        @for($d=1; $d<=31; $d++)
                            <option value="{{ $d }}" {{ old('birth_day', optional($user->birth_date)->day) == $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endfor
                    </select>
                    <select name="birth_month" class="input-field" required>
                        <option value="">Bulan</option>
                        @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'] as $i => $m)
                            <option value="{{ $i+1 }}" {{ old('birth_month', optional($user->birth_date)->month) == $i+1 ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                    <select name="birth_year" class="input-field" required>
                        <option value="">Tahun</option>
                        @for($y=date('Y')-80; $y<=date('Y')-5; $y++)
                            <option value="{{ $y }}" {{ old('birth_year', optional($user->birth_date)->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700 block mb-2">Jenis Kelamin</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer
                        {{ old('gender', $user->gender) == 'male' ? 'border-ng-orange bg-orange-50' : 'border-gray-200 hover:border-ng-orange' }}">
                        <input type="radio" name="gender" value="male" class="hidden" {{ old('gender', $user->gender) == 'male' ? 'checked' : '' }}>
                        <span class="text-2xl">👨</span>
                        <span class="font-semibold text-gray-700">Laki-laki</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer
                        {{ old('gender', $user->gender) == 'female' ? 'border-ng-orange bg-orange-50' : 'border-gray-200 hover:border-ng-orange' }}">
                        <input type="radio" name="gender" value="female" class="hidden" {{ old('gender', $user->gender) == 'female' ? 'checked' : '' }}>
                        <span class="text-2xl">👩</span>
                        <span class="font-semibold text-gray-700">Perempuan</span>
                    </label>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="mt-4 bg-red-50 border border-red-200 rounded-xl p-3">
                @foreach($errors->all() as $e)<p class="text-red-600 text-sm">⚠️ {{ $e }}</p>@endforeach
            </div>
        @endif

        <div class="mt-8 flex justify-end">
            <button type="submit" class="btn-primary">Selanjutnya →</button>
        </div>
    </form>
</div>

<script>
// Auto-combine birth date
document.querySelector('form').addEventListener('submit', function(e) {
    const d = document.querySelector('[name=birth_day]').value;
    const m = document.querySelector('[name=birth_month]').value;
    const y = document.querySelector('[name=birth_year]').value;
    if (d && m && y) {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'birth_date';
        hidden.value = `${y}-${String(m).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        this.appendChild(hidden);
    }
});
// Radio button visual feedback
document.querySelectorAll('input[type=radio]').forEach(r => {
    r.addEventListener('change', function() {
        document.querySelectorAll('input[type=radio][name=gender]').forEach(x => {
            x.closest('label').classList.remove('border-ng-orange','bg-orange-50');
            x.closest('label').classList.add('border-gray-200');
        });
        this.closest('label').classList.add('border-ng-orange','bg-orange-50');
    });
});
</script>
@endsection