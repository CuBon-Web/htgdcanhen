# 📝 Hướng Dẫn Định Dạng File Word Để Upload

## 🎯 Vị Trí Ảnh Trong File Word

### ✅ **ĐÚNG - Ảnh nằm NGAY SAU dòng "Câu X."**

```
PHẦN I. Trắc nghiệm

Câu 1. Quan sát hình vẽ sau và cho biết đây là hình gì?
[ẢNH 1 - CHÈN Ở ĐÂY]
A. Hình vuông
*B. Hình tròn
C. Hình tam giác
D. Hình chữ nhật

Câu 2. Dựa vào 2 hình dưới đây, hãy so sánh diện tích:
[ẢNH 1 - CHÈN Ở ĐÂY]
[ẢNH 2 - CHÈN Ở ĐÂY]
A. Hình 1 lớn hơn
B. Hình 2 lớn hơn
*C. Bằng nhau
D. Không xác định
Lời giải: Cả 2 hình có cùng diện tích...
```

### ❌ **SAI - Ảnh nằm TRƯỚC "Câu X."**

```
❌ SAI:
PHẦN I. Trắc nghiệm

[ẢNH - ĐẶT Ở ĐÂY LÀ SAI]
Câu 1. Quan sát hình vẽ...

→ Ảnh sẽ KHÔNG được map vào câu 1!
```

### ❌ **SAI - Ảnh nằm SAU đáp án**

```
❌ SAI:
Câu 1. Quan sát hình vẽ sau:
A. Đáp án A
B. Đáp án B
C. Đáp án C
D. Đáp án D
[ẢNH - ĐẶT Ở ĐÂY LÀ SAI]

→ Ảnh có thể bị map nhầm vào câu 2!
```

---

## 📐 Quy Tắc Vị Trí Ảnh

### **Quy tắc 1: Ảnh thuộc câu hỏi**
```
Câu X. [Nội dung câu hỏi]
[ẢNH 1]              ← Thuộc câu X
[ẢNH 2]              ← Thuộc câu X
[ẢNH 3]              ← Thuộc câu X
A. Đáp án A
B. Đáp án B
```

### **Quy tắc 2: Ảnh TRƯỚC đáp án**
```
Câu X. [Nội dung]
[ẢNH]                ← Phải đặt TRƯỚC đáp án
A. Đáp án A          ← Đây là ranh giới
```

### **Quy tắc 3: Nhiều ảnh cho 1 câu**
```
Câu X. So sánh 3 hình sau:
[ẢNH 1]              ← Ảnh thứ 1
[ẢNH 2]              ← Ảnh thứ 2  
[ẢNH 3]              ← Ảnh thứ 3
A. Đáp án
```

---

## 📋 Ví Dụ Hoàn Chỉnh

### **Ví dụ 1: Câu hỏi có 1 ảnh**

```
PHẦN I. Hình học

Câu 1. Quan sát hình vẽ. Tính chu vi hình chữ nhật ABCD?
[CHÈN ẢNH HÌNH CHỮ NHẬT]
A. 20cm
B. 24cm
*C. 28cm
D. 32cm
Lời giải: Chu vi = 2 × (8 + 6) = 28cm
```

### **Ví dụ 2: Câu hỏi có 2 ảnh**

```
Câu 2. So sánh 2 đồ thị sau:
[CHÈN ẢNH ĐỒ THỊ 1]
[CHÈN ẢNH ĐỒ THỊ 2]
A. Đồ thị 1 tăng nhanh hơn
*B. Đồ thị 2 tăng nhanh hơn
C. Cả 2 tăng như nhau
D. Không so sánh được
```

### **Ví dụ 3: Nhiều câu hỏi, mỗi câu có ảnh**

