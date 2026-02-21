<div
    x-data="{
        time: '',
        init() {
            this.updateTime()
            setInterval(() => this.updateTime(), 1000)
        },
        updateTime() {
            this.time = new Date().toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            })
        }
    }"
    class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200"
>

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        {{-- LEFT --}}
        <div>
            <p class="text-sm text-gray-500">Dashboard</p>

            <h1 class="text-2xl font-semibold text-gray-900">
                Welcome back,
                <span class="text-primary-600">
                    {{ auth()->user()->name }}
                </span>
            </h1>

            <p class="mt-1 text-sm text-gray-600">
                {{ now()->format('l, d F Y') }}
            </p>
        </div>

        {{-- RIGHT --}}
        <div class="text-right">
            <p class="text-xs uppercase tracking-widest text-gray-500">
                Current Time
            </p>

            <p
                x-text="time"
                class="mt-1 text-3xl font-bold text-gray-900 tabular-nums">
                --:--:--
            </p>
        </div>

    </div>
</div>