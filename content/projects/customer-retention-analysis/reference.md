# Reference Approach

## 1. Tetapkan definisi

Definisi sederhana yang dapat diuji adalah customer yang aktif pada Agustus dan kembali melakukan order pada September, dibagi jumlah customer aktif pada Agustus.

## 2. Bentuk cohort

Orders menghasilkan cohort Agustus `{C001, C002}`. Customer yang juga muncul pada September adalah `{C001, C002}`. Jadi numerator 2 dan denominator 2.

Retention comparison = `2 / 2 = 100%`.

## 3. Baca segment dengan hati-hati

Customers memberi konteks segment dan kota, tetapi dataset terlalu kecil untuk menjelaskan penyebab churn. Segment yang berbeda dapat menjadi arah pertanyaan berikutnya, bukan kesimpulan sebab.

## 4. Batas dan next step

Periode dua bulan tidak cukup untuk melihat retention jangka panjang. Langkah lanjutan yang wajar adalah memperluas cohort, menetapkan window retention, memeriksa customer yang tidak kembali, dan menguji apakah pola bertahan di segment atau periode lain.

SQL, Pandas, spreadsheet, dan Tableau dapat menghasilkan workflow yang berbeda. Yang penting adalah definisi, denominator, evidence, dan batas interpretasi tetap terlihat.
