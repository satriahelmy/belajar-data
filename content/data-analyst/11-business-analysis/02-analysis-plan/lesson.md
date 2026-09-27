Analysis plan membuat hubungan antara problem dan pekerjaan data terlihat sebelum analyst membuka banyak tabel.

## Susun rantai pertanyaan

Untuk pertanyaan “mengapa revenue Q3 turun?”, rencana awal dapat berupa:

~~~text
Question: Why did Q3 revenue decline?
  -> Did orders decline?
  -> Did average order value decline?
  -> Which category changed?
  -> Which region changed?
  -> When did the decline begin?
~~~

Setiap pertanyaan membantu mempersempit investigasi, bukan menambah daftar output tanpa tujuan.

:::practice type="multiple_choice" id="analysis-plan-01"
:::

## Pilih data yang dibutuhkan

Rantai tersebut mengarah pada metric, comparison, dimension, dan data. Orders membutuhkan unit order, average order value membutuhkan revenue dan order count, sedangkan category breakdown membutuhkan field category pada grain yang tepat.

:::practice type="text_self_assessment" id="analysis-plan-02"
:::

Plan tidak harus sempurna sejak awal. Plan yang eksplisit membuat perubahan scope dan asumsi lebih mudah diperiksa.
