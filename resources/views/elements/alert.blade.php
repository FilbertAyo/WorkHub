

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.body.addEventListener("click", function(event) {
            if (event.target.closest(".permission-alert")) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "You don't have permission to perform this action!",
                });
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>

    .toast-container {
  position: relative;
  width: 100%;
}

.toast {
  position: absolute;
  top: 10px;
  right: 30px;
  z-index: 1050;
  width: auto;
}

</style>

@if (session('success'))

<div class="toast-container">
    <div class="toast fade show bg-success" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <strong class="mr-auto">Success!</strong>
        <small>Now</small>

      </div>
      <div class="toast-body bg-white">{{ session('success') }}</div>
    </div>
  </div>
@elseif (session('error'))
<div class="toast-container">
    <div class="toast fade show bg-danger" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <strong class="mr-auto">Error!</strong>
        <small>Now</small>

      </div>
      <div class="toast-body bg-white">{{ session('error') }} </div>
    </div>
  </div>
@elseif (session('warning'))
<div class="toast-container">
    <div class="toast fade show bg-warning" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <strong class="mr-auto">Warning!</strong>
        <small>Now</small>

      </div>
      <div class="toast-body bg-white">{{ session('warning') }} </div>
    </div>
  </div>
@endif

@if (isset($errors) && $errors->any())
    <div class="toast-container">
        @foreach ($errors->all() as $error)
            <div class="toast fade show bg-danger mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="mr-auto">Validation Error!</strong>
                    <small>Now</small>
                </div>
                <div class="toast-body bg-white">
                    {{ $error }}
                </div>
            </div>
        @endforeach
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toasts = document.querySelectorAll('.toast');
        if (!toasts || toasts.length === 0) return;

        toasts.forEach(function (el) {
            try {
                if (window.bootstrap && bootstrap.Toast) {
                    var t = new bootstrap.Toast(el, { autohide: true, delay: 4000 });
                    t.show();
                } else {
                    setTimeout(function () {
                        el.classList.remove('show');
                        el.classList.add('hide');
                        if (el && el.parentNode) {
                            el.parentNode.removeChild(el);
                        }
                    }, 5000);
                }
            } catch (e) {}
        });
    });
</script>
