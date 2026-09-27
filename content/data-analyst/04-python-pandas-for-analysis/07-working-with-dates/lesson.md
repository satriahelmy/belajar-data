Tanggal memberi urutan waktu, tetapi analisis periode memerlukan definisi yang konsisten. Teks tanggal harus diproses sebelum dipakai sebagai dasar perbandingan.

## Parse lalu turunkan periode

Ubah order_date menjadi datetime, kemudian buat month. Dengan kolom ini, semua transaksi dapat dipetakan ke periode yang sama tanpa mengandalkan potongan teks yang mudah salah.

:::python-practice id="python-dates-07"
:::

## Bandingkan cakupan yang sepadan

Perbandingan August dan September harus memakai measure yang sama, yaitu revenue, serta definisi periode yang sama. Jangan membandingkan total satu bulan dengan satu transaksi atau memakai tanggal yang belum lengkap.

Di fixture NusaMart, August memiliki revenue 450 dan September 600. Angka ini menunjukkan perbedaan pada data yang tersedia. Angka ini belum menjelaskan mengapa perbedaan itu terjadi.

Kolom tanggal yang rapi membantu analisis berikutnya, tetapi batas waktu dan cakupan data tetap harus disebutkan dalam finding.
