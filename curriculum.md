
## Path Access & Prerequisites

The Data Analyst Path has a **recommended sequence, not a required sequence**.

> **The path is recommended, not required. Modules are always accessible. Prerequisites guide learners; they do not block them.**

This supports two common learner modes:

```text
BEGINNER
→ follow the recommended path from Module 01

EXPERIENCED LEARNER
→ jump directly to the module or skill they need
```

Modules must not be hard-locked because a learner has not completed an earlier module.

When prior knowledge would materially help, show a soft prerequisite instead:

```text
Exploratory Data Analysis

Recommended knowledge
✓ Thinking with Data
○ Python & Pandas

This module uses Pandas in several exercises.
You can still start now.

[ Start Module ]
```

Progress represents what the learner has completed, not permission to access later material.

A future version may offer optional skill checks such as:

```text
Already know this?

[ Take Skill Check ]
```

Passing a skill check could mark a learner as `Proficient` without requiring every lesson. This is **not required for V1**.

# BelajarData Curriculum

> **Working master curriculum — Data Analyst Path**
>
> Dokumen ini adalah source of truth kurikulum BelajarData yang sedang dikurasi. Modul yang sudah dibahas ditulis detail; modul berikutnya tetap dicantumkan sebagai roadmap dan akan dikurasi bertahap.

---

# 1. Product Learning Positioning

## Core Promise

**Belajar data, tanpa bingung mulai dari mana.**

BelajarData membantu learner memahami:

- apa yang perlu dipelajari;
- urutan belajarnya;
- bagaimana mempraktikkannya;
- bagaimana mengetahui apakah mereka benar-benar memahami materi;
- bagaimana menghubungkan skill teknis dengan pekerjaan Data Analyst.

BelajarData bukan sekadar kumpulan tutorial syntax dan bukan sekadar arena challenge.

## Learning Principles

### 1. Guide, don't overwhelm

Learner diberi jalur yang jelas. Materi dipilih berdasarkan kebutuhan Data Analyst, bukan karena sebuah tool memiliki banyak fitur.

### 2. Practice, don't memorize

Setiap konsep sebisa mungkin memiliki aktivitas yang membuat learner melakukan sesuatu, bukan hanya membaca.

### 3. Tools are not the curriculum. Analysis is the curriculum.

Spreadsheet, SQL, Python, Power BI, dan Tableau adalah alat. Tujuan akhirnya adalah menggunakan alat tersebut untuk memahami data dan menjawab pertanyaan.

### 4. Learn once, apply anywhere

Konsep seperti grain, aggregation, missing values, comparison, dan visual encoding dipelajari sebagai konsep yang dapat diterapkan lintas tool.

### 5. Use AI, but validate

AI diposisikan sebagai alat bantu kerja. Learner tetap harus memahami problem, memberikan context yang cukup, dan memvalidasi output AI.

### 6. Curate, don't duplicate

BelajarData tidak harus menjelaskan semua hal. Materi inti dijelaskan di platform, sedangkan dokumentasi dan sumber eksternal terbaik dapat diberikan melalui **Further Reading**.

### 7. Concept before blind implementation

Learner perlu memahami *why* dan *when*, bukan hanya *how*.

---

# 2. Learning Architecture

BelajarData memiliki tiga lapisan utama.

## 2.1 Learn — Data Analyst Path

Curated journey untuk learner yang ingin membangun kompetensi Data Analyst secara terstruktur.

## 2.2 Explore Skills

Learner dapat menemukan materi berdasarkan skill/tool seperti:

- Spreadsheet
- SQL
- Python & Pandas
- Power BI
- Tableau

Explore Skills **tidak harus menjadi kurikulum duplikat**. Topic yang sama dapat digunakan di Data Analyst Path dan ditemukan kembali berdasarkan tag/skill.

Contoh:

```text
SQL JOIN
   ├── digunakan dalam Data Analyst Path
   └── dapat ditemukan melalui Explore → SQL
```

## 2.3 Projects

Tempat learner mengintegrasikan beberapa kompetensi untuk menyelesaikan kasus yang lebih realistis.

---

# 3. Data Analyst Path

## Current Structure

```text
01 — Thinking with Data
02 — Spreadsheet for Analysis
03 — SQL for Data Analysis
04 — Python & Pandas for Analysis
05 — Data Cleaning
06 — Exploratory Data Analysis
07 — Statistics for Analysts
08 — Data Visualization
09 — Metrics & Dashboards
10 — BI Tools for Analysis
     ├── Power BI Track
     └── Tableau Track
     Choose one to complete the path
11 — Business Analysis
12 — Communicating Insights
13 — Projects
```

## Dependency Logic

```text
Thinking with Data
        ↓
Core Analysis Tools
Spreadsheet → SQL → Python/Pandas
        ↓
Working with Data
Cleaning → EDA → Statistics
        ↓
Presenting Data
Visualization → Metrics & Dashboards
        ↓
Industry BI Tool
Power BI OR Tableau
        ↓
Business Analysis
        ↓
Communicating Insights
        ↓
AI-Augmented Workflow
        ↓
End-to-End Projects
```

Power BI dan Tableau tetap hadir karena BI tools merupakan bagian penting dari praktik Data Analyst. Namun learner **tidak diwajibkan menguasai keduanya** untuk menyelesaikan core path. Menyelesaikan salah satu track sudah cukup.

---

# 4. Standard Topic Experience

Tidak setiap topic harus memiliki semua bagian, tetapi struktur berikut menjadi guideline.

## WHY

Mengapa seorang analyst membutuhkan konsep ini?

## LEARN

Penjelasan konsep secara ringkas dan fokus.

## EXAMPLE

Contoh realistis, sebisa mungkin menggunakan business context yang konsisten.

## PRACTICE

Aktivitas hands-on bila relevan.

## THINK

Pertanyaan yang menguji analytical reasoning.

## AI AT WORK

Opsional. Hanya digunakan ketika AI memang natural dalam workflow tersebut.

## FURTHER READING

2–4 resource berkualitas tinggi, misalnya:

- official documentation;
- interactive tutorial;
- reference;
- deeper explanation.

Setiap resource sebaiknya memiliki alasan singkat mengapa resource tersebut berguna.

## NEXT

Hubungkan topic dengan materi berikutnya.

---

# 5. Practice Architecture

## Lesson Practice

Latihan kecil sekitar 1–3 menit yang langsung terkait dengan topic.

## Module Challenge

Kasus interaktif sekitar 10–20 menit yang menggabungkan beberapa topic dalam module.

## Project

Analisis lebih terbuka menggunakan dataset dan tool nyata. Durasi dapat berkisar dari sekitar 30 menit hingga beberapa jam.

## Assessment Principle

Sebisa mungkin exercise dibuat **auto-checkable**:

- multiple choice;
- multi-select;
- numeric validation;
- SQL result validation;
- code/output validation;
- table/result validation;
- classification exercise.

Untuk jawaban subjektif seperti finding, insight, atau recommendation, gunakan **guided self-assessment**:

1. learner submit jawaban;
2. sistem menampilkan reference answer;
3. learner melihat checklist;
4. learner menandai exercise selesai.

Tidak diperlukan manual grading untuk core V1.

---

# 6. Shared Learning World — NusaMart

Untuk menjaga continuity, banyak lesson dapat menggunakan fictional business **NusaMart**.

Contoh domain:

- orders;
- order items;
- products;
- customers;
- regions;
- channels;
- sales targets;
- deliveries.

Dataset dapat berkembang seiring kurikulum.

Learner yang melihat NusaMart di Spreadsheet kemudian dapat menemukan business entities yang sama di SQL, Pandas, Visualization, Dashboard, dan Projects.

NusaMart bukan keharusan untuk setiap lesson. Dataset lain dapat digunakan jika konsep lebih mudah dijelaskan dengan context berbeda.

---

# MODULE 01 — THINKING WITH DATA

## Goal

Sebelum menggunakan Spreadsheet, SQL, atau Python, learner memahami bagaimana seorang Data Analyst mengubah masalah bisnis menjadi pertanyaan yang dapat dijawab menggunakan data.

## Learning Outcomes

Learner mampu:

- memahami workflow dasar seorang Data Analyst;
- mengubah pertanyaan bisnis ambigu menjadi pertanyaan analitis;
- memahami row, column, observation, variable, dan data type;
- membedakan metric dan dimension;
- menentukan grain sebuah dataset;
- memahami aggregation dan comparison;
- membedakan data, metric, finding, insight, dan recommendation;
- menghindari causal claim yang tidak didukung data.

---

## 1.1 — What Does a Data Analyst Actually Do?

### Why

Pemula sering mengasosiasikan Data Analyst dengan Excel, SQL, dashboard, atau coding. Module dimulai dengan memperjelas bahwa pekerjaan analyst berawal dari **question**, bukan tool.

### Learn

Workflow sederhana:

```text
Business Question
       ↓
Understand the Data
       ↓
Analyze
       ↓
Findings
       ↓
Insight
       ↓
Decision / Action
```

### Example

Stakeholder berkata:

> "Penjualan bulan ini kelihatannya turun."

Analyst tidak langsung membuat chart. Analyst perlu memahami:

- metric apa yang dimaksud dengan penjualan;
- dibandingkan dengan periode apa;
- apakah seluruh bisnis turun;
- kapan penurunan mulai terjadi;
- segment/category/region mana yang berubah.

### Practice

Learner memilih aktivitas mana yang merupakan analytical work dan mana yang hanya tool operation.

### Key Idea

> Tool membantu analyst bekerja. Tool bukan tujuan analisis.

---

## 1.2 — From Business Question to Data Question

### Why

Pertanyaan stakeholder sering terlalu luas untuk langsung dianalisis.

### Example

```text
"Penjualan kita kok terasa menurun?"
```

Diubah menjadi:

```text
"Apakah revenue 3 bulan terakhir menurun dibandingkan
3 bulan sebelumnya?"
```

Kemudian dapat diturunkan:

- kategori mana yang paling turun?
- region mana yang berubah?
- kapan penurunan mulai terjadi?
- apakah order volume turun?
- apakah average order value turun?

### Practice

Learner diberikan beberapa vague business questions dan memilih analytical question yang paling jelas.

### AI at Work

Bandingkan prompt:

```text
Analyze my sales decline.
```

dengan prompt yang memiliki:

- business context;
- metric definition;
- time period;
- comparison;
- grain.

Learner memahami bahwa AI tidak memperbaiki problem framing yang buruk secara otomatis.

---

## 1.3 — Understanding Data & Tables

### Learn

Konsep:

- row;
- column;
- observation;
- variable;
- categorical data;
- numerical data;
- date/time;
- identifier.

### Practice

Learner melihat dataset kecil dan menjawab:

- apa yang direpresentasikan oleh satu row?
- kolom mana identifier?
- mana categorical variable?
- mana numerical variable?
- mana yang mungkin menjadi metric?

---

## 1.4 — Metrics & Dimensions

### Learn

**Metric** adalah nilai yang diukur.

Contoh:

- Revenue
- Orders
- Quantity
- Customers
- Conversion Rate

**Dimension** digunakan untuk memecah atau mengelompokkan metric.

Contoh:

- Category
- Region
- Channel
- Customer Segment
- Month

### Example

```text
Revenue by Category
```

`Revenue` = metric  
`Category` = dimension

### Practice

Learner mengklasifikasikan field sebagai metric, dimension, identifier, atau membutuhkan context.

---

## 1.5 — Granularity: What Does One Row Represent?

### Why

Kesalahan memahami grain dapat menghasilkan angka yang terlihat benar tetapi sebenarnya salah.

### Example

`order_items`:

| order_id | product | quantity |
| --- | --- | ---: |
| ORD001 | Mouse | 1 |
| ORD001 | Keyboard | 1 |
| ORD002 | Monitor | 1 |

Dataset memiliki 3 rows tetapi hanya 2 unique orders.

Grain:

> One row = one item in an order.

### Practice

Learner menentukan grain dari beberapa dataset.

### Key Idea

> Sebelum menghitung sesuatu, pahami apa yang direpresentasikan oleh satu row.

---

## 1.6 — Aggregation & Comparison

### Learn

Konsep dasar:

- SUM;
- COUNT;
- DISTINCT COUNT;
- AVERAGE;
- RATE;
- contribution;
- period comparison.

Tidak ada fokus syntax pada module ini.

### Example

Revenue September = Rp120M.

Angka tersebut belum banyak berarti tanpa comparison:

- Agustus = Rp135M;
- target September = Rp150M;
- September tahun lalu = Rp105M.

