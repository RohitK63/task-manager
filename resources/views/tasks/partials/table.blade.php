<table class="table table-bordered">
    <thead>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($tasks as $task)
        <tr>
            <td>{{ $task->title }}</td>
            <td>{{ $task->description }}</td>
            <td>{{ ucfirst($task->status) }}</td>
            <td>
                <button class="btn btn-sm btn-success status-btn" data-id="{{ $task->id }}">
                    {{ $task->status === 'pending' ? 'Mark Completed' : 'Mark Pending' }}
                </button>
                <button class="btn btn-sm btn-info edit-btn" data-id="{{ $task->id }}">Edit</button>
                <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $task->id }}">Delete</button>
            </td>
        </tr>
        @empty
        <tr><td colspan="4">No tasks found.</td></tr>
        @endforelse
    </tbody>
</table>

{{ $tasks->links() }}
