{{-- Status + actions + history of a research-data record (PRD §4.11). Needs verificationLogs.actor loaded. --}}
@props(['record'])

@php
    $type = $record->getMorphClass();
    $status = $record->status;
    $noteError = $errors->has('note');
@endphp

<x-ui.card title="Status Verifikasi" {{ $attributes }}>
    <div class="space-y-4">
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.badge :tone="$status->tone()" class="text-sm">{{ $status->label() }}</x-ui.badge>
            @if ($status === \App\Enums\VerificationStatus::Verified && $record->verified_at)
                <span class="text-xs text-muted">{{ $record->verified_at->translatedFormat('d M Y') }}{{ $record->verifier ? ' oleh '.$record->verifier->name : '' }}</span>
            @elseif ($status === \App\Enums\VerificationStatus::Submitted && $record->submitted_at)
                <span class="text-xs text-muted">diajukan {{ $record->submitted_at->translatedFormat('d M Y') }}</span>
            @endif
        </div>

        @if ($status === \App\Enums\VerificationStatus::NeedsRevision && $record->revision_note)
            <x-ui.alert tone="warning" title="Catatan revisi dari admin">
                <p class="whitespace-pre-line">{{ $record->revision_note }}</p>
            </x-ui.alert>
        @endif

        @if (auth()->user()->isDosen() && $record->isLockedForOwner())
            <p class="text-xs text-muted">
                {{ $status === \App\Enums\VerificationStatus::Submitted ? 'Data sedang diverifikasi' : 'Data sudah terverifikasi' }}
                sehingga tidak dapat diubah. Hubungi admin BP2M bila perlu perubahan.
            </p>
        @endif

        {{-- Actions --}}
        <div class="flex flex-col gap-2">
            @can('submit', $record)
                <form method="POST" action="{{ route('submissions.store', [$type, $record->getKey()]) }}" x-data="{ busy: false }" @submit="busy = true">
                    @csrf
                    <x-ui.button class="w-full" icon="heroicon-o-paper-airplane" x-bind:disabled="busy">Ajukan Verifikasi</x-ui.button>
                </form>
            @endcan

            @can('approve', $record)
                <form method="POST" action="{{ route('verifications.approve', [$type, $record->getKey()]) }}" x-data="{ busy: false }" @submit="busy = true">
                    @csrf
                    <x-ui.button class="w-full" icon="heroicon-o-check-badge" x-bind:disabled="busy">Verifikasi</x-ui.button>
                </form>
            @endcan

            @foreach ([
                'requestRevision' => ['verifications.return', 'Kembalikan untuk Revisi', 'heroicon-o-arrow-uturn-left'],
                'reopen' => ['verifications.reopen', 'Buka Kembali', 'heroicon-o-lock-open'],
            ] as $ability => [$routeName, $buttonLabel, $icon])
                @can($ability, $record)
                    <div x-data="{ open: @js($noteError) }">
                        <x-ui.button type="button" variant="secondary" class="w-full" :icon="$icon" x-on:click="open = true">{{ $buttonLabel }}</x-ui.button>
                        <x-ui.modal :title="$buttonLabel" description="Data dikembalikan ke dosen dengan status Perlu Revisi.">
                            <form method="POST" action="{{ route($routeName, [$type, $record->getKey()]) }}" x-data="{ busy: false }" @submit="busy = true">
                                @csrf
                                <div class="px-5 py-4">
                                    <x-form.field name="note" label="Catatan untuk dosen" required hint="Jelaskan apa yang perlu diperbaiki (maks 1000 karakter).">
                                        <x-form.textarea name="note" rows="4" maxlength="1000" required />
                                    </x-form.field>
                                </div>
                                <div class="flex flex-col-reverse gap-2 border-t border-line px-5 py-3 sm:flex-row sm:justify-end">
                                    <x-ui.button type="button" variant="ghost" x-on:click="open = false">Batal</x-ui.button>
                                    <x-ui.button x-bind:disabled="busy">{{ $buttonLabel }}</x-ui.button>
                                </div>
                            </form>
                        </x-ui.modal>
                    </div>
                @endcan
            @endforeach
        </div>

        {{-- History --}}
        @if ($record->verificationLogs->isNotEmpty())
            <div class="border-t border-line pt-4">
                <p class="mb-3 text-sm font-medium text-ink">Riwayat</p>
                <ol class="space-y-3">
                    @foreach ($record->verificationLogs as $log)
                        <li class="relative border-l-2 border-line pl-4">
                            <span class="absolute -left-[5px] top-1.5 size-2 rounded-full bg-muted/40"></span>
                            <p class="text-sm text-ink">
                                <x-ui.badge :tone="$log->to_status->tone()">{{ $log->to_status->label() }}</x-ui.badge>
                                <span class="text-xs text-muted">· {{ $log->actor?->name }}</span>
                            </p>
                            <p class="mt-0.5 text-xs text-muted">{{ $log->created_at?->translatedFormat('d M Y, H:i') }}</p>
                            @if ($log->note)
                                <p class="mt-1 whitespace-pre-line text-xs text-ink">{{ $log->note }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif
    </div>
</x-ui.card>
