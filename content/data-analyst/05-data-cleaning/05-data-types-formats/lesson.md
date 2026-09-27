Format adalah bagian dari makna data. Angka, tanggal, dan identifier dapat terlihat mirip tetapi perlu diperlakukan berbeda.

## Kenali ambiguitas

Nilai 01/02/2026 dapat berarti 1 Februari atau 2 Januari. Nilai 1,250,000 dapat memakai koma sebagai pemisah ribuan, sedangkan pada context lain koma adalah desimal. Parsing yang berhasil belum tentu menghasilkan arti yang benar.

:::callout type="common-mistake"
Tidak adanya error saat parsing bukan bukti bahwa tanggal dan angka sudah benar.
:::

:::practice type="multiple_choice" id="formats-01"
:::

## Bedakan identifier dan angka

order_id seperti 00125 sebaiknya tidak dijumlahkan. Leading zero dapat menjadi bagian dari identifier. Revenue perlu tipe numeric agar dapat dihitung. Catat asumsi locale, timezone, unit mata uang, dan format tanggal.

:::practice type="text_self_assessment" id="formats-02"
:::

Setelah tipe dan format konsisten, nilai yang tidak biasa dapat dinilai tanpa langsung menghapus outlier.
