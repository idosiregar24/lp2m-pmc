---
name: blade-component
description: Use when creating a new reusable Blade component in resources/views/components (ui/, form/, shared/, layouts/) for the BP2M PMC system — e.g. a button, badge, table, modal, form field, contributors field, documents list, stat card, or filter bar. Keeps components consistent with the project's Tailwind v4 tokens, Alpine.js, and prop conventions instead of each one inventing its own pattern.
---

# Resep Komponen Blade

Untuk komponen yang dipakai ulang di `resources/views/components/`.
Visual mengikuti `.claude/rules/design-standards.md`; struktur folder
mengikuti `.claude/rules/frontend-standards.md` §1.

## 1. Tentukan lokasi

| Folder | Isi | Contoh |
|---|---|---|
| `components/ui/` | Primitif visual tanpa pengetahuan domain | `button`, `badge`, `card`, `table`, `modal`, `alert`, `empty-state`, `stat-card`, `tabs` |
| `components/form/` | Elemen form + error | `label`, `input`, `select`, `textarea`, `file`, `error`, `field` |
| `components/shared/` | Gabungan yang tahu domain BP2M, dipakai ≥2 modul | `page-header`, `filter-bar`, `contributors-field`, `documents-list`, `detail-list`, `delete-button` |
| `components/layouts/` | Kerangka halaman | `app`, `guest` |

Yang hanya dipakai satu modul → partial di folder view modul itu
(`researches/_filters.blade.php`), bukan komponen. Naikkan ke `shared/`
saat modul kedua membutuhkannya.

## 2. Anonymous dulu, class bila perlu PHP

- **Default: anonymous component** (`resources/views/components/ui/badge.blade.php`),
  props lewat `@props`.
- **Class component** (`php artisan make:component Shared/ContributorsField`)
  hanya bila butuh logika PHP yang tidak pantas di view (menyiapkan opsi
  dosen, memetakan `old()` bersarang). Tetap tanpa query di `render()` —
  data dikirim dari controller.

## 3. Konvensi props & atribut

```blade
@props([
    'tone' => 'neutral',   // success|warning|danger|info|neutral|brand
])

@php
    $tones = [
        'success' => 'bg-success/10 text-success-ink',
        'warning' => 'bg-warning/10 text-warning-ink',
        'danger'  => 'bg-danger/10 text-danger-ink',
        'info'    => 'bg-info/10 text-info-ink',
        'brand'   => 'bg-brand-50 text-brand-700',
        'neutral' => 'bg-canvas text-muted',
    ];
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', $tones[$tone] ?? $tones['neutral']]) }}>
    {{ $slot }}
</span>
```

- Props eksplisit dengan default di `@props`; nama prop camelCase di PHP,
  kebab-case di pemanggilan (`:per-page`).
- Selalu teruskan `$attributes` (`->class([...])` / `->merge([...])`) agar
  pemanggil bisa menambah `class`, `id`, `x-*`, `wire:`-less atribut lain.
- Variasi visual lewat prop (`variant`, `tone`, `size`) yang dipetakan ke
  daftar class **token** — tidak menerima class warna dari luar sebagai
  pengganti.
- Konten lewat `$slot` / slot bernama (`<x-slot:actions>`), bukan prop
  berisi HTML.
- Ikon diterima sebagai nama heroicon (`icon="heroicon-o-plus"`) lalu
  dirender `<x-dynamic-component :component="$icon" class="size-4" />`.

## 4. Form component

- `name` wajib; `id` default = `name` yang di-dot (`contributors.0.name` → `contributors_0_name`).
- Nilai: `old($dotName, $value)`.
- Error otomatis: `$errors->has($dotName)` → border `border-danger` +
  `aria-invalid="true"` + `aria-describedby` ke pesan error.
- `<x-form.label required>` menambahkan `<span class="text-danger-ink" aria-hidden="true">*</span>`
  dan teks `sr-only` "wajib".
- `<x-form.select>` menerima `:options="[id => label]"` + `placeholder`
  ("Pilih …"); jangan query di dalam komponen.

## 5. Alpine di komponen

- State kecil inline (`x-data="{ open: false }"`) boleh.
- Logika >10 baris → `resources/js/components/{namaCamel}.js`, daftar di
  `app.js` lewat `Alpine.data()`, panggil `x-data="contributorsField(@js($initial))"`.
- Data server → Alpine hanya lewat `@js()`.

## 6. Aksesibilitas

- Elemen interaktif = `<button>`/`<a>` asli, bukan `<div @click>`.
- Modal: `role="dialog"`, `aria-modal`, fokus ke tombol pertama, `Esc`
  menutup, `x-trap` (plugin `@alpinejs/focus`).
- Tombol ikon-saja punya `aria-label` Bahasa Indonesia.
- Kontras teks status pakai `*-ink`, bukan warna latar.

## 7. Selesai bila

- Dipakai minimal di satu halaman nyata (bukan dibuat "untuk nanti").
- Tidak ada hex/warna default Tailwind (`grep -nE "#[0-9a-fA-F]{3,6}|(gray|blue|red|green|slate)-[0-9]" resources/views/components`).
- Tampil benar di 375px dan 1280px; `npm run build` bersih.
- Bila komponennya baru di tabel `design-standards.md` §2, tambahkan barisnya.
