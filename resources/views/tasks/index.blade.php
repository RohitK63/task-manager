<!DOCTYPE html>
<html>
<head>
    <title>Task Manager</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        #spinner { display: none; }
    </style>
</head>
<body class="p-4">
    <div class="container">
        <h2 class="mb-4">Task Management System</h2>

        <div id="alert-box"></div>

        <div class="mb-3">
            <label for="statusFilter">Filter by Status:</label>
            <select id="statusFilter" class="form-select w-25 d-inline">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
            </select>
        </div>

        <form id="taskForm">
            <input type="hidden" id="taskId">
            <div class="mb-2">
                <input type="text" id="title" name="title" class="form-control" placeholder="Task Title" required>
            </div>
            <div class="mb-2">
                <textarea id="description" name="description" class="form-control" placeholder="Description"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Task</button>
        </form>

        <div id="spinner" class="mt-3">Loading...</div>

        <div id="taskTable" class="mt-4">
            @include('tasks.partials.table', ['tasks' => $tasks])
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

function loadTasks(status = '', page = 1) {
    $('#spinner').show();
    $.get("/", { status: status, page: page }, function(data) {
        $('#taskTable').html(data);
        $('#spinner').hide();
    });
}

$('#taskForm').submit(function(e) {
    e.preventDefault();
    let id = $('#taskId').val();
    let method = id ? 'PUT' : 'POST';
    let url = id ? '/tasks/' + id : '/tasks';
    $.ajax({
        url: url,
        method: method,
        data: { title: $('#title').val(), description: $('#description').val() },
        success: function(res) {
            $('#taskForm')[0].reset(); $('#taskId').val('');
            showAlert(res.message);
            loadTasks($('#statusFilter').val());
        }
    });
});

$(document).on('click', '.edit-btn', function() {
    let id = $(this).data('id');
    $.get('/tasks/' + id + '/edit', function(task) {
        $('#taskId').val(task.id);
        $('#title').val(task.title);
        $('#description').val(task.description);
    });
});

$(document).on('click', '.delete-btn', function() {
    if (!confirm('Delete this task?')) return;
    $.ajax({
        url: '/tasks/' + $(this).data('id'),
        method: 'DELETE',
        success: function(res) {
            showAlert(res.message);
            loadTasks($('#statusFilter').val());
        }
    });
});

$(document).on('click', '.status-btn', function() {
    $.ajax({
        url: '/tasks/' + $(this).data('id') + '/toggle-status',
        method: 'PATCH',
        success: function(res) {
            showAlert(res.message);
            loadTasks($('#statusFilter').val());
        }
    });
});

$('#statusFilter').change(function() {
    loadTasks($(this).val());
});

$(document).on('click', '.pagination a', function(e) {
    e.preventDefault();
    let page = $(this).attr('href').split('page=')[1];
    loadTasks($('#statusFilter').val(), page);
});

function showAlert(message) {
    $('#alert-box').html(`<div class="alert alert-success">${message}</div>`);
    setTimeout(() => $('#alert-box').html(''), 3000);
}
</script>
</body>
</html>
