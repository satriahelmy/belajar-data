“Penjualan bulan ini kelihatannya turun.”

Kalimat seperti ini sering menjadi awal pekerjaan analyst. Belum ada angka yang bisa dihitung, tetapi ada masalah yang perlu diperjelas.

## Pertanyaan dulu, tool belakangan

Sebelum membuka spreadsheet atau menulis query, cari tahu apa yang sebenarnya ingin diputuskan. Workflow-nya sederhana:

1. Perjelas business question.
2. Pahami data dan grain-nya.
3. Hitung, lalu cek kembali hasilnya.
4. Sampaikan apa yang didukung evidence.

| Langkah | Hasil yang dicari |
| --- | --- |
| Question | Pertanyaan analitis yang terukur |
| Data | Tabel, field, dan definisi |
| Analysis | Perhitungan yang bisa diulang |
| Evidence | Finding beserta konteks dan batasnya |

Chart, query, dan spreadsheet hanya alat kerja. Kalau pertanyaannya kabur, tool yang bagus pun bisa menghasilkan jawaban yang salah sasaran.

```text
Business question
        ↓
Understand the data
        ↓
Analyze and validate
        ↓
Finding → Insight → Decision
```

## Dari keluhan ke pekerjaan analitis

Di NusaMart, kata “turun” bisa berarti beberapa hal:

| Yang dikatakan stakeholder | Yang perlu dipastikan |
| --- | --- |
| Penjualan turun | Revenue, jumlah order, atau nilai rata-rata order? |
| Bulan ini | Bulan kalender yang mana? Datanya sudah lengkap? |
| Turun | Dibanding bulan lalu, tahun lalu, atau target? |
| Secara umum | Perlu dipecah per region, kategori, atau channel? |

Revenue total, jumlah order, dan average order value bisa bergerak berbeda. Jadi, satu keluhan bisa menghasilkan beberapa pertanyaan yang sama-sama valid.

Pemilik kedai yang bilang “jam makan siang makin sepi” juga belum tentu sedang bicara jumlah transaksi. Bisa jadi yang ia lihat adalah revenue, antrean, atau jumlah meja terisi. Tanyakan dulu sebelum memilih hitungan.

## Mulai dari apa yang perlu dipastikan

Pilih langkah pertama untuk kasus NusaMart, lalu tulis satu pertanyaan data yang bisa dipakai sebagai titik awal. Tidak perlu langsung menemukan seluruh analisisnya.

:::practice type="multiple_choice" id="thinking-analyst-01"
:::

:::practice type="text_self_assessment" id="thinking-analyst-02"
:::

Setelah metric dan comparison dipilih, pertanyaan itu bisa dibawa ke tabel: field apa yang tersedia, dan satu row sebenarnya mewakili apa?

:::callout type="common-mistake"
“Revenue turun” adalah finding. “Pelanggan kehilangan minat” adalah dugaan penyebab. Data yang dibutuhkan untuk mendukung keduanya tidak sama.
:::
