@extends('crm_course.main.master')
@section('title')
Danh sách đề thi
@endsection
@section('description')
Danh sách đề thi
@endsection
@section('image')
@endsection
@section('css_crm_course')
<style>
    .resource-toolbar {
        background: #fff;
        border-radius: 12px;
        padding: 18px 24px;
        box-shadow: 0 15px 30px rgba(82, 63, 105, 0.08);
        margin-bottom: 20px;
    }
    .resource-breadcrumb a {
        color: #2d3a8c;
        font-weight: 600;
        text-decoration: none;
    }
    .resource-breadcrumb span {
        color: #7c8aa5;
        margin: 0 8px;
    }
    .resource-table thead th {
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #7c8aa5;
        border-bottom: 0;
    }
    .resource-table tbody td {
        vertical-align: middle;
        border-top: 0;
        border-bottom: 1px solid #f1f4f8;
    }
    .resource-info {
        display: flex;
        align-items: center;
    }
    .resource-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 14px;
        background: #eef2ff;
        color: #2d3a8c;
        font-size: 18px;
    }
    .resource-icon.exam {
        background: #fff3e8;
        color: #ff7a18;
    }
    .resource-meta {
        color: #7c8aa5;
        font-size: 14px;
    }
    .resource-status {
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .resource-status.published {
        background: rgba(52, 199, 89, .15);
        color: #24a148;
    }
    .resource-status.draft {
        background: rgba(255, 149, 0, .15);
        color: #c87500;
    }
    .resource-status.closed {
        background: rgba(19, 104, 206, .15);
        color: #1368ce;
    }
    .resource-table .badge {
        font-size: 11px;
        background: #eef2ff;
        color: #2d3a8c;
        border-radius: 999px;
        padding: 4px 10px;
    }
    .folder-move-form select {
        font-size: 12px;
    }
    .resource-empty {
        text-align: center;
        padding: 60px 20px;
        color: #7c8aa5;
    }
</style>
@endsection
@section('js_crm_course')
<!-- MathJax CDN for LaTeX rendering -->
<script>
  window.MathJax = {
    tex: {
      inlineMath: [['$', '$'], ['\\(', '\\)']],
      displayMath: [['$$', '$$'], ['\\[', '\\]']],
      processEscapes: true,
      processEnvironments: true
    },
    options: {
      skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre']
    }
  };
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script>
    // Toggle select all checkboxes
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.item-checkbox:not(:disabled)');
        checkboxes.forEach(cb => {
            cb.checked = checkbox.checked;
        });
        updateBulkActions();
    }

    // Update bulk actions visibility and count
    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');
        
        if (!bulkActions) return;
        
        // Kiểm tra nếu có cut/copy session hoặc có checkbox được chọn
        const hasCheckedItems = checkboxes.length > 0;
        const hasCutData = typeof hasCutSession !== 'undefined' && hasCutSession;
        const hasCopyData = typeof hasCopySession !== 'undefined' && hasCopySession;
        const hasAnyData = hasCutData || hasCopyData;
        
        if (hasCheckedItems || hasAnyData) {
            bulkActions.style.display = 'flex';
            bulkActions.classList.remove('d-none');
            
            if (hasCheckedItems) {
                selectedCount.textContent = `Đã chọn: ${checkboxes.length} mục`;
            } else if (hasAnyData) {
                selectedCount.textContent = '';
            }
        } else {
            bulkActions.style.display = 'none';
            bulkActions.classList.add('d-none');
        }
        
        // Update select all checkbox state
        const allCheckboxes = document.querySelectorAll('.item-checkbox');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
        }
    }

    // Show delete confirmation modal
    function showDeleteConfirmModal() {
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Vui lòng chọn ít nhất một mục để xóa.');
            return;
        }

        const folders = [];
        const dethis = [];
        
        checkboxes.forEach(cb => {
            const type = cb.getAttribute('data-type');
            const id = cb.getAttribute('data-id');
            const name = cb.getAttribute('data-name');
            
            if (type === 'folder') {
                folders.push({ id, name });
            } else if (type === 'dethi') {
                dethis.push({ id, name });
            }
        });

        // Build warning content
        let warningContent = '<ul class="mb-0">';
        if (folders.length > 0) {
            warningContent += `<li><strong>${folders.length} thư mục</strong> sẽ bị xóa, bao gồm:</li>`;
            warningContent += '<ul>';
            warningContent += '<li>Tất cả thư mục con bên trong</li>';
            warningContent += '<li>Tất cả đề thi trong các thư mục đó</li>';
            warningContent += '</ul>';
        }
        if (dethis.length > 0) {
            warningContent += `<li><strong>${dethis.length} đề thi</strong> sẽ bị xóa</li>`;
        }
        warningContent += '<li class="mt-2"><strong class="text-danger">TẤT CẢ dữ liệu học sinh làm bài</strong> (kết quả, điểm số, câu trả lời) sẽ bị xóa vĩnh viễn</li>';
        warningContent += '<li><strong class="text-danger">TẤT CẢ các file liên quan</strong> (audio, hình ảnh) sẽ bị xóa</li>';
        warningContent += '<li class="mt-2"><strong>Hành động này không thể hoàn tác!</strong></li>';
        warningContent += '</ul>';

        // Build items list
        let itemsList = '<div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">';
        if (folders.length > 0) {
            itemsList += '<h6 class="text-primary"><i class="fas fa-folder"></i> Thư mục:</h6><ul>';
            folders.forEach(folder => {
                itemsList += `<li>${folder.name}</li>`;
            });
            itemsList += '</ul>';
        }
        if (dethis.length > 0) {
            itemsList += '<h6 class="text-warning mt-2"><i class="fas fa-file-alt"></i> Đề thi:</h6><ul>';
            dethis.forEach(dethi => {
                itemsList += `<li>${dethi.name}</li>`;
            });
            itemsList += '</ul>';
        }
        itemsList += '</div>';

        document.getElementById('deleteWarningContent').innerHTML = warningContent;
        document.getElementById('deleteItemsList').innerHTML = itemsList;

        // Store selected items for deletion
        window.selectedFolders = folders.map(f => f.id);
        window.selectedDethis = dethis.map(d => d.id);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('deleteBulkModal'));
        modal.show();
    }

    // Confirm bulk delete
    function confirmBulkDelete() {
        const folders = window.selectedFolders || [];
        const dethis = window.selectedDethis || [];

        if (folders.length === 0 && dethis.length === 0) {
            alert('Không có mục nào được chọn.');
            return;
        }

        // Show loading
        const deleteBtn = event.target;
        const originalText = deleteBtn.innerHTML;
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xóa...';

        // Prepare data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        if (folders.length > 0) {
            folders.forEach(id => {
                formData.append('folder_ids[]', id);
            });
        }
        if (dethis.length > 0) {
            dethis.forEach(id => {
                formData.append('dethi_ids[]', id);
            });
        }

        // Send delete request
        fetch('/de-thi/bulk-delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteBulkModal'));
                modal.hide();
                // Reload page
                window.location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Không thể xóa'));
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa. Vui lòng thử lại.');
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = originalText;
        });
    }

    // Show publish confirmation
    function showPublishConfirmModal() {
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Vui lòng chọn ít nhất một đề thi để xuất bản.');
            return;
        }

        const dethis = [];
        
        checkboxes.forEach(cb => {
            const type = cb.getAttribute('data-type');
            const id = cb.getAttribute('data-id');
            const name = cb.getAttribute('data-name');
            
            // Chỉ xuất bản đề thi, không xuất bản folder
            if (type === 'dethi') {
                dethis.push({ id, name });
            }
        });

        if (dethis.length === 0) {
            alert('Vui lòng chọn đề thi để xuất bản. Không thể xuất bản thư mục.');
            return;
        }

        // Build confirmation content
        let confirmContent = `<p>Bạn có chắc chắn muốn xuất bản <strong>${dethis.length} đề thi</strong> không?</p>`;
        confirmContent += '<p>Khi xuất bản, đề thi sẽ được chuyển sang trạng thái "Đã xuất bản" (status = 1).</p>';
        
        let itemsList = '<div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">';
        itemsList += '<h6 class="text-primary"><i class="fas fa-file-alt"></i> Các đề thi sẽ được xuất bản:</h6><ul>';
        dethis.forEach(dethi => {
            itemsList += `<li>${dethi.name}</li>`;
        });
        itemsList += '</ul></div>';

        // Update modal content (using same modal structure or create a new one)
        const modalBody = document.querySelector('#deleteBulkModal .modal-body');
        if (modalBody) {
            modalBody.innerHTML = `
                <div id="publishWarningContent">${confirmContent}</div>
                <div id="publishItemsList" class="mt-3">${itemsList}</div>
            `;
        }

        // Update modal title and buttons
        const modalTitle = document.querySelector('#deleteBulkModal .modal-title');
        if (modalTitle) {
            modalTitle.innerHTML = '<i class="fas fa-check-circle text-primary"></i> Xác nhận xuất bản đề thi';
        }

        const modalFooter = document.querySelector('#deleteBulkModal .modal-footer');
        if (modalFooter) {
            modalFooter.innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="confirmBulkPublish()">
                    <i class="fas fa-check"></i> Xuất bản
                </button>
            `;
        }

        // Store selected items for publishing
        window.selectedDethisForPublish = dethis.map(d => d.id);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('deleteBulkModal'));
        modal.show();
    }

    // Confirm bulk publish
    function confirmBulkPublish() {
        const dethis = window.selectedDethisForPublish || [];

        if (dethis.length === 0) {
            alert('Không có đề thi nào được chọn.');
            return;
        }

        // Show loading
        const publishBtn = event.target;
        const originalText = publishBtn.innerHTML;
        publishBtn.disabled = true;
        publishBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xuất bản...';

        // Prepare data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        dethis.forEach(id => {
            formData.append('dethi_ids[]', id);
        });

        // Send publish request
        fetch('/de-thi/bulk-publish', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteBulkModal'));
                modal.hide();
                // Reload page
                window.location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Không thể xuất bản'));
                publishBtn.disabled = false;
                publishBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xuất bản. Vui lòng thử lại.');
            publishBtn.disabled = false;
            publishBtn.innerHTML = originalText;
        });
    }

    // Show finish confirmation
    function showFinishConfirmModal() {
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Vui lòng chọn ít nhất một đề thi để kết thúc.');
            return;
        }

        const dethis = [];
        
        checkboxes.forEach(cb => {
            const type = cb.getAttribute('data-type');
            const id = cb.getAttribute('data-id');
            const name = cb.getAttribute('data-name');
            
            // Chỉ kết thúc đề thi, không kết thúc folder
            if (type === 'dethi') {
                dethis.push({ id, name });
            }
        });

        if (dethis.length === 0) {
            alert('Vui lòng chọn đề thi để kết thúc. Không thể kết thúc thư mục.');
            return;
        }

        // Build confirmation content
        let confirmContent = `<p>Bạn có chắc chắn muốn kết thúc <strong>${dethis.length} đề thi</strong> không?</p>`;
        confirmContent += '<p>Khi kết thúc, đề thi sẽ được chuyển sang trạng thái "Chưa xuất bản" (status = 0).</p>';
        confirmContent += '<p class="text-warning"><strong>Lưu ý:</strong> Đề thi sẽ không còn hiển thị cho học sinh sau khi kết thúc.</p>';
        
        let itemsList = '<div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">';
        itemsList += '<h6 class="text-warning"><i class="fas fa-file-alt"></i> Các đề thi sẽ được kết thúc:</h6><ul>';
        dethis.forEach(dethi => {
            itemsList += `<li>${dethi.name}</li>`;
        });
        itemsList += '</ul></div>';

        // Update modal content
        const modalBody = document.querySelector('#deleteBulkModal .modal-body');
        if (modalBody) {
            modalBody.innerHTML = `
                <div id="finishWarningContent">${confirmContent}</div>
                <div id="finishItemsList" class="mt-3">${itemsList}</div>
            `;
        }

        // Update modal title and buttons
        const modalTitle = document.querySelector('#deleteBulkModal .modal-title');
        if (modalTitle) {
            modalTitle.innerHTML = '<i class="fas fa-stop-circle text-warning"></i> Xác nhận kết thúc đề thi';
        }

        const modalFooter = document.querySelector('#deleteBulkModal .modal-footer');
        if (modalFooter) {
            modalFooter.innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-warning" onclick="confirmBulkFinish()">
                    <i class="fas fa-stop-circle"></i> Kết thúc
                </button>
            `;
        }

        // Store selected items for finishing
        window.selectedDethisForFinish = dethis.map(d => d.id);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('deleteBulkModal'));
        modal.show();
    }

    // Confirm bulk finish
    function confirmBulkFinish() {
        const dethis = window.selectedDethisForFinish || [];

        if (dethis.length === 0) {
            alert('Không có đề thi nào được chọn.');
            return;
        }

        // Show loading
        const finishBtn = event.target;
        const originalText = finishBtn.innerHTML;
        finishBtn.disabled = true;
        finishBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang kết thúc...';

        // Prepare data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        dethis.forEach(id => {
            formData.append('dethi_ids[]', id);
        });

        // Send finish request
        fetch('/de-thi/bulk-finish', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteBulkModal'));
                modal.hide();
                // Reload page
                window.location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Không thể kết thúc'));
                finishBtn.disabled = false;
                finishBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi kết thúc. Vui lòng thử lại.');
            finishBtn.disabled = false;
            finishBtn.innerHTML = originalText;
        });
    }

    // Hiển thị modal thiết lập đối tượng làm bài
    function showAccessConfigModal() {
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Vui lòng chọn ít nhất một đề thi để cập nhật đối tượng.');
            return;
        }

        const dethis = [];
        checkboxes.forEach(cb => {
            const type = cb.getAttribute('data-type');
            const id = cb.getAttribute('data-id');
            const name = cb.getAttribute('data-name');
            if (type === 'dethi') {
                dethis.push({ id, name });
            }
        });

        if (dethis.length === 0) {
            alert('Chỉ có thể thay đổi đối tượng cho đề thi (không áp dụng cho thư mục).');
            return;
        }

        window.selectedDethisForAccess = dethis.map(d => d.id);

        document.getElementById('accessTypeSelect').value = 'all';
        document.getElementById('accessClasses').value = [];
        document.getElementById('startTime').value = '';
        document.getElementById('endTime').value = '';
        toggleAccessExtraFields('all');

        const modal = new bootstrap.Modal(document.getElementById('accessConfigModal'));
        modal.show();
    }

    function toggleAccessExtraFields(value) {
        const classBox = document.getElementById('accessClassBox');
        const timeBox = document.getElementById('accessTimeBox');
        if (classBox && timeBox) {
            classBox.style.display = value === 'class' ? 'block' : 'none';
            timeBox.style.display = value === 'time_limited' ? 'block' : 'none';
        }
    }

    function confirmAccessUpdate(event) {
        const dethis = window.selectedDethisForAccess || [];
        if (dethis.length === 0) {
            alert('Không có đề thi nào được chọn.');
            return;
        }

        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang cập nhật...';

        const accessType = document.getElementById('accessTypeSelect').value;
        const classes = Array.from(document.getElementById('accessClasses').selectedOptions).map(o => o.value);
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        dethis.forEach(id => formData.append('dethi_ids[]', id));
        formData.append('access_type', accessType);
        classes.forEach(id => formData.append('classes[]', id));
        if (startTime) formData.append('start_time', startTime);
        if (endTime) formData.append('end_time', endTime);

        fetch('/de-thi/bulk-access', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('accessConfigModal'));
                modal.hide();
                window.location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Không thể cập nhật.'));
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật. Vui lòng thử lại.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    // Xử lý xem nội dung đề thi
    $(document).ready(function() {
        $(document).on('click', '.view-exam-content', function(e) {
            e.preventDefault();
            const examId = $(this).data('exam-id');
            const modal = new bootstrap.Modal(document.getElementById('examContentModal'));
            const modalBody = $('#examContentBody');
            const modalTitle = $('#examContentModalLabel');
            
            // Hiển thị loading
            modalBody.html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Đang tải...</span></div></div>');
            modal.show();
            
            // Gọi API để lấy nội dung đề thi
            $.ajax({
                url: '/de-thi/xem-noi-dung-de-thi/' + examId,
                method: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        modalTitle.text(response.data.title || 'Nội dung đề thi');
                        
                        let html = '<div class="exam-content-wrapper">';
                        
                        // Hiển thị thông tin đề thi
                        if (response.data.description) {
                            html += '<div class="mb-4"><p class="text-muted">' + response.data.description + '</p></div>';
                        }
                        if (response.data.time) {
                            html += '<div class="mb-4"><strong>Thời gian làm bài: </strong>' + response.data.time + ' phút</div>';
                        }
                        
                        // Hiển thị các phần
                        if (response.data.parts && response.data.parts.length > 0) {
                            response.data.parts.forEach(function(part, partIndex) {
                                html += '<div class="exam-part mb-4" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin-bottom: 20px;">';
                                html += '<h4 class="mb-3">' + part.part + '. ' + (part.part_title || '') + '</h4>';
                                
                                if (part.questions && part.questions.length > 0) {
                                    part.questions.forEach(function(question, qIndex) {
                                        html += '<div class="question-block mb-4" style="border-bottom: 1px solid #f0f0f0; padding-bottom: 15px;">';
                                        html += '<div class="mb-2"><strong>Câu ' + question.question_no + ':</strong> ' + question.content + '</div>';
                                        
                                        // Hiển thị ảnh nếu có
                                        if (question.image) {
                                            try {
                                                const images = typeof question.image === 'string' ? JSON.parse(question.image) : question.image;
                                                if (Array.isArray(images) && images.length > 0) {
                                                    html += '<div class="question-images-container mb-2" style="display: flex; gap: 10px; flex-wrap: wrap;">';
                                                    images.forEach(function(img) {
                                                        const imgUrl = typeof img === 'string' ? img : (img.url || img);
                                                        html += '<img src="' + imgUrl + '" alt="Ảnh câu hỏi" class="img-fluid" style="max-width: 200px; border-radius: 4px;">';
                                                    });
                                                    html += '</div>';
                                                }
                                            } catch(e) {
                                                // Nếu không parse được thì bỏ qua
                                            }
                                        }
                                        
                                        // Hiển thị audio nếu có
                                        if (question.audio) {
                                            html += '<div class="mb-2"><audio controls><source src="' + question.audio + '" type="audio/mpeg"></audio></div>';
                                        }
                                        
                                        // Hiển thị đáp án theo loại câu hỏi
                                        if (question.question_type === 'multiple_choice' && question.answers) {
                                            // Lấy tất cả các đáp án đúng
                                            const correctAnswers = question.answers.filter(function(ans) { return ans.is_correct; });
                                            
                                            html += '<div class="answers-list mt-2">';
                                            question.answers.forEach(function(answer) {
                                                const isCorrect = answer.is_correct ? '<span class="badge bg-success ms-2">Đúng</span>' : '';
                                                html += '<div class="form-check mb-2" style="padding-left: 0;">' + 
                                                        '<label class="form-check-label" style="cursor: default;">' +
                                                        '<strong>' + answer.label + '.</strong> ' + answer.content + isCorrect +
                                                        '</label></div>';
                                            });
                                            
                                            // Hiển thị tổng hợp đáp án đúng
                                            if (correctAnswers.length > 0) {
                                                const correctLabels = correctAnswers.map(function(ans) { return ans.label; }).join(', ');
                                                html += '<div class="mt-2 mb-2" style="background: #d4edda; padding: 10px; border-radius: 4px; border-left: 3px solid #28a745;">';
                                                html += '<strong>Đáp án đúng: </strong><span class="badge bg-success">' + correctLabels + '</span>';
                                                html += '</div>';
                                            }
                                            
                                            html += '</div>';
                                        } else if (question.question_type === 'true_false_grouped' && question.answers) {
                                            // Lấy tất cả các đáp án đúng và sai
                                            const correctAnswers = question.answers.filter(function(ans) { return ans.is_correct; });
                                            const wrongAnswers = question.answers.filter(function(ans) { return !ans.is_correct; });
                                            
                                            html += '<div class="answers-list mt-2">';
                                            question.answers.forEach(function(answer) {
                                                const isCorrect = answer.is_correct;
                                                const answerIcon = isCorrect 
                                                    ? '<i class="fas fa-check-circle" style="color: #28a745; font-size: 18px; margin-right: 8px;"></i>' 
                                                    : '<i class="fas fa-times-circle" style="color: #dc3545; font-size: 18px; margin-right: 8px;"></i>';
                                                const answerBadge = isCorrect 
                                                    ? '<span class="badge bg-success ms-2"><i class="fas fa-check"></i> Đúng</span>' 
                                                    : '<span class="badge bg-danger ms-2"><i class="fas fa-times"></i> Sai</span>';
                                                
                                                html += '<div class="mb-3" style="padding: 10px; border-radius: 6px; ' + 
                                                        (isCorrect ? 'background: #d4edda; border-left: 3px solid #28a745;' : 'background: #f8d7da; border-left: 3px solid #dc3545;') + '">';
                                                html += '<div style="display: flex; align-items: flex-start;">';
                                                html += '<div style="margin-right: 8px;">' + answerIcon + '</div>';
                                                html += '<div style="flex: 1;">';
                                                html += '<div><strong>' + answer.label + ')</strong> ' + answer.content + answerBadge + '</div>';
                                                html += '</div>';
                                                html += '</div>';
                                                html += '</div>';
                                            });
                                            
                                            // Hiển thị tổng hợp đáp án
                                            html += '<div class="mt-3 mb-2" style="background: #e7f3ff; padding: 12px; border-radius: 6px; border-left: 3px solid #007bff;">';
                                            html += '<div style="font-weight: 600; margin-bottom: 8px;">Tổng hợp đáp án:</div>';
                                            
                                            if (correctAnswers.length > 0) {
                                                const correctLabels = correctAnswers.map(function(ans) { return ans.label; }).join(', ');
                                                html += '<div class="mb-1"><strong>Đáp án đúng: </strong><span class="badge bg-success">' + correctLabels + '</span></div>';
                                            }
                                            
                                            if (wrongAnswers.length > 0) {
                                                const wrongLabels = wrongAnswers.map(function(ans) { return ans.label; }).join(', ');
                                                html += '<div><strong>Đáp án sai: </strong><span class="badge bg-danger">' + wrongLabels + '</span></div>';
                                            }
                                            
                                            html += '</div>';
                                            
                                            html += '</div>';
                                        } else if (question.question_type === 'fill_in_blank') {
                                            // Lấy tất cả các đáp án đúng
                                            const correctAnswers = question.answers ? question.answers.filter(function(ans) { return ans.is_correct; }) : [];
                                            
                                            if (correctAnswers.length > 0) {
                                                html += '<div class="mt-2 mb-2" style="background: #d4edda; padding: 10px; border-radius: 4px; border-left: 3px solid #28a745;">';
                                                html += '<strong>Đáp án đúng: </strong>';
                                                correctAnswers.forEach(function(ans, idx) {
                                                    html += '<span class="badge bg-success me-1">' + ans.content + '</span>';
                                                });
                                                html += '</div>';
                                            } else if (question.correct_answer) {
                                                html += '<div class="mt-2 mb-2" style="background: #d4edda; padding: 10px; border-radius: 4px; border-left: 3px solid #28a745;">';
                                                html += '<strong>Đáp án đúng: </strong><span class="badge bg-success">' + question.correct_answer + '</span>';
                                                html += '</div>';
                                            }
                                        } else if (question.question_type === 'short_answer') {
                                            html += '<div class="mt-2"><em>(Câu hỏi tự luận - ' + (question.score || 0) + ' điểm)</em></div>';
                                            // Hiển thị giải thích như đáp án mẫu nếu có
                                            if (question.explanation) {
                                                html += '<div class="mt-2" style="background: #fff3cd; padding: 10px; border-radius: 4px; border-left: 3px solid #ffc107;">';
                                                html += '<strong>Gợi ý đáp án: </strong><div class="mt-1">' + question.explanation + '</div>';
                                                html += '</div>';
                                            }
                                        }
                                        
                                        // Hiển thị giải thích nếu có
                                        if (question.explanation) {
                                            html += '<div class="explanation mt-2" style="background: #f8f9fa; padding: 10px; border-radius: 4px; border-left: 3px solid #007bff;">';
                                            html += '<strong>Giải thích: </strong>' + question.explanation;
                                            html += '</div>';
                                        }
                                        
                                        html += '</div>';
                                    });
                                }
                                
                                html += '</div>';
                            });
                        }
                        
                        html += '</div>';
                        modalBody.html(html);
                        
                        // Render MathJax sau khi nội dung được load vào modal
                        // Sử dụng setTimeout để đảm bảo DOM đã được render hoàn toàn
                        setTimeout(function() {
                            if (typeof MathJax !== 'undefined') {
                                if (MathJax.typesetPromise) {
                                    // MathJax 3.x
                                    MathJax.typesetPromise([modalBody[0]]).then(function() {
                                        console.log('MathJax rendered successfully');
                                    }).catch(function(err) {
                                        console.error('MathJax rendering error:', err);
                                        // Fallback: thử render lại
                                        if (MathJax.typeset) {
                                            MathJax.typeset([modalBody[0]]);
                                        }
                                    });
                                } else if (MathJax.typeset) {
                                    // MathJax 3.x fallback
                                    MathJax.typeset([modalBody[0]]);
                                } else if (MathJax.Hub) {
                                    // MathJax 2.x với Hub
                                    MathJax.Hub.Queue(["Typeset", MathJax.Hub, modalBody[0]]);
                                }
                            }
                        }, 100);
                    } else {
                        modalBody.html('<div class="alert alert-danger">Không thể tải nội dung đề thi. Vui lòng thử lại.</div>');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Có lỗi xảy ra khi tải nội dung đề thi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    modalBody.html('<div class="alert alert-danger">' + errorMsg + '</div>');
                }
            });
        });
    });

    // Copy/Cut/Paste functionality
    let currentAction = null; // 'copy' or 'cut'
    let selectedItemsForAction = { folders: [], dethis: [] };
    const hasCutSession = {{ isset($hasCutSession) && $hasCutSession ? 'true' : 'false' }};
    const cutFoldersCount = {{ $cutFoldersCount ?? 0 }};
    const cutDethisCount = {{ $cutDethisCount ?? 0 }};
    const hasCopySession = {{ isset($hasCopySession) && $hasCopySession ? 'true' : 'false' }};
    const copyFoldersCount = {{ $copyFoldersCount ?? 0 }};
    const copyDethisCount = {{ $copyDethisCount ?? 0 }};
    const actionType = '{{ $actionType ?? "cut" }}';
    const hasAnySession = {{ isset($hasAnySession) && $hasAnySession ? 'true' : 'false' }};
    const currentFolderId = {{ $currentFolderId ?? 'null' }};

    // Copy - lưu vào session (không mở modal)
    function showCopyModal() {
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Vui lòng chọn ít nhất một mục để copy.');
            return;
        }

        const folders = [];
        const dethis = [];
        
        checkboxes.forEach(cb => {
            const type = cb.getAttribute('data-type');
            const id = cb.getAttribute('data-id');
            const name = cb.getAttribute('data-name');
            
            if (type === 'folder') {
                folders.push({ id, name });
            } else if (type === 'dethi') {
                dethis.push({ id, name });
            }
        });

        if (folders.length === 0 && dethis.length === 0) {
            alert('Vui lòng chọn ít nhất một mục để copy.');
            return;
        }

        // Lưu vào session bằng API
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        folders.forEach(f => formData.append('folder_ids[]', f.id));
        dethis.forEach(d => formData.append('dethi_ids[]', d.id));

        fetch('/de-thi/bulk-copy-session', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hiển thị trạng thái copy
                document.getElementById('cutStatus').style.display = 'inline-block';
                document.getElementById('cutStatus').innerHTML = 
                    `<i class="fas fa-copy"></i> Đã copy: ${data.count.folders} thư mục, ${data.count.dethis} đề thi`;
                document.getElementById('pasteBtn').style.display = 'inline-block';
                
                // Bỏ chọn tất cả
                document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                    cb.checked = false;
                });
                updateBulkActions();
                
                // Hiển thị thông báo
                alert('Đã copy! Vui lòng chọn thư mục đích và nhấn Paste.');
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Không thể copy'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi copy. Vui lòng thử lại.');
        });
    }

    // Show cut modal
    function showCutModal() {
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Vui lòng chọn ít nhất một mục để cut.');
            return;
        }

        const folders = [];
        const dethis = [];
        
        checkboxes.forEach(cb => {
            const type = cb.getAttribute('data-type');
            const id = cb.getAttribute('data-id');
            const name = cb.getAttribute('data-name');
            
            if (type === 'folder') {
                folders.push({ id, name });
            } else if (type === 'dethi') {
                dethis.push({ id, name });
            }
        });

        if (folders.length === 0 && dethis.length === 0) {
            alert('Vui lòng chọn ít nhất một mục để cut.');
            return;
        }

        // Lưu vào session bằng API
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        folders.forEach(f => formData.append('folder_ids[]', f.id));
        dethis.forEach(d => formData.append('dethi_ids[]', d.id));

        fetch('/de-thi/bulk-cut', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hiển thị trạng thái cut
                const cutStatus = document.getElementById('cutStatus');
                const pasteBtn = document.getElementById('pasteBtn');
                if (cutStatus) {
                    cutStatus.style.display = 'inline-block';
                    cutStatus.innerHTML =
                        `<i class="fas fa-cut"></i> Đã chọn để cut: ${data.count.folders} thư mục, ${data.count.dethis} đề thi`;
                }
                if (pasteBtn) {
                    pasteBtn.style.display = 'inline-block';
                }
                
                // Bỏ chọn tất cả
                document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                    cb.checked = false;
                });
                updateBulkActions();
                
                alert('Đã lưu danh sách để cut. Vui lòng chọn thư mục đích và nhấn Paste.');
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Không thể cut'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cut. Vui lòng thử lại.');
        });
    }

    // Paste trực tiếp vào folder hiện tại (không cần modal)
    function showPasteModal() {
        // Lấy folder ID hiện tại
        const targetFolderId = currentFolderId || null;
        
        // Gọi API paste luôn
        const btn = document.getElementById('pasteBtn');
        if (btn) {
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang paste...';

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            if (targetFolderId) {
                formData.append('target_folder_id', targetFolderId);
            }

            fetch('/de-thi/bulk-paste', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Ẩn trạng thái cut/copy
                    document.getElementById('cutStatus').style.display = 'none';
                    document.getElementById('pasteBtn').style.display = 'none';
                    
                    // Hiển thị thông báo thành công bằng Lobibox notify
                    alert('Paste thành công!');
                    
                    // Reload trang
                    window.location.reload();
                } else {
                    if (window.Lobibox && Lobibox.notify) {
                        Lobibox.notify('error', {
                            msg: 'Có lỗi xảy ra: ' + (data.message || 'Không thể paste'),
                            title: 'Lỗi',
                            delay: 5000
                        });
                    } else {
                        alert('Có lỗi xảy ra: ' + (data.message || 'Không thể paste'));
                    }
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi paste. Vui lòng thử lại.');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }
    }

    // Confirm copy
    function confirmCopy() {
        const targetFolderId = document.getElementById('targetFolderSelect').value || null;
        const folders = selectedItemsForAction.folders.map(f => f.id);
        const dethis = selectedItemsForAction.dethis.map(d => d.id);

        if (folders.length === 0 && dethis.length === 0) {
            alert('Không có mục nào để copy.');
            return;
        }

        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang copy...';

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        folders.forEach(id => formData.append('folder_ids[]', id));
        dethis.forEach(id => formData.append('dethi_ids[]', id));
        if (targetFolderId) {
            formData.append('target_folder_id', targetFolderId);
        }

        fetch('/de-thi/bulk-copy', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('folderSelectModal'));
                modal.hide();
                window.location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Không thể copy'));
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi copy. Vui lòng thử lại.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    // Confirm paste
    function confirmPaste() {
        const targetFolderId = document.getElementById('targetFolderSelect').value || null;

        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang paste...';

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        if (targetFolderId) {
            formData.append('target_folder_id', targetFolderId);
        }

        fetch('/de-thi/bulk-paste', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Ẩn trạng thái cut
                document.getElementById('cutStatus').style.display = 'none';
                document.getElementById('pasteBtn').style.display = 'none';
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('folderSelectModal'));
                modal.hide();
                window.location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Không thể paste'));
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi paste. Vui lòng thử lại.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    // Check if there's a cut session on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initial check
        updateBulkActions();
        
        // Add event listeners to existing checkboxes
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkActions);
        });
        
        // Also listen to select all checkbox
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                updateBulkActions();
            });
        }
        
        // Use MutationObserver to handle dynamically added checkboxes (if pagination adds new rows)
        const observer = new MutationObserver(function(mutations) {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => {
                if (!cb.hasAttribute('data-listener-attached')) {
                    cb.addEventListener('change', updateBulkActions);
                    cb.setAttribute('data-listener-attached', 'true');
                }
            });
        });
        
        const tableContainer = document.querySelector('.table-responsive');
        if (tableContainer) {
            observer.observe(tableContainer, {
                childList: true,
                subtree: true
            });
        }

        // Hiển thị trạng thái cut/copy/paste nếu có session từ server
        if (typeof hasAnySession !== 'undefined' && hasAnySession) {
            const cutStatus = document.getElementById('cutStatus');
            const pasteBtn = document.getElementById('pasteBtn');
            if (cutStatus) {
                cutStatus.style.display = 'inline-block';
                if (typeof hasCutSession !== 'undefined' && hasCutSession) {
                    cutStatus.innerHTML =
                        `<i class="fas fa-cut"></i> Đã chọn để cut: ${cutFoldersCount} thư mục, ${cutDethisCount} đề thi`;
                } else if (typeof hasCopySession !== 'undefined' && hasCopySession) {
                    cutStatus.innerHTML =
                        `<i class="fas fa-copy"></i> Đã copy: ${copyFoldersCount} thư mục, ${copyDethisCount} đề thi`;
                }
            }
            if (pasteBtn) {
                pasteBtn.style.display = 'inline-block';
            }
            
            // Hiển thị bulkActions nếu có session
            updateBulkActions();
        }
    });
