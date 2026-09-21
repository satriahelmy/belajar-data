Chart yang terlihat menarik belum tentu membantu menjawab pertanyaan. Sebelum memilih bentuk visual, tentukan dulu apa yang dibandingkan, metric apa yang dipakai, dan konteks apa yang tidak boleh hilang.

## Mulai dari pertanyaan analitis

Stakeholder NusaMart berkata, “Kategori mana yang paling berkontribusi terhadap revenue?”

Pertanyaan itu mengarah pada perbandingan antar-kategori. Bar chart dapat membantu karena panjang bar membuat perbedaan antar-kategori mudah dibaca. Namun chart baru berguna setelah kita memilih **revenue** sebagai metric dan **category** sebagai dimensi.

:::callout type="common-mistake"
Jangan memilih chart karena bentuknya tersedia. Tanyakan dulu apa yang harus dibandingkan, dalam urutan apa, dan apakah angka di balik visual masih dapat diperiksa.
:::

## Bandingkan angka, bukan hiasan

Pada dataset NusaMart, kategori memiliki total revenue yang berbeda. Urutan descending membantu pembaca menemukan kontribusi terbesar lebih cepat, sedangkan highlight `top` menarik perhatian tanpa menghapus kategori lain.

:::visualization id="visual-question-01"
:::

Saat mencoba pilihan lain, perhatikan perubahan yang terjadi:

- Mengganti metric dari revenue ke quantity menjawab pertanyaan yang berbeda.
- Mengganti dimensi dari category ke region memindahkan fokus dari produk ke wilayah.
- Mengganti bar menjadi line tidak otomatis membuat data kategorikal menjadi data waktu.

## Skala dan tabel tetap penting

Sumbu yang dipotong dapat membuat selisih kecil tampak besar. Karena itu, pilihan skala harus dibaca bersama angka dan keterangan visualnya. Tabel di bawah visual berfungsi sebagai pemeriksaan: apakah urutan, nilai, dan jumlah kelompok sesuai dengan pertanyaan?

Visual menunjukkan pola yang terlihat pada data, bukan alasan mengapa pola itu terjadi. Revenue kategori yang lebih tinggi belum membuktikan bahwa kategori tersebut menyebabkan perubahan performa. Untuk menyusun finding, sebutkan metric, periode atau dimensi, pola yang terlihat, dan batas evidence-nya.

Pertanyaan visualnya sudah jelas. Berikutnya, gunakan visual untuk membandingkan perubahan lintas waktu dan tetap kembali ke angka yang mendasarinya.
