Nilai yang berbeda belum tentu salah, tetapi nilai yang melanggar aturan bisnis perlu dipisahkan dari variasi penulisan.

## Bedakan inconsistency dan invalid value

Contoh region berikut mungkin merujuk tempat yang sama:

~~~text
West Java
Jawa Barat
Jabar
WEST JAVA
Jawa  Barat
~~~

Ini masalah standardisasi. Sebaliknya, quantity = -5 dapat melanggar aturan, tetapi mungkin juga return atau adjustment.

:::practice type="multiple_choice" id="invalid-values-01"
:::

## Tanyakan aturan bisnisnya

Discount 130%, tanggal 2099, atau revenue negatif perlu dibandingkan dengan aturan domain dan status transaksi sebelum diubah. Buat mapping yang dapat diperiksa, simpan nilai raw dan nilai standar, lalu beri status suspicious jika pemetaan belum yakin.

:::practice type="text_self_assessment" id="invalid-values-02"
:::

Berikutnya, periksa apakah format dan tipe data merepresentasikan makna yang sama di semua baris.
