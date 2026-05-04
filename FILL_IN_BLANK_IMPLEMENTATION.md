# Triển khai Câu hỏi Điền đáp án [DT]

## Tổng quan
Đã thêm hỗ trợ cho dạng câu hỏi điền đáp án với cấu trúc `[DT]` vào hệ thống đề thi.

## Cấu trúc câu hỏi
```
Câu 2. [DT] The apple ___ the table.
Đáp án: On
```

## Các thay đổi đã thực hiện

### 1. Phương thức parse nội dung
- **`parseHtmlContent()`**: Thêm xử lý cho câu hỏi có `[DT]`
- **`parseTextContent()`**: Thêm xử lý cho câu hỏi có `[DT]`
- **`parseTextContentForSingleQuestion()`**: Thêm xử lý cho câu hỏi có `[DT]`

### 2. Xử lý chấm điểm
- **`submitTest()`**: Thêm case `fill_in_blank` để chấm điểm tự động
- So sánh không phân biệt hoa thường và bỏ qua khoảng trắng thừa

### 3. Thống kê và báo cáo
- **`result()`**: Thêm thống kê cho câu hỏi điền đáp án
- **`chamdiem()`**: Thêm thống kê cho câu hỏi điền đáp án
- **`getExamResult()`**: Cập nhật để hiển thị đúng đáp án

### 4. Export/Import
- **`exportExamToText()`**: Thêm hỗ trợ export câu hỏi điền đáp án
- **`exportExamToTextQuestion()`**: Thêm hỗ trợ export câu hỏi điền đáp án

## Cách sử dụng

### 1. Tạo câu hỏi điền đáp án
```
Câu 1. [DT] The apple ___ the table.
Đáp án: on
```

### 2. Lưu vào database
- `question_type`: `fill_in_blank`
- `content`: `The apple ___ the table.`
- `answers`: Một record với `label: 'answer'`, `content: 'on'`, `is_correct: 1`

### 3. Chấm điểm tự động
- Học sinh nhập: `on`, `On`, `ON`, ` on ` (có khoảng trắng)
- Hệ thống sẽ chấm đúng tự động

## Lưu ý
- Đáp án được so sánh không phân biệt hoa thường
- Khoảng trắng thừa sẽ được loại bỏ
- Chỉ có một đáp án đúng cho mỗi câu hỏi điền đáp án
- Điểm mặc định là 1 điểm nếu không có cấu hình điểm riêng

## Test
File `test_fill_in_blank.txt` chứa ví dụ test cho câu hỏi điền đáp án. 