### Practice

Learner memilih aggregation dan comparison yang tepat untuk beberapa business questions.

---

## 1.7 — From Data to Insight

### Learn

Bedakan:

```text
Data
  ↓
Metric
  ↓
Finding
  ↓
Insight
  ↓
Recommendation
```

### Example

**Metric**

> September revenue = Rp120M.

**Finding**

> Revenue turun 11% dibanding Agustus.

**Deeper finding**

> Sebagian besar penurunan berasal dari kategori Furniture di West Region.

**Insight**

Interpretasi yang didukung oleh evidence dan context.

**Recommendation**

Action yang masuk akal berdasarkan temuan.

### Important Principle

Jangan melompat dari correlation atau pattern menjadi causal claim.

Data transaksi mungkin menunjukkan **apa yang berubah**, tetapi belum tentu menjelaskan **mengapa** perubahan terjadi.

### AI at Work

Contoh output AI:

> "Sales declined because customers are losing interest."

Learner diminta mengevaluasi apakah claim tersebut benar-benar didukung dataset transaksi.

---

# Module Challenge 01 — NusaMart Sales Drop

## Scenario

Stakeholder berkata:

> "Sales NusaMart bulan ini turun. Tolong cari tahu apa yang terjadi."

### Step 1 — Understand the Grain

Auto-checkable multiple choice.

### Step 2 — Define the Metric

Learner menentukan apa yang dimaksud dengan "sales".

### Step 3 — Define the Comparison

Learner memilih baseline yang masuk akal.

### Step 4 — Choose Dimensions

Learner memilih breakdown yang dapat membantu investigation.

### Step 5 — Interpret Results

Sistem memberikan tabel hasil analisis.

### Step 6 — Write a Finding

Learner menulis 1–2 kalimat.

Gunakan guided self-assessment dengan reference answer dan checklist.

---

# MODULE 02 — SPREADSHEET FOR ANALYSIS

## Goal

Learner mampu menggunakan spreadsheet untuk melakukan analisis sederhana dan menjawab business question, bukan sekadar menghafal formula.

## Learning Outcomes

Learner mampu:

- memahami dataset sebelum melakukan calculation;
- melakukan filtering dan sorting;
- menghitung business metrics;
- menggunakan conditional logic;
- menghubungkan informasi dari tabel lain;
- membuat summary dengan Pivot Table;
- membandingkan performance;
- menyusun analisis spreadsheet sederhana.

---

## 2.1 — Getting to Know Your Dataset

Sebelum menulis formula, learner melakukan inspection:

- apa grain dataset?
- berapa rows?
- apa identifier?
- apa metric?
- apa dimension?
- apakah ada field yang mencurigakan?

### Practice

Mini spreadsheet playground untuk mengeksplorasi dataset NusaMart.

---

## 2.2 — Filtering & Sorting Data

### Why

Analyst sering perlu melihat subset tertentu sebelum melakukan analisis.

### Learn

- filter;
- multi-condition filter;
- sort;
- top/bottom observations.

### Practice

Contoh:

> Tampilkan transaksi September dari West Region dan urutkan berdasarkan revenue terbesar.

---

## 2.3 — Calculating Business Metrics

### Learn

Formula digunakan dalam context:

- SUM;
- COUNT;
- AVERAGE;
- unique count bila environment mendukung;
- simple arithmetic.

### Example

Dataset memiliki 1,250 rows tetapi hanya 840 unique `order_id`.

Learner harus memahami mengapa:

```text
COUNT(rows) ≠ number of orders
```

jika grain dataset berada pada order-item level.

---

## 2.4 — Adding Logic to Your Analysis

### Learn

- IF;
- conditional aggregation;
- COUNTIF / COUNTIFS;
- SUMIF / SUMIFS.

### Example

Pertanyaan:

> Berapa revenue dari West Region untuk category Electronics?

Formula dipelajari sebagai implementasi business logic.

---

## 2.5 — Connecting Information with Lookups

### Learn

Konsep lookup sebagai cara menghubungkan informasi dari tabel lain.

Contoh:

```text
Transactions
product_id
revenue

Products
product_id
category
brand
```

Learner menggunakan lookup untuk membawa `category` ke transaction table.

### Tools

Dapat menggunakan:

- XLOOKUP;
- VLOOKUP sebagai legacy/common alternative.

Fokus utama tetap pada relationship antar data.

---

## 2.6 — Summarizing Data with Pivot Tables

### Learn

Pivot Table sebagai cara cepat menjawab:

```text
Metric by Dimension
```

Contoh:

- Revenue by Category
- Orders by Region
- Revenue by Month and Channel

### Practice

Learner membuat summary berdasarkan business question, bukan sekadar mengikuti langkah UI.

---

## 2.7 — Comparing Performance

### Learn

- actual vs target;
- current vs previous period;
- absolute difference;
- percentage change;
- contribution.

### Practice

Learner membandingkan performa Agustus dan September.

---

## 2.8 — Building a Simple Analysis

Learner menggabungkan:

- filter;
- formulas;
- conditional logic;
- lookup;
- pivot;
- comparison.

Fokus bergeser dari:

> "Gunakan SUMIFS."

menjadi:

> "Revenue September turun. Cari bagian bisnis mana yang paling berkontribusi terhadap penurunan."

---

# Module Challenge 02 — NusaMart Monthly Sales Performance

## Business Question

> "Bagaimana performa penjualan September dibanding Agustus, dan area mana paling berkontribusi terhadap perubahan?"

### Tasks

1. Hitung revenue Agustus.
2. Hitung revenue September.
3. Hitung percentage change.
4. Buat breakdown berdasarkan category.
5. Identifikasi category dengan penurunan terbesar.
6. Tulis finding 1–2 kalimat.

### Validation

- numeric answer → auto-check;
- table/pivot result → result validation;
- category selection → auto-check;
- finding → guided self-assessment.

---

# MODULE 03 — SQL FOR DATA ANALYSIS

## Goal

Learner mampu menggunakan SQL untuk menjawab pertanyaan analitis pada relational data dan memvalidasi apakah hasil query masuk akal.

Module ini bukan SQL syntax encyclopedia. Syntax diperkenalkan melalui kebutuhan analisis.

## Example NusaMart Data Model

```text
orders
- order_id
- customer_id
- order_date
- channel

order_items
- order_id
- product_id
- quantity
- unit_price

products
- product_id
- category
- brand

customers
- customer_id
- city
- segment
```

---

## 3.1 — Understanding Data in Tables

### Learn

Sebelum query:

- table;
- primary/unique identifier;
- foreign key;
- relationship;
- grain.

### Practice

Pertanyaan:

> Table mana yang harus digunakan untuk menghitung jumlah order?

Learner memahami bahwa:

```sql
COUNT(*) FROM order_items
```

tidak otomatis sama dengan jumlah order.

---

## 3.2 — Answering Questions with Filters

### Learn

Business question terlebih dahulu, `WHERE` sebagai implementation.

Contoh:

> Berapa transaksi yang terjadi pada September melalui Online channel?

### Practice

SQL playground dengan result validation.

---

## 3.3 — Summarizing Data with Aggregations

### Learn

- COUNT;
- COUNT DISTINCT;
- SUM;
- AVG;
- MIN/MAX ketika relevan.

### Practice

Learner mengubah transaction rows menjadi business metrics.

---

## 3.4 — Breaking Down Performance

### Learn

`GROUP BY` sebagai implementasi:

```text
Metric by Dimension
```

Contoh:

- revenue by category;
- orders by region;
- revenue by channel.

### Think

Learner diminta menentukan grouping yang relevan sebelum menulis query.

---

## 3.5 — Combining Data with JOINs

### Learn

JOIN digunakan karena informasi bisnis sering tersebar di beberapa table.

### Important Concept

JOIN dapat mengubah jumlah rows.

Contoh:

```text
10,000 orders
      ↓ JOIN order_items
28,421 rows
```

Itu belum tentu salah.

Namun jika `shipping_cost` berada pada order level lalu dijumlahkan setelah one-to-many JOIN, nilainya dapat terduplikasi.

### Practice

Selain menulis JOIN, learner mendiagnosis query yang menghasilkan metric salah.

---

## 3.6 — Adding Business Logic

### Learn

`CASE WHEN` untuk menerjemahkan business rule menjadi analytical logic.

Contoh:

```text
High Value Order
Revenue >= threshold
```

Learner juga memahami perbedaan:

- threshold yang diberikan bisnis;
- threshold yang disimpulkan analyst dari data.

---

## 3.7 — Analyzing Changes Over Time

### Learn

Analisis:

- month-over-month;
- previous period;
- ranking;
- running/cumulative values bila relevan.

Window functions seperti `LAG()` diperkenalkan karena kebutuhan analysis, bukan sebagai daftar syntax.

---

## 3.8 — Structuring an Analysis

### Learn

Gunakan CTE untuk memecah analisis menjadi langkah yang dapat dipahami.

Contoh:

```text
base_orders
     ↓
monthly_sales
     ↓
comparison
     ↓
final_result
```

### Key Idea

SQL yang baik tidak hanya berjalan, tetapi juga mudah diverifikasi.

---

# Module Challenge 03 — NusaMart Sales Investigation

### Tasks

1. Hitung baseline revenue Agustus dan September.
2. Buat category breakdown.
3. Identifikasi region/category dengan penurunan terbesar.
4. Diagnose sebuah JOIN yang menghasilkan metric salah.
5. Tulis finding singkat.

### Validation

- SQL query → result validation;
- diagnostic question → auto-check;
- finding → guided self-assessment.

### Deferred / Explore SQL

Topik berikut tidak harus menjadi core module:

- deep string functions;
- exhaustive date functions;
- UNION / INTERSECT / EXCEPT;
- database administration;
- indexes;
- query optimization mendalam;
- DDL/DML;
- stored procedures;
- dialect-specific details.

---

# MODULE 04 — PYTHON & PANDAS FOR ANALYSIS

## Goal

Memberi learner fondasi Python/Pandas yang cukup untuk melakukan analisis programmatic dan menjadi tool yang dapat digunakan pada Data Cleaning, EDA, Statistics, dan Visualization berikutnya.

Module ini **bukan Python programming course lengkap**.

## Learning Outcomes

Learner mampu:

- memahami environment Python sederhana;
- membaca dataset ke Pandas;
- memahami DataFrame dan Series;
- memilih columns dan rows;
- melakukan filtering dan sorting;
- membuat calculated columns sederhana;
- melakukan aggregation dan GroupBy;
- menggabungkan datasets;
- bekerja dengan date secara dasar;
- menyusun analisis Pandas sederhana;
- membaca dan memvalidasi kode yang dihasilkan AI.

---

## 4.1 — Python for Data Analysis

### Why

Python memungkinkan workflow analisis yang:

- reproducible;
- programmable;
- scalable dibanding operasi manual;
- terintegrasi dengan ekosistem data.

### Learn

Python essentials secukupnya untuk mengikuti module:

- variables;
- basic data types;
- list/dictionary secara konseptual;
- function calls;
- importing libraries.

Tidak perlu masuk ke OOP, decorators, atau general software engineering.

### Example

```python
import pandas as pd
```

### AI at Work

AI boleh membantu menulis syntax, tetapi learner harus mampu membaca dan memeriksa apa yang dilakukan kode.

---

## 4.2 — Loading & Understanding DataFrames

### Learn

- `read_csv()` / loading dataset;
- DataFrame;
- Series;
- columns;
- index secara praktis;
- `head()`;
- `shape`;
- `info()`.

### Practice

Load dataset NusaMart lalu jawab:

- berapa rows?
- berapa columns?
- apa grain-nya?
- field apa yang terlihat numerical/categorical/date?

---

## 4.3 — Selecting & Filtering Data

### Learn

- selecting columns;
- filtering rows;
- boolean conditions;
- multiple conditions;
- sorting.

### Practice

Contoh:

> Ambil transaksi September dari West Region dengan revenue di atas threshold tertentu.

---

## 4.4 — Creating & Transforming Columns

### Learn

Calculated field sederhana.

Contoh:

```python
df["revenue"] = df["quantity"] * df["unit_price"]
```

Conditional transformation dapat diperkenalkan secukupnya.

### Think

Learner harus memahami business meaning dari column yang dibuat, bukan sekadar syntax.

---

## 4.5 — Summarizing with GroupBy

### Learn

Pandas implementation dari konsep yang sudah dikenal:

```text
Metric by Dimension
```

Contoh:

