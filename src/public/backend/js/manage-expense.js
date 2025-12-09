let table;

$(function () {
    const url = $('#listRoute').val();
    console.log(url);
    initList(url);

    function initList(url) {


        if ($.fn.DataTable.isDataTable('#datatable')) {
            table.ajax.url(url).load();
            return;
        }

        table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: true,
            paging: true,
            ordering: true,
            pageLength: 5,
            ajax: {
                url: url,
                type: "GET",

                // 🔥 FIX: The 'data' function MUST be inside the 'ajax' object
                data: function (d) {
                    // Calculate the page number: (offset / limit) + 1
                    var page = (d.start / d.length) + 1;

                    // Map DataTables parameters to Laravel's expected parameters
                    d.page = page;
                    d.per_page = d.length;

                    // Clean up DataTables' default parameters
                    delete d.start;
                    delete d.length;

                    // Note: DataTables will automatically handle the 'draw' parameter
                },

                // The dataSrc function is correctly placed here
                dataSrc: function (json) {
                    // Your backend is now sending the correct format, 
                    // so this function is correct for extracting the data array.
                    return json.data || [];
                }
            },

            // The 'data' function has been removed from here

            columns: [
                { data: "id" },
                { data: 'amount', render: function (data) { return '$' + parseFloat(data).toFixed(2); } },
                { data: "category.name" },
                { data: "expenseType.name" },
                { data: "paymentMethod.name" },
                { data: "cashback", render: function (data) { return '$' + parseFloat(data).toFixed(2); } },
                { data: "description" },
                { data: "notes" },
                {
                    data: "file_path",
                    render: data => filePath(data)

                },
                { data: "created_at" },
                { data: "creator.name" },
                {
                    data: "id",
                    render: id => renderActionButtons(id)
                }
            ],

            language: {
                processing: `<div class="spinner-border text-primary" role="status"></div> Loading...`
            }
        });

        // 🔥 Add loader inside table rows
        table.on('processing.dt', function (e, settings, processing) {
            if (processing) {
                showTableLoader();
            }
        });
    }

    function renderActionButtons(id) {
        const editUrl = window.routes.edit.replace(':id', id);
        const deleteUrl = window.routes.delete.replace(':id', id);

        const footerHtml = `
            <button type="submit" class="btn btn-primary waves-effect waves-light js-submit-btn">
                ${window.lang.edit}
            </button>
            <button type="button" class="btn btn-info waves-effect waves-light" data-dismiss="modal">
                ${window.lang.close}
            </button>
        `.trim();

        const titleText = `${window.lang.edit} ${window.lang.category_title}`;

        return `
            <button
                class="btn btn-sm btn-primary openModel"
                data-footer='<button type="submit" class="btn btn-primary waves-effect waves-light js-submit-btn">
                ${window.lang.edit}
            </button>
            <button type="button" class="btn btn-info waves-effect waves-light" data-dismiss="modal">
                ${window.lang.close}
            </button>'
                data-url="${editUrl}"
                data-id="${id}"
                data-size="lg"
                data-title="${titleText}">
                Edit
            </button>
            <button class="btn btn-sm btn-danger btn-delete" data-action="${deleteUrl}" data-id="${id}">
                Delete
            </button>
        `;
    }

    function filePath(data) {
        const fullUrl = `${window.location.origin}/storage/${data}`;
        if (!data) return "-"; // no file
            return `<a href="${fullUrl}" target="_blank" class="btn btn-secondary">View File</a>`;
    }

    function showTableLoader() {
        const colspan = $('#datatable thead th').length;

        $('#datatable tbody').html(`
            <tr class="table-loading-row">
                <td colspan="${colspan}">
                    <div class="spinner-border text-primary" role="status"></div>
                    <span class="ms-2">Loading...</span>
                </td>
            </tr>
        `);
    }

    function hideTableLoader() {
        // DataTables will repopulate rows automatically, so nothing needed here
    }
});
