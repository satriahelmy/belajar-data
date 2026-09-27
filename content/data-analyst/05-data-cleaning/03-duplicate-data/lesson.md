Dua row yang terlihat sama belum tentu duplicate. Keputusan bergantung pada grain dan identifier yang seharusnya unik.

## Gunakan grain

Jika satu row adalah satu order, order_id yang sama dua kali adalah sinyal kuat. Jika satu row adalah satu order-item, order_id yang sama dapat muncul berkali-kali karena satu order memiliki beberapa product line.

| order_id | product | amount | kemungkinan |
| --- | --- | ---: | --- |
| A001 | Coffee | 45000 | repeated transaction atau duplicate |
| A001 | Keyboard | 250000 | item berbeda dalam satu order |

:::callout type="common-mistake"
Duplicate check yang hanya membandingkan semua kolom dapat menghapus transaksi sah yang kebetulan memiliki nilai sama.
:::

:::practice type="multiple_choice" id="duplicate-data-01"
:::

## Pilih tindakan yang dapat diaudit

Periksa transaction_id, timestamp, source file, status, dan aturan bisnis. Jika bukti belum cukup, gunakan label potential duplicate. Simpan raw data dan dokumentasikan row yang dikeluarkan.

:::practice type="text_self_assessment" id="duplicate-data-02"
:::

Setelah duplicate ditangani, validasi kembali unique entity count dan metric yang terpengaruh.