```python
df.groupby("category")["revenue"].sum()
```

### Practice

Revenue by:

- category;
- region;
- channel.

Bandingkan dengan cara berpikir yang sama di Spreadsheet Pivot dan SQL `GROUP BY`.

---

## 4.6 — Combining Data with Merge

### Learn

Konsep relationship dari SQL digunakan kembali.

Contoh:

```python
orders.merge(customers, on="customer_id", how="left")
```

### Important Concept

Learner tetap memeriksa:

- key;
- relationship;
- row count before/after;
- unmatched records;
- possible row multiplication.

---

## 4.7 — Working with Dates

### Learn

Secukupnya untuk analisis:

- parse dates;
- extract year/month;
- filter periods;
- create period-based analysis.

Tidak perlu menjadi date/time reference lengkap.

---

## 4.8 — Building a Simple Pandas Analysis

Learner menyelesaikan pertanyaan bisnis dengan workflow:

```text
Load
  ↓
Inspect
  ↓
Filter
  ↓
Transform
  ↓
Aggregate
  ↓
Compare
  ↓
Interpret
```

### Important Boundary

Deep cleaning belum menjadi fokus. Missing values, duplicates, outliers, dan cleaning decisions dibahas lebih dalam di Module 05.

---

# Module Challenge 04 — NusaMart Sales Analysis with Pandas

### Business Question

> "Bagaimana performa revenue NusaMart bulan ini dan bagian bisnis mana yang mengalami perubahan terbesar?"

### Tasks

1. Load dataset.
2. Inspect structure.
3. Filter relevant periods.
4. Create necessary metric.
5. Aggregate by category/region.
6. Compare periods.
7. Identify largest change.
8. Write a short finding.

### Validation

- code/output → auto-check where feasible;
- numerical result → auto-check;
- finding → guided self-assessment.

---

# MODULE 05 — DATA CLEANING


> **Learning sequence ≠ working sequence.** BelajarData separates Cleaning, EDA, and Statistics so learners can focus on one analytical skill at a time. In real analysis, these activities are often iterative: cleaning can reveal new questions, exploration can expose new quality issues, and statistical checks can send the analyst back to investigate the data.


## Goal

Learner mampu menemukan masalah kualitas data, menentukan apakah sesuatu benar-benar masalah, memilih treatment yang tepat, dan memastikan cleaning tidak merusak informasi.

Fokus module adalah **decision making dalam data cleaning**, bukan sekadar menghafal `dropna()` atau `drop_duplicates()`.

## Learning Outcomes

Learner mampu:

- mengenali masalah kualitas data umum;
- mengevaluasi missing values;
- mengidentifikasi genuine dan potential duplicates;
- mengenali invalid dan inconsistent values;
- memahami data type dan format issues;
- membedakan unusual value dengan incorrect value;
- memilih treatment berdasarkan context;
- memvalidasi hasil cleaning;
- mendokumentasikan cleaning decisions.

---

## 5.1 — What Makes Data “Dirty”?

### Learn

Kategori masalah:

- Missing
- Duplicate
- Inconsistent
- Invalid
- Format issue
- Unusual value

Contoh:

| order_id | date | category | qty | revenue |
| --- | --- | --- | ---: | ---: |
| A001 | 2026-01-03 | Electronics | 2 | 450000 |
| A002 | 03/01/2026 | electronic | 1 | 225000 |
| A003 | NULL | Furniture | 2 | 780000 |
| A003 | NULL | Furniture | 2 | 780000 |
| A004 | 2026-01-04 | Electronics | -3 | 675000 |

### Practice

Spot the problems dan klasifikasikan issue.

### Key Idea

> Cleaning bukan sekadar menjalankan fungsi cleaning. Pahami masalahnya terlebih dahulu.

---

## 5.2 — Missing Values

### Learn

Sebelum treatment, tanyakan:

- berapa banyak yang missing?
- missing terjadi pada kelompok tertentu?
- apa arti NULL secara bisnis?
- apakah field dibutuhkan?
- apa dampaknya jika row dihapus?
- apakah imputasi masuk akal?

Contoh:

| status | delivery_date |
| --- | --- |
| Delivered | 2026-08-02 |
| Delivered | 2026-08-03 |
| Cancelled | NULL |
| Cancelled | NULL |

NULL pada cancelled order dapat merupakan kondisi valid.

### Practice

Pilih:

- Keep as missing
- Remove
- Fill / impute
- Recover from another source
- Need more investigation

### Implementation Example

```python
df.isna().sum()
df.dropna()
df.fillna()
```

Syntax bukan tujuan utama.

---

## 5.3 — Duplicate Data

### Learn

Contoh:

| customer | product | date | amount |
| --- | --- | --- | ---: |
| C001 | Coffee | 2026-09-01 | 45000 |
| C001 | Coffee | 2026-09-01 | 45000 |

Belum cukup untuk menyimpulkan duplicate.

Jika `transaction_id` berbeda, dua row tersebut dapat merupakan legitimate transactions.

Konsep grain dari Module 01 digunakan kembali.

### Practice

Klasifikasikan:

- Genuine duplicate
- Legitimate repeated transaction
- Potential duplicate
- Need more context

---

## 5.4 — Invalid & Inconsistent Values

Contoh:

```text
West Java
Jawa Barat
Jabar
WEST JAVA
Jawa  Barat
```

dan:

```text
quantity = -5
discount = 130%
order_date = 2099-12-31
```

### Practice

Klasifikasikan sebagai:

- Valid
- Suspicious
- Invalid
- Need more context

---

## 5.5 — Data Types & Formats

### Learn

Contoh:

```text
Rp 1.250.000
1250000
1,250,000
```

Tanggal:

```text
01/02/2026
```

Apakah 1 Februari atau 2 Januari?

Learner memahami bahwa standardisasi format harus mempertimbangkan **meaning**, bukan hanya berhasil di-parse.

---

## 5.6 — Outliers & Unusual Values

Mayoritas transaksi:

```text
Rp100k – Rp3 juta
```

Satu transaksi:

```text
Rp120 juta
```

Setelah diperiksa ternyata corporate bulk purchase.

### Key Idea

> Unusual ≠ Incorrect.

Formal outlier techniques dapat dibahas lebih jauh di EDA/Statistics.

---

## 5.7 — Validating Your Cleaning

Contoh:

Before:

| Metric | Value |
| --- | ---: |
| Rows | 10,000 |
| Orders | 8,420 |
| Revenue | Rp842M |

After:

| Metric | Value |
| --- | ---: |
| Rows | 8,100 |
| Orders | 7,910 |
| Revenue | Rp691M |

Cleaning menghilangkan sekitar 18% revenue. Learner harus menginvestigasi.

### Validate

- row count;
- unique entities;
- business totals;
- missing counts;
- distributions;
- category counts.

### Key Idea

> Dataset tidak otomatis lebih baik hanya karena warning berkurang.

---

## 5.8 — Building a Cleaning Workflow

```text
Understand
    ↓
Profile
    ↓
Identify Issues
    ↓
Investigate
    ↓
Decide
    ↓
Clean
    ↓
Validate
    ↓
Document
```

Data lifecycle:

```text
RAW DATA
   ↓
CLEANED DATA
   ↓
ANALYSIS
```

### Principles

- jangan overwrite raw data secara diam-diam;
- cleaning decision harus dapat dijelaskan;
- simpan raw data sebagai source of truth;
- validate before analysis;
- dokumentasikan asumsi penting.

---

# Module Challenge 05 — Fix the NusaMart Dataset

Dataset sekitar 500–1,000 rows.

Profiling awal:

```text
1,000 rows

⚠ 17 missing values
⚠ 12 potential duplicates
⚠ 8 inconsistent categories
⚠ 3 unusual quantities
⚠ 1 suspicious date
```

Tidak semua warning harus diperbaiki.

### Challenge Flow

1. Inspect grain dan structure.
2. Investigate missing values.
3. Investigate potential duplicates.
4. Recover missing data bila sumber valid tersedia.
5. Investigate suspicious values.
6. Apply cleaning.
7. Validate before/after.
8. Explain important cleaning decisions.

Contoh:

- 17 missing `delivery_date` semuanya cancelled → **keep as missing**.
- 12 potential duplicates → 7 genuine duplicates + 5 legitimate transactions.
- 4 missing `product_category` dapat dipulihkan dari product master menggunakan `product_id`.

### Tool Usage

Tidak perlu memaksa Spreadsheet + SQL + Pandas pada setiap lesson.

Gunakan tool sesuai masalah:

- missing-value investigation → Pandas;
- duplicate caused by JOIN → SQL;
- simple category standardization → Spreadsheet/Pandas;
- validation → tool yang digunakan pada exercise.

Tujuan transisi module:

> "Saya sudah punya beberapa tools. Sekarang saya belajar memilih dan menggunakannya untuk menyelesaikan masalah data."

---

# MODULE 06 — EXPLORATORY DATA ANALYSIS


> **EDA is not about making every chart. It is about deciding what is worth investigating next.**

A useful exploration loop is:

```text
Question
   ↓
Baseline
   ↓
Pattern
   ↓
Breakdown
   ↓
Interesting signal
   ↓
Investigate further — or abandon an uninformative branch
```


## Goal

Setelah data cukup bersih, learner mampu mengeksplorasi dataset secara sistematis untuk menemukan pola, perubahan, segment, relationship, dan hal-hal yang layak diinvestigasi lebih lanjut.

EDA tidak diajarkan sebagai checklist `describe() → histogram → correlation matrix`, tetapi sebagai proses eksplorasi yang dipandu oleh pertanyaan.

## Learning Outcomes

Learner mampu:

- membedakan question-driven dan open exploration;
- melakukan initial profiling dan menginterpretasikannya;
- mengeksplorasi distribution;
- membandingkan category dan segment;
- mengeksplorasi perubahan terhadap waktu;
- mengenali relationship antar-variable;
- melakukan drill-down dari overall pattern ke possible driver;
- membedakan observation, finding, hypothesis, dan evidence;
- menentukan pertanyaan/data berikutnya yang diperlukan.

## Tooling

- **Pandas** → analysis;
- **Seaborn** → primary EDA plotting;
- **Matplotlib** → basic plot control;
- **BelajarData interactive UI** → bounded exploration exercises.

Seaborn dan Matplotlib adalah supporting tools, bukan fokus utama module. API plotting tidak dibahas secara mendalam.

---

## 6.1 — What Are You Looking For?

EDA dimulai dari context dan pertanyaan.

Contoh stakeholder:

> "Revenue Q3 lebih rendah dari Q2. Kami ingin memahami apa yang berubah."

Dua mode exploration:

- **Question-driven exploration** — terdapat business question awal;
- **Open exploration** — memahami karakter dataset ketika pertanyaan belum spesifik.

Data Analyst Path lebih menekankan **question-driven EDA**.

Possible exploration path:

```text
Overall Performance
       ↓
Time
       ↓
Category
       ↓
Region
       ↓
Channel
       ↓
Customer Segment
       ↓
Unusual Patterns
```

---

## 6.2 — Understanding the Shape of Your Data

Initial profiling menggunakan tools seperti:

```python
df.shape
df.info()
df.describe()
df.nunique()
```

Tetapi learner harus menginterpretasikan hasilnya.

Contoh:

```text
Rows                 85,420
Orders               31,280
Customers             8,941
Date Range     Jan–Sep 2026
Categories                6
Regions                   5
```

Pertanyaan utama:

- apa yang sudah diketahui?
- apa yang belum diketahui?
- variable mana yang perlu dieksplorasi?
- apakah structure sesuai dengan grain yang dipahami?

`describe()` bukan endpoint EDA.

---

## 6.3 — Exploring Distributions

Learner mengeksplorasi:

- center;
- spread;
- skew;
- unusual values;
- distribution shape secara intuitif.

Contoh:

```text
Order Value

Median       Rp240k
Mean         Rp610k
P90          Rp1.4M
Max          Rp120M
```

Pertanyaan:

> Mengapa mean jauh lebih tinggi daripada median?

Visual yang digunakan:

- histogram;
- boxplot.

Probability distribution theory mendalam ditunda ke materi statistik yang relevan.

---

## 6.4 — Exploring Categories & Segments

Learner membandingkan metric berdasarkan dimension.

Contoh:

```text
Revenue by Category

Electronics      Rp420M
Furniture        Rp310M
Home             Rp180M
Fashion          Rp160M
Sports            Rp95M
Beauty            Rp70M
```

Eksplorasi tidak berhenti pada:

> Electronics memiliki revenue terbesar.

