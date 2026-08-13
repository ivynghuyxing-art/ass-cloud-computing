
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