Sebelum menghitung, pahami apa yang direpresentasikan oleh satu row dan apa arti setiap column.

## Row, column, and observation

Satu row adalah satu observation pada grain tertentu. Grain membantu kita memahami apakah tiga row berarti tiga order atau tiga item dari dua order.

| Concept | Question to ask |
| --- | --- |
| Row | Apa yang direpresentasikan oleh satu baris? |
| Column | Informasi apa yang disimpan oleh field ini? |
| Identifier | Apakah nilainya unik untuk observation tersebut? |

## Start with the grain

Jangan menganggap `COUNT(*)` selalu sama dengan jumlah order. Jika tabel berada pada grain order-item, satu order dapat muncul beberapa kali.

:::callout type="common-mistake"
Sebelum memilih aggregation, tuliskan kalimat sederhana: satu row pada tabel ini merepresentasikan apa?
:::

:::practice type="multiple_choice" id="thinking-tables-01"
:::
