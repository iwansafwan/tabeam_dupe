<form action="{{ route('admin.create_treasurer') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row mb-3 mx-0">
        <div class="col-md-4 d-flex align-items-center">
            <label class="form-label mb-0">Full Name : </label>
        </div>
        <div class="col-md-8">
            <input type="text" name="name" id="name" class="form-control" style="border-radius:15px !important;" value="{{ old('name') }}" placeholder="Please type full name." required>
        </div>
    </div>
    <div class="row mb-3 mx-0">
        <div class="col-md-4 d-flex align-items-center">
            <label class="form-label mb-0">Email : </label>
        </div>
        <div class="col-md-8">
            <input type="email" name="email" id="email" class="form-control" style="border-radius:15px !important;" value="{{ old('email') }}" placeholder="Please type email." required>
        </div>
    </div>
    <div class="row mb-3 mx-0">
        <div class="col-md-4 d-flex align-items-center">
            <label class="form-label mb-0">Password : </label>
        </div>
        <div class="col-md-8">
            <input type="password" name="password" id="password" class="form-control" style="border-radius:15px !important;" value="{{ old('password') }}" placeholder="Please type password." required>
        </div>
    </div>
    <div class="row mb-3 mx-0">
        <div class="col-md-4 d-flex align-items-center">
            <label class="form-label mb-0">Confirm Password : </label>
        </div>
        <div class="col-md-8">
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" style="border-radius:15px !important;" value="{{ old('password_confirmation') }}" placeholder="Please type confirm password." required>
        </div>
    </div>
    <div class="row mb-3 mx-0">
        <div class="col-md-4 d-flex align-items-center">
            <label class="form-label mb-0">Temporary Contact : </label>
        </div>
        <div class="col-md-8">
            <input type="text" name="contact_number" id="contact_number" class="form-control" style="border-radius:15px !important;" value="{{ old('contact_number') }}" placeholder="Please type temporary contact number." required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-success">
                Submit
            </button>
        </div>
    </div>
</form>