</script>
@endsection
@section('content_crm_course')
<main class="main-content-wrap">
    <div class="content">
        <!-- Start Features Area -->
        <div class="page-title-area">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-sm-6">
                        <div class="page-title">
                            <h3>Danh sách đề thi</h3>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-6">
                        <ul class="page-title-list">
                            <li>Trang chủ</li>
                            <li>Danh sách đề thi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="contact-list-area">
            <div class="container-fluid">
                <div class="form-file-upload d-lg-flex justify-content-between align-items-center">
                    <form class="search-form" method="GET" action="{{ route('khoiTaoDeThi') }}">
                        <input type="text" class="form-control" placeholder="Search Files" name="keyword" value="{{ request('keyword') }}">
                        <input type="hidden" name="folder_id" value="{{ $selectedFolderKey }}">
                        <img src="/frontend/crm-course/images/search-normal.svg" alt="search-normal">
                    </form>
                    @if (($profile->type == 1 || $profile->type == 3) && !($hideActionButtons ?? false))
                    @php
                        $folderQuery = $currentFolderId ? ['folder_id' => $currentFolderId] : [];
                        $createExamUrl = empty($folderQuery) ? route('tutaodethi') : route('tutaodethi', $folderQuery);
                        $importExamUrl = empty($folderQuery) ? route('uploadFile') : route('uploadFile', $folderQuery);
                        $isSuperAdmin = $profile->type == 3;
                    @endphp
                    <ul class="create-upload d-lg-flex">
                        <li class="mb-3 ml-3 mr-3 mt-lg-0 mr-lg-3">
                            <a href="#" class="upload-btn create-folder" data-toggle="modal" data-target="#createFolderModal" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                               Tạo thư mục
                                <img src="/frontend/crm-course/images/folder-plus-svgrepo-com.svg" alt="add-circle">
                            </a>
                        </li>
                        <li class="mb-3 mt-3 mt-lg-0 mr-lg-3">
                            <a href="{{ $createExamUrl }}" class="upload-btn">
                                Tự tạo đề thi
                                <img src="/frontend/crm-course/images/add-circle.svg" alt="add-circle">
                            </a>
                        </li>
                        <li class="mb-3 mt-3 mt-lg-0">
                            <a href="{{ $importExamUrl }}" class="upload-btn upload">
                                Nhập đề thi từ file
                                <img src="/frontend/crm-course/images/export.svg" alt="export">
                            </a>
                        </li>
                    </ul>
                    @endif
                </div>
                @if ($profile->type == 1 || $profile->type == 3)
                @php
                    $isSuperAdmin = $profile->type == 3;
                    $isTeacher = $profile->type == 1;
                    // Giáo viên cũng cần thấy tên người tạo để phân biệt folders/đề thi của super admin
                    $showOwnerInfo = $isSuperAdmin || $isTeacher;
                @endphp
                <div class="resource-toolbar d-flex flex-wrap align-items-center justify-content-between">
                    <div class="resource-breadcrumb">
                        <a href="{{ route('khoiTaoDeThi', ['folder_id' => 'root']) }}">{{ $isSuperAdmin ? 'Kho đề hệ thống' : 'Kho đề của tôi' }}</a>
                        @foreach($folderBreadcrumb as $crumb)
                            <span>/</span>
                            @if(!$loop->last)
                                <a href="{{ route('khoiTaoDeThi', ['folder_id' => $crumb->id]) }}">{{ $crumb->name }}</a>
                            @else
                                <span>{{ $crumb->name }}</span>
                            @endif
                        @endforeach
                    </div>
                    <div class="resource-meta">
                        {{ $childFolders->count() }} thư mục · {{ $data->total() }} đề thi
                    </div>
                </div>
                <div class="card p-0">
                    <div style="gap: 15px;" class="p-3 g-5 border-bottom d-flex justify-content-end align-items-center" id="bulkActions" style="display: none;">
                        <span id="selectedCount" class="text-muted"></span>
                        <span id="cutStatus" class="badge bg-info text-white me-2" style="display: none;">
                            <i class="fas fa-cut"></i> Đã chọn để cut
                        </span>
                        <button type="button" class="btn btn-success btn-sm" onclick="showCopyModal()">
                            <i class="fas fa-copy"></i> Sao chép
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="showCutModal()">
                            <i class="fas fa-cut"></i> Cut
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="showPasteModal()" id="pasteBtn" style="display: none;">
                            <i class="fas fa-paste"></i> Paste
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="showDeleteConfirmModal()">
                            <i class="fas fa-trash"></i> Xóa đã chọn
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="showPublishConfirmModal()">
                            <i class="fas fa-check-circle"></i> Xuất bản
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="showAccessConfigModal()">
                            <i class="fas fa-users-cog"></i> Đối tượng làm bài
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="showFinishConfirmModal()">
                            <i class="fas fa-stop-circle"></i> Kết thúc
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table resource-table mb-0">
                            <thead>
                                <tr>
                                    <th width="20">
                                        <input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)">
                                    </th>
                                    <th>Tên</th>
                                    @if (($profile->type == 1 || $profile->type == 3) && !($hideActionButtons ?? false))
                                    <th>Số bài đã nộp</th>
                                    <th>Trạng thái</th>
                                    <th>Đã giao cho</th>
                                    <th>Thời gian thi</th>
                                    @endif
                                    <th width="220">Tùy chọn</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($childFolders as $folder)
                                @php
                                    // Kiểm tra xem giáo viên có thể chọn folder này không
                                    $canSelectFolder = true;
                                    if ($profile->type == 1 && $folder->owner && $folder->owner->type == 3) {
                                        // Giáo viên không thể chọn folder của super admin
                                        $canSelectFolder = false;
                                    }
                                @endphp
                                <tr>
                                    <td width="20">
                                        @if($canSelectFolder)
                                            <input type="checkbox" class="item-checkbox" data-type="folder" data-id="{{ $folder->id }}" data-name="{{ $folder->name }}">
                                        @else
                                            <input type="checkbox" class="item-checkbox" data-type="folder" data-id="{{ $folder->id }}" data-name="{{ $folder->name }}" disabled title="Bạn không thể chọn thư mục của Cánh Én">
                                        @endif
                                    </td>
                                    <td>
                                        <div class="resource-info">
                                            <span class="resource-icon">
                                                <img src="/frontend/crm-course/images/open-file.svg" alt="Folder" width="22">
                                            </span>
                                            <div>
                                                <a href="{{ route('khoiTaoDeThi', ['folder_id' => $folder->id]) }}" class="fw-semibold">{{ $folder->name }} @if($showOwnerInfo && $folder->owner && $folder->owner->type == 3)
                                                    <span class="badge bg-secondary ms-1 text-white"> <i class="fas fa-crown"></i> Cánh Én</span>
                                                @endif</a>
                                                <div class="text-muted small">
                                                    @if($profile->type == 3 && $folder->owner)
                                                        @if($folder->owner->type == 1)
                                                            <i class="fas fa-user"></i> Giáo viên: {{ $folder->owner->name }}
                                                        @elseif($folder->owner->type == 3)
                                                            {{-- <i class="fas fa-crown"></i> Cánh Én --}}
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    @if (($profile->type == 1 || $profile->type == 3) && !($hideActionButtons ?? false))
                                    <td>—</td>
                                    <td><span class="resource-status draft">Thư mục</span></td>
                                    <td>—</td>
                                    <td>—</td>
                                    @endif
                                    <td></td>
                                </tr>
                                @endforeach
                                @foreach($data as $item)
                                @php
                                    $classNames = $item->allowedClasses->map(function($dc) {
                                        return $dc->schoolClass->class_name ?? '';
                                    })->filter()->implode(', ');
                                    if($item->status == 1){
                                        $statusLabel = 'Đã xuất bản';
                                        $statusClass = 'published';
                                    }elseif($item->status == 0){
                                        $statusLabel = 'Chưa xuất bản';
                                        $statusClass = 'draft';
                                    }else{
                                        $statusLabel = 'Đã kết thúc';
                                        $statusClass = 'closed';
                                    }
                                    if($item->access_type == 'all'){
                                        $accessLabel = 'Tất cả mọi người';
                                    }elseif($item->access_type == 'class'){
                                        $accessLabel = $classNames ?: 'Chưa chọn lớp';
                                    }else{
                                        $accessLabel = 'Giới hạn thời gian';
                                    }
                                    
                                    // Kiểm tra xem giáo viên có thể chọn đề thi này không
                                    $canSelectDethi = true;
                                    if ($profile->type == 1) {
                                        // Giáo viên chỉ có thể chọn đề thi của mình
                                        if ($item->customer && $item->customer->type == 3) {
                                            // Đề thi của super admin
                                            $canSelectDethi = false;
                                        } elseif ($item->created_by != $profile->id) {
                                            // Đề thi không phải của giáo viên này
                                            $canSelectDethi = false;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td width="20">
                                        @if($canSelectDethi)
                                            <input type="checkbox" class="item-checkbox" data-type="dethi" data-id="{{ $item->id }}" data-name="{{ $item->title }}">
                                        @else
                                            <input type="checkbox" class="item-checkbox" data-type="dethi" data-id="{{ $item->id }}" data-name="{{ $item->title }}" disabled title="Bạn chỉ có thể chọn đề thi của chính mình">
                                        @endif
                                    </td>
                                    <td>
                                        <div class="resource-info">
                                            <span class="resource-icon exam">
                                                <img src="/frontend/crm-course/images/i-exam-qualification-svgrepo-com.svg" alt="Exam" width="22">
                                            </span>
                                            <div>
                                                <a href="{{route('detailDeThi',['id'=>$item->id])}}" class="fw-semibold">{{$item->title}}</a>
                                                <div class="text-muted small">
                                                    {{ $item->folder->name ?? 'Thư mục gốc' }}
                                                    @if($profile->type == 3 && $item->customer)
                                                        @if($item->customer->type == 1)
                                                            · <i class="fas fa-user"></i> Giáo viên: {{ $item->customer->name }}
                                                        @elseif($item->customer->type == 3)
                                                            · <i class="fas fa-crown"></i> Cánh Én
                                                        @endif
                                                    @elseif($showOwnerInfo && $item->customer && $item->customer->type == 3)
                                                        · <i class="fas fa-crown"></i> Cánh Én
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    @php
                                        // Kiểm tra xem có phải giáo viên đang xem đề thi của superadmin không
                                        $isTeacherViewingSuperAdminExam = ($profile->type == 1 && $item->customer && $item->customer->type == 3);
                                    @endphp
                                    @if (($profile->type == 1 || $profile->type == 3) && !($hideActionButtons ?? false) && !$isTeacherViewingSuperAdminExam)
                                    <td>{{$item->sessions->count()}}</td>
                                    <td>
                                        <span class="resource-status {{ $statusClass }}">{{ $statusLabel }}</span>
                                    </td>
                                    <td>
                                        @if ($item->start_time && $item->end_time)
                                            @php
                                                $timeResult = calculateExamTimeStatus($item->start_time, $item->end_time);
                                                $timeStatus = $timeResult['status'];
                                                $timeText = $timeResult['text'];
                                            @endphp
                                            @if ($timeStatus == 'not_started')
                                                <span class="badge bg-info text-white">
                                                    <i class="fas fa-clock"></i> Bắt đầu sau {{ $timeText }}
                                                </span>
                                            @elseif ($timeStatus == 'active')
                                                <span class="badge bg-warning text-white">
                                                    <i class="fas fa-clock"></i> Còn {{ $timeText }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger text-white">
                                                    <i class="fas fa-times-circle"></i> Đã kết thúc
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-warning text-white">
                                                <i class="fas fa-clock"></i> Giới hạn thời gian
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{$item->time}} phút
                                    </td>
                                    <td>
                                        @php
                                            // Kiểm tra xem đề thi có thuộc về giáo viên hiện tại không
                                            $canMoveExam = ($profile->type == 3) || ($item->created_by == $profile->id);
                                            // Sử dụng moveFolderOptions nếu có, nếu không thì dùng folderOptions
                                            $optionsToUse = isset($moveFolderOptions) ? $moveFolderOptions : $folderOptions;
                                        @endphp
                                        @if ($canMoveExam)
                                            <form method="POST" action="{{ route('dethi.move-to-folder') }}" class="folder-move-form">
                                                @csrf
                                                <input type="hidden" name="dethi_id" value="{{ $item->id }}">
                                                <select name="folder_id" class="form-control form-control-sm mb-2" onchange="this.form.submit()">
                                                    <option value="">Thư mục gốc</option>
                                                    @foreach($optionsToUse as $option)
                                                        <option value="{{ $option['id'] }}" {{ $item->folder_id == $option['id'] ? 'selected' : '' }}>
                                                            {{ $option['label'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        @else
                                            <span class="text-muted small">
                                                {{ $item->folder ? $item->folder->name : 'Thư mục gốc' }}
                                            </span>
                                            
                                        @endif
                                    </td>
                                    @elseif ($profile->type == 1 && $item->customer && $item->customer->type == 3)
                                    <td class="text-end">
                                        <ul class="create-upload d-lg-flex justify-content-end">
                                            <li class="mb-3 mt-3 mt-lg-0 mr-lg-3">
                                                <a href="#" class="upload-btn create-folder text-white view-exam-content" data-exam-id="{{ $item->id }}">
                                                   Xem nội dung đề
                                                </a>
                                            </li>
                                            <li class="mb-3 mt-3 mt-lg-0 mr-lg-3">
                                                <a href="{{ route('cloneExam', $item->id) }}" class="upload-btn text-white" onclick="return confirm('Bạn có chắc muốn tải đề thi này về thư mục gốc?');">
                                                    Tải xuống
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                    @else
                                    <td></td>
                                    @endif

                                </tr>
                                @endforeach
                                @if($childFolders->isEmpty() && $data->count() === 0)
                                <tr>
                                    <td colspan="8">
                                        <div class="resource-empty">
                                            Chưa có thư mục hoặc đề thi nào trong thư mục này.
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">
                        {{ $data->appends(['folder_id' => $selectedFolderKey])->links() }}
                    </div>
                </div>
                @else 
                @if (count($data) > 0)
                    <table class="table align-middle mb-0">
                        <tbody>
                            <tr>
                                <td>
                                <b>Tên đề thi</b>
                                </td>
                                <td>
                                    <b>Số lần làm đề</b>
                                </td>
                                <td>
                                    <b>Giá</b>
                                </td>
                                <td>
                                    <b>Trạng thái</b>
                                </td>
                                <td>
                                    <b>Thời gian</b>
                                </td>
                                @if ($profile->type == 1)
                                <td>
                                    <b>Tùy chọn</b>
                                </td>
                                @endif
                            </tr>
                            @foreach($data as $item)
                            @php
                                $examId = $item['bill_id'] ?? $item['dethi']->id;
                                $dethi = $item['dethi'];
                                // Kiểm tra xem đề thi có thuộc về super admin không
                                $isSuperAdminExam = false;
                                if ($dethi && $dethi->customer && $dethi->customer->type == 3) {
                                    $isSuperAdminExam = true;
                                }
                                // Xác định access type label
                                $accessLabel = '';
                                if ($dethi) {
                                    if ($dethi->access_type == 'all') {
                                        $accessLabel = 'Tất cả mọi người';
                                    } elseif ($dethi->access_type == 'class') {
                                        $accessLabel = 'Theo lớp';
                                    } elseif ($dethi->access_type == 'time_limited') {
                                        $accessLabel = 'Theo thời gian';
                                    }
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div class=" d-flex align-items-center">
                                        <label class="form-check-label ms-2">
                                            <img width="30" src="/frontend/crm-course/images/i-exam-qualification-svgrepo-com.svg" alt="user-2">
                                        </label>
                                        <div class="info ml-3">
                                            <h4><a href="{{route('detailDeThi',['id'=>$examId])}}">{{ $dethi->title ?? 'Đề thi đã bị xóa'}}</a></h4>
                                            <div class="mt-1">
                                                @if ($dethi->access_type == 'all') 
                                                    <small class="badge bg-success text-white">
                                                        <i class="fas fa-users"></i> Tất cả mọi người
                                                    </small>
                                                @elseif ($dethi->access_type == 'class') 
                                                    <small class="badge bg-info text-white">
                                                        <i class="fas fa-class"></i> Theo {{ $dethi->allowedClasses->count() }} lớp
                                                    </small>
                                                @elseif ($dethi->access_type == 'time_limited') 
                                                    <small class="badge bg-warning text-white">
                                                        <i class="fas fa-clock"></i> Theo thời gian
                                                    </small>
                                                @endif
                                                @if($dethi && $dethi->customer)
                                                    @if($dethi->customer->type == 3)
                                                        <small class="badge bg-secondary text-white ms-1">
                                                            <i class="fas fa-crown"></i> Cánh Én
                                                        </small>
                                                    @elseif($dethi->customer->type == 1)
                                                        <small class="badge bg-secondary text-white ms-1">
                                                            <i class="fas fa-user"></i> {{ $dethi->customer->name }}
                                                        </small>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{$dethi ? $dethi->sessions()->where('student_id', Auth::guard("customer")->user()->id)->count() : 0}}
                                </td>
                                <td>
                                    @if($dethi && $dethi->price == 0)
                                    <span>Miễn phí</span>
                                    @else
                                    <span>{{number_format($dethi->price ?? 0,0,',','.')}} VNĐ</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($dethi->access_type == 'time_limited')
                                        @if ($dethi->start_time && $dethi->end_time)
                                            @php
                                                $timeResult = calculateExamTimeStatus($dethi->start_time, $dethi->end_time);
                                                $timeStatus = $timeResult['status'];
                                                $timeText = $timeResult['text'];
                                            @endphp
                                            @if ($timeStatus == 'not_started')
                                                <span class="badge bg-info text-white">
                                                    <i class="fas fa-clock"></i> Bắt đầu sau {{ $timeText }}
                                                </span>
                                            @elseif ($timeStatus == 'active')
                                                <span class="badge bg-warning text-white">
                                                    <i class="fas fa-clock"></i> Còn {{ $timeText }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger text-white">
                                                    <i class="fas fa-times-circle"></i> Đã kết thúc
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-warning text-white">
                                                <i class="fas fa-clock"></i> Giới hạn thời gian
                                            </span>
                                        @endif
                                    @elseif ($dethi->access_type == 'class')
                                    <span class="badge bg-info text-white">
                                        <i class="fas fa-class"></i> Theo {{ $dethi->allowedClasses->count() }} lớp
                                    </span>
                                    @elseif ($dethi->access_type == 'all')
                                    <span class="badge bg-success text-white">
                                        <i class="fas fa-users"></i> Tất cả mọi người
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    {{$dethi ? $dethi->time : 0}} phút
                                </td>
                                @if ($profile->type == 1)
                                <td>
                                    @if ($isSuperAdminExam && $dethi)
                                        <ul class="create-upload d-lg-flex" style="list-style: none; padding: 0; margin: 0;">
                                            <li class="mb-2 mr-2">
                                                <a href="#" class="upload-btn create-folder text-white view-exam-content" data-exam-id="{{ $dethi->id }}" style="padding: 6px 12px; font-size: 13px;">
                                                   Xem nội dung
                                                </a>
                                            </li>
                                            <li class="mb-2">
                                                <a href="{{ route('cloneExam', $dethi->id) }}" class="upload-btn text-white" onclick="return confirm('Bạn có chắc muốn tải đề thi này về thư mục gốc?');" style="padding: 6px 12px; font-size: 13px;">
                                                    Tải xuống
                                                </a>
                                            </li>
                                        </ul>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$data->links()}}
                    @else 
                    <div class="alert alert-warning">
                        <p>Không có đề thi nào</p>
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    <!-- End Features Area -->
    <!-- Start Footer Area -->
    @include('crm_course.main.footer')
    <!-- End Footer Area -->
    @if ($profile->type == 1 || $profile->type == 3)
    <div class="modal fade" id="createFolderModal" tabindex="-1" role="dialog" aria-labelledby="createFolderModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createFolderModalLabel">Tạo thư mục đề thi</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('exam-folders.store', ['type' => 'exam']) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="folder-name">Tên thư mục</label>
                            <input type="text" class="form-control" id="folder-name" name="name" placeholder="Ví dụ: Ôn tập học kỳ" required>
                        </div>
                        <input type="hidden" name="type" value="exam">
                        <input type="hidden" name="parent_id" value="{{ $currentFolderId }}">
                        <p class="text-muted small mb-0">
                            Thư mục sẽ được tạo trong: <strong>{{ $activeFolder->name ?? 'Thư mục gốc' }}</strong>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu thư mục</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    <div class="modal fade" id="deleteDethiModal" tabindex="-1" aria-labelledby="deleteDethiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="deleteDethiModalLabel">Xác nhận xoá đề thi</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p>Bạn có chắc chắn muốn xoá đề thi này? Tất cả dữ liệu học sinh làm bài và các file liên quan sẽ bị xoá vĩnh viễn!</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
              <button type="button" class="btn btn-danger" onclick="confirmDeleteDethi()">Xoá vĩnh viễn</button>
            </div>
          </div>
        </div>
      </div>
    <div class="modal fade" id="deleteBulkModal" tabindex="-1" aria-labelledby="deleteBulkModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header bg-danger text-white">
              <h5 class="modal-title" id="deleteBulkModalLabel">
                <i class="fas fa-exclamation-triangle"></i> Xác nhận xóa
              </h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="alert alert-danger" role="alert">
                <h6 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Cảnh báo quan trọng!</h6>
                <hr>
                <div id="deleteWarningContent">
                  <!-- Nội dung cảnh báo sẽ được điền bằng JavaScript -->
                </div>
              </div>
              <div id="deleteItemsList" class="mt-3">
                <!-- Danh sách items sẽ được điền bằng JavaScript -->
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
              <button type="button" class="btn btn-danger" onclick="confirmBulkDelete()">
                <i class="fas fa-trash"></i> Xóa vĩnh viễn
              </button>
            </div>
          </div>
        </div>
      </div>

    <!-- Modal cấu hình đối tượng làm bài -->
    <div class="modal fade" id="accessConfigModal" tabindex="-1" aria-labelledby="accessConfigModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="accessConfigModalLabel">
                        <i class="fas fa-users-cog"></i> Thiết lập đối tượng làm bài
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Đối tượng được làm bài</label>
                            <select id="accessTypeSelect" class="form-select" onchange="toggleAccessExtraFields(this.value)">
                                <option value="all">Tự do (Tất cả học sinh)</option>
                                <option value="class">Theo lớp</option>
                                <option value="time_limited">Giới hạn thời gian</option>
                            </select>
                        </div>

                        <div class="col-12" id="accessClassBox" style="display:none;">
                            <label class="form-label fw-semibold"><i class="fas fa-users"></i> Các lớp được phép làm bài</label>
                            <select id="accessClasses" class="form-select" multiple size="6">
                                @foreach($schoolClasses as $cls)
                                    <option value="{{ $cls->id }}">{{ $cls->class_name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">
                                Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều lớp
                            </small>
                        </div>

                        <div class="col-12" id="accessTimeBox" style="display:none;">
                            <label class="form-label fw-semibold"><i class="fas fa-clock"></i> Thời gian cho phép làm bài</label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Bắt đầu</label>
                                    <input type="datetime-local" id="startTime" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Kết thúc</label>
                                    <input type="datetime-local" id="endTime" class="form-control">
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1">Học sinh chỉ có thể làm bài trong khoảng thời gian này</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAccessUpdate(event)">
                        <i class="fas fa-save"></i> Lưu thiết lập
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal hiển thị nội dung đề thi -->
    <div class="modal fade" id="examContentModal" tabindex="-1" aria-labelledby="examContentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="examContentModalLabel">Nội dung đề thi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="examContentBody">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal chọn folder đích cho Copy/Cut/Paste -->
    <div class="modal fade" id="folderSelectModal" tabindex="-1" aria-labelledby="folderSelectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="folderSelectModalLabel">
                        <i class="fas fa-folder-open"></i> Chọn thư mục đích
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Thư mục đích</label>
                        <select id="targetFolderSelect" class="form-select">
                            <option value="">Thư mục gốc</option>
                            @foreach($folderOptions as $option)
                                <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-2">
                            Chọn thư mục để copy/cut/paste vào. Để trống để paste vào thư mục gốc.
                        </small>
                    </div>
                    <div id="folderSelectItemsList" class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                        <!-- Danh sách items sẽ được điền bằng JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="confirmFolderSelectBtn">
                        <i class="fas fa-check"></i> Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>

 </main>

@endsection
