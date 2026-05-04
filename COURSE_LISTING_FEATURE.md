# Tính năng Hiển thị Danh sách Khóa học (Course Listing Feature)

## Tổng quan
Tính năng này cập nhật trang danh sách khóa học online để có giao diện và cách xử lý tương tự như trang danh sách đề thi, với khả năng:
- Hiển thị danh mục khóa học với scroll ngang
- Hiển thị loại khóa học  
- Nhóm khóa học theo TypeProductTwo (Nhóm khóa học)
- Accordion để mở/đóng từng nhóm
- Load more qua AJAX cho từng nhóm

## Cấu trúc dữ liệu

### Models liên quan:
1. **Category (product_category)** - Danh mục khóa học chính (tương đương CategoryMain trong đề thi)
2. **TypeProduct (product_type)** - Loại khóa học (tương đương QuizCategory trong đề thi)
3. **TypeProductTwo (product_type_two)** - Nhóm khóa học chi tiết (tương đương TypeCategory trong đề thi)
4. **Product (products)** - Khóa học (tương đương Dethi trong đề thi)

### Quan hệ:
- Category hasMany Product
- Category hasMany TypeProduct
- TypeProduct hasMany TypeProductTwo
- Product belongsTo Category
- Product belongsTo TypeProduct (via type_cate)
- Product belongsTo TypeProductTwo (via type_two)

## Files đã thay đổi

### 1. Controller: `app/Http/Controllers/Client/CouseController.php`

#### Phương thức `couseList()` (dòng 230-306)
**Chức năng:**
- Lấy tất cả danh mục khóa học (categories)
- Lấy tất cả loại khóa học (types)
- Lấy tất cả nhóm khóa học (courseTypes via TypeProductTwo)
- Lấy tất cả khóa học đang active
- Nhóm khóa học theo TypeProductTwo
- Khóa học không có type_two sẽ được nhóm vào "Khóa học khác"
- Mỗi nhóm chỉ hiển thị 10 khóa đầu tiên, các khóa còn lại load qua AJAX

**Data trả về view:**
```php
- coursesByType: Array chứa các nhóm khóa học
  - type: Object TypeProductTwo
  - courses: Collection 10 khóa học đầu
  - count: Tổng số khóa học trong nhóm
  - total: Tổng số khóa học
- categories: Collection các danh mục
- types: Collection các loại khóa học
- courseTypes: Collection nhóm khóa học, grouped by cate_id
- totalCourses: Tổng số khóa học
- selectedCategory: null (dùng cho filter)
- selectedType: null (dùng cho filter)
- selectedCourseType: null (dùng cho filter)
```

#### Phương thức `loadMoreCourses()` (dòng 307-372)
**Chức năng:**
- API endpoint cho AJAX load more
- Nhận parameters: type_id, offset, per_page, category_id, type_product_id
- Filter khóa học theo type_two
- Hỗ trợ filter theo category và type_cate
- Trả về JSON với danh sách khóa học và metadata

**Request params:**
```json
{
  "type_id": "ID của TypeProductTwo",
  "offset": "Số khóa học đã load",
  "per_page": "Số khóa học cần load thêm (mặc định 10)",
  "category_id": "Filter theo danh mục (optional)",
  "type_product_id": "Filter theo loại khóa học (optional)"
}
```

**Response:**
```json
{
  "success": true,
  "courses": [
    {
      "id": 1,
      "name": "Tên khóa học",
      "slug": "slug",
      "description": "Mô tả",
      "images": "URL ảnh",
      "price": 100000,
      "discount": 10,
      "ingredient": 10,
      "thickness": 5,
      "category": "Tên danh mục",
      "type_cate": "Tên loại",
      "customer": {
        "name": "Tên giáo viên",
        "avatar": "URL avatar"
      }
    }
  ],
  "has_more": true,
  "remaining": 15,
  "total": 25
}
```

#### Phương thức `listCategoryMainCourse($id)` (dòng 374-463)
**Chức năng:**
- Filter khóa học theo Danh mục (Category)
- Lấy tất cả danh mục để hiển thị grid
- Chỉ lấy các loại khóa học thuộc danh mục được chọn
- Chỉ lấy các nhóm khóa học thuộc danh mục được chọn
- Chỉ lấy các khóa học có category = $id
- Nhóm khóa học theo TypeProductTwo
- Set `$selectedCategory` để highlight active state
- Trả về cùng view `couse.listAll` với dữ liệu đã filter

