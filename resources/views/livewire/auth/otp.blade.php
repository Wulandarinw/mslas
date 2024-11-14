<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
    <div class="flex h-full items-center">
        <main class="w-full max-w-md mx-auto p-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="p-4 sm:p-7">
                    <div class="text-center">
                        <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">Verify OTP</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Please enter the verification code sent to {{ $email }}
                        </p>
                    </div>

                    <hr class="my-5 border-slate-300">

                    <!-- Form -->
                    <form wire:submit.prevent="verify">
                        @if ($errors->has('verification'))
                            <div class="bg-red-500 text-sm text-white rounded-lg p-4 mb-4" role="alert">
                                {{ $errors->first('verification') }}
                            </div>
                        @endif

                        <div class="grid gap-y-4">
                            <!-- OTP Input Group -->
                            <div>
                                <label class="block text-sm mb-2 dark:text-white">Enter 6-digit code</label>
                                <div class="flex justify-center gap-2">
                                    @foreach ($otp as $index => $digit)
                                        <input type="text" maxlength="1" inputmode="numeric"
                                            wire:model="otp.{{ $index }}" x-data
                                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '');
                    if ($el.value.length === 1) {
                        $el.nextElementSibling?.focus()
                    }
                "
                                            x-on:keydown.backspace="
                    if ($el.value.length === 0) {
                        $el.previousElementSibling?.focus()
                    }
                "
                                            class="otp-input w-12 h-12 text-center border border-gray-200 rounded-lg text-lg focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 @error('otp.' . $index) border-red-500 @enderror">
                                    @endforeach
                                </div>
                                @error('otp.*')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                Verify Code
                            </button>

                            <div class="text-center">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Didn't receive the code?
                                    <button type="button" wire:click="resendOTP"
                                        @if ($resendDisabled) disabled @endif
                                        class="text-blue-600 decoration-2 hover:underline font-medium dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600 disabled:opacity-50 disabled:pointer-events-none">
                                        Resend {{ $resendDisabled ? "($countdown)" : '' }}
                                    </button>
                                </p>
                            </div>
                        </div>
                    </form>
                    <!-- End Form -->
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    // Hapus event listener yang lama
    document.addEventListener('livewire:load', function () {
        // Fokus ke input pertama saat halaman dimuat
        document.querySelector('.otp-input')?.focus();
    });

    // Kode countdown timer tetap sama
    window.addEventListener('start-countdown', event => {
        let countdown = 60;
        const timer = setInterval(() => {
            countdown--;
            @this.set('countdown', countdown);
            
            if (countdown <= 0) {
                clearInterval(timer);
                @this.set('resendDisabled', false);
            }
        }, 1000);
    });

    window.addEventListener('otp-resent', event => {
        alert(event.detail.message);
    });
</script>
