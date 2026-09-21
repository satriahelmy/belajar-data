Satu sample dapat membantu kita memperkirakan keadaan yang lebih besar, tetapi sample itu sendiri bukan seluruh population. Karena itu, analyst perlu melihat seberapa jauh hasil sample dapat berubah.

## Mulai dari unit yang ingin dipahami

NusaMart memiliki delapan order pada fixture latihan. Jika kita menghitung seluruh order, rata-rata revenue per order adalah **131.25**. Angka ini adalah **population mean** untuk data yang sedang kita lihat.

Namun, dalam pekerjaan nyata kita sering hanya memeriksa sebagian baris. Sample berukuran tiga mungkin menghasilkan rata-rata 120, 150, atau angka lain, tergantung baris yang terpilih. Sample mean adalah ringkasan dari sample itu, bukan janji bahwa seluruh population memiliki nilai yang sama.

## Sample berbeda, estimasi dapat berbeda

:::callout type="common-mistake"
Jangan menyimpulkan bahwa sample mean pasti sama dengan population mean. Sample mean adalah estimasi yang dipengaruhi oleh baris yang masuk ke sample.
:::

Gunakan latihan berikut untuk mengambil beberapa sample berulang dari metric yang sama. Seed membuat urutan sample dapat diulang ketika kita ingin memeriksa hasilnya kembali.

:::sampling-practice id="sampling-uncertainty-01"
:::

Perhatikan tiga hal saat mencoba pengaturan yang berbeda:

- sample size yang lebih kecil biasanya memberi estimasi yang lebih berubah-ubah;
- pengulangan memperlihatkan rentang sample mean, bukan hanya satu angka;
- seed yang berbeda memilih urutan sample yang berbeda, tetapi tetap dari population yang sama.

## Apa yang boleh kita katakan?

Jika beberapa sample mean berada dekat dengan population mean, kita memiliki gambaran tentang variasi hasil sampling pada pengaturan tersebut. Kita tetap belum boleh mengatakan bahwa perbedaan itu membuktikan penyebab, atau bahwa satu sample pasti mewakili semua kondisi.

Keputusan analitisnya sederhana: sebelum memperlakukan sebuah angka sebagai evidence, tanyakan apakah angka itu berasal dari seluruh unit yang ingin dipahami atau dari sample. Jika berasal dari sample, bandingkan beberapa hasil dan nyatakan ketidakpastiannya.

Setelah memahami bagaimana sample mengubah estimasi, kita dapat memakai ukuran dan visual yang membantu pembaca menilai kekuatan evidence tanpa memberi kepastian palsu.