**Data trả về view:**
```php
- coursesByType: Array các nhóm khóa học (đã filter theo danh mục)
- categories: Collection tất cả danh mục
- types: Collection loại khóa học (chỉ thuộc danh mục được chọn)
- courseTypes: Collection nhóm khóa học, grouped by cate_id
- totalCourses: Tổng số khóa học trong danh mục
- selectedCategory: Object Category được chọn (để highlight)
- selectedType: null
- selectedCourseType: null
```

#### Phương thức `listTypeCourse($id)` (dòng 465-553)
**Chức năng:**
- Filter khóa học theo Loại khóa học (TypeProduct)
- Lấy tất cả danh mục để hiển thị grid
- Chỉ lấy các loại khóa học thuộc cùng danh mục với loại được chọn
- Chỉ lấy các nhóm khóa học thuộc loại được chọn
- Chỉ lấy các khóa học có type_cate = $id
- Nhóm khóa học theo TypeProductTwo
- Set `$selectedType` và `$selectedCategory` để highlight active state
- Trả về cùng view `couse.listAll` với dữ liệu đã filter

**Data trả về view:**
```php
- coursesByType: Array các nhóm khóa học (đã filter theo loại)
- categories: Collection tất cả danh mục
- types: Collection loại khóa học (chỉ thuộc cùng danh mục)
- courseTypes: Collection nhóm khóa học, grouped by type_id
- totalCourses: Tổng số khóa học thuộc loại
- selectedCategory: Object Category parent (từ type_cate)
- selectedType: Object TypeProduct được chọn (để highlight)
- selectedCourseType: null
```

### 2. Routes: `routes/web.php`

#### Routes mới:
```php
// Load more AJAX
Route::post('khoa-hoc-online/load-more', 'CouseController@loadMoreCourses')->name('loadMoreCourses');

// Filter theo danh mục
Route::get('khoa-hoc-online/danh-muc/{id}.html', 'CouseController@listCategoryMainCourse')->name('listCategoryMainCourse');

// Filter theo loại khóa học
Route::get('khoa-hoc-online/loai/{id}.html', 'CouseController@listTypeCourse')->name('listTypeCourse');
```

### 3. View: `resources/views/couse/listAll.blade.php`

#### Cấu trúc layout:
1. **Smart Header** - Breadcrumb navigation
2. **Danh sách Danh mục** - Grid scroll ngang với các danh mục khóa học
3. **Danh sách Loại khóa học** - Grid scroll ngang với các loại khóa học
4. **Danh sách Nhóm khóa học và Khóa học** - Accordion groups

#### Sections chính:

**1. Smart Header (breadcrumb)**
- Hiển thị đường dẫn: Trang chủ > Khóa học > [Filter hiện tại]

**2. Quick Actions - Categories**
- Grid scroll ngang
- Scroll indicators (mũi tên trái/phải)
- Nút "Xem thêm/Thu gọn" nếu có > 6 items
- Active state khi được chọn

**3. Quick Actions - Types**
- Tương tự Categories
- Chỉ hiển thị khi có dữ liệu

**4. Course Type Groups (Accordion)**
- Mỗi nhóm là 1 accordion item
- Click header để mở/đóng
- Hiển thị số lượng khóa học trong header
- **Course Cards trong body** (Grid 3 cột responsive):
  - **Hình ảnh khóa học**: Thumbnail với link
  - **Giá**: Hiển thị giá gốc và giá giảm (nếu có)
  - **Tiêu đề**: Tên khóa học với link
  - **Action button**:
    - "Chi tiết" - Link đến trang chi tiết khóa học
  - **Thông tin giáo viên**: Avatar + tên + role
  - **Meta info**: 
    - Số bài học (icon book)
    - Số buổi học (icon clock)
    - "Bài tập online" (icon book)
- Nút "Xem thêm" nếu nhóm có > 10 khóa học

#### JavaScript Functions:

**toggleExamType(index)**
- Mở/đóng accordion group theo index

**loadMoreCourses(index)**
- Load thêm khóa học qua AJAX
- Disable button khi đang load
- Render HTML cho các khóa học mới
- Cập nhật counter và button state
- Animation fade in cho items mới

**createCourseHTML(course)**
- Tạo HTML cho 1 khóa học
- Handle logic hiển thị giá, giảm giá
- Handle button "Thêm giỏ hàng" vs "Xem chi tiết"

**scrollActions(type, direction)**
- Scroll grid theo hướng trái/phải
- Cập nhật scroll indicators

**updateScrollIndicators(type)**
- Hiển thị/ẩn mũi tên scroll dựa trên vị trí scroll

**toggleShowMore(type)**
- Mở rộng/thu gọn grid khi có nhiều items

