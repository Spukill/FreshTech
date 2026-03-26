import './bootstrap';

import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});

// Google was changing the zoom settings so let's make sure we reset it
document.addEventListener('DOMContentLoaded', function() {
    document.documentElement.style.zoom = '1';
    document.body.style.zoom = '1';
    document.documentElement.style.transform = 'none';
    document.body.style.transform = 'none';
});

$(document).ready(function () {
    const reviewEl = document.getElementById('editReviewModal');
    let modal = reviewEl ? new bootstrap.Modal(reviewEl) : null;

    const promoEl = document.getElementById('editPromotionModal');
    let editPromo = promoEl ? new bootstrap.Modal(promoEl) : null;

    const promoEl2 = document.getElementById('createPromotionModal');
    let createPromo = promoEl2 ? new bootstrap.Modal(promoEl2) : null;

    const productEl1 = document.getElementById('createProductModal');
    let createProduct = productEl1 ? new bootstrap.Modal(productEl1) : null;

    const editProductEl = document.getElementById('editProductModal');
    let editProduct = editProductEl ? new bootstrap.Modal(editProductEl) : null;

    const reportEl = document.getElementById('reportReviewModal');
    let reportReview = reportEl ? new bootstrap.Modal(reportEl) : null;

    const handleEl = document.getElementById('handleReportModal');
    let handleReport = handleEl ? new bootstrap.Modal(handleEl) : null;

    const createCategoryEl = document.getElementById('createCategoryModal');
    let createCategory = createCategoryEl ? new bootstrap.Modal(createCategoryEl) : null;

    const editCategoryEl = document.getElementById('editCategoryModal');
    let editCategory = editCategoryEl ? new bootstrap.Modal(editCategoryEl) : null;


    // Abrir modal e carregar dados
    $(document).on('click', '.botao-edit', function () {
        let id = $(this).data('id');

        $.get(`/reviews/${id}/edit`, function (data) {
            $('#review_id').val(data.id);
            $('#rating').val(data.rating);
            $('#comment').val(data.description);
            modal.show();
        });
    });

    // Atualizar review
    $('#editReviewForm').submit(function (e) {
        e.preventDefault();

        let id = $('#review_id').val();;

        $.ajax({
        url: `/reviews/${id}`,
        type: 'POST',
        data: {
            _method: 'PUT',
            rating: $('#rating').val(),
            comment: $('#comment').val(),
            reviewId: $('#review_id').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
    },

    success: function (response) {
    const id = $('#review_id').val();
    const newRating = $('#rating').val();
    const newComment = $('#comment').val();

    // Atualiza comentário
    const reviewEl = document.querySelector(`#review-${id} p`);
    if (reviewEl) {
        reviewEl.textContent = newComment;
    }

    // Atualiza estrelas
    const starsContainer = document.querySelector(`#review-${id} .text-warning`);
    if (starsContainer) {
        starsContainer.innerHTML = '';

        for (let i = 1; i <= 5; i++) {
            const star = document.createElement('i');
            star.classList.add('bi');

            if (i <= newRating) {
                star.classList.add('bi-star-fill');
            } else {
                star.classList.add('bi-star');
            }

            starsContainer.appendChild(star);
        }
    }

    modal.hide();
},



            error: function (xhr) {
    console.error('STATUS:', xhr.status);
    console.error('RESPONSE:', xhr.responseText);
    alert('Status: ' + xhr.status);
}
        });
    });

    $(document).on('click', '.botao-create-product', function () {
    document.getElementById('createProductForm').reset();
    createProduct.show();
});

// Submissão do formulário via AJAX
$('#createProductForm').submit(function (e) {
    e.preventDefault();

    var formData = new FormData(this); // para lidar com file input (imagem)

    $.ajax({
        url: `/products`, // rota de store do produto
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            const product = response.product;

            // Criar a nova linha da tabela (exemplo)
            const newRow = `
<tr id="row-${product.id}" class="table-success">
    <td>${product.image ? `<img src="/storage/${product.image}" alt="${product.name}" width="50" height="50" class="rounded">` : 'No Image'}</td>
    <td>${product.name}</td>
    <td>${parseFloat(product.price).toFixed(2)}</td>
    <td>${product.stock}</td>
    <td>${product.category.name}</td>
    <td class="text-center">
        <!-- Edit -->
        <a href="/products/${product.id}/edit" class="btn btn-sm btn-primary">Edit</a>

        <!-- Delete -->
        <form action="/products/${product.id}" method="POST" class="d-inline">
            <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                Delete
            </button>
        </form>
    </td>
</tr>
`;


            $('#productTableBody').prepend(newRow); // tabela de produtos
            setTimeout(() => $(`#row-${product.id}`).removeClass('table-success'), 2000);

            createProduct.hide(); // fecha o modal
        },

        error: function (xhr) {
            console.error('STATUS:', xhr.status);
            console.error('RESPONSE:', xhr.responseText);
            alert('Erro ao criar o produto. Status: ' + xhr.status);
        }
    });
});  

$(document).on('click', '.botao-edit-product', function() {
    const id = $(this).data('id');

    $.get(`/products/${id}/json`, function(product) {
        $('#edit_product_id').val(product.id);
        $('#edit_name').val(product.name);
        $('#edit_price').val(product.price);
        $('#edit_stock').val(product.stock);
        $('#edit_id_category').val(product.id_category);

        editProduct.show(); 
    });
});


    // Submissão do formulário via AJAX
    $('#editProductForm').submit(function(e) {
    e.preventDefault();

    const productId = $('#edit_product_id').val();
    const formData = new FormData(this);

    // Garantir que Laravel interprete como PATCH
    formData.append('_method', 'PATCH');

    $.ajax({
        url: `/products/${productId}`,
        type: 'POST', // Laravel interpreta via _method
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            const product = response.product;

            // Atualiza linha da tabela
            console.log(product);
            const row = $(`#row-${product.id}`);
row.html(`
    <td>${product.image1 ? `<img src="/public/images/products/${product.image1}" width="50" height="50" class="rounded">` : '<img src="/images/placeholder.png" width="50" height="50" class="rounded">'} </td>
    <td>${product.name}</td>
    <td>$${parseFloat(product.price).toFixed(2)}</td>
    <td>${product.stock}</td>
    <td>${product.category.name ?? 'N/A'}</td>
    <td class="text-center">
        <button class="btn btn-primary btn-sm botao-edit-product" data-id="${product.id}">
            Edit
        </button>
        <form action="/products/${product.id}" method="POST" class="d-inline">
            <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
        </form>
    </td>
`);


            row.addClass('table-success');
            setTimeout(() => row.removeClass('table-success'), 2000);

            editProduct.hide();
        },
        error: function(xhr) {
            console.error('STATUS:', xhr.status);
            console.error('RESPONSE:', xhr.responseText);
            alert('Erro ao atualizar o produto. Status: ' + xhr.status);
        }
    });
});


