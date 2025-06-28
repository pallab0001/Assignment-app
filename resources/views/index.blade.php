<!DOCTYPE html>
<html>
<head>
    <title>Assignment Form - Laravel + Ajax</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Add / Edit User</h2>
    <form id="userForm" enctype="multipart/form-data">
        <input type="hidden" id="user_id" name="user_id">

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" id="phone" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div id="photoFields" class="form-group">
            <label>Photo Upload</label>
            <div class="input-group mb-2">
                <input type="file" name="photos[]" class="form-control">
                <div class="input-group-append">
                    <button type="button" class="btn btn-success addPhoto">+</button>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>

    <button class="btn btn-info mt-3" id="viewJsonBtn">View JSON</button>

    <h3 class="mt-4">User List</h3>
    <table class="table table-bordered mt-2">
        <thead>
            <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Photos</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="userTable">
            @foreach($data as $user)
                <tr id="row_{{ $user->id }}">
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->photos)
                            @foreach(json_decode($user->photos) as $photo)
                                <img src="{{ asset('uploads/' . $photo) }}" width="50">
                            @endforeach
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-warning editBtn" data-id="{{ $user->id }}">Edit</button>
                        <button class="btn btn-danger deleteBtn" data-id="{{ $user->id }}">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div id="jsonOutput" class="mt-4" style="display: none;">
        <h4>JSON Data</h4>
        <pre id="jsonData"></pre>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    // Add new photo input
    $(document).on('click', '.addPhoto', function(){
        $('#photoFields').append(`
            <div class="input-group mb-2">
                <input type="file" name="photos[]" class="form-control">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger removePhoto">-</button>
                </div>
            </div>
        `);
    });

    // Remove photo input
    $(document).on('click', '.removePhoto', function(){
        $(this).closest('.input-group').remove();
    });

    // Handle form submit
    $('#userForm').on('submit', function(e){
        e.preventDefault();
        let formData = new FormData(this);
        let id = $('#user_id').val();
        let url = id ? '/update/' + id : '/store';

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            contentType: false,
            processData: false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
                alert('Data saved successfully!');
                location.reload();
            },
            error: function(err){
                alert('Error occurred');
            }
        });
    });

    // Edit button
    $('.editBtn').on('click', function(){
        let id = $(this).data('id');
        $.get('/edit/' + id, function(data){
            $('#user_id').val(data.id);
            $('#name').val(data.name);
            $('#phone').val(data.phone);
            $('#email').val(data.email);

            // Remove old photo fields
            $('#photoFields').html(`
                <label>Photo Upload</label>
                <div class="input-group mb-2">
                    <input type="file" name="photos[]" class="form-control">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-success addPhoto">+</button>
                    </div>
                </div>
            `);

            if (data.photos && data.photos.length > 0) {
                let preview = '';
                data.photos.forEach(photo => {
                    preview += `<img src="/uploads/${photo}" width="50" class="mr-1">`;
                });
                $('#photoFields').append(`<div>${preview}</div>`);
            }
        });
    });

    // Delete button
    $('.deleteBtn').on('click', function(){
        if(confirm("Are you sure?")){
            let id = $(this).data('id');
            $.get('/delete/' + id, function(){
                alert('Deleted successfully!');
                $('#row_' + id).remove();
            });
        }
    });

    // View JSON
    $('#viewJsonBtn').on('click', function(){
        $.get('/view-json', function(data){
            $('#jsonData').text(JSON.stringify(data, null, 4));
            $('#jsonOutput').show();
        });
    });

});
</script>

</body>
</html>
