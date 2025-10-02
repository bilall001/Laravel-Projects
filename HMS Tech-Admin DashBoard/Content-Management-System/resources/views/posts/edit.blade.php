@extends('layouts.main')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header text-white d-flex justify-content-between align-items-center"
                style="background-color: #001B2F">
                <h4 class="mb-0">Edit Post</h4>
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

                <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data"
                    id="postForm">
                    @csrf
                    @method('PUT')

                    {{-- Title --}}
                    <div class="form-group mb-3">
                        <label for="title">Post Title</label>
                        <input type="text" id="title" name="title" class="form-control"
                            value="{{ old('title', $post->title) }}" required>
                    </div>

                    {{-- Content --}}
                    <div class="form-group mb-3">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" rows="5" class="form-control">{{ old('content', $post->content) }}</textarea>
                    </div>

                    {{-- Featured Image --}}
                    <div class="form-group mb-3">
                        <label for="featured_image">Featured Image</label>
                        <input type="file" id="featured_image" name="featured_image" class="form-control">
                        <div class="mt-2">
                            <img id="featured_image_preview"
                                src="{{ $post->featured_image ? asset('storage/' . $post->featured_image) : '' }}"
                                alt="Preview" class="img-thumbnail"
                                style="max-height: 200px; {{ $post->featured_image ? '' : 'display:none;' }}">
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="form-group mb-3">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" class="form-control">
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sub Category --}}
                    <div class="form-group mb-3">
                        <label for="sub_category_id">Sub Category</label>
                        <select id="sub_category_id" name="sub_category_id" class="form-control">
                            <option value="">Select Subcategory</option>
                            @foreach ($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}"
                                    {{ old('sub_category_id', $post->sub_category_id) == $subcategory->id ? 'selected' : 'null' }}>
                                    {{ $subcategory->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tags --}}
                    <div class="form-group mb-3">
                        <label for="tags">Tags</label>
                        <select id="tags" name="tags[]" class="form-control" multiple="multiple" style="width:100%;">
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}"
                                    {{ in_array($tag->id, old('tags', $post->tags->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select name="status" class="form-control">
                            <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft
                            </option>
                            <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>
                                Published</option>
                        </select>
                    </div>

                    {{-- Published Date --}}
                    <div class="form-group mb-3">
                        <label for="published_at">Publish Date</label>
                        <input type="datetime-local" name="published_at" class="form-control"
                            value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}">
                    </div>

                    {{-- Dropzone for multiple images --}}
                    <div class="form-group mb-3">
                        <label for="images">Upload Images</label>
                        <div class="dropzone" id="postImageDropzone"></div>
                    </div>

                    <button type="submit" class="btn btn-success">Update Post</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        Dropzone.autoDiscover = false;
        $(document).ready(function() {

            // Featured image preview
            $('#featured_image').on('change', function() {
                let input = this;
                if (input.files && input.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#featured_image_preview').attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            });

            // Preselected tags
            let selectedTags = @json(old('tags', $post->tags->pluck('id')->toArray()));
            // Preselected subcategory
            let selectedSubcategory = "{{ old('sub_category_id', $post->sub_category_id) }}";

            // Initialize Select2 for tags
            $('#tags').select2({
                placeholder: "Select Tags",
                allowClear: true
            });

            // Category change -> fetch subcategories & tags
            function loadSubcategoriesAndTags(categoryId) {
                if (!categoryId) {
                    $('#sub_category_id').html('<option value="">Select Subcategory</option>');
                    $('#tags').empty().trigger('change');
                    return;
                }

                // Fetch subcategories
                $.getJSON('/categories/' + categoryId + '/subcategories', function(subs) {
                    let subOptions = '<option value="">Select Subcategory</option>';
                    subs.forEach(function(sub) {
                        let selected = (sub.id == selectedSubcategory) ? 'selected' : '';
                        subOptions += `<option value="${sub.id}" ${selected}>${sub.name}</option>`;
                    });
                    $('#sub_category_id').html(subOptions);
                });

                // Fetch tags
                $.getJSON('/categories/' + categoryId + '/tags', function(tags) {
                    let tagOptions = '';
                    tags.forEach(function(tag) {
                        let selected = selectedTags.includes(tag.id) ? 'selected' : '';
                        tagOptions += `<option value="${tag.id}" ${selected}>${tag.name}</option>`;
                    });
                    $('#tags').html(tagOptions).trigger('change');
                });
            }

            $('#category_id').on('change', function() {
                let categoryId = $(this).val();
                loadSubcategoriesAndTags(categoryId);
            });

            // If category is preselected, trigger AJAX to load subcategories and tags
            let initialCategoryId = $('#category_id').val();
            if (initialCategoryId) {
                loadSubcategoriesAndTags(initialCategoryId);
            }

            // Dropzone setup
            let myDropzone = new Dropzone("#postImageDropzone", {
                url: "{{ route('posts.uploadImage') }}",
                paramName: "file",
                maxFilesize: 5,
                acceptedFiles: ".jpeg,.jpg,.png,.gif",
                addRemoveLinks: true,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                init: function() {
                    let existingImages = @json($post->images);

                    existingImages.forEach(image => {
                        let mockFile = {
                            name: image.image_path.split('/').pop(),
                            size: 12345, // You can set actual size if available
                            serverId: image.id
                        };
                        this.emit("addedfile", mockFile);
                        this.emit("thumbnail", mockFile, "{{ asset('storage/') }}/" + image.image_path);
                        this.emit("complete", mockFile);

                        // Mark as already uploaded (optional)
                        mockFile.status = Dropzone.SUCCESS;
                        mockFile.accepted = true;

                        // Append hidden input for existing images
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'post_images[]';
                        input.value = "{{ asset('storage/') }}/" + image.image_path;
                        input.id = 'file-' + image.id;
                        document.getElementById('postForm').appendChild(input);
                    });
                },
                success: function(file, response) {
                    file.serverId = response.file_id;
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
                            success: function() {
                                let input = document.getElementById('file-' + file
                                .serverId);
                                if (input) input.remove();
                            }
                        });
                    }
                    return file.previewElement && file.previewElement.parentNode.removeChild(file
                        .previewElement);
                }
            });
        });
    </script>
@endsection