$(document).on('click', '.botao-create-category', function () {
    document.getElementById('createCategoryForm').reset();
    createCategory.show();
});

$('#createCategoryForm').submit(function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: `/categories`,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,

        success: function (response) {
            const category = response.category;

            const newRow = `
<tr id="row-${category.id}" class="table-success">
    <td>${category.name}</td>
    <td class="text-center">
        <button class="btn btn-sm btn-primary botao-edit-category" data-id="${category.id}">
            Edit
        </button>
        <form action="/categories/${category.id}" method="POST" class="d-inline">
            <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-sm btn-danger"
                onclick="return confirm('Are you sure?')">
                Delete
            </button>
        </form>
    </td>
</tr>`;

            $('#categoryTableBody').prepend(newRow);
            setTimeout(() => $(`#row-${category.id}`).removeClass('table-success'), 2000);

            createCategory.hide();
        },

        error: function (xhr) {
            console.error(xhr.responseText);
            alert('Erro ao criar categoria');
        }
    });
});
  

$(document).on('click', '.botao-edit-category', function () {
    const id = $(this).data('id');

    $.get(`/categories/${id}/json`, function (category) {
        console.log(category);
        $('#edit_category_id').val(category.id);
        $('#edit_name').val(category.name);
        editCategory.show();
    });
});



    // Submissão do formulário via AJAX
    $('#editCategoryForm').submit(function (e) {
    e.preventDefault();

    const categoryId = $('#edit_category_id').val();
    const formData = new FormData(this);
    formData.append('_method', 'PATCH');

    $.ajax({
        url: `/categories/${categoryId}`,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,

        success: function (response) {
            const category = response.category;

            const row = $(`#row-${category.id}`);
            row.html(`
                <td>${category.name}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-primary botao-edit-category" data-id="${category.id}">
                        Edit
                    </button>
                    <form action="/categories/${category.id}" method="POST" class="d-inline">
                        <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-sm btn-danger">
                            Delete
                        </button>
                    </form>
                </td>
            `);

            row.addClass('table-success');
            setTimeout(() => row.removeClass('table-success'), 2000);

            editCategory.hide();
        },

        error: function (xhr) {
            console.error(xhr.responseText);
            alert('Erro ao atualizar categoria');
        }
    });
});




    // Abrir modal e carregar dados
    $(document).on('click', '.botao-edit-promo', function () {
        let id = $(this).data('id');

        $.get(`/promotions/${id}`, function (data) {
            $('#promotion_id').val(data.id);
            $('#amount').val(data.amount);
            $('#level_limit').val(data.level_limit);
            editPromo.show();
        });
    });

    // Atualizar review
    $('#editPromotionForm').submit(function (e) {
        e.preventDefault();

        let id = $('#promotion_id').val();;

        $.ajax({
        url: `/promotions/${id}`,
        type: 'PATCH',
        data: {
            amount: $('#amount').val(),
            level_limit: $('#level_limit').val(),
            promotionId: $('#promotion_id').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        },

    success: function (response) {
    const id = $('#promotion_id').val();
    const newAmount = $('#amount').val();
    const newLevel = $('#level_limit').val();

    const row = $(`#row-${id}`);
    row.find('.amount-column').text(newAmount + '%');
    row.find('.level-column').text('Lvl ' + newLevel);

    row.addClass('table-success');
    setTimeout(() => row.removeClass('table-success'), 2000);

    editPromo.hide();
},



            error: function (xhr) {
    console.error('STATUS:', xhr.status);
    console.error('RESPONSE:', xhr.responseText);
    alert('Status: ' + xhr.status);
}
        });
    });

    $(document).on('click', '.botao-create-promo', function () {
            document.getElementById('createPromotionForm').reset();
            createPromo.show();
    });
    // Atualizar review
    $('#createPromotionForm').submit(function (e) {
        e.preventDefault();
        $.ajax({
        url: `/promotions/create`,
        type: 'POST',
        data: {
            amount: $('#amount-create').val(),
            level_limit: $('#level_limit-create').val(),
            productId: $('#product_search').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        },

    success: function (response) {
    const promo = response.promotion;
    console.log(promo);
    const newRow = `
        <tr id="row-${promo.id}" class="table-success">
            <td>${promo.product.name}</td>
            <td><code class="text-muted" style="padding-left: 2.5vw">#${promo.product.id}</code></td>
            <td class="amount-column" style="padding-left: 3.5vw">${promo.amount}%</td>
            <td style="padding-left: 1.5vw"><span class="badge bg-info text-dark level-column">Lvl ${promo.level_limit}</span></td>
            <td class="text-center">
                <button class="btn btn-primary btn-sm botao-edit-promo botao" data-id="${promo.id}">
                    Edit
                </button>
                <form action="/promotions/${promo.id}" method="POST" class="d-inline">
                    <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-sm btn-danger" 
                            onclick="return confirm('Are you sure?')">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
    `;

    // 3. Append to the table body (replace #promoTableBody with your <tbody> ID)
    $('#promoTableBody').prepend(newRow);
    setTimeout(() => $(`#row-${promo.id}`).removeClass('table-success'), 2000);
    createPromo.hide();
},



            error: function (xhr) {
    console.error('STATUS:', xhr.status);
    console.error('RESPONSE:', xhr.responseText);
    alert('Status: ' + xhr.status);
}
        });

        


    });

        $(document).on('click', '.botao-report', function () {
        let id = $(this).data('id');
        $('#review_id').val(id);
        $('#description').val('');
        reportReview.show();
    });

    $('#reportReviewForm').submit(function (e) {
        e.preventDefault();
        $.ajax({
        url: `/reports/create`,
        type: 'POST',
        data: {
            description: $('#description').val(),
            reviewId: $('#review_id').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        },

    success: function (response) {
    reportReview.hide();
    let alertHtml = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Report submitted successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    setTimeout(function() {
        let alertNode = document.querySelector('#message-container .alert');
        if (alertNode) {
            let bsAlert = new bootstrap.Alert(alertNode);
            bsAlert.close();
        }
    }, 2000);   
    // Put it inside the message container
    $('#message-container').html(alertHtml);
},



            error: function (xhr) {
    console.error('STATUS:', xhr.status);
    console.error('RESPONSE:', xhr.responseText);
    alert('Status: ' + xhr.status);
}
        });
    });

    $(document).on('click', '.botao-handle-report', function () {
        let id = $(this).data('id');
        $.get(`/reports/${id}`, function (data) {
            console.log(data.id);
            console.log(data.description);
            console.log(data.rep_review.review.description);
            $('#report_id').val(data.id);
            $('#description').text(data.description);
            $('#comment').text(data.rep_review.review.description);
            handleReport.show();
        });
    });

    $('#handleReportForm').submit(function (e) {
        e.preventDefault();
        $.ajax({
        url: `/reports/handle`,
        type: 'PATCH',
        data: {
            reportId: $('#report_id').val(),
            status: $('input[name="reportAction"]:checked').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        },

    success: function (response) {
    handleReport.hide();
    const reports = response.reports ?? [response.report];
    console.log(reports);
    $.each(reports, function(index, report) {
        console.log(report);
        let row = $('#row-' + report.id);
        let badge = row.find('.badge');
        let button = row.find('.botao-handle-report');

        badge.text(report.status.charAt(0).toUpperCase() + report.status.slice(1));
        badge.removeClass('bg-warning text-dark bg-success bg-danger');
        
        if (report.status === 'accepted') {
            badge.addClass('bg-success');
        } else if (report.status === 'rejected') {
            badge.addClass('bg-danger');
        }

        button.fadeOut(300, function() {
            $(this).remove(); 
        });
    });
},



            error: function (xhr) {
    console.error('STATUS:', xhr.status);
    console.error('RESPONSE:', xhr.responseText);
    alert('Status: ' + xhr.status);
}
        });
    });
});