**initializeScrollIndicators()**
- Khởi tạo scroll indicators khi load trang
- Bind events cho scroll và resize

#### Event Handlers:

**DOMContentLoaded**
- Initialize scroll indicators
- Mở accordion đầu tiên mặc định
- Bind event "Thêm giỏ hàng" (event delegation)
- Smooth scroll cho anchor links
- Animation cho course items

**Course card click**
- Click anywhere trên card để xem chi tiết
- Click nút "Chi tiết" để chuyển đến trang chi tiết khóa học

### 4. CSS: Kết hợp nhiều sources
- **File `public/frontend/css/listall.css`**: Cho layout chính
  - `.smart-exam-listing`
  - `.smart-header`
  - `.quick-actions`
  - `.exam-type-group`
  
- **Inline CSS trong view**: Grid layout cho course cards
  - `.courses-grid-container`: Grid 3 cột responsive
  - `.courses-two__single`: Override width và margin
  - Animation `fadeInUp` cho load effect
  
- **CSS có sẵn từ theme**: Course card design
  - `.courses-two__single`: Card container
  - `.courses-two__img-box`: Image container
  - `.courses-two__content`: Content area
  - `.courses-two__btn-and-client-box`: Actions và teacher info
  - `.courses-two__meta`: Meta information list

## Tính năng chính

### 1. Filter và Navigation
- **Click vào danh mục**: Filter khóa học theo danh mục đó
  - URL: `/khoa-hoc-online/danh-muc/{id}.html`
  - Chỉ hiển thị loại khóa học và nhóm khóa học thuộc danh mục đó
  - Chỉ hiển thị khóa học có `category = id`
  - Active state trên button danh mục được chọn
  
- **Click vào loại khóa học**: Filter khóa học theo loại đó
  - URL: `/khoa-hoc-online/loai/{id}.html`
  - Chỉ hiển thị nhóm khóa học thuộc loại đó
  - Chỉ hiển thị khóa học có `type_cate = id`
  - Active state trên button loại được chọn
  - Tự động highlight danh mục parent
  
- **Breadcrumb**: Hiển thị đường dẫn hiện tại
  - Trang chủ > Khóa học > [Danh mục/Loại được chọn]

### 2. Accordion Groups
- Mỗi nhóm khóa học (TypeProductTwo) là 1 accordion
- Click header để mở/đóng
- Accordion đầu tiên mở mặc định
- Hiển thị số lượng khóa học trong header

### 3. Load More
- Mỗi nhóm ban đầu hiển thị 10 khóa học
- Nút "Xem thêm" nếu có nhiều hơn 10
- Load 10 khóa tiếp theo mỗi lần click
- AJAX không reload trang
- Loading state với spinner
- Ẩn nút khi đã load hết

### 4. Responsive Grid
- Scroll ngang cho categories và types
- Scroll indicators (mũi tên)
- Auto hide/show indicators dựa vào scroll position
- Toggle xem thêm/thu gọn khi có > 6 items

### 5. Course Display
- Card layout với icon, title, meta info
- Hiển thị giảm giá với badge và giá gạch ngang
- Thông tin giáo viên với avatar
- Button khác nhau cho khóa trả phí vs miễn phí
- Animation fade in khi load

### 6. Course Detail Navigation
- Nút "Chi tiết" cho tất cả khóa học
- Link trực tiếp đến trang chi tiết khóa học
- Người dùng có thể xem thông tin đầy đủ trước khi quyết định đăng ký/mua

## Cách sử dụng

### 1. Truy cập trang chính
```
URL: /khoa-hoc-online.html
Route name: couseList
Method: GET
Hiển thị: Tất cả khóa học, grouped theo TypeProductTwo
```

### 2. Filter theo danh mục
```
URL: /khoa-hoc-online/danh-muc/{id}.html
Route name: listCategoryMainCourse
Method: GET
Params: id (Category ID)
Hiển thị: Chỉ khóa học thuộc danh mục này
```

### 3. Filter theo loại khóa học
```
URL: /khoa-hoc-online/loai/{id}.html
Route name: listTypeCourse
Method: GET
Params: id (TypeProduct ID)
Hiển thị: Chỉ khóa học thuộc loại này
```

### 4. Filter theo slug (legacy)
```
URL: /khoa-hoc-online/{cate_slug}.html
Route name: couseListCate
Method: GET
Note: Route cũ, vẫn giữ để tương thích ngược
```

### 3. Xem chi tiết khóa học
```
Click vào nút "Chi tiết" hoặc tiêu đề/hình ảnh khóa học
Chuyển đến trang chi tiết với route: couseDetail
```

