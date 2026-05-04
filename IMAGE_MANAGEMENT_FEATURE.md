# Tính năng Quản lý Ảnh cho Câu hỏi Đề thi

## Tổng quan
Tính năng này cho phép người dùng thêm, sửa, xóa ảnh cho từng câu hỏi trong đề thi thông qua giao diện preview ở `previewuploaddethi.vue`.

## Các tính năng chính

### 1. Thêm ảnh cho câu hỏi
- Click vào nút "Ảnh" trong toolbar của mỗi câu hỏi
- Chọn một hoặc nhiều file ảnh (hỗ trợ JPEG, PNG, GIF, WebP)
- Kích thước file tối đa: 5MB
- Ảnh sẽ được upload lên server và lưu vào thư mục `public/exam_images/`

### 2. Xem ảnh
- Ảnh được hiển thị dưới dạng gallery với layout responsive
- Hover để hiển thị các nút thao tác (sửa, xóa)
- Ảnh được hiển thị với tỷ lệ khung hình cố định và object-fit: cover

### 3. Sửa ảnh
- Click nút sửa (biểu tượng edit) trên ảnh
- Modal hiển thị ảnh hiện tại và cho phép chọn ảnh mới
- Ảnh mới sẽ thay thế ảnh cũ

### 4. Xóa ảnh
- Click nút xóa (biểu tượng trash) trên ảnh
- Xác nhận trước khi xóa
- Ảnh sẽ bị xóa khỏi câu hỏi

## Cấu trúc dữ liệu

### Trong database
- Bảng `dethi_questions` sử dụng trường `image` kiểu JSON (đã có sẵn)
- Lưu trữ thông tin ảnh: URL, tên file, kích thước, loại file, thời gian upload

### Trong component Vue
```javascript
question.images = [
  {
    url: '/exam_images/uuid-filename.jpg',
    name: 'original-filename.jpg',
    size: 1024000,
    type: 'image/jpeg',
    uploaded_at: '2025-01-20T10:30:00.000Z',
    server_filename: 'uuid-filename.jpg'
  }
]
```

## API Endpoints

### Upload ảnh
```
POST /api/upload-question-image
Content-Type: multipart/form-data

Body:
- image: file (ảnh cần upload)

Response:
{
  "success": true,
  "url": "/exam_images/uuid-filename.jpg",
  "filename": "uuid-filename.jpg",
  "size": 1024000,
  "type": "image/jpeg"
}
```

## Cài đặt và sử dụng

### 1. Kiểm tra cấu trúc database
- Trường `image` đã có sẵn trong bảng `dethi_questions`
- Không cần chạy migration mới

### 2. Đảm bảo thư mục upload có quyền ghi
```bash
chmod 775 public/exam_images
```

### 3. Sử dụng trong component
- Component đã được cập nhật tự động
- Không cần thay đổi gì thêm

## Tính năng bảo mật

### Validation file
- Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WebP)
- Kích thước file tối đa 5MB
- Tên file được generate bằng UUID để tránh conflict

### Xử lý lỗi
- Hiển thị thông báo lỗi rõ ràng
- Rollback khi có lỗi xảy ra
- Log lỗi để debug

## Responsive Design

### Desktop
- Gallery hiển thị 3-4 ảnh trên một hàng
- Hover effects cho các nút thao tác
- Modal đầy đủ thông tin

### Mobile
- Gallery hiển thị 2-3 ảnh trên một hàng
- Nút thao tác luôn hiển thị
- Modal tối ưu cho màn hình nhỏ

## Tương thích

### Browser Support
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

### Framework Support
- Laravel 8+
- Vue.js 2.6+
- Axios

## Troubleshooting

### Lỗi thường gặp

1. **Không upload được ảnh**
   - Kiểm tra quyền thư mục `public/exam_images`
   - Kiểm tra kích thước file (tối đa 5MB)
   - Kiểm tra loại file (chỉ ảnh)

2. **Ảnh không hiển thị**
   - Kiểm tra đường dẫn file
   - Kiểm tra quyền truy cập file
   - Kiểm tra console browser

3. **Lỗi database**
   - Chạy migration: `php artisan migrate`
   - Kiểm tra cấu trúc bảng `dethi_questions`

### Debug
- Kiểm tra console browser
- Kiểm tra log Laravel: `storage/logs/laravel.log`
- Kiểm tra network tab trong DevTools

## Phát triển tương lai

### Tính năng có thể thêm
- Drag & drop để sắp xếp ảnh
- Crop/resize ảnh trước khi upload
- Watermark tự động
- Compression ảnh
- Backup ảnh
- Quản lý ảnh tập trung

### Tối ưu hóa
- Lazy loading ảnh
- Progressive image loading
- WebP format với fallback
- CDN integration
