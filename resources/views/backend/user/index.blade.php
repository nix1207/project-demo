@extends("backend.layouts.main")
@section("title", 'Users')
@section("css")
    .far {
    font-size: 25px;
    }
    .fa-trash-alt {
    color: red;
    }
    .fa-edit {
    color: yellowgreen;
    margin-left: 5px;
    }
    .far:hover {
    color: #f1b0b7;

    }

@endsection
@section("main_content")
    <div class="main_content_iner ">
        @if(session('success'))

        @endif
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="white_card card_height_100 mb_30">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0">User Data</h3>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <div class="QA_section">
                                <div class="white_box_tittle list_header">
                                    <h4>Users</h4>
                                    <div class="box_right d-flex lms_block">
                                        <div class="serach_field_2">
                                            <div class="search_inner">
                                                <form Active="#">
                                                    <div class="search_field">
                                                        <input type="text" placeholder="Search content here...">
                                                    </div>
                                                    <button type="submit"><i class="ti-search"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="add_button ml-10">
                                            <a href="{{ route("user.create") }}" data-target="#addcategory"
                                               class="btn_1">Add New</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="QA_table mb_30">
                                    <!-- table-responsive -->
                                    <table class="table lms_table_active ">
                                        <thead>
                                        <tr>
                                            <th scope="col">STT</th>
                                            <th scope="col">H??? v?? t??n</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Phone</th>
                                            <th scope="col">Avatar</th>
                                            <th scope="col">Tr???ng th??i</th>

                                            <th scope="col">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <th scope="row"><a href="#" class="question_content">{{ $loop->index + 1 }}</a></th>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->phone_number }}</td>
                                                <td>
                                                    @if($user->avatar === "")
                                                    <img src="https://anhdep123.com/wp-content/uploads/2021/05/hinh-avatar-trang.jpg" alt="" width="100px">
                                                    @else
                                                        <img src="{{ asset("$user->avatar") }}" alt="" width="100px">
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($user->status == 1)
                                                    <a  href="#" class="status_btn">K??ch ho???t</a>
                                                    @else
                                                        <a style="background-color: red" href="#" class="status_btn">B??? kh??a</a>
                                                    @endif

                                                </td>
                                                <td>
                                                    <a data-url="{{ route("user.delete" , ['id' => $user->id]) }}"
                                                       href="" class="far fa-trash-alt"></a>
                                                    <a href="{{ route("user.edit" , ['id' => $user->id]) }}"
                                                       class="far fa-edit"></a>
                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">

                </div>
            </div>
        </div>
    </div>
@endsection
@section("append_js")
    <script>
        function removeProduct(e) {
            e.preventDefault();
            let urlRequest = $(this).data('url');
            let that = $(this);
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "GET",
                        url: urlRequest,
                        success : function (data) {
                            if (data.code == 200) {
                                that.parent().parent().remove();
                                Swal.fire(
                                    'Deleted!',
                                    'Your file has been deleted.',
                                    'success'
                                )
                            }
                        } ,
                        error : function () {
                        }
                    })
                }
            })
        }
        $(function () {
            $(document).on('click', '.fa-trash-alt', removeProduct)
        })
    </script>
@endsection