Learner melakukan **metric decomposition**:

```text
Revenue
   ↓
Orders
   ↓
Average Order Value
```

Pertanyaan:

> Apakah revenue besar karena jumlah order lebih banyak atau nilai per order lebih tinggi?

---

## 6.5 — Exploring Changes Over Time

Learner mencari:

- trend;
- spike;
- drop;
- structural change;
- potential recurring pattern.

Learner tidak boleh langsung menyimpulkan seasonality hanya dari pattern singkat.

Contoh reasoning exercise:

> Revenue naik setiap akhir bulan selama tiga bulan terakhir. Apakah cukup untuk menyimpulkan adanya seasonality?

Jawaban yang diharapkan:

> Belum cukup evidence.

---

## 6.6 — Exploring Relationships

Learner mengeksplorasi relationship seperti:

```text
discount vs quantity
delivery_time vs rating
order_value vs items_per_order
```

Visual utama:

- scatterplot;
- introductory correlation overview bila relevan.

Learner mengenali secara intuitif:

- positive relationship;
- negative relationship;
- weak/no obvious relationship;
- non-linear pattern;
- possible confounder.

Prinsip:

> Relationship ≠ causation.

Formal statistical interpretation correlation dibahas lebih lanjut di Module 07.

---

## 6.7 — Drill Down: From Pattern to Explanation

Ini adalah salah satu kompetensi inti module.

Contoh:

```text
Revenue ↓12%
     ↓
Which category?
     ↓
Furniture ↓28%
     ↓
Which region?
     ↓
West ↓41%
     ↓
Orders or order value?
     ↓
Orders ↓38%
```

Finding menjadi:

> Penurunan revenue terutama terkonsentrasi pada Furniture di West Region dan lebih banyak disebabkan penurunan jumlah order daripada perubahan average order value.

Fokusnya bukan membuat sebanyak mungkin chart, tetapi mempersempit investigation secara logis.

---

## 6.8 — From Exploration to Findings

Learner membedakan:

```text
Observation
    ↓
Finding
    ↓
Hypothesis
    ↓
Evidence Needed / Next Question
```

Contoh:

**Observation**

> Furniture revenue turun 28%.

**Finding**

> Furniture menyumbang 64% dari total penurunan revenue.

**Hypothesis**

> Penurunan mungkin berkaitan dengan stock availability.

Jika dataset tidak memiliki inventory data, hypothesis tersebut belum menjadi evidence.

Learner dapat menyimpulkan:

> Inventory data diperlukan untuk menguji hypothesis tersebut.

---

# Module Challenge 06 — Why Did NusaMart Revenue Drop?

## Business Question

> "Revenue NusaMart turun pada Q3. Apa yang sebenarnya terjadi?"

Dataset telah melalui cleaning dan cukup siap dianalisis.

Scaffolding mulai dikurangi. Learner tidak diberikan urutan `GroupBy category → region → channel`.

Learner dapat mengeksplorasi:

- Revenue by Month;
- Revenue by Category;
- Revenue by Region;
- Revenue by Channel;
- Orders;
- Average Order Value;
- relevant combinations.

Contoh hasil investigation:

```text
Q3 Revenue
-12.4%

Furniture
-28.1%

Furniture — West
-41.3%

Orders
-38.0%

Average Order Value
-5.3%
```

Learner kemudian menulis finding.

Reference finding:

> Q3 revenue turun 12.4% dibanding Q2. Penurunan terutama berasal dari kategori Furniture, khususnya West Region. Di segment tersebut, penurunan lebih banyak berasal dari berkurangnya jumlah order dibanding perubahan average order value.

Challenge ditutup dengan pertanyaan:

> What should you investigate next?

Tujuannya menunjukkan bahwa EDA sering menghasilkan **pertanyaan berikutnya**, bukan selalu jawaban final.

## Practice Design

Early EDA lessons dapat menggunakan bounded interactive exploration.

Module Challenge dapat menggunakan Pandas playground yang lebih terbuka.

Hindari menjadikan EDA sebagai checklist chart seperti histogram, boxplot, pairplot, dan correlation heatmap. Visual digunakan ketika membantu menjawab pertanyaan.

Terminologi univariate/bivariate/multivariate dapat disebut sebagai terminology/Further Reading, tetapi bukan struktur utama module.

---

# MODULE 07 — STATISTICS FOR ANALYSTS

## Goal

Learner mampu menggunakan statistik untuk merangkum data, memahami variasi dan ketidakpastian, serta menilai apakah perbedaan atau pola yang ditemukan cukup kuat untuk mendukung suatu kesimpulan.

Core transition dari EDA:

> **EDA:** Apa yang terlihat di data?  
> **Statistics:** Seberapa yakin kita terhadap apa yang terlihat?

Module ini bukan mini-kuliah statistika dan tidak berfokus pada menghafal katalog statistical tests.

## Learning Outcomes

Learner mampu:

- memilih summary statistic yang sesuai;
- memahami dan membandingkan variability;
- menggunakan percentile untuk membaca distribution;
- membedakan population dan sample;
- mengenali sampling/selection bias;
- memahami sampling variability dan confidence interval secara intuitif;
- menginterpretasikan correlation dengan hati-hati;
- memahami workflow hypothesis testing melalui A/B testing;
- membedakan statistical significance dan business significance;
- menghindari overclaim dari statistical evidence.

---

## 7.1 — Why Analysts Need Statistics

Contoh:

```text
Campaign A
100 visitors
12 purchases
Conversion = 12%

Campaign B
10,000 visitors
1,050 purchases
Conversion = 10.5%
```

Pertanyaan:

> Apakah cukup mengatakan Campaign A lebih baik karena 12% > 10.5%?

Statistics membantu analyst bertanya:

```text
What happened?
      ↓
How much does it vary?
      ↓
How confident are we?
```

---

## 7.2 — Mean, Median & Choosing the Right Summary

Contoh order value:

```text
200k
250k
270k
300k
320k
350k
25M
```

Mean dapat terdorong oleh transaksi sangat besar.

Learner memilih summary berdasarkan context:

- mean;
- median;
- mode bila relevan;
- combination of summaries.

Exercise berfokus pada interpretasi:

> Stakeholder mengatakan "rata-rata customer menghabiskan Rp3,8 juta." Apakah angka tersebut representatif?

Bukan sekadar menghitung mean secara manual.

---

## 7.3 — Understanding Variability

Contoh:

```text
Region A:
98  101  102  99  100

Region B:
40  170  60  160  70
```

Average dapat mirip, tetapi variability sangat berbeda.

Konsep:

- range;
- variance secara intuitif;
- standard deviation;
- IQR.

Formula diperkenalkan secukupnya untuk memahami makna, bukan untuk drill perhitungan manual.

Interactive exercise dapat membiarkan learner mengubah spread dan melihat bagaimana summary variability berubah.

---

## 7.4 — Percentiles & Distributions

Contoh:

```text
Delivery Time

Median    2.1 days
P75       3.4 days
P90       5.8 days
P95       8.2 days
```

Use cases:

- delivery SLA;
- customer spending;
- response time;
- transaction size;
- performance distribution.

Histogram dan distribution concepts dari Module 06 digunakan kembali.

---

## 7.5 — Samples, Populations & Bias

Contoh:

```text
Population
120,000 customers

Survey
2,000 respondents

But...
survey hanya dikirim kepada Premium Members.
```

Sample besar tidak otomatis representatif.

Konsep:

- population;
- sample;
- representativeness;
- selection bias;
- response bias;
- sampling bias.

Exercise:

> Mana survey yang lebih dapat dipercaya dan mengapa?

---

## 7.6 — Confidence Intervals & Uncertainty

Learner melihat bahwa sample estimate berubah antar-sample.

Contoh interactive simulation:

```text
Sample 1 → Average = 72
Sample 2 → Average = 68
Sample 3 → Average = 75
Sample 4 → Average = 70
```

Learner mengubah sample size:

```text
10 ─────────●──── 1000
```

Konsep:

- sampling variability;
- standard error secara intuitif;
- confidence interval.

Interpretasi confidence interval harus hati-hati dan tidak disederhanakan menjadi probabilitas frequentist yang keliru.

---

## 7.7 — Correlation & Relationships

Melanjutkan Module 06.

Learner memahami:

- direction;
- strength secara kontekstual;
- linear relationship;
- outlier influence;
- correlation ≠ causation.

Contoh:

```text
r = +0.82
r = +0.31
r =  0.02
r = -0.67
```

Poin penting:

> `r ≈ 0` tidak otomatis berarti tidak ada relationship.

Relationship dapat bersifat non-linear.

Interactive scatterplot cocok untuk topic ini.

---

## 7.8 — Hypothesis Testing & A/B Testing

Hypothesis testing diajarkan melalui satu practical workflow, bukan katalog test.

Contoh:

```text
Control
20,000 visitors
Conversion 8.1%

Variant
20,100 visitors
Conversion 8.7%
```

Pertanyaan:

> Apakah peningkatan 0.6 percentage point cukup kuat untuk dipercaya?

Workflow:

```text
Question
   ↓
Define H0 / H1
   ↓
Collect Data
   ↓
Estimate Difference
   ↓
Measure Uncertainty
   ↓
Statistical Test
   ↓
Interpret
   ↓
Decision Context
```

Konsep inti:

- null hypothesis;
- alternative hypothesis;
- p-value;
- significance level;
- confidence interval.

Learner tidak diwajibkan menghafal katalog z-test, t-test, ANOVA, chi-square, dan test lainnya. Test berbeda dapat diperkenalkan melalui Further Reading.

A/B testing menjadi vehicle utama untuk memahami hypothesis testing.

---

## 7.9 — Statistical Significance vs Business Significance

Contoh:

```text
Old Checkout
Conversion = 10.00%

New Checkout
Conversion = 10.04%

Sample = 8,000,000 users

p < 0.05
```

Perbedaan dapat statistically significant tetapi business impact-nya kecil.

Sebaliknya:

```text
Variant appears:
+15% conversion

Sample:
42 users
```

Potential business impact terlihat besar, tetapi evidence masih lemah.

Learner mempertimbangkan dua axis:

```text
          Evidence
             ↑
             │
             │
─────────────┼────────────→ Business Impact
             │
```

---

# Module Challenge 07 — Can We Trust This Result?

## Scenario

Tim Growth NusaMart mengklaim checkout baru meningkatkan conversion.

Learner menerima experiment report:

```text
Control
Visitors      18,420
Purchases      1,492
Conversion      8.10%

Variant
Visitors      18,615
Purchases      1,592
Conversion      8.55%
```

Additional context:

```text
Experiment duration:
7 days

Traffic allocation:
~50 / 50

Important note:
Variant traffic mostly came from mobile users.
```

## Tasks

1. Verify conversion rates.
2. Calculate/interpret effect size.
3. Interpret confidence interval/statistical-test output.
4. Identify possible experiment bias.
5. Determine which conclusions are supported by available evidence.
6. Evaluate potential business significance.
7. Identify additional information needed before a business decision.
8. Write a concise conclusion.

Challenge tidak berhenti pada menghitung p-value.

## Practice Design

Interactive JavaScript simulations dapat digunakan untuk:

- sampling;
- sample-size effects;
- confidence intervals;
- correlation;
- A/B testing.

Python tidak harus digunakan untuk setiap statistical concept.

## Deferred / Further Reading

Tidak menjadi core:

- probability theory mendalam;
- combinatorics;
- catalog probability distributions;
- manual derivation of variance;
- proof of Central Limit Theorem;
- ANOVA;
- chi-square test;
- regression inference;
- Bayesian statistics;
- advanced power analysis;
- multiple testing;
- advanced experimental design.

Normal distribution dan Central Limit Theorem dapat muncul secara intuitif ketika diperlukan untuk menjelaskan sampling, tanpa menjadi chapter tersendiri.

---

# MODULE 08 — DATA VISUALIZATION

## Goal

Learner mampu memilih dan merancang visual berdasarkan analytical purpose dan kebutuhan audience, bukan berdasarkan preferensi chart atau tool tertentu.

Core question:

> **Saya punya sesuatu yang ingin disampaikan dari data. Visual apa yang paling tepat, dan bagaimana membuatnya mudah dipahami?**

Perbedaan dengan EDA:

```text
06 — EDA
Visual membantu analyst menemukan pola.

08 — Data Visualization
Visual membantu manusia memahami data.
```

Module ini bersifat **tool-agnostic**. BelajarData tidak mengajarkan Matplotlib, Seaborn, Chart.js, Power BI, atau Tableau sebagai fokus utama module.

