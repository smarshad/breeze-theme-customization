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
            pageLength:$('meta[name="perPage"]').attr('content'),
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
                { data: 'name'},
                { data: "route" },
                { data: "url" },
                { data: "icon" },
                { data: "parent_name" },
                { data: "children.length" },
                { data: "order" },
                { data: "is_active" },
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
        const titleText = `${window.lang.edit} ${window.lang.category_title}`;

        return `
            <a
                class="btn btn-sm btn-primary"
                href='${editUrl}'>
                ${window.lang.edit}
            </a>'
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
