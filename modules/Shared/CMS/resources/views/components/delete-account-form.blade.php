
    <div class="form-container">
        @if (session()->has('message'))
            <div style="color: green;">
                {{ session('message') }}
            </div>
        @endif

        <p>Здесь вы можете запросить удаление вашего аккаунта, или вы можете удалить аккаунт в Профиле в вашем
            приложении</p>

        <form wire:submit="save">
            <div>
                <label>Введите ваш номер телефона</label>
                <input type="text" id="phone_number" wire:model="phone_number">
                @error('phone_number') <span>{{ $message }}</span> @enderror
            </div>
            <button type="submit">Запросить удаление аккаунта</button>
        </form>
    </div>
