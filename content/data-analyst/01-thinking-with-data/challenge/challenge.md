Stakeholder berkata:

> “Sales NusaMart bulan ini turun. Tolong cari tahu apa yang terjadi.”

Kamu punya data transaksi kecil dan satu klaim yang perlu diperiksa. Jangan mulai dari tool. Tentukan dulu unit, metric, comparison, dan evidence yang bisa mendukung jawabanmu.

## Satu row di sini mewakili apa?

Data latihan ini memiliki grain **one row per order-item transaction**. Satu order bisa muncul di beberapa row karena satu order dapat berisi beberapa item.

:::practice type="multiple_choice" id="challenge-01-grain"
:::

## “Sales” yang mana?

Untuk pemeriksaan awal, pakai `revenue` sebagai metric sales. Jumlah order dan quantity bisa berguna nanti, tetapi jangan campur semuanya menjadi satu angka.

:::practice type="multiple_choice" id="challenge-01-metric"
:::

## Bandingkan dengan apa?

Bandingkan revenue Agustus dan September dari data yang sama.

| Period | Revenue | Distinct orders |
| --- | ---: | ---: |
| August 2025 | 450 | 3 |
| September 2025 | 600 | 5 |

:::callout type="note"
Angka ini diringkas dari data transaksi latihan. Di data latihan ini, September lebih tinggi daripada Agustus. Jadi, klaim awal “sales turun” belum didukung oleh comparison tersebut.
:::

:::practice type="multiple_choice" id="challenge-01-comparison"
:::

## Satu metric belum cukup

Revenue naik dari 450 menjadi 600, tetapi jumlah order juga naik dari 3 menjadi 5. Average order value berubah dari 150 menjadi 120. Tambahkan metric ini agar kamu tidak hanya membaca headline revenue.

:::practice type="multiple_choice" id="challenge-01-secondary-metric"
:::

## Pecah untuk mencari pola

Setelah melihat total, pecah revenue berdasarkan `region` dan `channel` bila tersedia. Dimension membantu menunjukkan lokasi perubahan; ia tidak otomatis menjelaskan penyebabnya.

:::practice type="multiple_choice" id="challenge-01-dimension"
:::

## Jangan melampaui evidence

Berikut breakdown revenue per region:

| Region | August 2025 | September 2025 | Perubahan |
| --- | ---: | ---: | ---: |
| West | 330 | 380 | +50 |
| East | 120 | 120 | 0 |
| South | 0 | 100 | +100 |

Kita bisa melihat revenue total naik dan kontribusi region berubah. Kita belum tahu mengapa. South juga belum punya baseline Agustus di data ini.

:::practice type="multiple_choice" id="challenge-01-evidence"
:::

## Tulis finding untuk stakeholder

Tulis 1–2 kalimat. Sebutkan metric, comparison, cakupan data latihan, dan apa yang belum diketahui. Jangan menulis “pelanggan kehilangan minat” karena transaksi ini tidak mengukur hal tersebut.

:::practice type="text_self_assessment" id="challenge-01-finding"
:::