## Learning Outcomes

Learner mampu:

- menjelaskan fungsi visualisasi dalam explore, explain, dan monitor;
- memulai visualisasi dari question dan analytical purpose;
- memilih visual untuk comparison;
- memilih visual untuk trend;
- memilih visual untuk composition;
- memilih visual untuk distribution dan relationship;
- menggunakan visual encoding dengan lebih efektif;
- menggunakan sorting, labels, color, dan hierarchy secara purposeful;
- mengenali visual yang berpotensi misleading;
- menyusun beberapa visual menjadi visual story sederhana.

---

## 8.1 — Why Visualize Data?

Visual bukan dekorasi.

Learner memahami tiga fungsi umum:

```text
Explore
Explain
Monitor
```

Contoh tabel bulanan dapat dibaca secara numerik, tetapi line chart dapat membuat perubahan trend jauh lebih cepat terlihat.

Perbedaan konteks:

- EDA visualization → membantu analyst mengeksplorasi;
- explanatory visualization → membantu audience memahami finding;
- dashboard visualization → membantu monitoring/exploration dan dibahas lebih lanjut di Module 09.

---

## 8.2 — Start with the Question

Jangan mulai dari:

> "Saya ingin membuat pie chart."

Mulai dari:

> "Apa yang ingin saya tunjukkan?"

Mental model:

```text
Business Question
       ↓
Analytical Purpose
       ↓
Data Structure
       ↓
Chart
```

Contoh:

```text
Category mana yang menghasilkan revenue terbesar?
→ Comparison

Bagaimana revenue berubah selama 12 bulan?
→ Trend

Berapa kontribusi setiap channel terhadap total revenue?
→ Composition
```

---

## 8.3 — Comparison

Use cases:

- category comparison;
- ranking;
- actual vs target;
- before vs after.

Bar chart menjadi visual utama, tetapi learner memahami **mengapa** position/length pada common baseline memudahkan comparison.

Interactive exercise:

> Bandingkan revenue Furniture, Technology, dan Office Supplies.

Learner memilih visual dan mendapatkan reasoning feedback, bukan hanya Correct/Incorrect.

---

## 8.4 — Trend

Use cases:

- changes over time;
- trend;
- multiple temporal series;
- different time granularities.

Line chart menjadi visual utama untuk ordered temporal data.

Learner memahami:

- chronological ordering;
- missing periods;
- granularity;
- terlalu banyak series dapat mengurangi readability.

Chart tidak dipilih hanya karena rule "time = line", tetapi berdasarkan comparison yang perlu dilakukan audience.

---

## 8.5 — Composition

Question:

> Bagaimana bagian-bagian berkontribusi terhadap total?

Learner membandingkan:

- pie/donut;
- bar;
- stacked bar;
- 100% stacked bar.

Pie chart tidak diperlakukan sebagai chart yang selalu buruk.

Prinsip:

> **Chart choice depends on what comparison the audience needs to make.**

Semakin banyak categories atau semakin kecil perbedaannya, angle/area comparison dapat menjadi lebih sulit daripada bar-based alternatives.

---

## 8.6 — Distribution

Konsep telah diperkenalkan di EDA; sekarang fokusnya pada komunikasi.

### Distribution

Visual:

- histogram;
- boxplot.

Question:

> Bagaimana distribusi delivery time?

### Relationship

Visual:

- scatterplot.

Question:

> Apakah discount yang lebih tinggi berkaitan dengan quantity yang lebih tinggi?

Learner tetap menghindari causal claim yang tidak didukung evidence.

---

## 8.7 — Relationship

**Core question:** How should relationships between variables be represented so the audience can interpret the pattern appropriately?

Focus on purposeful relationship visuals such as scatterplots, selective use of additional encodings for subgroups, and reference/trend lines when useful. The lesson should connect back to Module 07: a visible relationship is evidence of a pattern, not proof of causation.

Keep the distinction explicit:

- **Module 06 EDA:** discover relationships worth investigating.
- **Module 07 Statistics:** interpret the strength and limitations of evidence.
- **Module 08 Visualization:** communicate the relationship clearly to an audience.

Do not turn this into a catalog of multivariate chart types.

## 8.8 — Visual Encoding & Design

Konsep inti:

- position;
- length;
- size;
- color;
- shape;
- labels;
- ordering;
- hierarchy.

Design principles:

- sorting sesuai analytical purpose;
- color harus memiliki fungsi;
- gunakan emphasis secara purposeful;
- kurangi visual clutter;
- gunakan whitespace;
- gunakan direct labels ketika membantu;
- gunakan meaningful titles.

Module tidak berubah menjadi graphic-design course.

---

## 8.9 — Avoiding Misleading Visualizations

Learner mengenali:

- truncated axis;
- inappropriate scales;
- inconsistent axes;
- cherry-picked periods;
- misleading 3D effects;
- area/size distortion;
- problematic dual axes;
- unnecessary categories/colors.

Interactive exercise:

> **Spot the Problem**

Learner melihat visual dan mengidentifikasi aspek yang dapat menyebabkan interpretation keliru.

---

# Interactive Visualization Playground

BelajarData menyediakan bounded interactive playground, bukan general-purpose chart/dashboard builder.

Contoh controls:

```text
Question:
"Bagaimana tren revenue selama 12 bulan?"

Metric       [ Revenue       ▼ ]
Dimension    [ Month         ▼ ]
Chart        [ Line          ▼ ]
Sort         [ Chronological ▼ ]
```

Feedback menjelaskan alasan pilihan visual.

Scaffolding berkurang bertahap:

### Early

```text
Question diberikan
Metric diberikan
Dimension diberikan
→ learner memilih chart
```

### Intermediate

```text
Question diberikan
→ learner memilih metric
→ dimension
→ chart
```

### Advanced

```text
Business situation diberikan
→ learner menentukan sendiri visual yang diperlukan
```

Playground dapat diimplementasikan menggunakan browser-side JavaScript chart library. Learner tidak perlu mengetahui implementation library di belakang component.

---

# Module Challenge 08 — Build the Visual Story

## Scenario

Management NusaMart ingin memahami performa Q3.

Learner diberikan findings/data seperti:

```text
Revenue Q3        -12.4%
Orders            -10.8%
AOV                -1.8%

Furniture Revenue -28.1%
Technology         +4.2%
Home               -3.1%

West Region       -18.7%
East Region        -2.3%
Central            +1.4%
```

## Communication Goals

### 1. Show overall revenue trend

Learner memilih metric, dimension, dan chart.

### 2. Show which category drove the decline

Learner memilih visual dan sorting.

### 3. Highlight regional performance

Learner memilih visual dan emphasis.

Hasil kemudian menjadi mini visual story.

Learner juga memilih/menulis headline yang lebih explanatory.

Contoh:

```text
Kurang informatif:
"Revenue by Month"

Lebih explanatory:
"Revenue declined consistently throughout Q3"
```

## Boundary

Module ini tidak menjadi:

- Matplotlib tutorial;
- Seaborn tutorial;
- Chart.js tutorial;
- Power BI visualization tutorial;
- Tableau visualization tutorial.

Prinsip visual dipelajari secara tool-agnostic. Implementasi menggunakan BI tool dibahas di Module 10.

---

# MODULE 09 — METRICS & DASHBOARDS

## Goal

Learner mampu menentukan apa yang penting untuk diukur oleh bisnis, membedakan berbagai fungsi metric, memahami hubungan antar-metric, dan menyusun metric tersebut menjadi dashboard yang berguna untuk audience tertentu.

Core question:

> **Apa yang sebenarnya perlu diukur oleh bisnis, dan bagaimana kumpulan metric tersebut disusun menjadi sistem monitoring yang berguna?**

Urutan pedagogis:

```text
Business Objective
       ↓
Metrics
       ↓
KPI / Outcome
       ↓
Drivers
       ↓
Dashboard
```

Dashboard datang setelah learner memahami apa yang perlu diukur.

## Learning Outcomes

Learner mampu:

- menjelaskan apa itu metric dan pentingnya metric definition;
- membedakan metric dan KPI;
- mengevaluasi kualitas sebuah metric;
- mengenali vanity metric secara contextual;
- membedakan leading dan lagging metrics;
- memahami konsep North Star Metric beserta keterbatasannya;
- membangun metric tree sederhana;
- memilih metric berdasarkan audience dan objective;
- menyusun dashboard monitoring sederhana;
- melakukan critique terhadap dashboard.

---

## 9.1 — What Is a Metric?

Metric adalah quantitative measure yang digunakan untuk memahami performance atau behavior.

Namun nama metric saja belum cukup.

Metric definition dapat membutuhkan:

- formula;
- unit;
- period;
- population;
- grain;
- inclusion/exclusion rules.

Contoh:

```text
Revenue = Rp2.4B
```

Learner bertanya:

- gross atau net revenue?
- period apa?
- cancelled orders termasuk?
- refund bagaimana?
- tax/shipping termasuk?

Prinsip:

> Metric yang tidak didefinisikan secara konsisten dapat menghasilkan dashboard yang terlihat rapi tetapi tidak dapat dipercaya.

---

## 9.2 — Metric vs KPI

Tidak semua metric adalah KPI.

KPI dipilih karena memiliki hubungan penting dengan objective/performance yang sedang dipantau.

Contoh:

```text
Business memiliki ratusan metrics
             ↓
Objective tertentu
             ↓
Beberapa metrics menjadi KPI
```

Learner memilih KPI berdasarkan business objective, bukan berdasarkan metric yang paling mudah tersedia.

---

## 9.3 — Good Metrics vs Bad Metrics

Learner mengevaluasi metric berdasarkan sifat seperti:

- relevant to objective;
- interpretable;
- consistently defined;
- comparable;
- actionable atau diagnostically useful;
- memiliki context.

Contoh:

```text
Orders = 14,820
```

lebih berguna jika memiliki context:

```text
Orders = 14,820
-8.2% MoM
```

Tidak semua metric harus actionable secara langsung; beberapa dapat berfungsi sebagai outcome atau diagnostic metric.

---

## 9.4 — Vanity Metrics

Vanity metric tidak ditentukan hanya berdasarkan nama metric.

Contoh kandidat:

```text
Downloads
Registered Users
Monthly Active Users
Repeat Purchase Rate
Revenue
Social Followers
```

Sebuah metric dapat menjadi vanity ketika terlihat impressive tetapi tidak membantu memahami objective atau decision yang relevan.

Prinsip:

> **Vanity is contextual.**

Exercise memberikan objective berbeda dan meminta learner mengevaluasi metric mana yang benar-benar membantu.

---

## 9.5 — Leading vs Lagging Metrics

Learner memahami perbedaan outcome yang sudah terjadi dan indicators yang dapat memberi sinyal lebih awal.

Contoh:

```text
Website Traffic
      ↓
Add-to-Cart Rate
      ↓
Checkout Conversion
      ↓
Orders
      ↓
Revenue
```

Revenue dapat menjadi lagging outcome, sedangkan upstream funnel metrics dapat membantu diagnosis atau memberikan signal lebih awal.

Leading/lagging bersifat contextual terhadap outcome yang sedang dibahas.

---

## 9.6 — North Star Metric

North Star Metric diperkenalkan sebagai framework untuk memusatkan perhatian pada metric yang merepresentasikan value utama yang diterima customer dan pertumbuhan/value creation bisnis.

Learner membandingkan kandidat NusaMart:

```text
Revenue
Orders
Monthly Active Customers
Repeat Purchasing Customers
Successful Orders
```

Learner mengevaluasi trade-off.

Important boundary:

> Tidak semua organisasi harus memiliki tepat satu North Star Metric, dan North Star tidak menggantikan supporting/guardrail metrics.

---

## 9.7 — Metric Trees & Driver Metrics

Salah satu kompetensi utama module.

Contoh:

```text
                    Revenue
                       │
              ┌────────┴────────┐
            Orders             AOV
              │                 │
       ┌──────┴──────┐      Items / Order
     Traffic     Conversion       ×
                            Price / Item
```

Metric tree membantu learner:

- memahami hubungan antar-metric;
- melakukan diagnosis;
- menghubungkan outcome dengan drivers;
- menentukan metric mana yang perlu dimonitor.

Ini menghubungkan kembali Module 06 EDA dengan dashboard monitoring.

---

## 9.8 — From Metrics to Dashboard

Dashboard dimulai dari audience dan purpose.

Contoh:

```text
CEO Dashboard
≠
Marketing Dashboard
≠
Operations Dashboard
```

