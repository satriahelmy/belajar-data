Dataset tidak otomatis lebih baik hanya karena jumlah warning berkurang. Hasil cleaning perlu dibandingkan dengan baseline.

## Bandingkan sebelum dan sesudah

| metric | before | after |
| --- | ---: | ---: |
| rows | 10,000 | 8,100 |
| unique orders | 8,420 | 7,910 |
| revenue | Rp842M | Rp691M |

Penurunan 18% revenue adalah sinyal penting. Telusuri row yang hilang dan alasan penghapusannya.

:::practice type="multiple_choice" id="validation-01"
:::

## Gunakan acceptance check

Validasi dapat mencakup row count, unique entity count, missing counts, distribusi category, rentang tanggal, dan business totals. Pemeriksaan harus terkait dengan grain dan metric analisis.

:::practice type="text_self_assessment" id="validation-02"
:::

Sebelum cleaning, tentukan apa yang diharapkan tetap sama dan apa yang boleh berubah. Dengan begitu hasil dapat diaudit oleh orang lain.