```
PHẦN II. Vật lý

Câu 3. Quan sát mạch điện:
[CHÈN ẢNH MẠCH ĐIỆN]
A. Mắc nối tiếp
*B. Mắc song song
C. Mắc hỗn hợp
D. Không xác định

Câu 4. Xác định loại dòng điện:
[CHÈN ẢNH DAO ĐỘNG]
*A. Dòng xoay chiều
B. Dòng một chiều
C. Dòng xung
D. Không có dòng điện

Câu 5. Phân tích lực tác dụng:
[CHÈN ẢNH SƠ ĐỒ LỰC]
A. 2 lực
*B. 3 lực
C. 4 lực
D. 5 lực
```

---

## 🎨 Định Dạng Ảnh Trong Word

### **Kích thước:**
- ✅ Khuyến nghị: 600-800px chiều rộng
- ✅ Tối đa: 5MB per ảnh
- ⚠️ Quá lớn: Sẽ bị reject khi upload manual

### **Định dạng:**
- ✅ PNG (khuyến nghị cho diagrams)
- ✅ JPG/JPEG (khuyến nghị cho photos)
- ✅ GIF (hỗ trợ)
- ✅ WebP (hỗ trợ)

### **Chất lượng:**
- ✅ Rõ nét, dễ đọc
- ✅ Nền trắng hoặc trong suốt
- ✅ Text trong ảnh đủ lớn

---

## 🔄 Xử Lý Của Hệ Thống

### **Bước 1: Pandoc Extract**
```
Word file → Pandoc --extract-media 
→ Ảnh được lưu vào /storage/app/converted/media/
```

### **Bước 2: Parse HTML**
```php
// Hệ thống tìm thẻ <img> trong HTML
$images = $dom->getElementsByTagName('img');
```

### **Bước 3: Upload**
```php
// Upload từng ảnh lên /public/exam_images/
// Tạo unique filename
$filename = uniqid() . '_' . time() . '.png';
```

### **Bước 4: Map vào câu hỏi**
```php
// Duyệt HTML để tìm:
// 1. "Câu X." → Bắt đầu câu hỏi
// 2. <img> → Gắn vào câu hiện tại
// 3. "Câu Y." → Kết thúc câu X, bắt đầu câu Y
```

### **Bước 5: Kết quả**
```json
{
  "question_no": 1,
  "content": "Quan sát hình vẽ...",
  "images": [
    {
      "url": "/exam_images/abc123.png",
      "name": "image1.png",
      "size": 45678,
      "type": "image/png"
    }
  ]
}
```

---

## 🎯 Best Practices

### **DO ✅:**

1. **Đặt ảnh NGAY SAU "Câu X.":**
   ```
   Câu 1. Nội dung
   [ẢNH]
   A. Đáp án
   ```

2. **Nhiều ảnh thì xếp liên tiếp:**
   ```
   Câu 2. So sánh:
   [ẢNH 1]
   [ẢNH 2]
   [ẢNH 3]
   A. Đáp án
   ```

3. **Đặt tên ảnh có ý nghĩa:**
   ```
   hinh_tam_giac.png
   do_thi_ham_so.png
   mach_dien_1.png
   ```

### **DON'T ❌:**

1. **KHÔNG đặt ảnh trước "Câu X.":**
   ```
   ❌ [ẢNH]
   Câu 1. Nội dung
   ```

2. **KHÔNG đặt ảnh sau đáp án:**
   ```
   ❌ Câu 1. Nội dung
   A. Đáp án
   [ẢNH]
   ```

3. **KHÔNG đặt ảnh giữa các đáp án:**
   ```
   ❌ Câu 1. Nội dung
   A. Đáp án A
   [ẢNH]
   B. Đáp án B
   ```

---

## 📊 Ví Dụ Thực Tế

### **File Word Mẫu:**

