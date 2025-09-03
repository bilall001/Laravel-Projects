@extends('layouts.main')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header text-white d-flex justify-content-between align-items-center"
                style="background-color: #001B2F">
                <h4 class="mb-0">Add New Post</h4>
                <a href="{{ route('posts.index') }}" class="btn btn-light btn-sm">Back</a>
            </div>
            <div class="card-body">

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" id="postForm">
                    @csrf

                    {{-- Title --}}
                    <div class="form-group mb-3">
                        <label for="title">Post Title</label>
                        <input type="text" id="title" name="title" class="form-control"
                            placeholder="Enter post title" value="{{ old('title') }}">
                    </div>

                    {{-- Content --}}
                    <div class="form-group mb-3">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" rows="5" class="form-control" placeholder="Write post content">{{ old('content') }}</textarea>
                    </div>

                    {{-- Featured Image --}}
                    <div class="form-group mb-3">
                        <label for="featured_image">Featured Image</label>
                        <input type="file" id="featured_image" name="featured_image" class="form-control">

                        {{-- Preview --}}
                        <div class="mt-2">
                            <img id="featured_image_preview" src="" alt="Preview" class="img-thumbnail"
                                style="max-height: 200px; display: none;">
                        </div>
                    </div>


                    {{-- Category --}}
                    <div class="form-group mb-3">
                        <label for="category">Category</label>
                        <select id="category_id" name="category_id" class="form-control">
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sub Category --}}
                    <div class="form-group mb-3">
                        <label for="sub_category">Sub Category</label>
                        <select id="sub_category_id" name="sub_category_id" class="form-control">
                            <option value="">Select Sub Category</option>
                            {{-- AJAX Options Here --}}
                        </select>
                    </div>

                    {{-- Tags --}}
                    {{-- Tags --}}
                    <div class="form-group mb-3">
                        <label for="tags">Tags</label>
                        <select id="tags" name="tags[]" class="form-control" multiple="multiple" style="width: 100%;">
                            {{-- AJAX Options Here --}}
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select name="status" class="form-control">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published
                            </option>
                        </select>
                    </div>

                    {{-- Published Date --}}
                    <div class="form-group mb-3">
                        <label for="published_at">Publish Date</label>
                        <input type="datetime-local" name="published_at" class="form-control"
                            value="{{ old('published_at') }}">
                    </div>

                    {{-- Multiple Images Dropzone --}}
                    {{-- <div class="form-group mb-3">
                    <label>Upload Extra Images</label>
                    <div class="dropzone" id="postImagesDropzone"></div>
                </div> --}}
                    <div class="form-group mb-3">
                        <label for="images">Upload Images</label>
                        <div class="dropzone" id="postImageDropzone"></div>
                    </div>
                    <button type="submit" class="btn btn-success">Save Post</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Dropzone --}}


    <script>
        Dropzone.options.postImageDropzone = {
            url: "{{ route('posts.uploadImage') }}", // route to upload
            paramName: "file",
            maxFilesize: 5, // MB
            acceptedFiles: ".jpeg,.jpg,.png,.gif",
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(file, response) {
                console.log(response);
                file.serverId = response.file_id;

                // Add hidden input to form
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'post_images[]';
                input.value = response.file_path;
                input.id = 'file-' + response.file_id;
                document.getElementById('postForm').appendChild(input);
            },
            removedfile: function(file) {
                if (file.serverId) {
                    $.ajax({
                        url: "/posts/delete-image/" + file.serverId,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            console.log("Image deleted");

                            // Remove hidden input
                            let input = document.getElementById('file-' + file.serverId);
                            if (input) input.remove();
                        }
                    });
                }
                return file.previewElement && file.previewElement.parentNode.removeChild(file.previewElement);
            }
        };

        $(document).ready(function() {
            // When Category changes → fetch Subcategories
            $('#category_id').on('change', function() {
                let categoryId = $(this).val();
                if (categoryId) {
                    $.ajax({
                        url: '/categories/' + categoryId + '/subcategories',
                        type: 'GET',
                        success: function(res) {
                            $('#sub_category_id').empty().append(
                                '<option value="">Select Subcategory</option>');
                            $.each(res, function(key, subcategory) {
                                $('#sub_category_id').append('<option value="' +
                                    subcategory.id + '">' + subcategory.name +
                                    '</option>');
                            });
                            $('#tags').empty(); // reset tags
                        }
                    });
                } else {
                    $('#sub_category_id').empty().append('<option value="">Select Subcategory</option>');
                    $('#tags').empty();
                }
            });

            // When Subcategory changes → fetch Tags
            // Initialize Select2
            $('#tags').select2({
                placeholder: "Select Tags",
                allowClear: true
            });

            // When Category changes → fetch Tags
            $('#category_id').on('change', function() {
                let CategoryId = $(this).val();
                if (CategoryId) {
                    $.ajax({
                        url: '/categories/' + CategoryId + '/tags',
                        type: 'GET',
                        success: function(res) {
                            // Clear old options
                            $('#tags').empty();

                            // Populate new options
                            $.each(res, function(key, tag) {
                                let option = new Option(tag.name, tag.id, false, false);
                                $('#tags').append(option);
                            });

                            // Refresh Select2
                            $('#tags').trigger('change');
                        }
                    });
                } else {
                    $('#tags').empty().trigger('change');
                }
            });
        });
        const input = document.getElementById('featured_image');
        const preview = document.getElementById('featured_image_preview');

        input.addEventListener('change', function(event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }

                reader.readAsDataURL(file);
            } else {
                // If no file selected, hide preview
                preview.src = "";
                preview.style.display = "none";
            }
        });
    </script>
@endsection
