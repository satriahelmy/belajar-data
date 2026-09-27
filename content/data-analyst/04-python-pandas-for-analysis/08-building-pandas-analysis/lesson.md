Sebuah analisis Pandas yang baik adalah rangkaian langkah yang dapat dibaca ulang: memahami sumber, membentuk ukuran, memberi konteks, meringkas, lalu memeriksa finding.

## Susun alur dari pertanyaan

Mulai dari pertanyaan NusaMart: bagaimana performa September dan kategori mana yang berkontribusi? Alurnya adalah membaca dua tabel, membuat revenue dan month, merge category, filter September, lalu groupby category.

:::python-practice id="python-analysis-08"
:::

## Tulis finding dengan batas evidence

Hasil ringkasan dapat menunjukkan total revenue dan kategori terbesar. Finding yang baik menyebutkan periode, measure, angka, dan batas data. Jangan mengubah kategori terbesar menjadi klaim bahwa kategori itu menyebabkan perubahan.

Jika langkah analisis dapat dijalankan ulang dan setiap transformasi punya alasan, notebook menjadi catatan kerja yang dapat diperiksa, bukan hanya tempat menyimpan angka akhir.

Gunakan latihan terakhir untuk menghubungkan langkah teknis dengan keputusan komunikasi sebelum masuk ke challenge module.