// Listen for the PostLike event on the notifications channel
window.Echo.channel("notifications").listen(".post.like", (e) => {
    // Notice the dot before post.like, since we defined a custom event name
    console.log("Notification received:", e);

    // Get the notification elements
    const notification = document.getElementById("notification");
    const closeButton = document.getElementById("closeButton");
    const notificationText = document.getElementById("notificationText");

    // Display the notification message
    notificationText.textContent = e.message;
    notification.classList.add("show");

    // Add event listener to close button
    closeButton.addEventListener("click", function () {
        notification.classList.remove("show");
    });

    // Automatically hide the notification after 5 seconds
    setTimeout(function () {
        notification.classList.remove("show");
    }, 5000);
});

// The like function to be called when the like button is clicked
window.like = function (postId) {
    try {
        fetch(`/post/${postId}/like`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
        })
            .then((response) => response.json())
            .then((data) => {
                console.log("Like successful:", data);

                // Find the button and update its appearance
                const button = document.querySelector(`#post${postId} button`);
                if (button) {
                    button.textContent = "Liked!";
                    button.classList.remove("not-clicked");
                    button.classList.add("clicked");
                }
            })
            .catch((error) => {
                console.error("Error liking post:", error);
            });
    } catch (error) {
        console.error("Error in like function:", error);
    }
};

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.update-status-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const orderId = this.dataset.id;
            const status = this.querySelector('select[name="status"]').value;
            const token = this.querySelector('input[name="_token"]').value;

            fetch(`/orders/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    _method: 'PATCH',
                    status: status
                })
            })
            .then(data => {
                let alertHtml = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Status updated successfully! ✅
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $('#message-container').html(alertHtml);

                // 3. Set the auto-close timer
                setTimeout(function() {
                    let alertNode = document.querySelector('#message-container .alert');
                    if (alertNode) {
                        let bsAlert = new bootstrap.Alert(alertNode);
                        bsAlert.close();
                    }
                }, 2000);
            })
            .catch(err => console.error(err));
        });
    });
});
