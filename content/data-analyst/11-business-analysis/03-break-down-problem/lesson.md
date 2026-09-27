Decomposition membantu analyst bergerak dari metric utama ke driver tanpa melakukan eksplorasi acak.

## Pecah metric menjadi komponen

Revenue dapat dipahami melalui orders dan average order value. Average order value kemudian dapat dilihat melalui quantity per order dan price per item.

~~~text
Revenue
  -> Orders
  -> Average order value
       -> Quantity per order
       -> Price
~~~

Pecahan ini adalah hipotesis kerja. Data tetap perlu mengonfirmasi apakah komponen tersebut berubah.

:::practice type="multiple_choice" id="decomposition-01"
:::

## Bedakan decomposition dan metric tree

Metric tree pada dashboard membantu monitoring hubungan metric. Decomposition pada investigation membantu memilih cabang berikutnya yang paling mungkin menjelaskan perubahan yang sedang diteliti.

:::practice type="text_self_assessment" id="decomposition-02"
:::

Jangan terus memecah metric tanpa batas. Berhenti ketika sebuah cabang tidak materially menjelaskan perubahan atau tidak mendukung keputusan.
