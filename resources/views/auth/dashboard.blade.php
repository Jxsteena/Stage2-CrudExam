@extends('auth.layouts')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><strong>Dashboard</strong></div>
            <div class="card-body">
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    {{ $message }}
                </div>
                @else
                <div class="alert alert-success">
                    Hello, {{ Auth::user()->name }}, You are logged in!
                </div>
                @endif

                <form id="fileUploadForm" method="POST" enctype="multipart/form-data">
                    @csrf
                <button type="button" id="addRowButton">Add Row</button><br>
                <table id="editable-table" style="width: 100%; margin-top: 10px">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($crud) && is_iterable($crud))
                            @foreach ($crud as $row)
                                <tr id="row-{{ $row->id }}">
                                    <td style="display:none"><input type="text" name="editcrud[{{ $row->id }}][id]" value="{{ $row->id }}" disabled /></td>
                                    <td><input type="text" name="editcrud[{{ $row->id }}][title]" value="{{ $row->title }}" disabled /></td>
                                    <td><input type="text" name="editcrud[{{ $row->id }}][description]" value="{{ $row->description }}" disabled /></td>
                                    <td><input type="date" name="editcrud[{{ $row->id }}][date]" value="{{ $row->date }}" disabled /></td>
                                    <td>
                                        <input type="hidden" class="is-deleted-input-{{ $row->id }}" name="editcrud[{{ $row->id }}][isdeleted]" value="{{ $row->isdeleted }}" />
                                        <button type="button" class="editRowButton" data-row-id="{{ $row->id }}">Edit</button>
                                        <button type="button" class="deleteRowButton" data-row-id="{{ $row->id }}">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <br><br>
                <center>
                    <button type="submit" class="btn btn-primary btn-sm btndesign btnsubmit"><i class="fa fa-send"></i> Save</button>
                    <button type="button" class="btn btn-default btn-sm btndesign" data-dismiss="modal">Cancel</button>
                </center>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){

    $(".editRowButton").click(function () {
            var rowId = $(this).data("row-id");
            var row = $("#row-" + rowId);

            // Toggle disabled attribute for input fields in the row
            row.find('input').prop('disabled', function (i, disabled) {
                return !disabled;
            });
        });

	$('#fileUploadForm').submit(function(e) {
    e.preventDefault(); // Prevent the default form submission

    // Enable all disabled input fields temporarily
    $(this).find(':input:disabled').prop('disabled', false);

    // Create a FormData object to store the form data
    var formData = new FormData(this);

    // Disable the input fields again after creating FormData
    $(this).find(':input:disabled').prop('disabled', true);
    
      $.ajax({
            url: "{{ route('savecrud') }}",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: formData,
            processData: false,     
            contentType: false,     
            cache: false,
            method:"post",
            dataType:"json",
            success:function(e){
              if(e.status == "OK")
              {
                alert('Table has been updated');
                location.reload();
              }
              else
              {
                  alert(e.status);
                  location.reload();
              }
            },
            error:function(e){
                console.log(e);
                console.log(JSON.stringify(e));
            }
        });
}); 


    $('#addRowButton').on('click', function() {
        var timestamp = Date.now();
        var newRow = `
            <tr>
                <td style="display:none"><input type="text" name="editcrud[${timestamp}][id]" value="" /></td>
                <td><input type="text" name="editcrud[${timestamp}][title]" value="" /></td>
                <td><input type="text" name="editcrud[${timestamp}][description]" value="" /></td>
                <td><input type="date" name="editcrud[${timestamp}][date]" value="" required/></td>
                <td>
                    <input type="hidden" class="is-deleted-input-${timestamp}" name="editcrud[${timestamp}][isdeleted]" value="0" />
                    <button type="button" class="deleteRowButton">Delete</button>
                </td>
            </tr>
        `;

        $('#editable-table tbody').append(newRow);
    });
});

$(document).on('click', '.deleteRowButton', function() {
        var rowId = $(this).data('row-id');

        // Ask for confirmation
        if (confirm('Are you sure you want to delete this row?')) {
            var row = $(this).closest('tr');
            var isDeletedInput = row.find('input[name^="editcrud["][name$="][isdeleted]"]');
            var idInput = row.find('input[name^="editcrud["][name$="][id]"]');
            var arrayId = row.find('input[name^="id"]');

            // Ask for confirmation
            if (confirm('Are you sure you want to delete this row?')) {
                // Set isdeleted to 1
                isDeletedInput.val(1);

                 // Check if the id is null (empty)
                 if (idInput.val() === "") {
                    // Completely remove the row from the DOM
                    // alert('remove');
                    row.remove();
                } else {
                    // You can also hide or visually indicate that the row is deleted
                    row.hide();
                }
            }
        }
        });
</script>

@endsection