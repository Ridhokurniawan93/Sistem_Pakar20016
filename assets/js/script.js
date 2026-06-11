// ============================================
// Sistem Pakar Penentuan Jurusan - Custom JS
// ============================================

$(document).ready(function() {
    
    // ===== Sidebar Toggle =====
    $('#sidebarToggle').on('click', function() {
        $('#sidebar').toggleClass('active');
        $('#sidebarOverlay').toggleClass('active');
    });
    
    $('#sidebarOverlay').on('click', function() {
        $('#sidebar').removeClass('active');
        $(this).removeClass('active');
    });
    
    // ===== DataTables Init =====
    if ($('.data-table').length) {
        $('.data-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json'
            },
            responsive: true,
            pageLength: 10,
            order: [[0, 'asc']]
        });
    }
    
    // ===== SweetAlert Confirm Delete =====
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { id: id, action: 'delete' },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Terhapus!', response.message, 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
                    }
                });
            }
        });
    });
    
    // ===== AJAX Form Submit =====
    $(document).on('submit', '.ajax-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();
        
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Berhasil!', response.message, 'success');
                    var modal = bootstrap.Modal.getInstance(document.getElementById(form.closest('.modal').id));
                    if (modal) modal.hide();
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
            }
        });
    });
    
    // ===== Edit Modal - Load Data =====
    $(document).on('click', '.btn-edit', function() {
        var url = $(this).data('url');
        var id = $(this).data('id');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: { id: id, action: 'get' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    $.each(data, function(key, value) {
                        var field = $('[name="' + key + '"]');
                        if (field.is('select')) {
                            field.val(value);
                        } else if (field.is('textarea')) {
                            field.val(value);
                        } else {
                            field.val(value);
                        }
                    });
                }
            }
        });
    });
    
    // ===== Reset Modal Form on Close =====
    $(document).on('hidden.bs.modal', '.modal', function() {
        $(this).find('form')[0].reset();
        $(this).find('[name="id"]').val('');
        $(this).find('[name="action"]').val('add');
    });
    
    // ===== Auto-dismiss alerts =====
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // ===== Konsultasi Form Validation =====
    $('#formKonsultasi').on('submit', function(e) {
        var questions = $(this).find('.question-group');
        var allAnswered = true;
        
        questions.each(function() {
            var answered = $(this).find('input[type="radio"]:checked').length > 0;
            if (!answered) {
                allAnswered = false;
                $(this).find('.card-header').css('background', 'linear-gradient(135deg, #dc3545, #b02a37)');
            }
        });
        
        if (!allAnswered) {
            e.preventDefault();
            Swal.fire({
                title: 'Peringatan!',
                text: 'Harap jawab semua pertanyaan sebelum melanjutkan.',
                icon: 'warning',
                confirmButtonColor: '#0d6efd'
            });
        }
    });
    
    // Reset question card color on answer
    $(document).on('change', 'input[type="radio"]', function() {
        $(this).closest('.question-group').find('.card-header').css('background', 'linear-gradient(135deg, #0d6efd, #0a58ca)');
    });
});
