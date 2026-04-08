<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>User Management</title>

<style>

* {
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', sans-serif;
    background: #f4f6f9;
    margin: 0;
}

/* Container */
.container {
    width: 95%;
    max-width: 1200px;
    margin: 20px auto;
}

/* Card */
.card {
    background: #fff;
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

/* Buttons */
.btn {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    color: #fff;
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.btn-primary { background: #007bff; }
.btn-success { background: #28a745; }
.btn-danger { background: #dc3545; }
.btn-warning { background: #ffc107; color: black; }

/* Inputs */
input, textarea {
    width: 100%;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

/* Flex */
.flex {
    display: flex;
    gap: 10px;
}

/* Responsive Flex */
@media(max-width:768px){
    .flex {
        flex-direction: column;
    }
}

/* Table */
.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px;
}

th, td {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

/* Top Bar */
.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

@media(max-width:768px){
    .top-bar {
        flex-direction: column;
        align-items: stretch;
    }
}

/* Search */
.search-box {
    display: flex;
    gap: 8px;
}

.search-box input {
    min-width: 150px;
}

/* Actions */
.action-btns {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

/* Mobile tweak */
@media(max-width:500px){
    .btn {
        font-size: 12px;
        padding: 5px 8px;
    }
}

</style>
</head>

<body>

<div class="container">
    @yield('content')
</div>

<script>
function deleteUser(id){
    if(confirm('Delete this user?')){
        document.getElementById('delete-form-'+id).submit();
    }
}
</script>

</body>
</html>