Learner menentukan:

- audience;
- objective;
- primary outcomes;
- drivers;
- diagnostic metrics;
- comparison/context;
- update frequency.

Tidak semua metric perlu masuk dashboard.

---

## 9.9 — Designing a Useful Dashboard

Konsep:

- information hierarchy;
- overview → drivers → details;
- monitoring vs exploration;
- comparison/context;
- layout;
- filters;
- interactions;
- dashboard critique.

Basic visual design tidak diulang secara mendalam karena telah dibahas di Module 08.

Interactive exercises dapat berupa:

- choose the metric;
- dashboard critique;
- arrange information hierarchy;
- identify unnecessary metrics;
- choose useful filters;
- compare two dashboard structures.

BelajarData tidak membuat full dashboard builder.

---

# Module Challenge 09 — Design the NusaMart Metric System

## Scenario

NusaMart ingin membuat executive dashboard untuk memonitor pertumbuhan dan performa bisnis.

Learner bekerja melalui:

```text
Business Objective
        ↓
North Star / Primary Outcome
        ↓
KPI
        ↓
Driver Metrics
        ↓
Diagnostic / Guardrail Metrics
        ↓
Dashboard
```

## Tasks

1. Clarify the business objective.
2. Evaluate candidate metrics.
3. Identify potential vanity metrics in context.
4. Choose primary outcome/KPIs.
5. Identify leading/lagging or driver metrics.
6. Build a simple metric tree.
7. Choose metrics for an executive dashboard.
8. Arrange information hierarchy.
9. Critique the resulting dashboard.

Final dashboard may resemble:

```text
NusaMart Executive Performance

Revenue      Orders      AOV
Rp2.4B       14.8K       Rp162K
-8.2%        -6.7%       -1.6%

Revenue Trend
────────────────╲
                 ╲

Revenue by Category
█████████████
█████████
██████
```

Focus bukan pada pixel-perfect dashboard authoring, tetapi pada reasoning:

> **Mengapa metric ini ada di dashboard?**

Implementation di Power BI/Tableau dibahas pada Module 10.

---

# MODULE 10 — BI TOOLS FOR ANALYSIS

## Goal

Learner mampu mengimplementasikan analytical workflow yang telah dipelajari sebelumnya menggunakan salah satu BI tool profesional: **Power BI atau Tableau**.

Module ini adalah integration module, bukan kursus software dari absolute zero sampai expert.

Core idea:

> **Saya sudah memahami analisis data. Sekarang bagaimana saya mengimplementasikan workflow tersebut di BI tool profesional?**

## Learning Outcomes

Learner mampu:

- memahami peran BI tool dalam analytical workflow;
- memahami workflow connect → prepare → model → calculate → visualize → interact;
- memilih Power BI atau Tableau sebagai completion track;
- menghubungkan konsep grain, cleaning, modeling, metrics, dan visualization dengan implementasi BI;
- membuat calculated metrics/measures yang dibutuhkan;
- membangun dashboard berdasarkan metric system dan audience;
- menggunakan interactions secara purposeful;
- memvalidasi hasil dashboard;
- menggunakan dashboard untuk menjawab business questions.

---

## 10.1 — From Analysis to BI

Learner memahami perbedaan workflow manual:

```text
CSV
 ↓
Python
 ↓
Analysis
 ↓
Export Chart
 ↓
Presentation
```

dengan BI workflow:

```text
Data Source
     ↓
Data Model
     ↓
Metrics
     ↓
Dashboard
     ↓
Interactive Analysis
     ↓
Refresh / Consumption
```

BI tidak diperkenalkan hanya sebagai software untuk membuat dashboard, tetapi sebagai environment yang menghubungkan:

```text
Data → Model → Metrics → Visualization → Interaction → Consumption
```

---

## 10.2 — Understanding the BI Workflow

Common conceptual workflow:

```text
Connect
   ↓
Prepare
   ↓
Model
   ↓
Calculate
   ↓
Visualize
   ↓
Interact
   ↓
Share
```

Module ini mengintegrasikan kompetensi sebelumnya:

```text
Module 01 → Grain & analytical thinking
Module 05 → Cleaning decisions
Module 08 → Visualization
Module 09 → Metrics & Dashboards
                    ↓
Module 10 → Implementation in BI
```

Power BI dan Tableau memiliki terminology serta workflow UI yang berbeda, tetapi banyak analytical principles tetap transferable.

---

## 10.3 — Choose Your Tool

Learner memilih salah satu:

```text
○ Power BI
○ Tableau
```

Menyelesaikan salah satu track sudah cukup untuk menyelesaikan Module 10.

Track lainnya tetap tersedia sebagai optional learning.

Contoh progress:

```text
Common Foundation       ✓

Power BI
██████████████ 100% ✓

Tableau
░░░░░░░░░░░░░░ Optional

Module Completed ✓
```

BelajarData tidak perlu menentukan satu tool sebagai tool yang secara universal "lebih baik".

---

# POWER BI TRACK

## PBI.1 — Getting Started with Power BI

Learner mengenal environment secukupnya:

- Power BI Desktop;
- Report view;
- Data view;
- Model view;
- fields;
- visuals;
- filters.

Tidak menjadi tour seluruh UI.

Learner langsung mengimpor dataset NusaMart dan memahami structure yang muncul.

---

## PBI.2 — Preparing Data with Power Query

Data Cleaning concepts tidak diajarkan ulang.

Fokus:

> Bagaimana cleaning decision yang sudah dipahami diimplementasikan secara reproducible menggunakan Power Query?

Core operations:

- change data type;
- rename;
- filter;
- split;
- standardize;
- merge;
- applied steps.

Contoh:

```text
West Java
Jawa Barat
Jabar

      ↓ Power Query

Jawa Barat
```

Learner memahami bahwa transformation steps dapat dijalankan kembali ketika data diperbarui.

---

## PBI.3 — Data Modeling

NusaMart model:

```text
             Products
                │
                │
Customers ─── Sales ─── Calendar
                │
                │
              Stores
```

Core concepts:

- fact;
- dimension;
- relationships;
- cardinality;
- filter direction secara praktis;
- star schema.

Concepts tentang grain dan JOIN digunakan kembali.

Scope tidak masuk ke enterprise data modeling secara mendalam.

---

## PBI.4 — Measures & DAX

Core DAX dibatasi pada kebutuhan analyst.

Potential functions/concepts:

- `SUM`;
- `COUNTROWS`;
- `DISTINCTCOUNT`;
- `DIVIDE`;
- `CALCULATE`;
- basic time comparison.

Business measures:

```text
Revenue
Orders
Customers
AOV
Revenue Previous Month
Revenue Growth %
```

Key concepts:

- Calculated Column vs Measure;
- filter context secara intuitif.

Deferred:

- advanced iterator patterns;
- complex context transition;
- advanced virtual tables;
- deep DAX optimization.

---

## PBI.5 — Building the Dashboard

Chart-selection theory tidak diulang.

Learner menggunakan principles dari Module 08 dan metric system dari Module 09 untuk mengimplementasikan dashboard.

Contoh structure:

```text
NUSAMART EXECUTIVE PERFORMANCE

Revenue       Orders       AOV
Rp2.4B        14.8K        Rp162K
-8.2%         -6.7%        -1.6%

Revenue Trend
────────────────────

Revenue by Category
████████████████
████████████
████████

Regional Performance
██████████████
██████████
██████
```

Focus:

> Implement the analytical design, not decorate the canvas.

---

## PBI.6 — Interactions & Analysis

Learner menggunakan secara purposeful:

- filters;
- slicers;
- cross-filtering;
- drill-down;
- tooltips secukupnya.

Pertanyaan desain:

> Jika management memilih West Region, apakah dashboard membantu mereka memahami apa yang terjadi?

Interaction harus memiliki analytical purpose.

---

# TABLEAU TRACK

## TAB.1 — Getting Started with Tableau

Learner mengenal:

- connecting data;
- worksheet;
- dimensions;
- measures;
- shelves;
- Marks card.

Tidak menjadi tour semua feature.

NusaMart digunakan sejak awal.

---

## TAB.2 — Preparing & Understanding Data

Fokus:

- data types;
- fields;
- basic preparation;
- grouping/aliases bila relevan;
- generated fields;
- understanding dataset structure.

Heavy data cleaning tidak diulang karena learner sudah memiliki Spreadsheet, SQL, dan Pandas.

---

## TAB.3 — Data Relationships

Core concepts:

- relationships;
- joins;
- logical/physical data layers secara praktis;
- implications terhadap grain.

Pertanyaan:

> Jika Orders dan Order Items digabungkan, apa yang terjadi terhadap grain?

Tujuannya bukan menghafal architecture Tableau, tetapi menjaga metric tetap valid.

---

## TAB.4 — Calculated Fields

Calculated fields diperkenalkan melalui analytical needs.

Contoh:

```text
Revenue
Orders
AOV
Growth
Category Contribution
```

Tidak menjadi calculated-field encyclopedia.

---

## TAB.5 — Building the Dashboard

Learner menggunakan business case dan metric system yang sama dengan Power BI track.

Output visual Power BI dan Tableau boleh berbeda, tetapi business questions tetap sama.

Learner menerapkan:

- visualization principles;
- metric hierarchy;
- dashboard structure;
- meaningful titles;
- appropriate context/comparison.

---

## TAB.6 — Interactions & Analysis

Core interactions:

- filters;
- parameters secukupnya;
- dashboard actions;
- highlighting;
- tooltips.

Interaction harus membantu learner/audience melakukan analysis, bukan ditambahkan hanya karena feature tersedia.

---

# BI Project 10 — NusaMart Executive Dashboard

## Scenario

NusaMart management membutuhkan dashboard untuk memonitor performance dan memahami penyebab perubahan revenue.

## Dataset

Contoh:

```text
sales.csv
products.csv
customers.csv
calendar.csv
```

## Business Requirements

Monitor:

- Revenue;
- Orders;
- AOV;
- Growth.

Analyze by:

- Time;
- Category;
- Region;
- Channel.

Learner tidak diberikan layout final yang harus ditiru pixel-by-pixel.

## Tasks

1. Connect/import data.
2. Prepare data.
3. Create relationships.
4. Create required metrics/measures.
5. Build dashboard.
6. Add useful interactions.
7. Validate totals.
8. Use dashboard to answer business questions.

Example analytical questions:

> Which category contributed most to the Q3 revenue decline?

> Which region experienced the largest decrease?

> Was the decline driven more by Orders or AOV?

Dashboard harus digunakan untuk analysis, bukan hanya menjadi portfolio screenshot.

---

## Assessment Model for External BI Tools

BelajarData V1 tidak perlu membaca atau memvalidasi `.pbix` maupun Tableau workbook secara otomatis.

Flow:

```text
Task
 ↓
Learner works in the real BI tool
 ↓
Expected Result / Checkpoint
 ↓
Self-check
 ↓
Answer Analytical Questions
 ↓
BelajarData validates answers
```

Auto-checkable checkpoints dapat berupa:

- total Revenue;
- number of Orders;
- calculated Growth;
- category with highest/lowest performance;
- answer to specific analytical questions.

Dashboard design menggunakan guided self-assessment.

Example checklist:

```text
☐ Primary KPIs are visible
☐ Time comparison is available
☐ Category breakdown is available
☐ Region can be explored
☐ Filters have a clear purpose
☐ Metrics match the defined business logic
```

---

## Boundary

Module 10 tidak bertujuan membuat learner menjadi Power BI/Tableau specialist.

BelajarData tidak perlu mengajarkan seluruh feature kedua tools.

Target:

> Learner dapat mengimplementasikan analytical workflow yang sudah dipahami menggunakan salah satu BI tool profesional.


---

# MODULE 11 — BUSINESS ANALYSIS

## Goal

Learner mampu mengubah business problem yang ambigu menjadi analysis plan, menggunakan data untuk menemukan drivers, memahami batas evidence, dan menghasilkan recommendation atau next action yang proporsional terhadap evidence.

Module ini adalah **business analysis for Data Analysts**, bukan curriculum Business Analyst.

Core question:

> **Apa yang terjadi pada bisnis, apa yang mendorongnya, apa yang belum kita ketahui, dan apa langkah berikutnya yang dapat didukung oleh evidence?**

## Learning Outcomes

Learner mampu:

- mengklarifikasi business problem sebelum menganalisis data;
- menghubungkan analysis dengan decision yang akan didukung;
- membuat analysis plan;
- melakukan issue decomposition;
- memilih comparison/context yang tepat;
- melakukan driver/contribution analysis;
- menggunakan business-defined segmentation;
- membedakan finding, interpretation, hypothesis, dan recommendation;
- menyesuaikan kekuatan recommendation dengan kekuatan evidence;
- mengidentifikasi evidence gap dan data tambahan yang diperlukan.

---

## 11.1 — Understanding the Business Problem

Stakeholder sering memberikan problem yang belum analytical-ready.

Contoh:

> "Penjualan kita jelek bulan ini."

Analyst tidak langsung membuat query.

Pertanyaan klarifikasi:

```text
What does "sales" mean?
What does "bad" mean?
Compared with what?
Which part of the business?
What decision needs to be made?
```

Pertanyaan penting:

> **What decision will this analysis support?**

Contoh refinement:

```text
Original:
"Sales kita jelek."

↓ Clarify

Objective:
Understand why revenue declined in Q3
and identify areas requiring attention.

Primary Metric:
Revenue

Comparison:
Q3 vs Q2

Possible Dimensions:
Category
Region
Channel
Customer Segment
```

Analisis tanpa decision context berisiko menghasilkan banyak output tetapi sedikit value.

---

## 11.2 — From Business Problem to Analysis Plan

Sebelum menyentuh data, learner membuat analysis plan.

Contoh:

```text
BUSINESS QUESTION
Why did Q3 revenue decline?
        │
        ├── Did Orders decline?
        │
        ├── Did AOV decline?
        │
        ├── Which category changed?
        │
        ├── Which region changed?
        │
        └── When did the decline begin?
```

Planning framework:

```text
Question
   ↓
Metric
   ↓
Comparison
   ↓
Dimensions
   ↓
Data Needed
```

Practice dapat meminta learner membandingkan beberapa analysis plan dan memilih struktur yang paling sesuai dengan business question.

---

## 11.3 — Breaking Down a Problem

Learner menggunakan issue decomposition agar investigation tidak berubah menjadi random exploration.

Contoh:

```text
                  Revenue
                     │
            ┌────────┴────────┐
          Orders              AOV
            │                  │
     ┌──────┴──────┐     Quantity / Order
   Traffic    Conversion           ×
                                Price
```

Perbedaan dengan Module 09:

- Module 09 menggunakan metric tree untuk memahami metric system dan monitoring;
- Module 11 menggunakan decomposition untuk investigation.

Contoh:

```text
Revenue ↓12%
   │
   ├── Orders ↓11%
   │
   └── AOV ↓1%
```

Investigation kemudian dapat difokuskan pada drivers dari Orders.

---

## 11.4 — Measuring Performance Against Context

Angka tanpa comparison/context sering tidak cukup.

Contoh:

```text
Revenue Q3
Rp2.4B

QoQ       -12%
YoY        +8%
vs Target  -18%
```

Learner mempertimbangkan comparison seperti:

- previous period;
- same period last year;
- target;
- forecast;
- benchmark/other segments bila relevan.

Comparison berbeda dapat menghasilkan interpretation berbeda.

Learner tidak terburu-buru memberi label "good" atau "bad" hanya berdasarkan satu angka.

---

## 11.5 — Finding Drivers

Learner melakukan practical contribution/driver analysis.

Contoh:

```text
Total Revenue Change
-120M

Furniture      -90M
Home           -25M
Technology     +20M
Fashion        -15M
Others         -10M
```

Furniture merupakan contributor terbesar terhadap decline.

Investigation dapat dilanjutkan:

```text
Furniture
    ↓
Region
    ↓
West
    ↓
Orders
    ↓
Returning Customers
```

Tujuannya bukan menghasilkan sebanyak mungkin breakdown, tetapi menemukan area yang materially menjelaskan perubahan.

---

## 11.6 — Segmentation for Analysis

Segmentation dalam module ini adalah **business-defined segmentation**, bukan machine-learning clustering.

Contoh:

```text
Customer
├── New
├── Returning
└── Reactivated

Channel
├── Online
├── Store
└── Marketplace

Customer Value
├── High
├── Medium
└── Low
```

Learner memahami bahwa aggregate performance dapat menyembunyikan behavior segment.

Contoh:

```text
Overall Revenue
-4%

New Customers
+18%

Returning Customers
-16%
```

Overall number saja tidak cukup menjelaskan apa yang terjadi.

Machine-learning segmentation/clustering bukan scope core Data Analyst Path.

---

## 11.7 — From Evidence to Recommendation

Learner membedakan:

```text
Finding
   ↓
Interpretation
   ↓
Recommendation
```

Contoh finding:

> Furniture revenue di West turun 41%, terutama karena jumlah order turun.

Evidence tersebut belum cukup untuk langsung merekomendasikan:

> Berikan diskon 20%.

Jika penyebab order decline belum diketahui, recommendation yang lebih defensible:

> Investigate stock availability dan acquisition/traffic changes untuk Furniture di West sebelum menentukan intervention.

Jika inventory evidence kemudian menunjukkan stock-out pada high-demand SKUs, recommendation dapat menjadi lebih specific.

Prinsip:

> **Recommendation strength should match evidence strength.**

---

## 11.8 — Knowing What You Don't Know

Analyst tidak harus selalu memiliki causal answer.

Contoh:

```text
West Furniture Orders ↓38%
```

Available data:

```text
orders
customers
products
```

Missing potentially relevant data:

```text
inventory
marketing spend
website traffic
customer feedback
competitor pricing
```

Conclusion yang defensible:

> Data saat ini menunjukkan **where the decline happened**, tetapi belum cukup untuk menentukan **why it happened**.

Learner kemudian menyusun:

```text
Possible Hypothesis:
Stock availability

Data Needed:
Inventory / stock-out history
```

Kemampuan mengenali evidence gap adalah bagian dari analytical maturity.

---

# Module Challenge 11 — NusaMart Growth Investigation

## Scenario

CEO NusaMart melihat pertumbuhan revenue melambat selama dua kuartal terakhir.

Tim ingin mengetahui:

> Apa yang terjadi, area mana yang mendorong perubahan tersebut, dan apa yang perlu diinvestigasi atau ditindaklanjuti?

## Available Data

Contoh datasets:

```text
orders
order_items
customers
products
marketing
```

Learner dapat memilih tool yang sesuai:

- Spreadsheet;
- SQL;
- Python/Pandas;
- Power BI/Tableau.

Tool tidak lagi ditentukan secara default.

## Investigation Flow

```text
1. Clarify Problem
        ↓
2. Build Analysis Plan
        ↓
3. Choose Metrics
        ↓
4. Analyze Performance
        ↓
5. Drill Down
        ↓
6. Identify Drivers
        ↓
7. Identify Evidence Gaps
        ↓
8. Recommend Next Action
```

Example embedded evidence:

```text
Overall Revenue Growth

Q1  +14%
Q2   +8%
Q3   +2%

Returning Customer Revenue

Q1  +12%
Q2   +3%
Q3  -11%

New Customer Revenue

Q1  +18%
Q2  +17%
Q3  +16%
```

Potential investigation:

```text
Returning Customers
       ↓
West Region
       ↓
Furniture
       ↓
Repeat Orders ↓
```

Dataset sengaja tidak selalu menyediakan evidence yang cukup untuk causal conclusion.

Learner harus dapat mengatakan:

> Evidence menunjukkan penurunan terkonsentrasi pada repeat orders Furniture di West, tetapi dataset saat ini belum menjelaskan penyebab perilaku tersebut.

---

## Challenge Output — Analysis Brief

Challenge tidak menghasilkan dashboard lagi.

Example structure:

```text
NUSAMART GROWTH INVESTIGATION

Business Question
─────────────────
Why has revenue growth slowed?

Key Findings
────────────
1. Revenue growth slowed from +14% to +2%.
2. New-customer revenue remains strong.
3. Returning-customer revenue declined 11%.
4. Decline is concentrated in Furniture / West.

What We Know
────────────
Repeat orders declined substantially.

What We Don't Know
──────────────────
Current data cannot determine why
returning customers reduced purchases.

Next Data Needed
────────────────
Inventory availability
Customer behavior / retention data
Marketing exposure

Recommended Next Step
─────────────────────
Investigate the identified drivers before
selecting a commercial intervention.
```

Assessment combines:

- auto-checkable analytical results;
- reasoning questions;
- guided self-assessment for open-ended findings/recommendations.

---

## Boundary

Module ini tidak menjadi general Business Analyst curriculum.

Not core:

- SWOT;
- PESTLE;
- Porter's Five Forces;
- BPMN;
- UML;
- requirements documentation;
- project management.

Scope tetap pada penggunaan data untuk business problem solving.

Boundary dengan Module 12:

```text
11 — Business Analysis
"What does the evidence mean for the business
and what should we do next?"

12 — Communicating Insights
"How do I communicate that analysis
clearly to the right audience?"
```

Module 11 dapat menghasilkan finding dan recommendation, tetapi storytelling, executive summary, presentation structure, chart narrative, dan audience adaptation dibahas lebih dalam di Module 12.


---

# MODULE 12 — COMMUNICATING INSIGHTS

## Goal

Learner mampu mengubah hasil analisis menjadi pesan yang jelas, akurat, evidence-based, dan relevan untuk audience yang akan mengambil keputusan.

Core principle:

> **Analyst tidak perlu menceritakan semua yang dianalisis. Analyst perlu mengkomunikasikan apa yang perlu diketahui audience.**

Module ini bukan kelas public speaking atau presentation design.

## Learning Outcomes

Learner mampu:

- menyaring analytical output menjadi key message;
- menyesuaikan detail komunikasi dengan audience;
- membedakan observation, finding, insight, dan recommendation;
- menggunakan takeaway-oriented headline ketika appropriate;
- menyusun analytical story berdasarkan evidence;
- menulis komunikasi data secara concise dan contextual;
- mengkomunikasikan uncertainty tanpa overclaim;
- memilih format komunikasi yang sesuai dengan kebutuhan;
- menggunakan AI untuk membantu drafting sambil tetap memvalidasi accuracy dan claims.

---

## 12.1 — From Analysis to Message

Analisis dapat menghasilkan banyak output, tetapi tidak semuanya harus dikomunikasikan.

Example:

```text
Revenue Q3        +2%
Revenue Q2        +8%
Revenue Q1       +14%

New Customer     +16%
Returning        -11%

West              -8%
East              +4%
Central           +3%

Furniture        -17%
Technology        +9%
...
```

Communication workflow:

```text
Analysis
   ↓
Relevant Evidence
   ↓
Key Finding
   ↓
Takeaway
   ↓
Decision / Next Action
```

Possible key message:

> **Revenue growth slowed primarily because purchases from returning customers declined.**

Detail lain menjadi supporting evidence.

---

## 12.2 — Know Your Audience

Evidence tetap sama, tetapi level of detail, terminology, dan emphasis dapat berubah berdasarkan audience.

Example evidence:

> Returning-customer revenue declined 11%.

For executive:

> Slower repeat purchases are the main drag on growth.

For marketing:

> Returning-customer revenue declined 11%, concentrated in West-region Furniture customers.

For analyst:

> Returning-customer revenue declined 11% QoQ; decomposition shows the largest absolute decline in Furniture-West.

Principle:

```text
Same Evidence
     ↓
Different Audience
     ↓
Different Detail
```

Audience adaptation tidak boleh mengubah fakta.

---

## 12.3 — Finding vs Insight vs Recommendation

Learner membedakan level komunikasi:

```text
Observation
"Furniture revenue = Rp420M"

Finding
"Furniture revenue declined 28% QoQ."

Insight
"The decline is concentrated in West,
where order volume fell substantially."

Recommendation / Next Step
"Investigate inventory availability and
repeat-customer behavior in West."
```

Tidak setiap finding harus dipaksakan menjadi insight yang terdengar dramatic.

Evidence menentukan seberapa jauh analyst dapat menyimpulkan.

---

## 12.4 — Lead with the Takeaway

Dalam explanatory communication, descriptive titles sering dapat diperkuat.

Example:

```text
Before:
Revenue by Quarter

After:
Revenue growth slowed sharply in Q3
```

Another example:

```text
Before:
Customer Analysis

After:
Returning-customer decline is driving
the slowdown in revenue growth
```

Principle:

> **A title can communicate the takeaway, not merely describe the chart.**

Boundary:

Takeaway titles tidak harus digunakan untuk semua context. Exploratory analysis dan monitoring dashboards dapat membutuhkan naming yang berbeda.

---

## 12.5 — Building an Analytical Story

