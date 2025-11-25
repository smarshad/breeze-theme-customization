$(document).ready(function() {

    $(document).on('submit', '.data-ajax-submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let action = form.attr('action');
        let method = form.attr('method') || 'POST';
        let formData = form.serialize();
        handleAjaxFormSubmit(action, method, formData);
    });

});

function handleAjaxFormSubmit(action, method, formData) {
    $.ajax({
        url: action,
        method: method,
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        success(response) {
            console.log(response);
            alert('Updated successfully!');
        },
        error(xhr) {
            console.log(xhr.responseJSON);

            if (xhr.status === 422) {
                // Laravel validation error
                showValidationErrors(xhr.responseJSON.errors);
            } else {
                alert('An error occurred.');
            }
        }
    });
}

function showValidationErrors(errors) {
    // Clear previous
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    console.log(errors);

    $.each(errors, function(field, messages) {
        let input = $('[name="' + field + '"]');
        input.addClass('is-invalid');
        input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
    });
}