```
PHẦN I. Toán học

Câu 1. Tính diện tích hình sau:
[CHÈN ẢNH: HÌNH CHỮ NHẬT 8cm × 6cm]
A. 42 cm²
B. 46 cm²
*C. 48 cm²
D. 52 cm²
Lời giải: S = 8 × 6 = 48 cm²

Câu 2. So sánh diện tích 2 hình:
[CHÈN ẢNH: HÌNH VUÔNG 5×5]
[CHÈN ẢNH: HÌNH CHỮ NHẬT 6×4]
A. Hình 1 lớn hơn
*B. Hình 1 = Hình 2 (cùng 25 vs 24 → gần bằng)
C. Hình 2 lớn hơn
D. Không xác định

PHẦN II. Vật lý

Câu 3. Xác định loại mạch điện:
[CHÈN ẢNH: SƠ ĐỒ MẠCH ĐIỆN]
*A. Mạch nối tiếp
B. Mạch song song
C. Mạch hỗn hợp
D. Không xác định
Lời giải: Các điện trở mắc nối tiếp...

Câu 4. Đọc số chỉ đồng hồ đo:
[CHÈN ẢNH: ĐỒNG HỒ]
A. 2.5A
*B. 3.0A
C. 3.5A
D. 4.0A
```

### **Kết quả sau khi upload:**

- ✅ Câu 1: 1 ảnh (hình chữ nhật)
- ✅ Câu 2: 2 ảnh (hình vuông + hình chữ nhật)
- ✅ Câu 3: 1 ảnh (mạch điện)
- ✅ Câu 4: 1 ảnh (đồng hồ)

---

## 🔍 Debug Guide

### **Kiểm tra ảnh có được extract không:**

Sau khi upload Word file, check:
```
storage/app/converted/media/image1.png
storage/app/converted/media/image2.png
...
```

Nếu **KHÔNG CÓ** → Pandoc không extract được ảnh:
- Check Pandoc version: `pandoc --version` (cần >= 2.0)
- Check ảnh trong Word có bị corrupt không

### **Kiểm tra ảnh có được upload không:**

Check folder:
```
public/exam_images/abc123_1234567890.png
public/exam_images/def456_0987654321.png
...
```

Nếu **KHÔNG CÓ** → Upload failed:
- Check permissions của folder
- Check Laravel logs: `storage/logs/laravel.log`

### **Kiểm tra ảnh có map đúng câu hỏi không:**

Trong Vue component, check console:
```javascript
console.log(this.questions);
// Xem mỗi question có field 'images' không
```

---

## 🎨 Tips & Tricks

### **Tip 1: Căn chỉnh ảnh**
- Trong Word, căn giữa ảnh để đẹp hơn
- Hệ thống vẫn extract được bình thường

### **Tip 2: Caption cho ảnh**
- Có thể thêm caption dưới ảnh
- Caption sẽ không bị parse làm đáp án

```
Câu 1. Quan sát hình:
[ẢNH]
Hình 1: Sơ đồ mạch điện
A. Đáp án
```

### **Tip 3: Nhiều ảnh nhỏ**
- Có thể chèn nhiều ảnh nhỏ cạnh nhau
- Hệ thống sẽ parse theo thứ tự trái → phải, trên → dưới

### **Tip 4: Ảnh trong đáp án**
- ⚠️ Hiện tại CHƯA hỗ trợ ảnh trong đáp án
- Chỉ hỗ trợ ảnh trong **nội dung câu hỏi**

---

## 🎯 Luồng Xử Lý Chi Tiết