## Cấu hình

### Database
Các bảng cần có:
- `product_category` - Danh mục khóa học
- `product_type` - Loại khóa học
- `product_type_two` - Nhóm khóa học
- `products` - Khóa học

### Fields quan trọng trong `products`:
- `category` - ID danh mục
- `type_cate` - ID loại khóa học
- `type_two` - ID nhóm khóa học (để group)
- `status` - 1 = active
- `price` - Giá khóa học
- `discount` - % giảm giá
- `ingredient` - Số bài học
- `thickness` - Số buổi học
- `user_id` - ID giáo viên

## Lưu ý kỹ thuật

1. **Performance**
   - Ban đầu chỉ load 10 khóa/nhóm
   - AJAX pagination cho các khóa còn lại
   - Không load toàn bộ dữ liệu lên frontend

2. **UX/UI**
   - Animation smooth khi load more
   - Loading state rõ ràng
   - Scroll indicators tự động
   - Responsive design

3. **Code Organization**
   - Tái sử dụng CSS từ tính năng đề thi
   - JavaScript functions modular
   - Event delegation cho dynamic content
   - Consistent naming convention

4. **Security**
   - CSRF token cho AJAX requests
   - Input validation
   - Filter chỉ load khóa học status = 1

## Mở rộng tương lai

1. **Filter nâng cao**
   - Filter theo giá
   - Filter theo giáo viên
   - Search trong trang

2. **Sorting**
   - Sắp xếp theo giá
   - Sắp xếp theo mới nhất
   - Sắp xếp theo phổ biến

3. **Pagination**
   - Thêm pagination cho toàn bộ trang
   - Infinite scroll

4. **Caching**
   - Cache danh sách khóa học
   - Cache counter

## Testing

### Test cases cơ bản:
1. **Load trang ban đầu** - hiển thị đúng cấu trúc, tất cả danh mục và loại
2. **Click danh mục** - filter đúng khóa học, highlight active, breadcrumb cập nhật
3. **Click loại khóa học** - filter đúng khóa học, highlight active, breadcrumb cập nhật
4. **Click accordion** - mở/đóng đúng nhóm
5. **Click "Xem thêm"** - load đúng 10 khóa tiếp theo, giữ filter
6. **Click "Chi tiết"** - chuyển đến trang chi tiết khóa học
7. **Scroll grid** - indicators hiển thị/ẩn đúng
8. **Responsive** - hoạt động trên mobile
9. **Active states** - highlight đúng khi filter
10. **Load more với filter** - chỉ load khóa học thuộc filter hiện tại

## Changelog

### Version 1.3 (2025-10-20 - Update 4)
- ✅ Thay đổi tất cả nút thành "Chi tiết"
- ✅ Loại bỏ logic phân biệt button cho khóa trả phí/miễn phí
- ✅ Tất cả khóa học đều link đến trang chi tiết (couseDetail)
- ✅ Loại bỏ JavaScript xử lý giỏ hàng trong trang listing
- ✅ Đơn giản hóa flow: Listing → Detail → Cart

### Version 1.2 (2025-10-20 - Update 3)
- ✅ Thay đổi UI course cards sử dụng design từ trang home
- ✅ Grid layout 3 cột responsive cho course cards
- ✅ Card design với hình ảnh lớn và layout đẹp hơn
- ✅ Giá hiển thị rõ ràng với giá gốc và giá giảm
- ✅ Button actions đồng nhất cho tất cả khóa học
- ✅ Animation fade-in cho load more
- ✅ Responsive: 3 cột desktop, 2 cột tablet, 1 cột mobile
- ✅ Đổi tên "Khóa học khác" thành "Danh sách khóa học"

### Version 1.1 (2025-10-20 - Update 2)
- ✅ Thêm filter theo danh mục (listCategoryMainCourse)
- ✅ Thêm filter theo loại khóa học (listTypeCourse)
- ✅ Active state highlighting khi filter
- ✅ Breadcrumb cập nhật theo filter
- ✅ Load more giữ được filter state
- ✅ Routes mới cho filter

### Version 1.0 (2025-10-20)
- ✅ Tạo layout mới cho trang danh sách khóa học
- ✅ Implement accordion groups theo TypeProductTwo
- ✅ AJAX load more cho từng nhóm
- ✅ Scroll indicators cho grids
- ✅ Integration với giỏ hàng
- ✅ Reuse CSS từ tính năng đề thi

## Tác giả
AI Assistant - Cursor IDE

## License
Thuộc dự án toanthaytuuver2

