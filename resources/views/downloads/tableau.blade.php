@extends('layouts.public', ['active' => 'learn'])

@section('title', 'Dataset Tableau NusaMart | BelajarData')

@section('content')
    <main class="page-shell site-container">
        <a class="back-link" href="{{ route('learning.module', ['moduleKey' => '10-tableau-for-data-analysis']) }}"><span aria-hidden="true">←</span> Kembali ke Tableau</a>
        <header class="page-intro page-intro--compact">
            <p class="eyebrow">Dataset untuk Tableau</p>
            <h1>NusaMart {{ strtoupper($datasetVersion) }}</h1>
            <p class="lede">Dataset fiktif untuk mengikuti workflow Tableau di BelajarData. Versi dan checksum di bawah membantu memastikan file yang digunakan sesuai dengan latihan.</p>
        </header>

        <section class="dataset-download" aria-labelledby="dataset-files-title">
            <div class="section-heading-row">
                <div>
                    <p class="eyebrow">File tersedia</p>
                    <h2 id="dataset-files-title">Download CSV</h2>
                </div>
                <span class="quiet-note">Versi {{ $datasetVersion }}</span>
            </div>
            <div class="dataset-download__table-wrap">
                <table class="data-table dataset-download__table">
                    <thead><tr><th scope="col">File</th><th scope="col">Ukuran</th><th scope="col">SHA-256</th><th scope="col"><span class="sr-only">Aksi</span></th></tr></thead>
                    <tbody>
                        @foreach ($files as $file)
                            <tr>
                                <th scope="row">{{ $file['name'] }}</th>
                                <td>{{ number_format((int) $file['bytes']) }} bytes</td>
                                <td><code>{{ $file['sha256'] }}</code></td>
                                <td><a class="button button--quiet" href="{{ $file['url'] }}">Download CSV</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="soft-guidance">
            <strong>Gunakan data yang sesuai konteks.</strong>
            <p>File transactions dan products dipakai untuk workflow Revenue Slowdown. File orders dan customers dipakai untuk contoh Customer Retention. Jangan mengunggah data perusahaan, pribadi, atau rahasia ke Tableau Public.</p>
        </aside>
    </main>
@endsection