```
┌──────────────────────────────────────────┐
│  1. File Word với ảnh                    │
│     Câu 1. Nội dung                      │
│     [ẢNH 1]                              │
│     [ẢNH 2]                              │
│     A. Đáp án                            │
└──────────────┬───────────────────────────┘
               │
               ▼
┌──────────────────────────────────────────┐
│  2. Pandoc extract                       │
│     → /converted/media/image1.png        │
│     → /converted/media/image2.png        │
└──────────────┬───────────────────────────┘
               │
               ▼
┌──────────────────────────────────────────┐
│  3. HTML output                          │
│     <p>Câu 1. Nội dung</p>              │
│     <img src="media/image1.png">        │
│     <img src="media/image2.png">        │
│     <p>A. Đáp án</p>                    │
└──────────────┬───────────────────────────┘
               │
               ▼
┌──────────────────────────────────────────┐
│  4. extractAndUploadImages()             │
│     Tìm thẻ <img>                        │
│     Copy image1.png → /exam_images/      │
│     Copy image2.png → /exam_images/      │
│     Return: [                            │
│       0 => [url, name, size...],         │
│       1 => [url, name, size...]          │
│     ]                                    │
└──────────────┬───────────────────────────┘
               │
               ▼
┌──────────────────────────────────────────┐
│  5. mapImagesToQuestions()               │
│     Duyệt HTML:                          │
│     - Gặp "Câu 1" → currentQuestion = 1  │
│     - Gặp <img> → Add vào câu 1          │
│     - Gặp <img> → Add vào câu 1          │
│     - Gặp "Câu 2" → Switch to câu 2      │
└──────────────┬───────────────────────────┘
               │
               ▼
┌──────────────────────────────────────────┐
│  6. Kết quả:                             │
│     {                                    │
│       "question_no": 1,                  │
│       "content": "Nội dung",             │
│       "images": [                        │
│         {url: "/exam_images/...1.png"},  │
│         {url: "/exam_images/...2.png"}   │
│       ]                                  │
│     }                                    │
└──────────────┬───────────────────────────┘
               │
               ▼
┌──────────────────────────────────────────┐
│  7. Vue component hiển thị:              │
│     [Ảnh câu hỏi (2)]                    │
│     ┌─────┐ ┌─────┐                     │
│     │ IMG1│ │ IMG2│                      │
│     └─────┘ └─────┘                     │
└──────────────────────────────────────────┘
```

---

## ⚙️ Code Logic

### **mapImagesToQuestions() Logic:**

```php
$currentQuestionPart = null;  // PHẦN I, II, III...
$currentQuestionNo = null;    // 1, 2, 3...
$currentImageIndex = 0;       // Vị trí ảnh trong imageMapping

foreach ($allNodes as $node) {
    // 1. Gặp "PHẦN X" → Update currentQuestionPart
    if (preg_match('/^PHẦN\s+([IVXLCDM1-9]+)\./', $text)) {
        $currentQuestionPart = 'PHẦN ' . $romanNumber;
    }
    
    // 2. Gặp "Câu X." → Update currentQuestionNo
    if (preg_match('/^Câu\s+(\d+)\./', $text)) {
        $currentQuestionNo = (int)$number;
        // Tạo array rỗng cho câu hỏi này
        $questionPositions[$currentQuestionPart][$currentQuestionNo] = [
            'images' => []
        ];
    }
    
    // 3. Gặp <img> → Add vào câu hỏi hiện tại
    if ($node->nodeName === 'img' && $currentQuestionNo) {
        $questionPositions[$currentQuestionPart][$currentQuestionNo]['images'][] 
            = $imageMapping[$currentImageIndex];
        $currentImageIndex++;
    }
}
```

### **Ví dụ parsing:**

**HTML:**
```html
<p>PHẦN I. Toán</p>
<p>Câu 1. Nội dung</p>
<img src="media/image1.png">
<img src="media/image2.png">
<p>A. Đáp án</p>
<p>Câu 2. Nội dung</p>
<img src="media/image3.png">
<p>B. Đáp án</p>
```

**Kết quả:**
```php
$questionPositions = [
    'PHẦN I' => [
        1 => ['images' => [imageData1, imageData2]],
        2 => ['images' => [imageData3]]
    ]
];
```

---

## ❓ FAQ

### **Q1: Ảnh có thể đặt trước câu hỏi không?**
❌ **Không**. Ảnh phải đặt NGAY SAU dòng "Câu X."

### **Q2: Có thể có ảnh trong đáp án không?**
❌ **Chưa hỗ trợ**. Chỉ hỗ trợ ảnh trong nội dung câu hỏi.

### **Q3: Tối đa bao nhiêu ảnh cho 1 câu?**
✅ **Không giới hạn**. Nhưng khuyến nghị 1-3 ảnh/câu.

### **Q4: Ảnh bị mờ sau khi upload?**
✅ Pandoc giữ nguyên chất lượng. Check ảnh gốc trong Word.

