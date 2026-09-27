Logic spreadsheet menerjemahkan aturan bisnis menjadi kondisi yang dapat diperiksa. Batas dan kategori harus berasal dari context, bukan dari warna cell.

## Mulai dari rule yang eksplisit

Contoh: tandai revenue West di atas 600 sebagai “Review West”. Formula IF hanya mengimplementasikan rule; ia tidak menjelaskan mengapa West harus direview.

:::spreadsheet-playground id="sheet-logic-01"
:::

## Gabungkan kondisi dan aggregation

SUMIF atau SUMIFS menjawab pertanyaan seperti revenue West atau revenue West untuk category tertentu. Pastikan setiap criteria memakai field yang tepat dan tidak mencampur unit.

:::practice type="multiple_choice" id="sheet-logic-02"
:::

Logic membantu membuat keputusan yang konsisten. Namun hasilnya tetap perlu dibandingkan dengan definisi metric dan evidence yang digunakan.
