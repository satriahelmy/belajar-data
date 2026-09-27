# Reference Approach

## 1. Mulai dari definisi

Gunakan transactions sebagai tabel utama pada grain order-item. Revenue adalah jumlah kolom `revenue`, sedangkan jumlah order harus dihitung dengan unique `order_id`.

## 2. Bandingkan periode

Pada fixture NusaMart v1:

| Period | Revenue |
| --- | ---: |
| Agustus 2025 | 450 |
| September 2025 | 600 |

Revenue naik 150 atau sekitar 33,3%. Karena itu, klaim awal tentang slowdown tidak didukung oleh comparison dua bulan ini.

## 3. Pecah dan validasi

Breakdown category September memberi Electronics 320, Home 180, dan Grocery 100. Angka tersebut menjumlah kembali ke 600. Join ke products harus tetap many-to-one agar total tidak berubah.

## 4. Batas dan next step

Data hanya mencakup dua bulan dan tidak menjelaskan sebab. Pendekatan berikutnya adalah memperluas periode, memeriksa perubahan order count dan average order value, lalu menguji driver yang paling berubah.

Pendekatan ini bukan satu-satunya solusi. Workflow SQL, Pandas, spreadsheet, atau Tableau dapat berbeda selama definisi, evidence, dan batas kesimpulannya dapat diperiksa.
