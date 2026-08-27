
function showAddForm() {

    document.getElementById('addForm').style.display = 'block';

}


function hideAddForm() {

    document.getElementById('addForm').style.display = 'none';

}


function editCategory(id) {

    document.getElementById(
        'name-' + id
    ).style.display = 'none';


    document.getElementById(
        'edit-' + id
    ).style.display = 'block';

}


function cancelEdit(id) {

    document.getElementById(
        'name-' + id
    ).style.display = 'inline';


    document.getElementById(
        'edit-' + id
    ).style.display = 'none';

}
document.addEventListener('DOMContentLoaded', function() {
    var fileInput = document.querySelector('.upload input[type="file"]');
    var previewImg = document.getElementById('profilePreview');
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(ev) {
                    previewImg.src = ev.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});
