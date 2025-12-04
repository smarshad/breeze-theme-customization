let table;

$(function () {
    const url = $('#listRoute').val();
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
            
            ajax: {
                url: url,
                type: "GET",
                dataSrc: function (json) {
                    return json.data || [];
                }
            },
    
            columns: [
                { data: "id" },
                { data: "name" },
                { data: "slug" },
                { data: "description" },
                { data: "color_code" },
                { data: "created_at" },
                { data: "created_by" },
                {
                    data: "is_active",
                    render: data => renderStatusBadge(data)
                },
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
        return `
            <button class="btn btn-sm btn-primary edit-btn" data-id="${id}">
                Edit
            </button>
            <button class="btn btn-sm btn-danger delete-btn" data-id="${id}">
                Delete
            </button>
        `;
    }

    function renderStatusBadge(isActive) {
        return isActive
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
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