Storytelling digunakan sebagai cara menyusun evidence, bukan mengarang narrative.

Simple structure:

```text
What happened?
      ↓
Where did it happen?
      ↓
What appears to be driving it?
      ↓
What do we know / not know?
      ↓
What should happen next?
```

NusaMart example:

```text
Growth slowed
     ↓
Returning customer revenue declined
     ↓
Largest decline: Furniture / West
     ↓
Repeat orders declined
     ↓
Cause not yet established
     ↓
Investigate inventory + retention signals
```

Analytical story harus tetap traceable ke evidence.

---

## 12.6 — Writing with Data

Analyst berkomunikasi melalui:

- email;
- chat/collaboration tools;
- dashboard notes;
- reports;
- executive summaries;
- presentations.

Writing principles:

```text
Specific
Concise
Contextual
Evidence-based
```

Example:

Less effective:

> Based on the analysis that has been conducted, it can be seen that there was a decrease in revenue in the Furniture category in the West region which experienced a decrease of approximately 41%.

More effective:

> **Furniture revenue in West fell 41% QoQ, the largest decline across category-region combinations.**

Learner juga menghindari vague claims seperti:

> Revenue declined significantly.

ketika yang dimaksud bukan statistical significance.

Prefer:

> Revenue declined 12.4% QoQ.

---

## 12.7 — Communicating Uncertainty

Learner menjaga claim tetap sesuai dengan evidence.

Example:

```text
Unsupported:
Discounts caused sales to increase.

Supported:
Higher discounts were associated with
higher order quantities in this dataset.
```

Another:

```text
Unsupported:
Customers left because products were unavailable.

Supported:
Repeat orders declined, but the current data
does not establish why. Inventory availability
is one hypothesis worth investigating.
```

Useful framing:

```text
We know
We suspect
We don't know yet
```

Interactive exercise:

> **Which statement is supported by the evidence?**

Ini dapat dibuat auto-checkable.

---

## 12.8 — Choosing the Right Format

Tidak semua insight membutuhkan dashboard atau slide deck.

Example decision guide:

```text
Simple Answer
→ message / email

Recurring Monitoring
→ dashboard

Detailed Investigation
→ analysis brief / report

Executive Decision
→ short presentation / executive brief

Exploration
→ notebook / BI / analytical workspace
```

Example:

CEO asks:

> "Kenapa revenue kemarin turun?"

Potential concise answer:

> Revenue yesterday was down 8% versus the previous Tuesday, mainly due to 14% fewer orders in West. AOV was largely unchanged.

Analyst memilih medium berdasarkan communication need, bukan berdasarkan tool yang ingin digunakan.

---

# Module Challenge 12 — Present the NusaMart Growth Story

Module ini menggunakan kembali hasil **Module 11 — NusaMart Growth Investigation**.

Module 11:

```text
Analysis Brief
```

Module 12:

```text
Executive Communication
```

## Evidence

Example:

```text
Revenue Growth
Q1 +14%
Q2  +8%
Q3  +2%

Returning Customer Revenue
-11%

New Customer Revenue
+16%

Largest Decline
Furniture / West

Repeat Orders
-18%

Cause
Not established
```

## Tasks

### Task 1 — Choose the Key Message

Learner memilih statement yang paling mewakili evidence.

Auto-checkable.

### Task 2 — Write the Headline

Example:

```text
Before:
Revenue Analysis

After:
Declining repeat purchases are slowing NusaMart's growth
```

Guided self-assessment.

### Task 3 — Select Supporting Evidence

Learner memilih hanya evidence yang benar-benar mendukung key message.

Tujuan:

> Don't communicate every output just because it exists.

### Task 4 — Build the Story

Possible ordering:

```text
Growth slowing
     ↓
Returning customers
     ↓
Furniture / West
     ↓
Repeat orders
     ↓
Evidence gap
     ↓
Next investigation
```

Dapat dibuat sebagai ordering/drag-and-drop exercise.

### Task 5 — Communicate Uncertainty

Learner memilih wording yang tidak melebihi evidence.

Auto-checkable.

### Task 6 — Create the Executive Summary

Expected output sekitar 3–5 kalimat.

Reference example:

> NusaMart's revenue growth slowed from 14% in Q1 to 2% in Q3. The slowdown is primarily associated with an 11% decline in returning-customer revenue, while new-customer revenue remains strong. The largest decline is concentrated in Furniture customers in the West region, where repeat orders fell. Current data does not establish the cause of this behavior. Inventory availability and retention signals should be investigated next.

Open-ended response menggunakan reference answer + checklist, bukan manual admin grading.

---

# Communication Builder

V1 tidak perlu meminta learner membuat PowerPoint deck.

BelajarData dapat menyediakan bounded Communication Builder:

```text
┌────────────────────────────────────────────┐
│ EXECUTIVE BRIEF                            │
│                                            │
│ Headline                                   │
│ [______________________________________]   │
│                                            │
│ Key Evidence                               │
│ ☑ Revenue growth +2%                       │
│ ☑ Returning customers -11%                │
│ ☐ Technology +9%                           │
│ ☑ Furniture / West largest decline         │
│                                            │
│ What We Know                               │
│ [______________________________________]   │
│                                            │
│ What We Don't Know                         │
│ [______________________________________]   │
│                                            │
│ Recommended Next Step                      │
│ [______________________________________]   │
└────────────────────────────────────────────┘
```

Focus tetap pada reasoning dan communication, bukan font, template, alignment, atau slide decoration.

---

# AI at Work

AI memiliki natural role sebagai drafting assistant.

Raw analyst notes:

```text
revenue q3 growth 2%
returning cust -11
west furniture biggest decline
repeat order down
don't know cause
```

Example task:

> Turn these findings into a concise executive summary. Do not infer causes that are not supported by the findings.

Learner kemudian memvalidasi AI output:

```text
☐ Are all numbers correct?
☐ Did AI introduce unsupported causality?
☐ Did it preserve uncertainty?
☐ Is the key message clear?
☐ Is anything important missing?
```

Principle:

> AI can help draft the message. The analyst remains responsible for the evidence and claims.

Ini menjadi natural bridge menuju Module 13 — Projects.

---

## Boundary

Module 12 tidak menjadi:

- public speaking course;
- PowerPoint tutorial;
- slide-design course;
- personal branding course;
- generic storytelling framework catalog.

Core focus tetap analytical communication.


---

# MODULE 13 — PROJECTS

## Goal

Learner menerapkan kompetensi dari Modules 01–12 untuk menyelesaikan realistic data problems secara end-to-end dengan scaffolding yang jauh lebih sedikit.

Projects bukan tutorial panjang dan bukan final exam tunggal.

Core principle:

> **Give the learner a business problem, not a sequence of commands.**

## Completion Model

V1 menyediakan **3 substantial projects**.

Learner menyelesaikan **2 dari 3 projects** untuk menyelesaikan Data Analyst Path.

```text
Projects

Complete any 2 projects
to complete the Data Analyst Path.

● NusaMart Performance        Completed
● Customer Retention          Completed
○ Delivery Performance        Optional
```

Project library dapat terus bertambah tanpa mengubah core curriculum Modules 01–12.

Principle:

> **Three strong projects are better than a large library of shallow projects.**

## Project Experience

Setiap project mengikuti struktur umum:

```text
01 — The Brief
       ↓
02 — Understand the Data
       ↓
03 — Plan Your Analysis
       ↓
04 — Investigate
       ↓
05 — Validate
       ↓
06 — Build Your Evidence
       ↓
07 — Communicate Your Findings
       ↓
08 — Reference Approach
```

Reference Approach dibuka setelah learner mencoba menyelesaikan problem.

Project tidak menjadi:

```text
Step 1: Run this SQL.
Step 2: Create this chart.
Step 3: Copy this dashboard.
```

Semakin akhir curriculum, semakin sedikit prescriptive instruction.

---

## Tool Choice

Capstone menilai analytical outcome, bukan penggunaan satu tool tertentu.

Learner dapat memilih workflow yang sesuai, misalnya:

```text
SQL + Power BI

or

Python + Tableau

or

SQL + Pandas + Visualization
```

Project brief menyediakan data dan business context, tetapi tidak menentukan tool kecuali ada alasan khusus.

Expected competencies:

- understand the business problem;
- inspect and understand data;
- define metrics correctly;
- clean/prepare data when needed;
- choose an analysis approach;
- investigate patterns and drivers;
- validate results;
- distinguish evidence from hypothesis;
- communicate findings;
- propose defensible next steps.

---

# Project 01 — NusaMart Revenue Slowdown

**Business Case · Intermediate**

## Brief

Revenue growth NusaMart melambat.

Learner diminta menentukan:

- what happened;
- where the decline is concentrated;
- which metrics/drivers explain the change;
- what is supported by available evidence;
- what should be investigated next.

## Potential Data

```text
customers.csv
orders.csv
order_items.csv
products.csv
```

Dataset lebih mentah dan scaffolding lebih sedikit dibanding module challenges sebelumnya.

## Skills

- SQL and/or Pandas;
- Data Cleaning;
- EDA;
- Statistics where relevant;
- Visualization;
- Metrics;
- Business Analysis;
- Communication.

## Expected Output

Analysis brief atau equivalent analytical deliverable yang mencakup:

- business question;
- key metrics;
- key findings;
- supporting evidence;
- evidence limitations;
- next action/recommendation.

Suggested estimated effort:

> **4–6 hours**

---

# Project 02 — Customer Retention Analysis

**Business Case · Intermediate**

## Brief

Sebuah subscription business melihat customer retention menurun.

Learner diminta:

- measure the retention problem;
- identify affected customer segments;
- investigate behavioral patterns associated with churn/retention;
- distinguish association from causation;
- identify what additional evidence may be needed.

## Skills

- SQL and/or Pandas;
- cohort/segment thinking at an appropriate analyst level;
- Statistics;
- Visualization;
- Business Analysis;
- Communication.

## Expected Output

A concise retention analysis that explains:

- how retention is defined;
- which segments changed;
- relevant patterns;
- limitations;
- recommended next investigation/action.

Suggested estimated effort:

> **4–6 hours**

---

# Project 03 — Delivery Performance Investigation

**Business Case · Intermediate**

## Brief

Late deliveries are increasing.

Learner diminta menemukan:

- whether delivery performance has actually deteriorated;
- where the issue is concentrated;
- which operational dimensions are associated with the change;
- what current data can and cannot explain.

## Potential Dimensions

- time;
- region;
- warehouse;
- shipping method;
- product category;
- order volume.

## Skills

- Data Cleaning;
- SQL and/or Pandas;
- EDA;
- Metrics;
- Visualization;
- Business Analysis;
- Communication.

## Expected Output

Operational analysis containing:

- metric definition;
- performance comparison;
- driver breakdown;
- supporting visuals;
- evidence gaps;
- defensible next steps.

Suggested estimated effort:

> **4–6 hours**

---

# Project Assessment

Projects should combine objective checkpoints with guided self-assessment.

Auto-checkable examples:

- correct metric value;
- correct number of unique orders/customers;
- correct period comparison;
- correct segment/category identification;
- correct interpretation among multiple choices.

Open-ended components:

- analysis plan;
- key finding;
- evidence limitation;
- recommendation;
- executive summary.

These use:

```text
Learner Submission
       ↓
Reference Approach
       ↓
Checklist
       ↓
Self-Assessment
```

No manual admin grading and no AI grading are required for V1.

---

# Reference Approach

Reference Approach is not a single mandatory solution.

It should show:

- one reasonable analysis plan;
- important validation steps;
- example analytical workflow;
- expected core findings;
- alternative valid approaches where relevant;
- common mistakes;
- example communication output.

Principle:

> **Teach learners how to evaluate an approach, not merely how to reproduce one.**

---

# Future Project Library

After V1, BelajarData can add projects such as:

- Marketing Performance;
- Product Analytics;
- Financial Performance;
- Marketplace Analysis;
- HR/People Analytics;
- Customer Experience;
- additional Operations cases.

New projects should introduce meaningful business contexts rather than duplicate the same analysis with different column names.

Core Modules 01–12 should remain relatively stable while the project library grows.

---

# DATA ANALYST PATH — COMPLETION

```text
Modules 01–12
Learn → Practice → Challenge
          ↓
Module 13
Choose Projects → Solve → Validate → Communicate
          ↓
Complete any 2 projects
          ↓
Data Analyst Path Completed
```

Completion represents exposure to the full analytical workflow, not mastery of every possible Data Analyst domain.

