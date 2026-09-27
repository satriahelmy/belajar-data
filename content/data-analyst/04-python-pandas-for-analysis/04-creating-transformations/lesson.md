Kolom turunan membuat definisi ukuran terlihat di dalam data. Dengan begitu, langkah hitung dapat diperiksa dan diulang.

## Dari dua kolom menjadi ukuran

NusaMart tidak menyimpan revenue sebagai kolom sumber. Revenue harus dihitung dari quantity dikali unit_price. Definisi ini penting karena angka yang sama dapat berarti berbeda jika unitnya berubah.

:::python-practice id="python-transform-04"
:::

## Simpan konteks periode

Tanggal dalam bentuk teks belum otomatis menjadi periode yang dapat dibandingkan. Parse order_date, lalu buat month agar filter dan ringkasan memakai definisi periode yang sama.

Periksa beberapa baris hasil transformasi. Jika revenue atau month tidak masuk akal, jangan lanjut ke groupby. Kesalahan pada kolom turunan akan terlihat meyakinkan ketika sudah diringkas, tetapi tetap salah.

Transformasi yang baik menambah konteks tanpa menghapus kolom sumber yang diperlukan untuk pemeriksaan.