### **Q5: Hệ thống có resize ảnh không?**
❌ **Không**. Ảnh giữ nguyên kích thước gốc.

### **Q6: File Word không có PHẦN thì sao?**
✅ Hệ thống **TỰ ĐỘNG** tạo "PHẦN I. Câu hỏi chung"

### **Q7: Ảnh trong Word bị corrupt?**
⚠️ Pandoc sẽ skip ảnh đó. Check console log.

---

## 🧪 Test Cases

### **Test 1: 1 câu, 1 ảnh**
```
Câu 1. Nội dung
[ẢNH]
A. Đáp án
```
**Expected**: Question 1 có 1 ảnh

### **Test 2: 1 câu, nhiều ảnh**
```
Câu 1. Nội dung
[ẢNH 1]
[ẢNH 2]
[ẢNH 3]
A. Đáp án
```
**Expected**: Question 1 có 3 ảnh theo thứ tự

### **Test 3: Nhiều câu, mỗi câu có ảnh**
```
Câu 1. Nội dung
[ẢNH A]
A. Đáp án

Câu 2. Nội dung  
[ẢNH B]
A. Đáp án

Câu 3. Nội dung
[ẢNH C]
A. Đáp án
```
**Expected**: 
- Question 1 → ẢNH A
- Question 2 → ẢNH B
- Question 3 → ẢNH C

### **Test 4: Không có PHẦN**
```
Câu 1. Nội dung
[ẢNH]
A. Đáp án
```
**Expected**: Tự tạo "PHẦN I. Câu hỏi chung"

---

## 🎨 Template Word Mẫu

```
════════════════════════════════════════════════════════════
                    ĐỀ THI MẪU CÓ ẢNH
════════════════════════════════════════════════════════════

PHẦN I. TRẮC NGHIỆM

Câu 1. Quan sát sơ đồ sau và xác định loại tam giác:

[CHÈN ẢNH: TAM GIÁC ABC VỚI 3 CẠNH BẰNG NHAU]

A. Tam giác vuông
B. Tam giác cân
*C. Tam giác đều
D. Tam giác thường

Lời giải: Tam giác có 3 cạnh bằng nhau nên là tam giác đều.

────────────────────────────────────────────────────────────

Câu 2. So sánh độ dài 2 đoạn thẳng:

[CHÈN ẢNH: ĐOẠN AB DÀI 5cm]
[CHÈN ẢNH: ĐOẠN CD DÀI 7cm]

A. AB > CD
*B. AB < CD
C. AB = CD
D. Không so sánh được

════════════════════════════════════════════════════════════

PHẦN II. ĐÚNG/SAI

Câu 3. Xét tính đúng/sai của các phát biểu về hình sau:

[CHÈN ẢNH: HÌNH TRÒN BÁN KÍNH R=5cm]

*a) Chu vi = 10π cm
*b) Diện tích = 25π cm²
c) Đường kính = 5 cm
d) Bán kính = 10 cm

Lời giải: 
- a) Đúng: C = 2πr = 10π
- b) Đúng: S = πr² = 25π
- c) Sai: d = 2r = 10cm
- d) Sai: r = 5cm

════════════════════════════════════════════════════════════
```

---

## 🚀 **Kết Luận**

### **Vị trí ảnh CHUẨN:**
```
Câu X. [Nội dung câu hỏi]
[ẢNH 1] ← ĐÂY
[ẢNH 2] ← ĐÂY
[ẢNH 3] ← ĐÂY
A. Đáp án A
B. Đáp án B
```

### **Quy tắc vàng:**
1. ✅ Ảnh NGAY SAU "Câu X."
2. ✅ Ảnh TRƯỚC đáp án
3. ✅ Nhiều ảnh xếp liên tiếp
4. ❌ KHÔNG đặt ảnh trước "Câu X."
5. ❌ KHÔNG đặt ảnh sau đáp án

---

**Created**: October 16, 2025  
**Status**: ✅ Ready to use  
**Version**: 1.0

